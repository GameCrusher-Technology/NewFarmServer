<?php
include_once GAMELIB.'/model/UserFieldDataManager.class.php';
class UpgradeUserSkill extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$user_account = $this->user_account_mgr->getUserAccount($gameuid);
		$skill = $user_account['skill'];
		$love = $user_account['love'];
		$grade = StaticFunction::expToGrade($user_account['exp']);
		if (empty($skill) ||$skill ==""){
			$skill = "2|5";
		}
		$skillarr = explode("|",$skill);
		$level = $skillarr[0]+$skillarr[1];
		
		$cost_coin = ($level-6) *1000;
		$cost_love = StaticFunction::gradeToLove($level-7);
		
		if ($cost_love > $love){
			$this->throwException("love is not enough gameuid :".$gameuid,GameStatusCode::DATA_ERROR);
		}
		if ($cost_coin >$user_account['coin']){
			$this->throwException("coin is not enough gameuid :".$gameuid,GameStatusCode::DATA_ERROR);
		}
		$r = rand(0,10);
		$change = array("love"=>-$love,'coin'=>-$cost_coin);
		if($r <= 3){
			$change['skill'] = ($skillarr[0]+1)."|".$skillarr[1];
		}else{
			$change['skill'] = ($skillarr[0])."|".($skillarr[1]+1);
		}
		
		$this->user_account_mgr->updateUserStatus($gameuid,$change);
		return  $change;
	}
}
?>