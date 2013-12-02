<?php
	if(!defined('IN_GAME')) exit('Access Denied');
	global $limitvalue, $admin_logger;
	$password=$_POST['password'];
	$gameuid=$_POST['gameuid'];
	set_time_limit(0);
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
	if ($limitvalue!=1&&$limitvalue!=2){
			$error_msg = "没有执行权限。";
			return;
	}
	try {
		$today = date("Ymd");   
		if (!empty($password)&&!empty($gameuid)){
			if(intval($password)!=intval($today)){
				echo "password error";
				die();
			}
	
			if(intval($gameuid)<=0){
				echo "gamuid error";
				die();
			}
			
			$logFile="deleteItemListMemcache_".$gameuid."_".$today.".log";
			$fpLog=fopen($logFile, 'a');
			if(empty($fpLog)){
				die("cannot open log file");
			}
		//	$gameuid=1778;
	
			if (!defined('APP_ROOT')) define('APP_ROOT',realpath(dirname(__FILE__)));
			if (!defined('GAMELIB')) define('GAMELIB', APP_ROOT . '/libgame');
			if (!defined('FRAMEWORK')) define('FRAMEWORK', APP_ROOT . '/framework');
				
			require_once GAMELIB . '/config/GameConfig.class.php';
			require_once FRAMEWORK . '/log/LogFactory.class.php';
			require_once FRAMEWORK . '/db/RequestFactory.class.php';
			require_once GAMELIB . '/GameConstants.php';
				
			require_once GAMELIB.'/model/ManagerBase.class.php';
			require_once GAMELIB.'/model/UserDecorationManager.class.php';
			require_once GAMELIB.'/model/UidGameuidMapManager.class.php';
			require_once GAMELIB.'/model/UserGameItemManager.class.php';
			require_once GAMELIB.'/model/TradeLogManager.class.php';
			require_once GAMELIB.'/common.func.php';
			require_once GAMELIB.'/model/UserItemManager.class.php';				
			require_once GAMELIB.'/model/XmlManager.class.php';
			
			$xml_mgr = new ItemManager();
			$deflist = $xml_mgr->getDefList();//获取所有的ItemList
			$user_item_mgr=new UserItemManager($gameuid);
			$table_name="user_item";
			$cache_helper = get_app_config($gameuid)->getTableServer($table_name)->getCacheInstance();
			
			foreach ($deflist as $defsKey => $defsValue) {
				if(!empty($defsKey)){
					$item_id=intval($defsKey);
					$house_id=0;
					$key=array('gameuid'=>$gameuid,'item_id'=>intval($item_id),'house_id'=>intval($house_id));
					ksort($key);
					$cache_key=$table_name. '_' . join('_',$key);
					$item_in_cache=$cache_helper->get($cache_key);
					$count_in_cache=0;
					if(!empty($item_in_cache)){
						$cache_helper->delete($cache_key);
						$item_in_database=$user_item_mgr->getEntry($item_id);
						$count_in_cache=$item_in_cache['count'];
						if(!empty($item_in_database)){
							$count_in_db=$item_in_database['count'];
						}else{
							$count_in_db=0;
						}
						$string=sprintf("gameuid\t%d\titem_id\t%d\tinCache\t%d\tinDb\t%d\tdbEmepty\t\t%d\r\n",$gameuid,$item_id,$count_in_cache,$count_in_db,empty($item_in_database));
						fwrite($fpLog, $string);
					}
				}
			}
			$cache_key=$table_name.'_list_'.$gameuid;
			$cache_helper->delete($cache_key);
			$admin_logger->writeInfo("successfully delete cache[$cache_key]");

			fclose($fpLog);
			echo 'end';
		}
	} catch (Exception $e){
		$error_msg = $e->__toString();
		$admin_logger->writeError("exception while delete cache[$cache_key] from memcache\n".$e->getTraceAsString());
	}
?>