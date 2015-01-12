<?php
	class Compiler{
		public $methods = array(
			"users.get" => array(
				"name" => "users.get",
				"data" => ""
			)
		);
		public function __construct(){
			define('REGEXP_DATA_KEY', "/\\{\\{([()a-zA-Z0-9_.-]+)\\}\\}/");
			define('REGEXP_VARS_BLOCK', "/\\{%(.+)%\\}/s");
			define('REGEXP_VAR_KEY', "/([a-zA-Z0-9._-]+)\\={1,1}([a-zA-Z]+\\.[a-zA-Z]+)\\(((?:[a-zA-Z_]+:(?:[a-zA-Z0-9-_],?)+\\|?)+)+\\);+/");
		}
		function remove_spaces($s){
			return preg_replace("/\s+/", "", $s);
		}
		public function get_vars($src){
			preg_match_all(REGEXP_VARS_BLOCK, $src, $src);
			$src = $src[1];
			$vars =array();
			for($i=0; $i<count($src); $i++){
				$src[$i] = $this->remove_spaces($src[$i]);
				preg_match_all(REGEXP_VAR_KEY, $src[$i], $d);
				
				for($k=0; $k<count($d[1]); $k++){
					$params = array();
					$p = explode('|', $d[3][$k]);
					
					for($j=0;$j<count($p); $j++){
						$p[$j] = preg_split('/:/', $p[$j]);
						$params[$p[$j][0]] = $p[$j][1];
					}
					$vars[$d[1][$k]] = array(
						"method" => $d[2][$k],
						"params" => $params
					);
				}
			}
			return $vars;
		}
		public function get_data_requests($src){
			preg_match_all(REGEXP_DATA_KEY, $src, $data_keys);
			$data_keys = $data_keys[1];
			$requests = array();
			for($i =0; $i<count($data_keys); $i++){
				$requests[$i] = array($data_keys[$i], preg_split('/\\./',$data_keys[$i]));
			}
			return $requests;
		}
		public function parse($src){
			$logs = array();
			$vars = $this->get_vars($src);
			$src = preg_replace(REGEXP_VARS_BLOCK, '', $src);
			$reqests = $this->get_data_requests($src);
			foreach($vars as $k => $v){
				$vars[$k] = Registry::get('vk')->api_call($vars[$k]['method'], $vars[$k]['params']);
			}
			for($i =0; $i<count($reqests); $i++){
				if(isset($vars[$reqests[$i][1][0]])){
					$d = $reqests[$i][1];
					$t = $vars[$d[0]];
					if(isset($t['error'])){
						$logs[] = array('t' => 'warning', 'm' => "ошибка API: ".$t['error']['error_msg']);
					}else{
						for($k = 1;$k<count($d); $k++){
							if(isset($t[$d[$k]])) $t = $t[$d[$k]];
						}	
						$src = preg_replace('/\\{\\{'.$reqests[$i][0].'\\}\\}/', $t, $src);
					}
				}
			}
			return array('src'=> $src, 'logs' => $logs); 
		}
		public function vkapi_get($method, $data= array()){
			$r = Registry::get('vk')->api_call($method, $data);
			return $r;
			//return file_get_contents("https://api.vk.com/method/users.get?access_token=c6dd6cc209d2f0b9beae5291e24bec5d10e758743f46535d393acab8f2e10ba3cb46001fa5d7ff9a73ccb");
		}
	}
?>