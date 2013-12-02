<?php
if(!defined('IN_GAME')) exit('Access Denied');
require_once ADMIN_ROOT . '/common.func.php';
require_once ADMIN_ROOT . '/control/user/log.php';
global $limitvalue, $admin_logger;
ini_set('mbstring.internal_encoding','UTF-8');

if (isset ( $_POST ['uid']) || isset($_POST['gameuid'])) {
	try {
		$uid = trim ( $_POST ['uid']);
		$gameuid = trim ( $_POST ['gameuid']);
		if(empty($uid) && empty($gameuid)){
			$sql = "select * from user_account_deleted ";
		}
		elseif(!empty($uid)){
			$sql = "select * from user_account_deleted where uid = $uid";
		}
		elseif(!empty($gameuid)){
			$sql = "select * from user_account_deleted where gameuid = $gameuid";
		}
		$limit = intval($_POST['limit']);
		$offset = intval($_POST['offset']);
		if(empty($limit)){
			$limit = 20;
		}
		if($offset > 0){
			$sql .= " limit  $offset,$limit";
		}
		else{
			$sql .= ' limit ' . $limit;
		}
		$db_helper = get_app_config()->getTableServer("user_account_deleted")->getDBHelperInstance();
		
		$users = $db_helper->getAll($sql);
		
		if(count($users) == 1){
			$user = $users[0];
			$action_logs = _get_actionlogs($user['gameuid'],'',0,$offset,$limit);
			$event_logs = _get_eventlogs($user['gameuid'],$offset,$limit);
		}
		
	} catch ( Exception $e ) {
		$error_msg = $e->getMessage ();
	}
}
?>