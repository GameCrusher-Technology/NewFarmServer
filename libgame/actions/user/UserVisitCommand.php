<?php
require_once GAMELIB.'/model/UidGameuidMapManager.class.php';
require_once GAMELIB.'/model/UserFieldDataManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
include_once GAMELIB.'/model/TaskManager.class.php';
include_once GAMELIB.'/model/UserMessageManager.class.php';
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
		
		//获取 message
		$mes_mgr = new UserMessageManager();
		$friend_account['user_message'] = $mes_mgr->getMessages($friend_gameuid);
		
		$result["friend_account"]= $friend_account;
		return $result;
	}
}
?>