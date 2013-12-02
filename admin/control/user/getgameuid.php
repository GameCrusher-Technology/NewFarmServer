<?php
if(!defined('IN_GAME')) exit('Access Denied');
include_once ADMIN_ROOT . 'common.func.php';
global $limitvalue, $audit_logger, $login_user, $admin_logger;

$action_getGameuid = getGPC("getGameuid", "string");
if (empty($action_getGameuid)) return;

try {
	if (isset($_POST['getGameuid'])){
		$uid=trim($_POST['uid']);
		if (empty($uid)){
			$error_msg = "用户uid不能为空";
			return;
		}
		
		$gameuid=get_gameuid_from_uid($uid);
	}
	
}catch (Exception $e){
	$admin_logger->writeError($e->getTraceAsString());
	$error_msg = $e->getMessage ();
}
?>