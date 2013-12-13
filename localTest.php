<?php
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
include_once FRAMEWORK.'/database/IDSequence.class.php';
try {
//	$appConfigInstance = get_app_config();
//	$cache = $appConfigInstance->getTableServer("user_message")->getCacheInstance();
////	$cache->delete("user_message_list_26003");
//	print_r ($cache->get("user_message_list_4"));

	
	
	function my_sort($a, $b)
  {
  if ($a == $b) return 0;
  return ($a > $b) ? -1 : 1;
  }

$arr = array("1", "2","3","4","7", "6");

usort($arr, my_sort);

print_r($arr);


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
	
}catch (Exception $e){
	
}
?>