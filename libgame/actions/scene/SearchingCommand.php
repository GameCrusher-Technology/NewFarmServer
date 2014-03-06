<?php
include_once GAMELIB.'/model/UserGameItemManager.class.php';
include_once GAMELIB.'/model/FarmDecorationManager.class.php';
class SearchingCommand extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$data_id = $this->getParam("data_id",'int');
		$deco_mgr = new FarmDecorationManager();
		$deco = $deco_mgr->getDecoration($gameuid,$data_id);
		if(empty($deco)){
			$this->throwException("no deco data_id:".$data_id." gameuid :".$gameuid,GameStatusCode::DATA_ERROR);
		}
		$item_id = $deco['item_id'];
		$item_info = get_xml_def($item_id);
		if(empty($item_info)){
			$this->throwException("no deco data_id:".$data_id." gameuid :".$gameuid." item_id:".$item_id,GameStatusCode::DATA_ERROR);
		}
		$change = array();
		$account = $this->user_account_mgr->getUserAccount($gameuid);
		$changeObj=array();
		if($item_info['gemPrice'] && $item_info['gemPrice']>0){
			$change = array("gem" => -$item_info['gemPrice']);
			$changeObj = array("id"=>"gem","count" => -$item_info['gemPrice']);
			if($account['gem'] < $item_info['gemPrice']){
				$this->throwException("gem not enough :".$gameuid,GameStatusCode::MONEY_NOT_ENOUGH);
			}
		}else{
			$change = array("coin" => -$item_info['coinPrice']);
			$changeObj = array("id"=>"coin","count" => -$item_info['coinPrice']);
			if($account['coin'] < $item_info['coinPrice']){
				$this->throwException("coin not enough :".$gameuid,GameStatusCode::COIN_NOT_ENOUGH);
			}
		}
		$this->user_account_mgr->updateUserStatus($gameuid,$change);
		$step = $item_info['rateStep'];
		$reward = StaticFunction::getWildReward($step);
		$hasDeco = FALSE;
		$key = $reward["id"];
		$count =$reward["count"];
		if($key == "coin" || $key == "exp"){
			$this->user_account_mgr->updateUserStatus($gameuid,array($key=>$count));
			$deco_mgr->removeDeco($gameuid,$data_id);
		}else if(floor($key/10000)==5){
			//装饰 只有一个
			$deco_mgr->updateDeco($gameuid,$data_id,array('item_id'=>$key));
			$hasDeco = TRUE;
		}else{
			$item_mgr = new UserGameItemManager($gameuid);
			$item_mgr->addItem($key,$count);
			$item_mgr->commitToDB();
			$deco_mgr->removeDeco($gameuid,$data_id);
		}
		
		
		return array("reward"=>$reward,"hasDeco"=>$hasDeco,"cost"=>$changeObj);
		
		
	}
}
?>