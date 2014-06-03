<?php
include_once GAMELIB.'/model/UserRanchManager.class.php';
include_once GAMELIB.'/model/UserAnimalManager.class.php';
class CreatRanchCommand extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$ranch = $this->getParam("ranch",'array');
		$animal = $this->getParam("animal",'array');
		
		$ranch_mgr = new UserRanchManager();
		$animal_mgr = new UserAnimalManager();
		$user_account = $this->user_account_mgr->getUserAccount($gameuid);
		$userLevel = StaticFunction::expToGrade($user_account['exp']);
		$ranchs = $ranch_mgr->getRanchs($gameuid);
		$ranch_id = $ranch['item_id'];
		$ranch_def = get_xml_def($ranch_id);
		
		
		$extendLevel = $ranch_def['extendLevel'];
		$extendLevelArr = explode("|",$extendLevel);
		$buyCostArr = explode("|", $ranch_def['buyCost']);
		foreach ($ranchs as $ranchEntity){
			if ($ranchEntity['item_id'] == $ranch_id){
				if (count($extendLevelArr)<=0 || count($buyCostArr)<=0){
					$this->throwException("ranch is out of max ".$gameuid,GameStatusCode::DATA_ERROR);
				}else{
					array_shift($extendLevelArr);
					array_shift($buyCostArr);
				}
			}
		}
		$accountChange = array();
		if (count($extendLevelArr)<=0 || count($buyCostArr)<=0){
			$this->throwException("ranch is out of max ".$gameuid,GameStatusCode::DATA_ERROR);
		}else{
			$cur_level = $extendLevelArr[0];
			if ($userLevel < $cur_level){
				$this->throwException("level is not enough to creat ranch ".$gameuid."cur level : ".$cur_level."userLevel: ".$userLevel,GameStatusCode::DATA_ERROR);
			}
			$costObj = explode(":", $buyCostArr[0]);
			if ($costObj[0] == "gem"){
				if ($costObj[1] > $user_account['gem']){
					$this->throwException("gem is not enough to creat ranch ".$gameuid." cost gem : ".$costObj[1],GameStatusCode::DATA_ERROR);
				}else{
					$accountChange = array("gem"=>-$costObj[1]);
				}
			}else{
				if ($costObj[1] > $user_account['coin']){
					$this->throwException("coin is not enough to creat ranch ".$gameuid." cost coin : ".$costObj[1],GameStatusCode::DATA_ERROR);
				}else{
					$accountChange = array("coin"=>-$costObj[1]);
				}
			}
		}
		$ranch_mgr->addRanch($gameuid,$ranch);
		$animal_mgr->addAnimal($gameuid,$animal);
		
		$this->user_account_mgr->updateUserStatus($gameuid,$accountChange);
		
		return array('change'=>array('type'=>$costObj[0],'number'=>-$costObj[1]));
	}
}
?>