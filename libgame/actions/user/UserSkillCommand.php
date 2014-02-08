<?php
include_once GAMELIB.'/model/UserFieldDataManager.class.php';
class UserSkillCommand extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$speedArr = $this->getParam("speed","array");
		$user_account = $this->user_account_mgr->getUserAccount($gameuid);
		$skill_time = $user_account['skill_time'];
		$skill = $user_account['skill'];
		if (empty($skill) ||$skill ==""){
			$skill = "2|5";
		}
		$skillarr = explode("|",$skill);
		$skill_water_time = $skillarr[1]*60;
		if(time() - $skill_time < GameModelConfig::SKILL_CD){
			$this->throwException("skill time is not right gameuid:".$gameuid,
					GameStatusCode::DATA_ERROR);
		}
		
		$filed_mgr = new UserFieldDataManager();
		foreach ($speedArr as $speed_id){
			$cache_crop = $filed_mgr->get($gameuid,$speed_id);
			if (empty($cache_crop)){
				$this->throwException("no field,[".$speed_id."] gameuid:".$gameuid,
					GameStatusCode::NOT_OWN_FIELD);
			}
			$plant_time = $cache_crop['plant_time'] - $skill_water_time;
			$modify = array("plant_time"=>$plant_time);
			$filed_mgr->update($gameuid, $speed_id, $modify,false);
		}
		
		$this->user_account_mgr->updateUserStatus($gameuid,array("skill_time"=>time()));
		return TRUE;
	}
}
?>