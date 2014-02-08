<?php
class CreatPerson extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$name = $this->getParam("name",'string');
		$sex = $this->getParam("sex",'int');
		
		$change = array("sex"=>$sex,"name"=>$name);
		$this->user_account_mgr ->updateUserStatus($gameuid,$change);
		
		return TRUE;
	}
}
?>