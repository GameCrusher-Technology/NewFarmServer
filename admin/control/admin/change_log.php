<?php
global $login_user, $admin_account_mgr,$admin_logger;
$username = $login_user->username;
$uid = $login_user->uid;

require_once GAMELIB . '/model/AccountLogManager.class.php';
$change_log=new AccountLogManager();
if(!empty($_POST)){
	$uid=$_POST;
	foreach ( $uid as $k=>$gameuid){
		$change_log->delete($gameuid);
	}
}

$changelist = $change_log->getchangeList(); 
foreach ($changelist as $key=>$value){
	$changelist[$key] = $value;
	}

	
	
	
?>