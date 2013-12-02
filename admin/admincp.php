<?php

include './config.inc.php';

$title = "shinning farm 管理页面";
global $session, $admin_account_mgr;

$op_msg = '';
$login_user = $admin_account_mgr->isLogin();
if(empty($login_user)){
	$GLOBALS['admin_logger']->writeInfo('user not login, redirect to login page.');
	include ("./control/admin/login.php");
	include (renderTemplate('admin', 'login'));
	exit();
}
$GLOBALS['admin_logger']->writeInfo('get login user info:%s',print_r($login_user,true));

$modules = array('admin','user','item','system','xml','stat','notice');
$actions = array(
	'admin' => array('index','login','quit')
);
$limitgroup = array('11111'=>'普通管理员', '11112'=>'高级管理员', '12222'=>'超级管理员','10000'=>'迷你用户');
$case = $login_user->group_id;
$last_login_time = $login_user->last_login_time;
switch($case){
	case '11111':
		// 普通管理员
		$limitvalue = 0;
		$actions = array(
			'admin' => array('index','login','session','quit'),
			'user' => array('log','getdeleteaccount','admin','tradelog','databasemgr','eventlog'),
			'system'=>array('getmemcache'),
			'stat'=>array('system','pay','shop','temp'),
		);
		break;
	case '12222':
		// 超级管理员
		$limitvalue = 1;
		$actions = array(
			'admin' => array('index','login','session','quit','edit','changepasswd','resetpasswd','adduser','change_log','close_log'),
			'user' => array('log','deleteaccount','getdeleteaccount','additem','admin',
			'tradelog','viewland','batchprocess','databasemgr',"eventlog",'rewardSomething'),
			'item'=> array('edit','package','packagelist'),
			'system'=>array('getsql','deletememcache','getmemcache','editcache','memcache','log','deleteItemListMemcache'),
			'xml'=>array('modify','get'),
			'stat'=>array('system','pay','shop','temp'),
			'notice'=>array('list','add','edit'),
		);
		break;
	case '11112':
		// 高级管理员
		$limitvalue = 2;
		$actions = array(
			'admin' => array('index','login','session','quit','changepasswd'),
			'user' => array('log','deleteaccount','getdeleteaccount','additem','admin',
			'tradelog','viewland','batchprocess','databasemgr','eventlog','rewardSomething'),
			'item'=> array('edit','package','packagelist'),
			'system'=>array('getsql','deletememcache','getmemcache','editcache','memcache','log','deleteItemListMemcache'),
			'xml'=>array('modify','get'),
		);
	break;
	case '10000':
		// 迷你管理员
		$limitvalue = 3;
		$actions = array(
			'user' => array('tradelog','getgameuid'),
		);
	break;
	default:
		// 超级管理员
		$limitvalue = 1;
		$actions = array(
			'admin' => array('index','login','session','quit','edit','changepasswd','resetpasswd','adduser','change_log','close_log'),
			'user' => array('log','deleteaccount','getdeleteaccount','additem','admin',
			'tradelog','viewland','batchprocess','databasemgr',"eventlog",'rewardSomething'),
			'item'=> array('edit','package','packagelist'),
			'system'=>array('getsql','deletememcache','getmemcache','editcache','memcache','log','deleteItemListMemcache'),
			'xml'=>array('modify','get'),
			'stat'=>array('system','pay','shop','temp'),
			'notice'=>array('list','add','edit'),
		);
}
include_once ADMIN_ROOT.'/menu_config.php';
$module = getGPC("mod","string");
if(!$module || !in_array($module,$modules)){
	$module = "admin";
}

$action = getGPC('act',"string");
if($action && in_array($action, $actions[$module])){
	if(!file_exists("./control/$module/$action.php")){
		$module = 'admin';
		$action = 'limiterror';
		$error_msg = "该模块不存在!";
	}
} elseif(!empty($action)){
	$module = 'admin';
	$action = 'limiterror';
	$error_msg = "您没有权限访问该模块，请与管理员联系!";
} else{
	$module = 'admin';
	$action = 'index';
}
try{
	include ("./control/{$module}/{$action}.php");
	include (renderTemplate($module, $action));
}catch (Exception $e){
	$error_msg = $e->__toString();
	$GLOBALS['admin_logger']->writeError($e->getMessage().", and the stacktrace is as below:\n");
	$GLOBALS['admin_logger']->writeError($e->getTraceAsString());
}
?>