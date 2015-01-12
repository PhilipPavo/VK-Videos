<?php
	class Ajax {
		/*
			codes: 
				0 = normal
				1 = wrong data;
				2 = permission error
				3 = error
		*/
		public $response = array();
		public $post = array();
		public function __construct(){
			$this->post = json_decode(file_get_contents('php://input'), true);
			if(!isset($this->post['method'])) $this->error(1);
			$path = "methods/".$this->post['method'].".ajax.php";
			if(file_exists($path)) include($path);
			$this->build();
		}
		public function get_input($params, $ci = true){
			$in = [];
			if($ci){
				$input = $this->post;
			}else{
				$input = $_POST;
			}
			for($i=0; $i<count($params); $i++){
				if(!isset($input[$params[$i]])){
					$this->error("PARAM NOT FOUND : ".$params[$i]);
				}else{
					$in[$params[$i]] = $input[$params[$i]];
				}
			}
			return $in;
		}
		public function addResponse($title, $data){
			$this->response[$title] = $data;
		}
		public function build($data = false){
			if(!$data) $data = array(
				'response' => $this->response,
			);
			die(json_encode($data));
		}
		public function error($code = 0){
			die(json_encode(array(
				"error" => $code
			)));
		}
	}
?>