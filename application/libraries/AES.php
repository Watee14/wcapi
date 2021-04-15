<?php

/**
 * Wrapper class for using of mcrypt functions
 * @author Vászka Mihály
 */
class AES
{

	protected $key;
	protected $data;
	protected $method;

	/**
	 * Available OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
	 *
	 * @var type $options
	 */
	protected $options = 0;

	/**
	 *
	 * @param type $data
	 * @param type $key
	 * @param type $block_size
	 * @param type $mode
	 */
	function __construct($data = NULL, $key = NULL, $block_size = NULL, $mode = 'CBC')
	{
		$this->set_data($data);
		$this->set_key($key);
		$this->set_methode($block_size, $mode);
	}

	/**
	 *
	 * @param type $data
	 */
	public function set_data($data)
	{
		$this->data = $data;
	}

	/**
	 *
	 * @param type $key
	 */
	public function set_key($key)
	{
		$this->key = $key;
	}

	/**
	 * CBC 128 192 256
	 * CBC-HMAC-SHA1 128 256
	 * CBC-HMAC-SHA256 128 256
	 * CFB 128 192 256
	 * CFB1 128 192 256
	 * CFB8 128 192 256
	 * CTR 128 192 256
	 * ECB 128 192 256
	 * OFB 128 192 256
	 * XTS 128 256
	 * @param type $block_size
	 * @param type $mode
	 */
	public function set_methode($block_size, $mode = 'CBC')
	{
		if ($block_size == 192 && in_array('', array('CBC-HMAC-SHA1', 'CBC-HMAC-SHA256', 'XTS')))
		{
			$this->method = NULL;
			throw new Exception('Invlid block size and mode combination!');
		}
		$this->method = 'AES-'.$block_size.'-'.$mode;
	}

	/**
	 *
	 * @return boolean
	 */
	public function validate_params()
	{
		if ($this->data !== NULL && $this->method !== NULL)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// protected function get_iv()
	// {
	// 	// return mcrypt_create_iv(mcrypt_get_iv_size($this->cipher, $this->mode), MCRYPT_RAND);
	// 	return openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
	// }

	/**
	 * @return type
	 * @throws Exception
	 */
	public function encrypt()
	{
		if ($this->validate_params())
		{
			$ret = openssl_encrypt($this->data, $this->method, $this->key, $this->options);
		   	return trim($ret);
		}
		else
		{
			throw new Exception('Invlid params!');
		}
	}

	/**
	 *
	 * @return type
	 * @throws Exception
	 */
	public function decrypt()
	{
		if ($this->validate_params())
		{
			$ret = openssl_decrypt($this->data, $this->method, $this->key, $this->options);
			return trim($ret);
		}
		else
		{
			throw new Exception('Invlid params!');
		}
	}

}
