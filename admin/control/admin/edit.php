<?php
if(!defined('IN_GAME')) exit('Access Denied');

ini_set('mbstring.internal_encoding','UTF-8');

global $admin_account_mgr, $limitgroup, $admin_logger;

$addadmin = $_REQUEST['addadmin'];
$username = $_REQUEST['username'];
$useremail = $_REQUEST['useremail'];
$userpwd = $_REQUEST['userpwd'];
$groupid = $_REQUEST['groupid'];
$deladmin = $_REQUEST['deladmin'];
$uid = $_REQUEST['uid'];

$adminlist = $admin_account_mgr->getUserList();

foreach ($adminlist as $key=>$value){
	$usernames[] = $value['username'];
}
foreach($limitgroup as $key=>$limit){
	$groups[$key] = $limit;
}
try{
	if(isset($deladmin) && $deladmin=='del'){
		$admin_account_mgr->deleteUserList($uid);
	}
}catch(Exception $e){
	$admin_logger->writeError("exception while modify users to database.".$e->getMessage());
}
?>