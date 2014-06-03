<?php
require_once GAMELIB.'/model/UserFieldDataManager.class.php';
include_once GAMELIB.'/model/TaskManager.class.php';
include_once GAMELIB.'/model/UserMessageManager.class.php';
include_once GAMELIB.'/model/UserFriendManager.php';
include_once GAMELIB.'/model/FarmDecorationManager.class.php';
include_once GAMELIB.'/model/UserAnimalManager.class.php';
include_once GAMELIB.'/model/UserRanchManager.class.php';

class UserVisitCommand extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam('gameuid','string');
		$friend_gameuid = $this->getParam('friend_gameuid','string');
		$friend_account = $this->user_account_mgr->getUserAccount($friend_gameuid);
		
		//获取 作物
		$field_mgr = new UserFieldDataManager();
		$user_crops = $field_mgr->loadFarm($friend_gameuid);
		$friend_account["user_fields"] =  $this->implodeRows($user_crops);
		
		//获取任务
		$task_mgr = new TaskManager();
		$taskinfo = $task_mgr->getTask($friend_gameuid);
		$friend_account['user_task'] = $taskinfo;
		
		//获取装饰
		$deco_mgr = new FarmDecorationManager();
		$friend_account['user_deco'] = $deco_mgr->getDecorations($friend_gameuid);
		
		//获取 message
		$mes_mgr = new UserMessageManager();
		$friend_account['user_message'] = $mes_mgr->getMessages($friend_gameuid);
		
		//获取 chulan
		$ranch_mgr = new UserRanchManager();
		$friend_account['user_ranch'] = $ranch_mgr->getRanchs($friend_gameuid);
		
		//获取 动物
		$animal_mgr = new UserAnimalManager();
		$friend_account['user_animal'] = $animal_mgr->getAnimals($friend_gameuid);
		
		$fri_mgr = new UserFriendManager();
		$last_help_time = $fri_mgr->getHelpFriendTag($gameuid,$friend_gameuid);
		$result["lastHelp"] = $last_help_time;
		$result["friend_account"]= $friend_account;
		return $result;
	}
}
?>