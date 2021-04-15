<?php
defined('BASEPATH')OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
class Tweet extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->config('twiggy');
		$this->load->library('twiggy');
	}
	public function index(){
		if(isset($_POST['share_tweet'])){
			$url_post = $_SERVER['HTTP_REFERER'];

			if(isset($_POST['message_tweet']) && $_POST['message_tweet'] != ""){
				$message = $_POST['message_tweet'];
			}else{
				$message = "";
			}

			if ($_FILES['image_tweet']['size'][0] == 0 && $_FILES['image_tweet']['error'][0] == 4){
				$image = "";
			}else{
				$count_img = count($_FILES['image_tweet']['name']);
				for ($x = 0; $x < $count_img; $x++) {
					$type = explode("/",$_FILES['image_tweet']['type'][$x]);
					if(($type[1] == "jpg") || ($type[1] == "jpeg") || ($type[1] == "png")){
						if($_FILES['image_tweet']['size'][$x] > 5000000){
							echo "<script>alert('รูป ".$_FILES['image_tweet']['name'][$x]." ขนาดเกิน 5 MB');</script>";
							echo "<script>window.history.back();</script>";
							die();
						}else{
							$file_tmp = $_FILES['image_tweet']['tmp_name'][$x];
							$data = file_get_contents($file_tmp);
							$image[$x] = base64_encode($data);
						}
					}else{
						echo "<script>alert('ไฟล์ ".$_FILES['image_tweet']['name'][$x]." ไม่ใช่ไฟล์รูปภาพที่กำหนด');</script>";
						echo "<script>window.history.back();</script>";
						die();
					}
				}
			}
		}
		
		$send = $this->tweet($message,$image);
		$array = json_decode(json_encode($send), true);

		if(isset($array['error_input'])){
			echo "<script>alert('".$array['error_input']."');</script>";
			echo "<script>location.href = '".$url_post."';</script>";
			die();
		}

		if(!isset($array['errors'])){
			echo "<script>alert('แชร์ลง Twitter สำเร็จ');</script>";
			echo "<script>location.href = '".$url_post."';</script>";
			die();
		}else{
			foreach ($array['errors'] as $key => $value) {
				if($value['code'] == 187){
					echo "<script>alert('ล้มเหลว!เหตุการณ์นี้เคยแชร์ลง Twitter แล้ว');</script>";
					echo "<script>location.href = '".$url_post."';</script>";
					die();
				}else{
					echo "<script>alert('".$array."');</script>";
					echo "<script>location.href = '".$url_post."';</script>";
					die();
				}
			}
		}
	}
	public function tweet($message,$image){
		require_once(APPPATH.'libraries/codebird/src/codebird.php');

		\Codebird\Codebird::setConsumerKey("AUAmsZaGATWyFyUuIHM2A", "RMQjR7rh9doba9dkqAFpwPYu6AnX0uvgJtwx7e0O0w");
		$cb = \Codebird\Codebird::getInstance();
		$cb->setToken("390101500-UtHUjSMmWCEbmyu26Je7iuZMvNweRCVKaUUwEit9", "yrM3VW9zeI5pb3yBUkYlWfE9fROZvWnvjQDIW4geVBugS");

		if((!isset($message) || $message == "") && (!isset($image) || $image == "")){
			$error['error_input'] = "ไม่พบข้อความและรูปภาพที่ส่งมา";
			return $error;
			die();
		}

		if(mb_strlen($message, 'UTF-8') > 280){
			$error['error_input'] = "อักษรเกิน 280 ตัว";
			return $error;
			die();
		}

		if(count($image) > 4){
			$error['error_input'] = "ห้ามส่งรูปเกิน 4 รูป";
			return $error;
			die();
		}

		// ถ้าอัปโหลดเป็น base64 ต้องเปลี่ยน 'media' => $image เป็น 'media_data' => $image
		if((isset($message) && $message != "") && (isset($image) && $image != "")){
			foreach ($image as $key => $value) {
				$reply[$key] = $cb->media_upload(array(
					'media_data' => $value
				));
				
				$mediaID[$key] = $reply[$key]->media_id_string;
			}
			$params = array(
				'status' => $message,
				'media_ids' => implode(',', [$mediaID[0], $mediaID[1], $mediaID[2], $mediaID[3]])
			);

		}elseif ((!isset($message) || $message == "") && (isset($image) && $image != "")) {
			foreach ($image as $key => $value) {			
				$reply[$key] = $cb->media_upload(array(
					'media_data' => $value
				));

				$mediaID[$key] = $reply[$key]->media_id_string;
			}

			$params = array(
				'media_ids' => implode(',', [$mediaID[0], $mediaID[1], $mediaID[2], $mediaID[3]])
			);
		}else{
			$params = array(
				'status' => $message
			);
		}

		$reply = $cb->statuses_update($params);
		return $reply;
	}

	public function approve(){
		try{
			$this->twiggy->template('lbs/approve_tweet')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}
}