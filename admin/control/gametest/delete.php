<?php
if(!defined('IN_GAME')) exit('Access Denied');
require_once FRAMEWORK.'/platform/TesterControl.class.php';
global $admin_logger;
$uid = $_POST['uid'];
$action_delete = getGPC('delete','string');
if (empty($action_delete)) return ;
try {
	if (empty($uid)){
		$error_msg="uid is empty";
		return ;
	}
	$mgr = new TesterControl();
	$result=$mgr ->deleteTester($uid);
	if (!empty($result)){
		$op_msg = "删除测试用户[$uid]成功";
	}else {
		$error_msg = "删除测试用户[$uid]不成功";
	}
}catch (Exception $e){
	$admin_logger->writeError("exception while get definiton.".$e->getMessage());
	$error_msg = $e->getMessage ();
}
?>