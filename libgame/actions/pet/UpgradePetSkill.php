<?php
include_once GAMELIB.'/model/UserPetManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class UpgradePetSkill extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$item_id = $this->getParam("item_id",'string');
		$skill_id = $this->getParam("skill_id",'skill_id');
		
		//check 价格
		$petXml = get_xml_def($item_id);
		if (empty($petXml)){
			$this->throwException("not  this pet".$item_id,GameStatusCode::DATA_ERROR);
		}
		$skillXml = get_xml_def($skill_id);
		if (empty($skillXml)){
			$this->throwException("not  this skill".$skill_id,GameStatusCode::DATA_ERROR);
		}
		
		
		$petSkills = explode("|",$petXml['skills']);
		$hasSkill = false;
		foreach ($petSkills as $petskillStr){
			$petskillM = explode(":",$petskillStr);
			if($petskillM[0] == $skill_id){
				$hasSkill = TRUE;
				break;
			}
		}
		if (!$hasSkill){
			$this->throwException("pet $item_id do not have this skill".$skill_id,GameStatusCode::DATA_ERROR);
		}
		
		$pet_mgr = new UserPetManager();
		$petInfo = $pet_mgr->getPet($gameuid,$item_id);
		$levelStr = $petInfo['skillLevel'];
		$levelArr = explode("|",$levelStr);
		
		$item_Mgr= new UserGameItemManager($gameuid);
		
		$curLevel = -1;
		foreach ($levelArr as $key=>$skillStr){
			$skillM = explode(":",$skillStr);
			if($skillM[0] == $skill_id){
				$curLevel = $skillM[1];
				if ($curLevel >= $skillXml['maxLevel']){
					$this->throwException("pet $item_id do  have max skill".$skill_id,GameStatusCode::DATA_ERROR);
				}
				
				$levelArr[$key] = $skill_id.":".($curLevel+1);
				break;
			}
		}
		
		if($curLevel<0){
			$curLevel=0;
			array_push($levelArr,$skill_id.":".($curLevel+1));
		}
				
		//cost 
		$cost = ($curLevel+1)*$skillXml['upgradecost'];
		$item_Mgr->subItem("20002",$cost);
		$item_Mgr->checkReduceItemCount();
				
				
		
		$newLevelStr = implode("|",$levelArr);
		if(!empty($newLevelStr) && $newLevelStr != $levelStr){
			$pet_mgr->updatePet($gameuid,$item_id,array("skillLevel"=>$newLevelStr));
			$item_Mgr->commitToDB();
			return array("skill"=>$newLevelStr);
		}else{
			return FALSE;
		}
		
		
	}
}
?>