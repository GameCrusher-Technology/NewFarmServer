<?php
/**
 * 该类主要是处理宠物逻辑
 */
class UserPetManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function getPets($gameuid)
	{
		return $this->getFromDb($gameuid,array('gameuid'=>$gameuid),TCRequest::CACHE_KEY_LIST);
	}
	
	public function getPet($gameuid,$item_id)
	{
		return $this->getFromDb($gameuid,array('gameuid'=>$gameuid,'item_id'=>$item_id));
	}
	
	public function updatePet($gameuid,$item_id,$change)
	{
		$this->updateDB($gameuid,$change,array('gameuid'=>$gameuid,'item_id'=>$item_id));
	}
	public function removePet($gameuid,$item_id)
	{
		$this->deleteFromDb($gameuid,array('gameuid'=>$gameuid,'item_id'=>$item_id));
		return true;
	}
	public function  addPet($gameuid,$item_id)
	{
		$petData = $this->creatPetData($gameuid,$item_id);
		$this->insertDB($petData,TCRequest::CACHE_KEY_LIST);
		return $petData;
	}
	
	public function creatPetData($gameuid,$itemDef)
	{
		$pet = array();
		$pet['gameuid'] = $gameuid;
		$pet['item_id'] = $itemDef['item_id'];
		$pet['skillLevel'] = $itemDef['skills'];
		return $pet;
	}

	protected function getTableName(){
		return "user_pet";
	}
}
?>