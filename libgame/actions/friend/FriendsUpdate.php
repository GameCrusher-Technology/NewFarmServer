<?php
include_once GAMELIB.'/model/UserFriendManager.class.php';
class FriendsUpdate extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$method = $this->getParam("method",'int');
		$dataList = $this->getParam("list",'array');
	}
}
?>