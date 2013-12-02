<?php
define("FILTER_PATH", GAMELIB. "/extends/filter/");

class WordFilter {
	
	public static function filterWord($str){
		$path = realpath(self::getFilterPath()) . "/";
		if(!is_dir($path)){
			return $str;
		}
		
		//去除字符中的空格和特殊字符
//		$reg = "/[\\~\\!\\@\\#\\$\\%\\^\\&\\*\\(\\)\\_\\+\\`\\-\\=\\～\\！\\＠\\＃\\＄\\".
//    "％\\＾\\＆\\＊\\\\（\\）\\＿\\＋\\＝\\－\\｀\\[\\]\\\\'\\;\\/\\.\\,\\<\\>\\?\\:".
//    "\"\\{\\}\\|\\，\\．\\／\\；\\＇\\［\\］\\＼\\＜\\＞\\？\\：\\＂\\｛\\｝\\｜]/";
//		$str_trim = preg_replace("$reg" ,"",$str);
		if ($dh = opendir($path)) {
	        while (($file = readdir($dh)) !== false) {
	        	if(!preg_match("/^filter[0-9]*\.txt$/",$file)){
	        		continue;
	        	}
	        	if(!is_readable($path.$file)){
	        		continue;
	        	}
	        	$words = file($path.$file);
//	        	error_log(print_r($words,true),3,"/data/log/nginx/debug.log");
	        	foreach ($words as $word){
	        		$str = str_replace(trim($word),"xxx",$str);
//	        		if(preg_match("/^[.\n]*".trim($word)."[.\n]*$/m",$str)){
//	        			closedir($dh);
//	        			return false;
//	        		}
	        	}
	        	
	        }
	        closedir($dh);
    	}
		return $str;
	}
	
	private static function getFilterPath(){
		$path = "../../libgame/extends/filter/";
		if(defined("FILTER_PATH")){
			$path = FILTER_PATH;
		}
		
		return $path;
	}
	
}


?>