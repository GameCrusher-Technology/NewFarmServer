<?php
include_once GAMELIB.'/model/UserFriendManager.php';
class GetFriendsInfo extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$friendsArr  = $this->getParam("friends",'array');
		
		$friend_mgr = new UserFriendManager();
		$result=array();
		foreach ($friendsArr as $frienduid){
			if($frienduid != NULL || $frienduid!=""){
				$result[]= $friend_mgr->getfriendinfo($frienduid);
			}
		}
		return array("friends"=>$result);
	}
}
?>