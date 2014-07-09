<?php
require_once GAMELIB.'/model/UidGameuidMapManager.class.php';
require_once GAMELIB.'/model/UserFieldDataManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
include_once GAMELIB.'/model/TaskManager.class.php';
include_once GAMELIB.'/model/UserActionCountManager.class.php';
include_once GAMELIB.'/model/UserFriendManager.php';
include_once GAMELIB.'/model/UserMessageManager.class.php';
include_once GAMELIB.'/model/FarmDecorationManager.class.php';
include_once GAMELIB.'/model/UserFactoryManager.class.php';
include_once GAMELIB.'/model/UserAnimalManager.class.php';
include_once GAMELIB.'/model/UserRanchManager.class.php';
include_once GAMELIB.'/model/UserPetManager.class.php';
class UserLoginCommand extends GameActionBase{
	protected function _exec()
	{
		$loginTime = $this->getmicrotime();
		$platform_uid = $this->getParam('uid','string');
		$mapping_handler = new UidGameuidMapManager();
		$gameuid = $mapping_handler->getGameuid($platform_uid);
		$is_newer = FALSE;
		if ($gameuid === false) {
			if ($this->logger->isDebugEnabled()) {
				$this->logger->writeDebug("can not map platform uid[$platform_uid], create new user.");
			}
			require_once FRAMEWORK.'/database/FarmIDSequence.class.php';
    		$sequence_handler = new FarmIDSequence();
    		$gameuid = $sequence_handler->creatId();
    		if ($this->logger->isDebugEnabled()) {
    			$this->logger->writeDebug("generated new garmuid[$gameuid]");
    		}
    		$GLOBALS['gameuid'] = $gameuid;
			//创建uid,gameuid的对照关系
			$mapping_handler->createMapping($platform_uid, $gameuid);
			$this->init($gameuid);
			//统计信息
			$is_newer = TRUE;
		}
		$time1 = $this->getmicrotime();
//		addSystemStat('install',1);
		$GLOBALS['gameuid'] = $gameuid;
		$user_account = $this->user_account_mgr->getUserAccount($gameuid);
		$time2 = $this->getmicrotime();
		//获取 作物
		$field_mgr = new UserFieldDataManager();
		$user_crops = $field_mgr->loadFarm($gameuid);
		$user_account["user_fields"] =  $user_crops;
		$time3 = $this->getmicrotime();
		//获取背包
		$item_mgr=new UserGameItemManager($gameuid);
		$user_account['items'] = $item_mgr->getItemList();
		$time4 = $this->getmicrotime();
		//获取装饰
		$deco_mgr = new FarmDecorationManager();
		$user_account['user_deco'] = $deco_mgr->getDecorations($gameuid);
		$time5 = $this->getmicrotime();
		//获取任务
		$task_mgr = new TaskManager();
		$taskinfo = $task_mgr->getTask($gameuid);
		$user_account['user_task'] = $taskinfo;
		$time6 = $this->getmicrotime();
		//获取 actioncount
		$action_mgr = new UserActionCountManager();
		$user_account['user_actions'] = $action_mgr->getEntryList($gameuid);
		$time7 = $this->getmicrotime();
		//获取 好友
		$friend_mgr = new UserFriendManager();
		$friend_obj = $friend_mgr->getFriends($gameuid);
		$user_account['user_friend'] = $friend_obj['friends'];
		$time8 = $this->getmicrotime();
		//获取 加工厂
		$fac_manager = new UserFactoryManager();
		$fac_obj = $fac_manager->getUserFac($gameuid);
		$fac_obj['workTimeIndex'] = $fac_manager->getFormulaIndex($gameuid);
		$user_account['user_factory'] = $fac_obj;
		$time9 = $this->getmicrotime();
		//获取 message
		$mes_mgr = new UserMessageManager();
		$user_account['user_message'] = $mes_mgr->getMessages($gameuid);
		$time10 = $this->getmicrotime();
		//获取 chulan
		$ranch_mgr = new UserRanchManager();
		$user_account['user_ranch'] = $ranch_mgr->getRanchs($gameuid);
		$time11 = $this->getmicrotime();
		//获取 动物
		$animal_mgr = new UserAnimalManager();
		$user_account['user_animal'] = $animal_mgr->getAnimals($gameuid);
		$time12 = $this->getmicrotime();
		
		$pet_mgr = new UserPetManager();
		$user_account['user_pet'] = $pet_mgr->getPets($gameuid);
		//设置 活动
		$activity = InitUser::$treasure_activity;
		$result["treasuresActivity"] = $activity;
		$result["user_account"]= $user_account;
		
		//广告 id
		
		$result['ad_ids'] = array("andriod"=>array("ca-app-pub-5842267306366018/9726636086","ca-app-pub-5842267306366018/1226163682"),
									"ios"=>array("ca-app-pub-5842267306366018/9650107283","ca-app-pub-5842267306366018/7534245685"),
									"ads"=>100);
		$result['is_new'] = $is_newer;
		$time13 = $this->getmicrotime();
		
		if($time13 - $loginTime >1){
			
			$loginLoger =  LogFactory::getLogger(array(
				'prefix' => "login", // 文件名的前缀
				'log_dir' => APP_ROOT.'/log/', // 文件所在的目录
				'archive' => ILogger::ARCHIVE_YEAR_MONTH, // 文件存档的方式
				'log_level' => get_app_config()->getLogLevel(LogFactory::LOG_MODULE_ACTIONS)
			));
			$loginLoger->writeError("gameuid : ".$gameuid." loginTime : ".$loginTime." last time:".$time13." totalTime : ".($time13-$loginTime)
			." st1st : ".($time1-$loginTime)
			." st2st : ".($time2-$time1)
			." st3st : ".($time3-$time2)
			." st4st : ".($time4-$time3)
			." st5st : ".($time5-$time4)
			." st6st : ".($time6-$time5)
			." st7st : ".($time7-$time6)
			." st8st : ".($time8-$time7)
			." st9st : ".($time9-$time8)
			." st10st : ".($time10-$time9)
			." st11st : ".($time11-$time10)
			." st12st : ".($time12-$time11)
			." st13st : ".($time13-$time12)
			);
		}
		return $result;
	}
	
	private function getmicrotime() 
	{ 
	    list($usec, $sec) = explode(" ",microtime()); 
	    return ((float)$usec + (float)$sec); 
	}
	
	protected function init($gameuid){
		//添加仓库数据
		$item_mgr=new UserGameItemManager($gameuid);
		//给用户的工具表中加入数据,qq平台需要将玩家等级礼包去掉，在升级的时候进行领取
		foreach (InitUser::$own_arr as $own_item){
			$item_mgr->addItem($own_item['item_id'],$own_item['count']);
		}
		$item_mgr->commitToDB();
		
		
		//添加新的植物
		$field_mgr = new UserFieldDataManager();
		$init_fields = InitUser::$new_field;
		$fields = array();
		foreach($init_fields as $field){
        	$field['gameuid'] = $gameuid;
        	$fields[] = $field;
        }
		$field_mgr->insertField($gameuid,$fields);
		
		//添加装饰
		$deco_mgr = new FarmDecorationManager();
		$init_decos = InitUser::$new_deco;
		$decos = array();
		foreach($init_decos as $deco){
        	$deco['gameuid'] = $gameuid;
        	$decos[] = $deco;
        }
		$deco_mgr->insertDecos($gameuid,$decos);
		
		//进行用户金钱和农民币的初始化
		$new_account=InitUser::$account_arr;
		$new_account['name']= $gameuid;
		return $this->user_account_mgr->createUserAccount($gameuid, $new_account);
	}
}
?>