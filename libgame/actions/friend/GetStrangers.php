<?php
include_once GAMELIB.'/model/UserFriendManager.php';
include_once FRAMEWORK.'/database/FarmIDSequence.class.php';
class GetStrangers extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam('gameuid','string');
		$sequence_handler = new FarmIDSequence();
    	$cur_gameuid = $sequence_handler->getCurrentId();
    	$friend_mgr = new UserFriendManager();
    	$strangersList = array();
		$i=1;
		while($i<=5)
	  	{
	  		$str_gameuid = rand(3,$cur_gameuid);
	  		if($gameuid != $str_gameuid && empty($strangersList[$str_gameuid])){
		  		$friend_info = $friend_mgr->getfriendinfo($str_gameuid);
		  		if(!empty($friend_info)){
		  			$strangersList[$str_gameuid] = $friend_info;
		  		}
	  		}
	  		$i++;
	  	}
	  	
	  	$result = array();
	  	foreach ($strangersList as $key =>$value){
	  		
	  		$result[] = $value;
	  	}
	  	return array('strangers'=>$result);
	}
}
?>