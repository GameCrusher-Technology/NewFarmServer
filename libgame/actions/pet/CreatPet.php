<?php
include_once GAMELIB.'/model/UserPetManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class CreatPet extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$item_id = $this->getParam("item_id",'string');
		
		//check 价格
		$petXml = get_xml_def($item_id);
		if (!empty($petXml['petCost'])){
			$itemMgr = new UserGameItemManager($gameuid);
			$itemMgr->subItem("20002",$petXml['petCost']);
			$itemMgr->checkReduceItemCount();
			$itemMgr->commitToDB();
		}
		
		$pet_mgr = new UserPetManager();
		$petData = $pet_mgr->getPet($gameuid,$item_id);
		if(empty($petData)){
			$petData = $pet_mgr->addPet($gameuid,$petXml);
		}
		return array('pet'=>$petData);
		
	}
}
?>