<?php
if(!defined('IN_GAME')) exit('Access Denied');
$no_db_cache = array(
	"everyday_action_count"=>'_format_everyday_action_count',
	"user_event_log"=>'_format_user_event_log',
	"user_action_log"=>'_format_user_action_log',
	"user_account_cache"=>'_format_user_account_cache',
	"user_help_log"=>'_format_user_help_log',
	"daily_visit_info"=>'_format_user_visit_info',
	"user_send_gift_log"=>'_format_user_send_gift_log',
	"user_scene_create_flag"=>'_format_user_scene_create_flag',
	"user_item_list"=>'_format_user_item_list',
	"other_flag"=>'_format_other_flag',
);
function _format_friends() {
	global $error_msg, $uid;
	if (empty($uid)) {
		$error_msg = '获取缓存的用户好友信息，必须要提供uid';
		return '';
	}
	
	$cache_instance = get_app_config()->getTableServer('platform_cache')->getCacheInstance();
	$cache_key = sprintf(CacheKey::CACHE_KEY_PLATFORM_USER_FRIENDS,$uid);
	$user_friends = $cache_instance->get($cache_key);
	if (!empty($user_friends)) {
		return array(
			"header"=>"好友的uids",
			"data"=>array(array($user_friends)),
			"extra_info"=>"缓存键值:$cache_key"
		);
	}
	return array();
}
function _format_everyday_action_count() {
	global $error_msg, $gameuid, $action_defs;
	$action_id = $_POST['action_id'];
	if (empty($action_id)) {
		$error_msg = "没有指定操作类型";
		return array();
	}
	$action_def = $action_defs[$action_id];
	$cache_helper = get_app_config($gameuid)->getDefaultCacheInstance();
	$flag_key = sprintf(CacheKey::CACHE_KEY_CAN_ACTION_HAPPEN_FLAG, $gameuid, $action_id);
	$cache_entry = $cache_helper->get($flag_key);
	
	$count = 0;
	$last_update = '';
	if ($cache_entry !== false) {
		$count = $cache_entry['count'];
		$last_update = date('Y-m-d H:i:s', $cache_entry['last_update']);
	}
	return array(
		"header"=>array("动作id", "动作名称", "发生次数", "最近更新时间"),
		"data"=>array(array($action_id, $action_def['action_description'], $count, $last_update)),
		"extra_info"=>"缓存键值:$flag_key"
	);
}
function _format_user_account_cache(){
	global $gameuid;
	$mem_key=sprintf('ranche_cache_account_%d',$gameuid);
	$cache_helper=get_app_config($gameuid)->getDefaultCacheInstance();
	$datas=$cache_helper->get($mem_key);
	return array(
		"header"=>array("用户受到好友帮助的缓存记录"),
		"data"=>$datas,
		"extra_info"=>"缓存键值:$mem_key",
		"print_type"=>1
	);
}
function _format_user_event_log() {
	global $gameuid;
	require_once GAMELIB.'/model/UserEventLogManager.class.php';
	require_once GAMELIB.'/model/XmlManager.class.php';
	$user_event_log_mgr = new UserEventLogManager($gameuid);
	$action_def_mgr = new ActionDefManager();
	$event_logs = $user_event_log_mgr->getEventLog();
	$datas = array();
	foreach ($event_logs as $event_log) {
		$action_def = $action_def_mgr->getDef($event_log['action']);
		$datas[] = array(
			$action_def['action_description'], 
			$event_log['content'], 
			date("Y-m-d H:i:s", $event_log['create_time']));
	}
	return array(
		"header"=>array("动作类型","参数","发生时间"),
		"data"=>$datas,
		"extra_info"=>""
	);
}
function _format_user_action_log() {
	require_once GAMELIB.'/model/XmlManager.class.php';
	$action_def_mgr = new ActionDefManager();
	global $gameuid;
	$cache_helper = get_cache_instance($gameuid, 'user_action_log');
	$action_log_key = sprintf(CacheKey::CACHE_KEY_USER_ACTION_LOG, $gameuid);
	$action_logs = $cache_helper->get($action_log_key);
	$datas = array();
	foreach ($action_logs as $action_log) {
		$action_def = $action_def_mgr->getDef($action_log['action_type']);
		$datas[] = array(
			$action_def['action_description'], 
			$action_log['coin'], 
			$action_log['money'],
			$action_log['coupon'], 
			$action_log['experience'], 
			date("Y-m-d H:i:s", $action_log['create_time']),
			$action_log['content']);
	}
	return array(
		"header"=>array("动作类型","金币变化数","农民币变化数","点券变化","经验变化数","发生时间","详细信息"),
		"data"=>$datas,
		"extra_info"=>""
	);
}
function _format_user_help_log(){
	global $gameuid;
	$mem_key=sprintf(CacheKey::CACHE_KEY_HELP_LOG,$gameuid);
	$cache_helper=get_app_config($gameuid)->getDefaultCacheInstance();
	$datas=$cache_helper->get($mem_key);
	return array(
		"header"=>array("用户受到好友帮助的缓存记录"),
		"data"=>$datas,
		"extra_info"=>"缓存键值:$mem_key",
		"print_type"=>1
	);
}
function _format_user_visit_info(){
	global $gameuid;
	$mem_key=sprintf(CacheKey::CACHE_KEY_USER_DAILY_VISIT_INFO,$gameuid);
	$cache_helper=get_app_config($gameuid)->getDefaultCacheInstance();
	$datas=$cache_helper->get($mem_key);
	return array(
		"header"=>array("用户已经拜访的好友的缓存记录"),
		"data"=>$datas,
		"extra_info"=>"缓存键值:$mem_key",
		"print_type"=>1
	);
}
function _format_user_send_gift_log(){
	global $gameuid;
	$mem_key=sprintf(CacheKey::CACHE_KEY_USER_PLATEFORM_GIFT_FLAG,$gameuid);
	$cache_helper=get_app_config($gameuid)->getDefaultCacheInstance();
	$datas=$cache_helper->get($mem_key);
	return array(
		"header"=>array("用户已经送礼的缓存记录"),
		"data"=>$datas,
		"extra_info"=>"缓存键值:$mem_key",
		"print_type"=>1
	);
}
function _format_user_scene_create_flag(){
	global $gameuid;
	$mem_key=sprintf(CacheKey::CACHE_KEY_USER_CREATE_SCENE_TIME,$gameuid);
	$cache_helper=get_app_config($gameuid)->getDefaultCacheInstance();
	$datas=$cache_helper->get($mem_key);
	return array(
		"header"=>array("用户创建每日场景的缓存"),
		"data"=>array("flag"=>date("Y-m-d h:i:s",$datas),"now"=>date("Y-m-d h:i:s")),
		"extra_info"=>"缓存键值:$mem_key",
		"print_type"=>1
	);
}
function _format_user_item_list(){
	global $gameuid;
	require_once GAMELIB.'/model/UserGameItemManager.class.php';
	$user_item_mgr=new UserGameItemManager($gameuid);
	$list = $user_item_mgr->getItemList();
	return array(
		"header"=>array("用户item_list缓存"),
		"data"=>array($list),
		"extra_info"=>"缓存键值:use_item",
		"print_type"=>1
	);
}
function _format_other_flag(){
	global $gameuid;
	$mem_key=$_POST['mem_key'];
//	$mem_key=sprintf(CacheKey::CACHE_KEY_USER_CREATE_SCENE_TIME,$gameuid);
	$cache_helper=get_app_config($gameuid)->getDefaultCacheInstance();
	$datas=$cache_helper->get($mem_key);
	return array(
		"header"=>array("其它缓存信息"),
		"data"=>$datas,
		"extra_info"=>"缓存键值:$mem_key",
		"print_type"=>1
	);
}
global $limitvalue, $admin_logger;
require_once GAMELIB.'/model/XmlManager.class.php';
$user_action_mgr = new ActionDefManager();
$action_defs = $user_action_mgr->getDefList();

$table_names = array_keys($no_db_cache);
$action_get = getGPC("get", "string");
if(empty($action_get)){return;}
try {
	$uid = $_POST ['uid'];
	if (!empty($uid)) {
		$gameuid = get_gameuid_from_uid(trim($_POST['uid']));
	} else {
		$gameuid = $_POST['gameuid'];
		$uid = get_uid_from_gameuid($gameuid);
	}
//	if (intval($gameuid) <= 0) {
//		$error_msg = "用户信息不存在[uid=$uid,gameuid=$gameuid]";
//		return;
//	}
//	$user_account = get_user_account($gameuid);
//	if (empty($user_account)) {
//		$error_msg = "用户信息不存在[uid=$uid,gameuid=$gameuid]";
//		return;
//	}
	$table_name = $_POST['table_name'];
		
	if (isset($no_db_cache[$table_name])) {
		$entries = $no_db_cache[$table_name]();
	}
	$header = $entries['header'];
	$rows = $entries['data'];
	$extra_info = strval($entries['extra_info']);
	$column_count = count($header);
	$print_type=intval($entries['print_type']);
	
} catch ( Exception $e ) {
	$admin_logger->writeError("exception while manage memcache.".$e->getMessage());
	$error_msg = $e->getMessage ();
}
?>