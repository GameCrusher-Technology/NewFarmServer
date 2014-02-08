<?php
include_once GAMELIB.'/model/UserFriendManager.php';
include_once GAMELIB.'/model/UserMessageManager.class.php';
class AcceptFriend extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$target_gameuid = $this->getParam("target",'string');
		$mes_id = $this->getParam("data_id",'int');
		
		$friend_mgr = new UserFriendManager();
		//添加好友
		$friend_obj = $friend_mgr->getFriends($gameuid);
		if(empty($friend_obj)){
			$change = array('gameuid'=>$gameuid,'friends'=>$target_gameuid);
			$friend_mgr->creatFriends($change);
		}else{
			$change = $this->mergeFriend($friend_obj,$target_gameuid);
			if(!empty($change) && $change != FALSE){
				$friend_mgr->updateFriends($change,$gameuid);
			}
		}
		//添加 自己
		
		$friend_obj = $friend_mgr->getFriends($target_gameuid);
		if(empty($friend_obj)){
			$change = array('gameuid'=>$target_gameuid,'friends'=>$gameuid);
			$friend_mgr->creatFriends($change);
		}else{
			$change = $this->mergeFriend($friend_obj,$gameuid);
			if(!empty($change) && $change != FALSE){
				$friend_mgr->updateFriends($change,$target_gameuid);
			}
		}
		
		//mes;
		$mes_mgr = new UserMessageManager();
		$mes_mgr->delMessage($gameuid,$mes_id);
		
		return TRUE;
	}
	
	private function mergeFriend($friend_obj,$target_gameuid){
		
		if (empty($friend_obj) || empty($friend_obj['friends'])){
			$change = array("friends"=>$target_gameuid);
			return $change;
		}else{
			$friends = explode(",",$friend_obj['friends']);
			if(in_array($target_gameuid,$friends)){
				return FALSE;
			}else{
				array_push($friends,$target_gameuid);
				$change = array("friends" => implode(",",$friends));
				return $change;
			}
		}
	}
}
?>