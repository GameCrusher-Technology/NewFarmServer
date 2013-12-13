<?php
include_once GAMELIB.'/model/UserFriendManager.php';
include_once GAMELIB.'/model/UserMessageManager.class.php';
class InviteFriend extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$f_gameuid = $this->getParam("target",'string');
		
		$mes_mgr = new UserMessageManager();
		$type = MethodType::MESSTYPE_INVITE ;
		
		$merge = array();
		$merge['gameuid'] = $f_gameuid;
		$merge['f_gameuid']=$gameuid;
		$merge['type'] = $type;
		$merge['updatetime'] = time();
		
		return $mes_mgr->addMessage($f_gameuid,$merge);
		
	}
	
}
?>