<?php
/**
 * 该类主要是处理动物逻辑
 */
class UserAnimalManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	
	public function harvest($gameuid,$data){
		$data_id=$data['data_id'];
		
		$cache_animal = $this->getAnimal($gameuid,$data_id);
		if (empty($cache_animal)){
			$this->throwException("no animal,[".$data_id."] gameuid:".$gameuid,
				"harvest");
		}
		$item_id = $cache_animal['item_id'];
		$feedtime = $cache_animal['feedTime'];
		if (empty($feedtime)|| $feedtime == 0){
			$this->throwException("ANIMAL [$data_id] item_id [$item_id] not EXIST", GameStatusCode::DATA_ERROR);
		}
		$animal_itemspec = get_xml_def($item_id, XmlDbType::XMLDB_ITEM);
		$growtime = $animal_itemspec['life']*60;
		
		$leftTime = (time() - $feedtime) - $growtime;
		if ($leftTime < -100) {
//			$this->logger->writeError("gameuid [$gameuid] crop[$data_id] item_id [$item_id] not immatual. time : ".$leftTime);
			$this->throwException("ANIMAL [$data_id] item_id [$item_id] not immatual. time : ".$leftTime, GameStatusCode::IMMATUAL_CROP);
		}
		
		//增加 收获成就统计次数
		include_once GAMELIB.'/model/UserActionCountManager.class.php';
		$action_mgr = new UserActionCountManager();
		$action_mgr->updateActionCount($gameuid,$item_id - 45000,1);
		//判断树 不会死掉
		$modify = array('feedTime' => 0);
		$addItem = array($animal_itemspec['produce']=>$animal_itemspec['pCount']);
		
		$this->updateAnimal($gameuid, $data_id, $modify,false);
		
		$result = array();
		$result['exp']=$animal_itemspec['exp'];
		$result['addItem'] = $addItem;
		return $result;
	}
	
	public function feed($gameuid,$animal_data){
		$data_id = $animal_data['data_id'];
		$cache_animal = $this->getAnimal($gameuid,$data_id);
		if (empty($cache_animal)){
			$this->throwException("no animal,[".$data_id."] gameuid:".$gameuid,
				"feed");
		}
		$item_id = $cache_animal['item_id'];
		$animal_itemspec = get_xml_def($item_id, XmlDbType::XMLDB_ITEM);
		
		$feed_time = $cache_animal['feedTime'];
		if($feed_time != 0 ){
			$this->throwException("no animal,[".$data_id."] gameuid:".$gameuid,"feed");
		}
		$modify = array("feedTime"=>time());
		$this->updateAnimal($gameuid, $data_id, $modify,false);
		$result = array();
		$result['delItem'] = array($animal_itemspec['feedId']=>1);
		return $result;
		
	}
	
	public function getAnimals($gameuid)
	{
		return $this->getFromDb($gameuid,array('gameuid'=>$gameuid),TCRequest::CACHE_KEY_LIST);
	}
	
	public function getAnimal($gameuid,$data_id)
	{
		return $this->getFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$data_id));
	}
	
	public function updateAnimal($gameuid,$data_id,$change)
	{
		$this->updateDB($gameuid,$change,array('gameuid'=>$gameuid,'data_id'=>$data_id));
	}
	
	public function removeAnimal($gameuid,$data_id)
	{
		$this->deleteFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$data_id));
		return true;
	}
	public function  addAnimal($gameuid,$animal)
	{
		$animal['gameuid']=$gameuid;
		$this->insertDB($animal,TCRequest::CACHE_KEY_LIST);
	}
	
	protected function getTableName(){
		return "user_animal";
	}
}
?>