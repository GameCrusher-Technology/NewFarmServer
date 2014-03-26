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
class UserLoginCommand extends GameActionBase{
	protected function _exec()
	{
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
    		$this->init($gameuid);
			//创建uid,gameuid的对照关系
			$mapping_handler->createMapping($platform_uid, $gameuid);
			//统计信息
			$is_newer = TRUE;
		}
//		addSystemStat('install',1);
		$GLOBALS['gameuid'] = $gameuid;
		$user_account = $this->user_account_mgr->getUserAccount($gameuid);
		//获取 作物
		$field_mgr = new UserFieldDataManager();
		$user_crops = $field_mgr->loadFarm($gameuid);
		$user_account["user_fields"] =  $this->implodeRows($user_crops);
		//获取背包
		$item_mgr=new UserGameItemManager($gameuid);
		$user_account['items'] = $item_mgr->getItemList();
		//获取装饰
		$deco_mgr = new FarmDecorationManager();
		$user_account['user_deco'] = $deco_mgr->getDecorations($gameuid);
		
		//获取任务
		$task_mgr = new TaskManager();
		$taskinfo = $task_mgr->getTask($gameuid);
		$user_account['user_task'] = $taskinfo;
		
		//获取 actioncount
		$action_mgr = new UserActionCountManager();
		$user_account['user_actions'] = $action_mgr->getEntryList($gameuid);
		
		//获取 好友
		$friend_mgr = new UserFriendManager();
		$friend_obj = $friend_mgr->getFriends($gameuid);
		$user_account['user_friend'] = $friend_obj['friends'];
		
		//获取 加工厂
		
		$fac_manager = new UserFactoryManager();
		$fac_obj = $fac_manager->getUserFac($gameuid);
		$fac_obj['workTimeIndex'] = $fac_manager->getFormulaIndex($gameuid);
		$user_account['user_factory'] = $fac_obj;
		
		//获取 message
		$mes_mgr = new UserMessageManager();
		$user_account['user_message'] = $mes_mgr->getMessages($gameuid);
		
		//设置 活动
		$activity = InitUser::$treasure_activity;
		$result["treasuresActivity"] = $activity;
		$result["user_account"]= $user_account;
		$result['is_new'] = $is_newer;
		return $result;
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