<?php
/**
 * 该类主要是处理装饰逻辑
 */
class FarmDecorationManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	
	public function move($gameuid,$deco_data){
		$data_id=$deco_data['data_id'];
//		
//		$cache_deco = $this->getDecoration($gameuid,$data_id);
//		if (empty($cache_deco)){
//			$this->throwException("no deco,[".$data_id."] gameuid:".$gameuid,
//				GameStatusCode::DATA_ERROR);
//		}
		$dec_modify = array('positionx' => $deco_data['positionx'], 'positiony' => $deco_data['positiony']);
		$this->updateDeco($gameuid, $data_id, $dec_modify);
		return TRUE;
	}
	
	public function getDecorations($gameuid)
	{
		return $this->getFromDb($gameuid,array('gameuid'=>$gameuid),TCRequest::CACHE_KEY_LIST);
	}
	
	public function getDecoration($gameuid,$data_id)
	{
		return $this->getFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$data_id));
	}
	
	public function updateDeco($gameuid,$data_id,$change)
	{
		$this->updateDB($gameuid,$change,array('gameuid'=>$gameuid,'data_id'=>$data_id));
	}
	
	public function removeDeco($gameuid,$data_id)
	{
		$this->deleteFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$data_id));
		return true;
	}
	public function  addDeco($gameuid,$deco)
	{
		$deco['gameuid']=$gameuid;
		$this->insertDB($deco,TCRequest::CACHE_KEY_LIST);
	}
	
	public function insertDecos($gameuid,$decos)
	{
		$this->insertDBBatch($decos);
	}
	
	protected function getTableName(){
		return "user_deco";
	}
	
	public function getWeedCacheTime($gameuid)
	{
		$key  = "creat_weed_".$gameuid;
		return $this->getFromCache($key,$gameuid);
	}
	public function setWeedCacheTime($gameuid)
	{
		$key  = "creat_weed_".$gameuid;
		$time = time();
		$this->setToCache($key,$time,$gameuid,3600);
	}
}
?>