<?php
if(!defined('IN_GAME')) exit('Access Denied');
include_once ADMIN_ROOT . 'common.func.php';
require_once GAMELIB . '/model/UserEventLogManager.class.php';
require_once GAMELIB . '/model/UserAccountManager.class.php';
require_once GAMELIB . '/model/AuditLogManager.class.php';
global $limitvalue, $audit_logger, $login_user, $admin_logger;

ini_set('mbstring.internal_encoding','UTF-8');
try {
	if (isset($_POST['getEventLog'])){
		$uid=trim($_POST['uid']);
		if (!empty($uid)){
			$gameuid=get_gameuid_from_uid($uid);
		}else {
			$gameuid=trim($_POST['gameuid']);
			$uid=get_uid_from_gameuid($gameuid);
		}
		if (intval($gameuid) <= 0) {
			$error_msg = "用户信息不存在[uid=$uid,gameuid=$gameuid]";
			return;
		}
		$game_config=get_app_config($gameuid);
		$sql="select * from %s where gameuid=%d";
		$table_name=$game_config->getTableServer("user_event_log",$gameuid)->getTableName();
		$sql=sprintf($sql,$table_name,$gameuid);
		
		$start_date=getGPC('start_date','string');
		if (!empty($start_date)){
			$start_time=strtotime($start_date);
			echo $start_time;
			$sql.=" AND create_time>'$start_time'";
		}
		$end_date=getGPC('end_date','string');
		if (!empty($end_date)){
			$end_time=strtotime($end_date);
			$sql.=" AND create_time<'$end_time'";
		}
		$action_id=$_POST['action'];
		if (!empty($action_id)){
			$sql.=" AND action=$action_id";
		}
		$special_content=$_POST['special_content'];
		if (!empty($special_content)){
			$sql.=" AND content like \"%$special_content%\"";
		}
		$limit=$_POST['limit'];
		if (empty($limit)){
			$limit=100;
		}
		$sql.=" order by create_time desc limit 0,$limit";
		
		$db_helper=$game_config->getTableServer('user_event_log',$gameuid)->getDBHelperInstance();
		$event_logs=$db_helper->getAll($sql);
	}
	
	
}catch (Exception $e){
	$admin_logger->writeError($e->getTraceAsString());
	$error_msg = $e->getMessage ();
}
?>