<?php
require_once GAMELIB.'/model/ManagerBase.class.php';

class AdminManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function getDbInstance($gameuid = null){
		return $this->getDBHelperInstance($gameuid);
	}
	
	public function getCacheInstance($gameuid = null){
		return parent::getCacheInstance($gameuid);
	}
	
	protected function getTableName(){
		return "item_definition";
	}
	
}
?>