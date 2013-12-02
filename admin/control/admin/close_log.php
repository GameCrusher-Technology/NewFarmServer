<?php
global $login_user, $admin_account_mgr,$admin_logger;
$username = $login_user->username;
$uid = $login_user->uid;
require_once GAMELIB.'/model/activity/UserCloseManager.class.php';
$change_log=new UserCloseManager();
if(!empty($_POST)){
	if(!empty($_POST['uid'])&&$_POST['uid']>0){
		$changelist = $change_log->get($_POST['uid']); 
	}else{
		unset($_POST['uid']);
		$uid=$_POST;
		foreach ($uid  as $k=>$gameuid){
			$change_log->delete($gameuid);
		}
		$changelist = $change_log->getchangeList();
	}
}else{
	$changelist = $change_log->getchangeList(); 
	foreach ($changelist as $key=>$value){
		$changelist[$key] = $value;
		}
}
?>