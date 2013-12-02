<?php
if(!defined('IN_GAME')) exit('Access Denied');
if(isset($_POST['user_login'])){
	global $admin_account_mgr,$session,$audit_logger;
	$resp = $admin_account_mgr->login();
	if($resp->status == ELEX_AUTHENTICATE_STATUS_SUCCESS){
		$audit_logger->write($resp,'',AuditLogManager::ACTION_LOGIN,'login');
		$prev_url = $session->get('prev_url');
		if(empty($prev_url) || $prev_url == 'mod=admin&act=login'){
			header('Location:admincp.php');
		}
		else{
			header("Location:admincp.php?" . $prev_url);
		}
	}
	else{
		if(!empty($resp->fail_times)){
			if($resp->fail_times == 5){
				$error_msg = "你已经登录失败5次,2个小时内不能登陆。";
			}
			else{
				$error_msg = sprintf("你已经登录失败%d次，还可以重试%d次。",
					$resp->fail_times, 5 - $resp->fail_times);
			}
		}
		else{
			$error_msg = "登录失败，请确认用户名和密码。";
		}
	}
}else{
	$error_msg = "您需要登录以后才能访问该页面。";
	if(!empty($_SERVER['QUERY_STRING'])){
		$session->set('prev_url',$_SERVER['QUERY_STRING']);
	}
}
?>
