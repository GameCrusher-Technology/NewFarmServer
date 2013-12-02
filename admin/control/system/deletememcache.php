<?php
if(!defined('IN_GAME')) exit('Access Denied');
global $limitvalue, $admin_logger;
$table_names = array(
	"","user_account"
);
$cache_key = $_POST['cache_key'];
try{
	if ($limitvalue!=1&&$limitvalue!=2){
		$error_msg = "没有执行权限。";
			return;
	}
	if (!empty($cache_key)) {
		$uid = $_POST ['uid'];
		if (!empty($uid)) {
			$gameuid = get_gameuid_from_uid(trim($uid));
		} else {
			$gameuid = $_POST['gameuid'];
			$uid = get_uid_from_gameuid($gameuid);
		}
		$table_name = $_POST['table_name'];
		$cache_helper = get_cache_instance($gameuid, $table_name);
		$cache_helper->delete($cache_key);
		$admin_logger->writeInfo("successfully delete cache[$cache_key]");
	}
}catch (Exception $e){
	$error_msg = $e->__toString();
	$admin_logger->writeError("exception while delete cache[$cache_key] from memcache\n".$e->getTraceAsString());
}
?>
