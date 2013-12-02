<?php
class ChangeName extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$newname = $this->getParam("newname",'string');
		
		$cost = GameModelConfig::CHANGENAME_COST;
		$account = $this->user_account_mgr ->getUserAccount($gameuid);
		
		if($cost > $account['gem']){
			throw $this->throwException("gameuid:".$gameuid."gem not enough to change name",
				GameStatusCode::MONEY_NOT_ENOUGH);
		}
		$change = array("gem" => -$cost,"name"=>$newname);
		$this->user_account_mgr ->updateUserStatus($gameuid,$change);
		
		return TRUE;
	}
}
?>