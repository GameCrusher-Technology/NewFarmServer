<?php
if(!defined('IN_GAME')) exit('Access Denied');
require_once ADMIN_ROOT . '/common.func.php';
require_once GAMELIB . '/model/AuditLogManager.class.php';
global $limitvalue, $audit_logger, $login_user, $admin_logger;
if (isset ( $_POST ['uid'] ) || isset($_POST['gameuid'])) {
	try {
		$uid = $_POST ['uid'];
		if (!empty($uid)) {
			$gameuid = get_gameuid_from_uid(trim($_POST['uid']));
		} else {
			$gameuid = $_POST['gameuid'];
			$uid = get_uid_from_gameuid($gameuid);
		}
		$delete_type = trim($_POST['deleteType']);
		$reason = trim($_POST['reasonDetail']);
		$userAccount = get_user_account($gameuid);
		if (empty($userAccount)) {
			$error_msg = "用户[uid=$uid,gameuid=$gameuid] 不存在";
			return;
		}
		if(isset($_POST['deleteUserAccount'])){
			$msg = sprintf(
				'user %s(uid=%s) delete user account uid=%s,gameuid=%d',
				$login_user->username,$login_user->uid,$uid,$gameuid);
			$GLOBALS['admin_logger']->writeInfo($msg);
			$affected_rows = deleteUserAccount($gameuid);
			if(intval($affected_rows) > 0){
				$op_msg = "成功删除用户档案";
				deleteMapping($uid);
				insertIntoUserDeleted(
					array(
						$gameuid,
						$userAccount['coin'],
						$userAccount['money'],
						$userAccount['experience'],
						$userAccount['charm'],
						$delete_type,
						$reason,time()));
				$audit_logger->write($login_user,$uid,AuditLogManager::ACTION_DELETE_ACCOUNT,$msg);
			}
		}
	} catch ( Exception $e ) {
		$error_msg = $e->getMessage ();
	}
}
function deleteUserAccount($gameuid){
	$req = RequestFactory::createDeleteRequest(get_app_config($gameuid));
	$req->setKey('gameuid',$gameuid);
	$req->setTable('user_account');
	$req->addKeyValue('gameuid',$gameuid);
	return $req->execute();
}
function deleteMapping($uid){
	require_once GAMELIB.'/model/UidGameuidMapManager.class.php';
	$uid_gameuid_map_mgr = new UidGameuidMapManager();
	$uid_gameuid_map_mgr->deleteMapping($uid);
}
function insertIntoUserDeleted($userAccountDeleted){
	$req = RequestFactory::createInsertUpdateRequest(get_app_config());
	$req->setTable('user_account_deleted');
	$req->setColumns('gameuid,coin,money,experience,charm,delete_type,reason,create_time');
	$req->addValues($userAccountDeleted);
	$req->setNoCache(true);
	$req->execute();
}
?>