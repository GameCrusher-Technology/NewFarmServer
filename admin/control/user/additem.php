<?php
if(!defined('IN_GAME')) exit('Access Denied');
require_once GAMELIB . '/model/XmlManager.class.php';
require_once GAMELIB . '/model/UserEventLogManager.class.php';
require_once GAMELIB . '/model/UserOwnedManager.class.php';
try{
	$item_mgr = new ItemManager();
	$itemlist = $item_mgr->getItemList();
	if(!empty($_POST['gameuid']) || !empty($_POST['uid'])){
		$uid = trim($_POST['uid']);
		if (!empty($uid)) {
			$gameuid = get_gameuid_from_uid($uid);
		} else {
			$gameuid = trim($_POST['gameuid']);
		}
		if(empty($gameuid)){
			$error_msg = "user not exists. gameuid=$gameuid,uid=$uid";
		} else{
			$user_owned_manager = new UserOwnedManager($gameuid);
			$event_logger = new UserEventLogManager($gameuid);
			$user_account = get_user_account($gameuid);
			$item_id = intval($_POST['item_id']);
			$item_count = intval($_POST['item_count']);
			if ($item_count > 0) {
				if(!empty($user_account)){
					$addItem = $item_mgr->getItem($item_id);
					$unit = $addItem['unit']?$addItem['unit']:'个';
					$user_owned_manager->modifyEntry(array("item_id"=>$item_id, "count"=>$item_count), PackDataManager::METHOD_ADD);
					$user_owned_manager->commitToDB();
					$event_msg = sprintf("管理员给你赠送了%d%s%s，请注意查收。",$item_count,$unit,$addItem['cname']);
					$op_msg = sprintf('赠送%d%s%s成功。',$item_count,$unit,$addItem['cname']);
					if(isset($event_msg) && !empty($event_msg)){
						$event_logger->writeLog(ActionCode::ACTION_LITERAL_EVENT, $event_msg);
					}
				} else{
					$error_msg = "user not exists. gameuid=$gameuid,uid=$uid";
				}
			} else {
				$error_msg = '物品数量为0';
			}
			$user_owned_items = $user_owned_manager->getUserOwnedList();
			foreach ($user_owned_items as $key=>$user_owned_item) {
				$item_def = $item_mgr->getItem($user_owned_item['item_id']);
				$user_owned_items[$key]['item_name'] = $item_def['cname'];
			}
		}
	}
}catch (Exception $e){
	$error_msg = $e->__toString();
	$admin_logger->writeError("exception while save owned item to db.".$e->getMessage());
}
?>