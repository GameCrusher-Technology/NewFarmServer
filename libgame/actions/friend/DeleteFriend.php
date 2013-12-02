<?php
include_once GAMELIB.'/model/UserFriendManager.class.php';
class DeleteFriend extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$target_gameuid = $this->getParam("target",'string');
		
		$friend_mgr = new UserFriendManager();
		//删除好友
		$friend_obj = $friend_mgr->getFriends($gameuid);
		$change = $this->delFriend($friend_obj,$target_gameuid);
		if(!empty($change) && $change != FALSE){
			$friend_mgr->updateFriends($change,$gameuid);
		}
		//删除自己
		
		$friend_obj = $friend_mgr->getFriends($target_gameuid);
		$change = $this->delFriend($friend_obj,$gameuid);
		if(!empty($change) && $change != FALSE){
			$friend_mgr->updateFriends($change,$target_gameuid);
		}
		
		return TRUE;
	}
	
	private function delFriend($friend_obj,$target_gameuid){
		
		if (empty($friend_obj['friends'])){
			return FALSE;
		}else{
			$friends = explode(",",$friend_obj['friends']);
			if(in_array($target_gameuid,$friends)){
				$newfriends = array_diff($friends,array($target_gameuid));
				$change = array("friends" => implode(",",$newfriends));
				return $change;
			}else{
				return FALSE;
			}
		}
	}
}
?>