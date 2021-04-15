<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class CryptoJS extends CI_Model {



        public function __construct()
        {
                $this->load->library('AES');
                //$lib= new library_name();
                //$lib->somemethod();
        }

        function cryptoJsAesDecrypt($passphrase, $jsonString){ 
            $jsondata = json_decode($jsonString, true);
            try {
                $salt = hex2bin($jsondata["s"]);
                $iv  = hex2bin($jsondata["iv"]);
            } catch(Exception $e) { return null; }
            $ct = base64_decode($jsondata["ct"]);
            $concatedPassphrase = $passphrase.$salt;
            $md5 = array();
            $md5[0] = md5($concatedPassphrase, true);
            $result = $md5[0];
            for ($i = 1; $i < 3; $i++) {
                $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
                $result .= $md5[$i];
            }
            $key = substr($result, 0, 32);
            $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
            return json_decode($data, true);
        }

        function cryptoJsAesEncrypt($passphrase, $value){
            $salt = openssl_random_pseudo_bytes(8);
            $salted = '';
            $dx = '';
            while (strlen($salted) < 48) {
                $dx = md5($dx.$passphrase.$salt, true);
                $salted .= $dx;
            }
            $key = substr($salted, 0, 32);
            $iv  = substr($salted, 32,16);
            $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
            $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
            return json_encode($data);
        }

        function aes_decrypt($str=''){
                $aes=new AES($str,AES_KEYS,AES_SIZE);
                return $aes->decrypt();
        }
        function aes_encrypt($str=''){
                $aes=new AES($str,AES_KEYS,AES_SIZE);
                return $aes->encrypt();
        }

}
?>