<?php
include_once GAMELIB.'/model/UserGameItemManager.class.php';
include_once GAMELIB.'/model/UserFieldDataManager.class.php';
include_once GAMELIB.'/model/FarmDecorationManager.class.php';
class SceneDataUpdate extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$method = $this->getParam("method",'int');
		$dataList = $this->getParam("list",'array');
		
		$return_data = array();
		switch ($method){
			case MethodType::ADD_FIELD:
				$return_data = $this->addFields($dataList,$gameuid);
				break;
			case MethodType::SPEED:
				$return_data = $this->speedCrop($gameuid,$dataList);
				break;
			case MethodType::HARVEST:
				$return_data = $this->harvestCrop($gameuid,$dataList);
				break;
			case MethodType::PLANT:
				$return_data = $this->plantCrop($gameuid,$dataList);
				break;
			case MethodType::MOVE:
				$return_data = $this->moveEntity($gameuid,$dataList);
				break;
			case MethodType::SELL:
				$return_data = $this->sellEntities($dataList,$gameuid);
				break;
		}
		$modify = array_intersect_key($return_data,array('coin'=>0,'exp'=>0));
	    //修改用户的经验值和金币数量
	    if (count($modify) > 0) {
	       	$this->user_account_mgr->updateUserStatus($gameuid, $modify);
	    }
	    
	    $item_mgr = new UserGameItemManager($gameuid);
	    if(isset($return_data['addItem'])){
	    	foreach ($return_data['addItem'] as $item_id=>$count) {
	    		$item_mgr->addItem($item_id,$count);
	    	}
	    	$item_mgr->commitToDB();
	    }
		if(isset($return_data['delItem'])){
	    	foreach ($return_data['delItem'] as $item_id=>$count) {
	    		$item_mgr->subItem($item_id,$count);
	    		$item_mgr->checkReduceItemCount();
	    	}
	    	$item_mgr->commitToDB();
	    }  
		return $return_data;
	}
	private function addFields($list,$gameuid){
		$filed_mgr = new UserFieldDataManager();
		$filed_mgr->insertField($gameuid,$list);
		return $filed_mgr->loadFarm($gameuid);
	}
	
	private function sellEntities($list,$gameuid){
		$filed_mgr = new UserFieldDataManager();
		$deco_mgr = new FarmDecorationManager();
		foreach ($list as $entity){
			if($entity['type'] == "Crop"){
				$filed_mgr->delete($gameuid,$entity['data_id']);
			}elseif ($entity['type'] == "Entity"){
				$deco_mgr->removeDeco($gameuid,$entity['data_id']);
			}
		}
		return TRUE;
		
	}
	
	private function plantCrop($gameuid,$cropList){
		$filed_mgr = new UserFieldDataManager();
		$result=array();
		foreach ($cropList as $crop){
			$return = $filed_mgr->plant($gameuid,$crop);
			$result = $this->mergeResultArr($result,$return);
		}
		return $result;
	}
	
	private function harvestCrop($gameuid,$cropList){
		$filed_mgr = new UserFieldDataManager();
		$result=array();
		foreach ($cropList as $crop){
			$return = $filed_mgr->harvest($gameuid,$crop);
			$result = $this->mergeResultArr($result,$return);
		}
		return $result;
	}
	
	private function speedCrop($gameuid,$cropList){
		$filed_mgr = new UserFieldDataManager();
		$result=array();
		foreach ($cropList as $crop){
			$return = $filed_mgr->fertilize($gameuid,$crop);
			$result = $this->mergeResultArr($result,$return);
		}
		return $result;
	}
	private function moveEntity($gameuid,$dataList){
		$filed_mgr = new UserFieldDataManager();
		$deco_mgr = new FarmDecorationManager();
		foreach ($dataList as $entity){
			if($entity['type'] == "Crop"){
				$filed_mgr->move($gameuid,$entity);
			}elseif ($entity['type'] == "Entity"){
				$deco_mgr->move($gameuid,$entity);
			}
		}
		return TRUE;
	}
	private function mergeResultArr($oldArr,$change)
	{
		$oldArr['exp'] += $change['exp'];
		$oldArr['coin']+= $change['coin'];
		
		if(isset($oldArr['addItem'])){
			foreach ($change['addItem'] as $item_id=>$count){
				if(isset($oldArr['addItem'][$item_id])){
					$oldArr['addItem'][$item_id] += $count;
				}else{
					$oldArr['addItem'][$item_id] = $count;
				}
			}
		}else{
			$oldArr['addItem'] = $change['addItem'];
		}
		
		if(isset($oldArr['delItem'])){
			foreach ($change['delItem'] as $item_id=>$count){
				if(isset($oldArr['delItem'][$item_id])){
					$oldArr['delItem'][$item_id] += $count;
				}else{
					$oldArr['delItem'][$item_id] = $count;
				}
			}
		}else{
			$oldArr['delItem'] = $change['delItem'];
		}
		return $oldArr;
	}
	
}
?>