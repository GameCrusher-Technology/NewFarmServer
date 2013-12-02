<?php
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class BuyItem extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$item_id = $this->getParam("item_id",'string');
		$count = abs($this->getParam("count",'int'));
		$buy_method = $this->getParam("method","int");
		
     	$account = $this->user_account_mgr->getUserAccount($gameuid);
     	
     	$itemspec = get_xml_def($item_id, XmlDbType::XMLDB_ITEM);
     	$item_mgr = new UserGameItemManager($gameuid);
     	if($buy_method == MethodType::METHOD_COIN){
     		if(!isset($itemspec["coinPrice"])){
     			throw $this->throwException("item,[".$item_id."] gameuid:".$gameuid."cant buy by coin",
				GameStatusCode::BUY_METHOD_ERROR);
     		}
     		$total_cost = $count * $itemspec["coinPrice"];
     		
     		if(!isset($account['coin'])||$account['coin']<$total_cost){
     			throw $this->throwException("gameuid:".$gameuid."coin not enough",
				GameStatusCode::COIN_NOT_ENOUGH);
     		}
     	
     		$item_mgr->addItem($item_id,$count);
     		$item_mgr->commitToDB();
     		$this->user_account_mgr->updateUserCoin($gameuid,-$total_cost);
     		
     		
     	}else{
     		if(!isset($itemspec["gemPrice"])){
     			throw $this->throwException("item,[".$item_id."] gameuid:".$gameuid."cant buy by gems",
				GameStatusCode::BUY_METHOD_ERROR);
     		}
     		$total_cost = $count * $itemspec["gemPrice"];
     		
     		if(!isset($account['gem'])||$account['gem']<$total_cost){
     			throw $this->throwException("gameuid:".$gameuid."gem not enough",
				GameStatusCode::MONEY_NOT_ENOUGH);
     		}
     		
     		$item_mgr->addItem($item_id,$count);
     		$item_mgr->commitToDB();
     		
     		$this->user_account_mgr->updateUserMoney($gameuid,-$total_cost);
     		
     	}
     	return TRUE;
	}
	
}