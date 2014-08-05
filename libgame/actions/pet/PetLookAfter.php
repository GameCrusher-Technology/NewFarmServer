<?php
include_once GAMELIB.'/model/UserPetManager.class.php';
include_once GAMELIB.'/model/UserAnimalManager.class.php';
class PetLookAfter extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$item_id = $this->getParam("item_id",'string');
		$animal_id = $this->getParam("animal_id",'string');
		
		$pet_Mgr = new UserPetManager();
		$pet_info = $pet_Mgr->getPet($gameuid,$item_id);
		if(empty($pet_info)){
			return FALSE;
			$this->throwException("no this pet $item_id",GameStatusCode::DATA_ERROR);
		}
		
		$animal_Mgr = new UserAnimalManager();
		$animalInfo = $animal_Mgr->getAnimal($gameuid,$animal_id);
		if(empty($animalInfo)){
			$this->throwException("no this animal $animal_id",GameStatusCode::DATA_ERROR);
		}
	
		$levelArr = explode("|",$pet_info['skillLevel']);
		$skillLevel = 1;
		foreach ($levelArr as $skillStr){
			$skillM = explode(":",$skillStr);
			if($skillM[0] == "110003"){
				$skillLevel = $skillM[1];
				break;
			}
		}
		$CDS=array(35,30,25,20,15,10,8,6,4);
		$skillCD = $CDS[$skillLevel]*60;
		
		$lastLookAfterTime = $pet_info['refillTime'];
		if (time()-$lastLookAfterTime >= $skillCD){
			$animal_Mgr->updateAnimal($gameuid,$animal_id,array('feedTime'=>(time()-860000)));
			$pet_Mgr->updatePet($gameuid,$item_id,array('refillTime'=>time()));
			
			return array('type'=>TRUE);
		}
		return array('type'=>FALSE);
	}
}
?>