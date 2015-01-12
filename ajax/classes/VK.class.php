<?php
	class VK{
		public $app_id;
		public $secret;
		public $api_version = '5.21';
		public $token;
		public function __construct($token){
			$this-> token = $token;
		}
		public function api_call($method, $data = array(), $decode = true){
			$data['access_token'] = $this->token;
			$data['v']=$this->api_version;
			$data['https'] = 1;
			$resp = $this->post('https://api.vk.com/method/'.$method, $data);
			if($decode && $resp){
				if(array_key_exists('response', $resp)){
					return $resp['response'];
				}
			}
			return $resp;
		}
		function post($link,$data){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $link);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
			$response = curl_exec($ch);
			curl_close($ch);
			return json_decode($response, true);
		}
	}
?>