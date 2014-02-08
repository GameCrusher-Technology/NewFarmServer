<?php
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class UserSellItem extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$item_id = $this->getParam("item_id","int");
		$count = $this->getParam("count","int");
		
		$item_mgr = new UserGameItemManager($gameuid);
		$item_mgr->subItem($item_id,$count);
		$item_mgr->checkReduceItemCount();
		
		$item_xml_info = get_xml_def($item_id);
		$add_coin = $item_xml_info['coinPrice'] * $count;
		$this->user_account_mgr->updateUserCoin($gameuid,$add_coin);
		$item_mgr->commitToDB();
		return TRUE;
	}
}
?>