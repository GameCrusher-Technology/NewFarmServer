<?php
/**
 * 通过uid返回用户的gameuid
 *
 * @param 用户的uid $uid
 * @return $uid对应的gameuid
 */
function get_gameuid_from_uid($uid) {
	if (empty($uid)) return 0;
	require_once GAMELIB . '/model/UidGameuidMapManager.class.php';
	$map_mgr = new UidGameuidMapManager();
	return $map_mgr->getGameuid($uid);
}
/**
 * 通过gameuid返回用户的uid
 *
 * @param 用户的gameuid $gameuid
 * @return $gameuid对应的uid
 */
function get_uid_from_gameuid($gameuid) {
	if (intval($gameuid) <= 0) return 0;
	require_once GAMELIB . '/model/UidGameuidMapManager.class.php';
	$map_mgr = new UidGameuidMapManager();
	return $map_mgr->getUid($gameuid);
}
/**
 * 返回具体用户的操作缓存，因为缓存现在和具体的用户以及表相关，所以需要传递$gameuid和$table参数
 *
 * @param 用户的游戏id $gameuid
 * @param 相关的数据库表 $table
 * @return 缓存操作句柄
 */
function get_cache_instance($gameuid = null, $table = null) {
	$cache_instance = get_app_config($gameuid)->getTableServer($table)->getCacheInstance();
	if (!empty($cache_instance)) return $cache_instance;
	return $GLOBALS['cache_helper'];
}
/**
 * 返回gameuid对应的用户实例
 *
 * @param 用户的游戏id $gameuid
 * @return 用户的数据
 */
function get_user_account($gameuid) {
	if (intval($gameuid) <= 0) return false;
	require_once GAMELIB . '/model/UserAccountManager.class.php';
	$user_account_mgr = new UserAccountManager();
	return $user_account_mgr->getUserAccount($gameuid);
}
/**
 * 返回用户的event log
 * 
 * @param 用户的游戏id $gameuid
 * @param 数据库记录集的offset $offset
 * @param 数据库记录集的limit $limit
 */
function _get_eventlogs($gameuid,$offset,$limit) {
	$db_helper = get_app_config($gameuid)->getTableServer("user_event_log")->getDBHelperInstance();
	$t = $gameuid % 100;
	$limit_cond = " limit $offset,$limit";
	$sql = "SELECT gameuid,content,action, create_time
			FROM `user_event_log_$t`
			WHERE gameuid = '$gameuid' order by create_time desc $limit_cond";
	return $db_helper->getAll($sql);		
}
/**
 * 返回用户的action log
 * 
 * @param 用户的游戏id $gameuid
 * @param 动作的限制条件，只取出相关的动作日志 $action_cond
 * @param 取出第$w周的日志 $w
 * @param 数据库记录集的offset $offset
 * @param 数据库记录集的limit $limit
 */
function _get_actionlogs($gameuid,$action_cond,$w,$offset,$limit){
	$db_helper = get_app_config()->getTableServer("user_action_log")->getDBHelperInstance();
	$limit_cond = " limit $offset,$limit";
	if(empty($w)) $w = date('W');
	$sql = "SELECT * FROM `user_action_log_$w` 
		WHERE gameuid = $gameuid $action_cond order by create_time desc $limit_cond";
	return $db_helper->getAll($sql);
}
function _get_all_gameuid(){
	$db_helper = get_app_config()->getTableServer("uid_gameuid_mapping")->getDBHelperInstance();
	$sql = "SELECT * FROM `uid_gameuid_mapping`";
	return $db_helper->getAll($sql);
}
function _get_plateform(){
	return GameConfig::getPlatForm();
}
?>