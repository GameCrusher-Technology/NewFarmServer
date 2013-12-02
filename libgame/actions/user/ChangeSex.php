<?php
class ChangeSex extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$newsex = $this->getParam("newsex",'int');
		
		$cost = GameModelConfig::CHANGENAME_SEX;
		$account = $this->user_account_mgr ->getUserAccount($gameuid);
		
		if($cost > $account['gem']){
			throw $this->throwException("gameuid:".$gameuid."gem not enough to change SEX",
				GameStatusCode::MONEY_NOT_ENOUGH);
		}
		$change = array("gem" => -$cost,"sex"=>$newsex);
		$this->user_account_mgr ->updateUserStatus($gameuid,$change);
		
		return TRUE;
	}
}
?>