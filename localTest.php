<?php
error_reporting(100);
set_time_limit(0);
if (!defined('APP_ROOT')) define('APP_ROOT',realpath(dirname(__FILE__)));
if (!defined('GAMELIB')) define('GAMELIB', APP_ROOT . '/libgame');
if (!defined('FRAMEWORK')) define('FRAMEWORK', APP_ROOT . '/framework');

require_once GAMELIB . '/config/GameConfig.class.php';
require_once FRAMEWORK . '/log/LogFactory.class.php';
require_once FRAMEWORK . '/db/RequestFactory.class.php';
require_once GAMELIB . '/GameConstants.php';

require_once GAMELIB.'/model/ManagerBase.class.php';
require_once GAMELIB.'/model/UserAccountManager.class.php';
require_once GAMELIB.'/model/UidGameuidMapManager.class.php';
require_once GAMELIB.'/model/XmlManager.class.php';
include_once GAMELIB.'/model/TradeLogManager.class.php';


		

print_r(time());

//		
//		$payLog = LogFactory::getLogger(array(
//			'prefix' => LogFactory::LOG_MODULE_PLATFORM,
//			'log_dir' => APP_ROOT.'/log/payment/', // 文件所在的目录
//			'archive' => ILogger::ARCHIVE_YEAR_MONTH, // 文件存档的方式
//			'log_level' => 1
//		));
//		$payLog->writeInfo(" || ". json_encode($receipt) );
//		echo '<br />';
//		if (openssl_verify($signed_data, $signature, $pub_k) !== 1) {
//			print_r( array('status'=>'error'));
//		}

		
		
////	$cache->delete("user_message_list_26003");
//	print_r ($cache->get("user_message_list_4"));
//	$log_file = APP_ROOT.'/log/payment/';
//	if(!file_exists($log_file)){
//		mkdir($log_file,0777,true);
//	}
//	$test = file_put_contents($log_file."localtest.log", "dadwewadsas"."\n",FILE_APPEND);
//	print_r("test".$test);
//	
//	if(!file_exists($log_file."2014/")){
//		mkdir($log_file."2014/",0777,true);
//	}
//	$test = file_put_contents($log_file."2014/"."localtest1.log", "dadwewadsas"."\n",FILE_APPEND);
//	
//	$payLog = LogFactory::getLogger(array(
//			'prefix' => LogFactory::LOG_MODULE_PLATFORM,
//			'log_dir' => APP_ROOT.'/log/payment/', // 文件所在的目录
//			'archive' => 14, // 文件存档的方式
//			'log_level' => 1
//		));
//	
//	$a = $payLog->writeInfo(" || ".time());
//	print_r($a);
//	$xml_mgr = new XmlManager();
//	$dom = new DOMDocument();
//	$dom->loadXML($xml_mgr->getList(XmlDbType::XMLDB_ITEM));
//	print_r($dom);
//	print_r(getArray($dom->documentElement));
	
function getArray($node) {
	  $array = false;
	  if ($node->hasAttributes()) {
	    foreach ($node->attributes as $attr) {
	      $array[$attr->nodeName] = $attr->nodeValue;
	    }
	  }
	
	  if ($node->hasChildNodes()) {
	    if ($node->childNodes->length == 1) {
	      $array[$node->firstChild->nodeName] = getArray($node->firstChild);
	    } else {
	      foreach ($node->childNodes as $childNode) {
	      if ($childNode->nodeType != XML_TEXT_NODE) {
	        $array[$childNode->nodeName][] = getArray($childNode);
	      }
	    }
	  }
	  } else {
	    return $node->nodeValue;
	  }
	  return $array;
	}
	
	
//$weedArr = array(50013,50014,50015,50016,50017,50018,50002,50003,50004,50005,50006,50007,50008);
//$rateArr = array(100,90,80,70,60,50,50,0,40,35,30,0,20);
//    	
//		$index =0;
//		while ($index<=100){
//			$key = StaticFunction::getOneByRate($rateArr);
//			print_r($weedArr[$key].'<br />');
//			$index++;
//		}
//		

//	$sequence_handler = new IDSequence("farm_account", "gameuid");
//    	$cur_gameuid = $sequence_handler->getCurrentId();
//    	print_r($cur_gameuid);
//$strs = "Hello world";
//echo substr_replace($strs,"earth",4,5).'<br />';
//echo $strs.'<br />';
//
//$str = "00000000000000000000000000000000000000000000000000";
//echo substr_replace($str,"1",6,1);
	//刷新 xml
//	$xml_mgr = new ItemManager();
//	print_r($xml_mgr->updateDef());

//	$a = array(123141,1413215415,151354223,512352523);
//	$b = array(512352523);
//	print_r(array_diff($a,$b));
function getOneByRate($awards_rate){
		$total_rand=array_sum($awards_rate);
		$rand_key=mt_rand(0,$total_rand);
		foreach ($awards_rate as $k=>$rate){
			if ($rand_key<=$rate){
				return $k;
			}else {
				$rand_key-=$rate;
			}
		}
    }
?>