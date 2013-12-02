<?php
include_once GAMELIB.'/model/UserFriendManager.php';
class RefreshFriends extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$friend_mgr = new UserFriendManager();
		//添加好友
		$friend_obj = $friend_mgr->getFriends($gameuid);
		
		if(empty($friend_obj)){
			return array("friends"=>NULL);
		}
		$friends = explode(",",$friend_obj['friends']);
		$result=array();
		foreach ($friends as $frienduid){
			if($frienduid != NULL || $frienduid!=""){
				$result[]= $friend_mgr->getfriendinfo($frienduid);
			}
		}
		return array("friends"=>$result);
	}
}
?>