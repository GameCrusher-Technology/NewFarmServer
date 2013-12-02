<?php
global $login_user, $admin_account_mgr,$admin_logger;
$username = $login_user->username;
$uid = $login_user->uid;
if(isset($_POST['password'])){
	$credit = array();
	$credit['username'] = $username;
	$credit['password'] = getGPC('password','string');
	$credit['oldpass'] = getGPC('oldpass','string');
	try{
		$admin_account_mgr->updatePassword($credit);
		$audit_logger->write($login_user,'',AuditLogManager::ACTION_CHANGE_PASSWORD,'change password');
		$op_msg = '修改密码成功！';
	}
	catch (Exception $e){
		$admin_logger->writeError("exception while change login user's password.".$e->getMessage());
		$error_msg = $e->getMessage();
	}
}
?>