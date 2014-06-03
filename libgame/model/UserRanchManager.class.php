<?php
/**
 * 该类主要是处理畜栏逻辑
 */
class UserRanchManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function getRanchs($gameuid)
	{
		return $this->getFromDb($gameuid,array('gameuid'=>$gameuid),TCRequest::CACHE_KEY_LIST);
	}
	
	public function getRanch($gameuid,$data_id)
	{
		return $this->getFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$data_id));
	}
	
	public function updateRanch($gameuid,$data_id,$change)
	{
		$this->updateDB($gameuid,$change,array('gameuid'=>$gameuid,'data_id'=>$data_id));
	}
	public function move($gameuid,$ranch_data){
		$data_id=$ranch_data['data_id'];
//		
//		$cache_deco = $this->getDecoration($gameuid,$data_id);
//		if (empty($cache_deco)){
//			$this->throwException("no deco,[".$data_id."] gameuid:".$gameuid,
//				GameStatusCode::DATA_ERROR);
//		}
		$dec_modify = array('positionx' => $ranch_data['positionx'], 'positiony' => $ranch_data['positiony']);
		$this->updateRanch($gameuid, $data_id, $dec_modify);
		return TRUE;
	}
	public function removeRanch($gameuid,$data_id)
	{
		$this->deleteFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$data_id));
		return true;
	}
	public function  addRanch($gameuid,$ranch)
	{
		$ranch['gameuid']=$gameuid;
		$this->insertDB($ranch,TCRequest::CACHE_KEY_LIST);
	}
	
	protected function getIndexCacheKey(){
		return "user_factory_index_";
	}
	protected function getTableName(){
		return "user_ranch";
	}
}
?>