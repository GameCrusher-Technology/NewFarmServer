<?php
/**
 * 该类主要是处理加工逻辑
 */
class UserFactoryManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 创建加工厂
	 *
	 */
	public function createUserFac($gameuid) {
		$new_fact = InitUser::$new_fac;
		$new_fact['gameuid'] = $gameuid;
		$this->insertDB($new_fact);
		return $new_fact;
	}
	
	//更新 加工
	public function updateUserFac($gameuid,$merge) {
		$merge['gameuid'] = $gameuid;
		$this->updateDB($gameuid,$merge,array('gameuid'=>$gameuid));
	}
	//获取 加工信息
	public function getUserFac($gameuid)
	{
		$fac = $this->getFromDb($gameuid,array('gameuid'=>$gameuid));
		if(empty($fac)){
			$fac = $this->createUserFac($gameuid);
		}
		return $fac;
	}
	//缓存 收获index值
	public function getFormulaIndex($gameuid){
		$key = $this->getIndexCacheKey().$gameuid;
		$cacheinfo = $this->getFromCache($key);
		if(empty($cacheinfo)){
			$cacheinfo = 0;
		}
		return $cacheinfo;
	}
		
	public function setFormulaIndex($gameuid,$index){
		$key = $this->getIndexCacheKey().$gameuid;
		$this->setToCache($key,$index,$gameuid,86400);
	}
	
	protected function getIndexCacheKey(){
		return "user_factory_index_";
	}
	protected function getTableName(){
		return "user_factory";
	}
}
?>