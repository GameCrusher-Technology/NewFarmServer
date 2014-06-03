<?php
include_once GAMELIB.'/model/UserRanchManager.class.php';
include_once GAMELIB.'/model/UserAnimalManager.class.php';
class BuyAnimalCommand extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$item_id = $this->getParam("item_id",'string');
		$ranchId = $this->getParam("house_id",'string');
		$buy_method = $this->getParam("method","int");
		
     	$account = $this->user_account_mgr->getUserAccount($gameuid);
     	
     	$ranch_mgr = new UserRanchManager();
     	$ranchInfo = $ranch_mgr->getRanch($gameuid,$ranchId);
     	if(empty($ranchInfo)){
     		$this->throwException("ranch,[".$ranchId."] gameuid:".$gameuid."not exist",
				GameStatusCode::DATA_ERROR);
     	}
     	
     	$animal_mgr = new UserAnimalManager();
     	$animals = $animal_mgr->getAnimals($gameuid);
     	$curAnimalCount = 0;
     	$maxId=1000;
     	foreach ($animals as $animal){
     		$maxId = max($maxId,$animal['data_id']);
     		if ($animal['house_id'] == $ranchId){
     			$curAnimalCount ++;
     		}
     	}
     	$maxId++;
     	$ranchspec =  get_xml_def($ranchInfo['item_id'], XmlDbType::XMLDB_ITEM);
		if(empty($ranchspec)){
     		$this->throwException("ranch,[".$ranchId."] gameuid:".$gameuid."not this type",
				GameStatusCode::DATA_ERROR);
     	}
     	$this->logger->writeFatal("cur animal Count ".$curAnimalCount);
     	if ($curAnimalCount > $ranchspec['maxNumber']){
     		$this->throwException("ranch,[".$ranchId."] gameuid:".$gameuid." is out limit",
				GameStatusCode::DATA_ERROR);
     	}
     	
		if ($item_id != $ranchspec['boundId']){
     		$this->throwException("ranch,[".$ranchId."] gameuid:".$gameuid." is not item id",
				GameStatusCode::DATA_ERROR);
     	}
     	
     	$animalspec = get_xml_def($item_id, XmlDbType::XMLDB_ITEM);
     	if($buy_method == MethodType::METHOD_COIN){
     		if(!isset($animalspec["coinPrice"])){
     			$this->throwException("item,[".$item_id."] gameuid:".$gameuid."cant buy by coin",
				GameStatusCode::BUY_METHOD_ERROR);
     		}
     			$total_cost = $animalspec["coinPrice"];
     		
     		if(!isset($account['coin'])||$account['coin']<$total_cost){
     			$this->throwException("gameuid:".$gameuid."coin not enough",
				GameStatusCode::COIN_NOT_ENOUGH);
     		}
     	
     		$this->user_account_mgr->updateUserCoin($gameuid,-$total_cost);
     		
     	}else{
     		if(!isset($animalspec["gemPrice"])){
     			$this->throwException("item,[".$item_id."] gameuid:".$gameuid."cant buy by gems",
				GameStatusCode::BUY_METHOD_ERROR);
     		}
     		$total_cost = $animalspec["gemPrice"];
     		
     		if(!isset($account['gem'])||$account['gem']<$total_cost){
     			throw $this->throwException("gameuid:".$gameuid."gem not enough",
				GameStatusCode::MONEY_NOT_ENOUGH);
     		}
     		
     		$this->user_account_mgr->updateUserMoney($gameuid,-$total_cost);
     	}
     	$animal = array("gameuid"=>$gameuid,"data_id"=>$maxId,"item_id"=>$item_id,"house_id"=>$ranchId,"feedTime"=>0);
     	$animal_mgr ->addAnimal($gameuid,$animal);
     	return array("animal"=>$animal);
	}
	
}