<?php
include_once GAMELIB.'/model/UserFriendManager.php';
class RemoveFriend extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$target_gameuid = $this->getParam("target",'string');
		
		$friend_mgr = new UserFriendManager();
		//删除好友
		$friend_obj = $friend_mgr->getFriends($gameuid);
		$change = $this->mergeFriend($friend_obj,$target_gameuid);
		if(!empty($change) && $change != FALSE){
			$friend_mgr->updateFriends($change,$gameuid);
		}
		//删除自己
		
		$friend_obj = $friend_mgr->getFriends($target_gameuid);
		$change = $this->mergeFriend($friend_obj,$gameuid);
		if(!empty($change) && $change != FALSE){
			$friend_mgr->updateFriends($change,$target_gameuid);
		}
		
		
		return TRUE;
	}
	
	private function mergeFriend($friend_obj,$target_gameuid){
		
		if (!empty($friend_obj))
		{
			$friends = explode(",",$friend_obj['friends']);
			$newfriends = array();
			foreach ($friends as $key=>$id){
				if ($target_gameuid != $id){
					array_push($newfriends,$id);
				}
			}
			$change = array("friends" => implode(",",$newfriends));
			return $change;
		}
	}
}
?>