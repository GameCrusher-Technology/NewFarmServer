<?php
require_once GAMELIB.'/model/UidGameuidMapManager.class.php';
require_once GAMELIB.'/model/UserFieldDataManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
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
		
		$result["friend_account"]= $friend_account;
		return $result;
	}
}
?>