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

//	$appConfigInstance = get_app_config();
//	$cache = $appConfigInstance->getTableServer("user_message")->getCacheInstance();
//	$a = $cache->get("ck_item_30001");
//	print_r ($a);
//$receipt= array('signedData'=>array(
//												'nonce'=>2431466409516996718,
//												'orders'=>array(
//																'notificationId'=>'-415567515321859968',
//																'orderId'=>'3785995196307657958',
//																'packageName'=>'air.Farmland.andriod',
//																'productId'=>'sunny_farm.largefarmgem',
//																'purchaseTime'=>1392065952149,
//																'purchaseState'=>0,
//																'purchaseToken'=>'msanfqkgcdekahycknsqeywn.dlS5SsXIyRZRkANz5_4xLqeOu8DrdDo9VtSjjTKJ9VHwBWNW5gxYUX9XqDBbF5KskKdAdRIYGScis7894dzzkI_1E7S86bIVAbHe_9DftYYy6YzMccj2qz8'
//														)
//											),
//									'startId'=>10,
//									'signature'=>"7tsGL+msTbVxVSoOKe6M4k8KCRM7toa2kWde3gRkQK+sGm0EgNpI2gVwm65eue0reqLhEZG1vKpqlvKGFl9XT5ltTAV8uKdF91iaZ8FKpSiYvjKTG/jqsd0WmE0MwshZ263751lYmBy5Vwll9J1bgBmKDsHEa64nmKdVCtEo6s0/RSNlD6a1TJiFPIbJ2nZf5CTn85fskzGmZVFamj+NT3F20sYVGnUxw/lPT6MevoruCveiOFyZZ61ae8rKq+ksw3bzlZCGyy7vPTGdzyU/PwXjNrAYC644S6hEqK1/AGShcU10A/sc09X143ibIeyzqYiYdk4RyJhOceNFTNUWrQ"
//								);

	$receipt= array('signedData'=>array(
												'nonce'=>-5465853498583976126,
												'orders'=>array(
																'notificationId'=>'-6441531684346654540',
																'orderId'=>'12999763169054705758.1324177654896095',
																'packageName'=>'air.Farmland.andriod',
																'productId'=>'sunny_farm.littlefarmgem',
																'purchaseTime'=>1392002287294,
																'purchaseState'=>0,
																'purchaseToken'=>'anvtjlveifgtpmxvncnxjeua'
														)
											),
									'startId'=>3,
									'signature'=>"MlaguT03ZwiL0xRNfPwTb9ifhgp3VyMOBjk+9OK55algYgLIaf4PvTnt4HLsTqK8BiZIYhinZb4DWluI5M+9g2joOEg4j2gx9CkaVEzJ4QKMteJG7WN2nJaVLAbaFjP9cELAE34bjPihfRdRGUhcH3O5GiSUN/gwsZzQG9mZsq6ntKsBAuARAjeOTJcr9KLcQRKXhJQQRf0uvLoW2km724aNG+6ZAk9CqbQG2e9+ko3El2r6nUdslFF7eqDE1a8f6HxQKzWTauCwoMTIhgb52RP882l7e85a6HuFAbBHQcKOezTMEFE/7jvCvviONuuDPz0yXhjD5rGqtmM5rmeSIw=="
								);
								
	$publicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnGq+mkH8cFacOY9UoWyi1tmAxa55pdmTpoexuMVKbOjbpsY8jwzBOxTO3VBsu7HSibYDTrn79t0uFj0YMsQ/wGK1sO/Ab08DlGEYqV7m5+QsqMcAtQ8UNUER+sGnQxnzTmr3Uq9izMkk69NXzkZRaO5lp8f4gbfRx3KT2JweWihjOyFhWdlWmHRBAJE81Wn2iFJzNGNr50XIC4VDOlt+ljcUD3vu9bZmqmgMryKwn4WtxV2o4UwT5RehpyGHAyQ6YX2jmDSfoR6z2UgajCedxGK5bfmnPZXj75DC4P08O+SlBCGhEq62o/I0sDNtdWdSVnb+HM7IcqqaEMEd6taZEwIDAQAB";
	$KEY_PREFIX = "-----BEGIN PUBLIC KEY-----\n";
    $KEY_SUFFIX = '-----END PUBLIC KEY-----';
	$key = $KEY_PREFIX . chunk_split($publicKey, 64, "\n") . $KEY_SUFFIX;
	

    $key = openssl_pkey_get_public($key);							
			
		$signature = $receipt['signature'];	
		$signed_data = json_encode($receipt['signedData']);
		
		print_r($signed_data);
		echo '<br />';
		print_r($signature);
		echo '<br />';
		
		$r = openssl_verify(base64_decode($signed_data), base64_decode($signature), $key);
//		$pub_key = <<<EOF
//-----BEGIN PUBLIC KEY-----
//MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnGq+mkH8cFacOY9UoWyi1tmAxa55pdmTpoexuMVKbOjbpsY8jwzBOxTO3VBsu7HSibYDTrn79t0uFj0YMsQ/wGK1sO/Ab08DlGEYqV7m5+QsqMcAtQ8UNUER+sGnQxnzTmr3Uq9izMkk69NXzkZRaO5lp8f4gbfRx3KT2JweWihjOyFhWdlWmHRBAJE81Wn2iFJzNGNr50XIC4VDOlt+ljcUD3vu9bZmqmgMryKwn4WtxV2o4UwT5RehpyGHAyQ6YX2jmDSfoR6z2UgajCedxGK5bfmnPZXj75DC4P08O+SlBCGhEq62o/I0sDNtdWdSVnb+HM7IcqqaEMEd6taZEwIDAQAB
//-----END PUBLIC KEY-----
//EOF;
//		
		print_r($r);
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