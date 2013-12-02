<?php
global $admin_logger, $admin_account_mgr;
$uid = getGPC('adminuid','string');
$username = getGPC('username','string');
try {
	if(isset($_POST['password'])){
		$credit = array();
		if(empty($uid) && !empty($username)){
			$user = $admin_account_mgr->getUserByName($username);
			$uid = $user['adminuid'];
		}
		$credit['uid'] = $uid;
		$credit['password'] = getGPC('password','string');
			if (!empty($uid) && !empty($credit['password'])){
				$admin_account_mgr->resetPassword($credit);
				$op_msg = '重置密码成功！';
			} else{
				$error_msg = "用户和密码不能为空！";
			}
	} elseif(!empty($uid)){
		$target_user = $admin_account_mgr->getUserByUid($uid);
		$username = $target_user['username'];
	}
}
catch (Exception $e){
	$admin_logger->writeError("exception while reset user's password to database.".$e->getMessage());
}
?>