<?php
if(!defined('IN_GAME')) exit('Access Denied');
$table_names=array("user_data","user_data_animal","user_owned","user_warehouse","user_event_log","user_action_log","user_decoration","user_owned_tasks","user_background","trade_log","user_account","user_action_count","user_employee","admin_users","uid_gameuid_mapping");
if (empty($_POST['get'])) return;
$uid = $_POST ['uid'];
if (!empty($uid)) {
	$gameuid = get_gameuid_from_uid(trim($_POST['uid']));
} else {
	$gameuid = $_POST['gameuid'];
	$uid = get_uid_from_gameuid($gameuid);
}
if (intval($gameuid) > 0) {
	$table_name = $_POST['table_name'];
	if (empty($table_name)) {
		$error_msg = "数据库表不能为空";
		return;
	}
	$table_name = _get_table_name($table_name, get_app_config($gameuid)->getSection($table_name), $gameuid);
	$select_sql = "SELECT * FROM $table_name WHERE gameuid=$gameuid";
} elseif (!empty($uid)) {
	$error_msg = "uid[$uid]对应的用户不存在";
} else {
	$error_msg = "必须要指定相关的uid或者gameuid";
}
function _get_table_name($table,$table_config,$key_value){
	$db_name = $table_config[AppConfig::TABLE_DB_NAME];
	$db_max_num = intval($table_config[AppConfig::TABLE_MAX_DB_NUM]);
	if($db_max_num < 1){
		$db_max_num = 1;
	}
	$table_max_num = intval($table_config[AppConfig::TABLE_MAX_TABLE_NUM]);
	if($table_max_num < 1){
		$table_max_num = 1;
	}
	switch ($table_config[AppConfig::TABLE_DEPLOY]){
		case AppConfig::DEPLOY_PART_DB:
			// 只分库
			$idx = $key_value % $db_max_num;
			$table_name =  sprintf('%s%d.%s',$db_name,$idx,$table);
			break;
		case AppConfig::DEPLOY_PART_TABLE:
			// 只分表
			$idx = intval($key_value % $table_max_num);
			$table_name =  sprintf('%s.%s_%d',$db_name,$table,$idx);
			break;
		case AppConfig::DEPLOY_PART_DB_TABLE:
			// 既分库，又分表
			$db_idx = intval($key_value / $table_max_num) % $db_max_num;
			$table_idx = intval($key_value % $table_max_num);
			$table_name =  sprintf('%s%d.%s_%d',$db_name,$db_idx,$table,$table_idx);
			break;
		default:
			// 既不分库，也不分表
			$table_name = sprintf('%s.%s',$db_name,$table);
			break;
	}
	return $table_name;
}
?>