<?php
class BuyCoin extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$item_id = $this->getParam("item_id",'string');
		
		if ($item_id == "littleFarmCoin"){
			$addCoin = 10000;
			$costGem = 10;
			
		}else{
			$addCoin = 100000;
			$costGem = 100;
		}
			
     	$account = $this->user_account_mgr->getUserAccount($gameuid);
     	if($costGem > $account['gem']){
     		$this->throwException("gameuid:".$gameuid." gem not enough",GameStatusCode::MONEY_NOT_ENOUGH);
     	}
     	$change = array('coin'=>$addCoin,'gem'=>-$costGem);
     	$this->user_account_mgr->updateUserStatus($gameuid,$change);
     		
     	return array('change'=>$change);
	}
}