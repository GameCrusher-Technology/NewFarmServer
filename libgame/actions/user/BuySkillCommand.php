<?php
include_once GAMELIB.'/model/UserFieldDataManager.class.php';
class BuySkillCommand extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');	
		$gem = $this->getParam('gem',"int");
		
		$user_account = $this->user_account_mgr->getUserAccount($gameuid);
		
		$skill_time = $user_account['skill_time'];
		$lefttime = GameModelConfig::SKILL_CD - (time() - $skill_time );
		$costGem = ceil($lefttime/3600);
		if($costGem < 0){
			$costGem = 8;
		}
		if($gem != $costGem){
			$this->throwException("gem cost is not right gameuid:".$gameuid,
					GameStatusCode::DATA_ERROR);
		}
		$change = array("gem"=>-$costGem,"skill_time" => $skill_time-GameModelConfig::SKILL_CD);
		$this->user_account_mgr->updateUserStatus($gameuid,$change);
		
		return  TRUE;
	}
}
?>