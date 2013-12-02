<?php
global $limitvalue, $admin_logger;
$table_names = array(
	"","user_account","user_data","user_decoration"
);
if(isset($_POST['getItem'])){
	$cache_key = getGPC('cache_key','string');
	if(!empty($cache_key)){
		$uid = $_POST ['uid'];
		if (!empty($_POST ['uid'])) {
			$gameuid = get_gameuid_from_uid(trim($_POST['uid']));
		} else {
			$gameuid = $_POST['gameuid'];
			$uid = get_uid_from_gameuid($gameuid);
		}
		$table_name = $_POST['table_name'];
		$cache_helper = get_cache_instance($gameuid, $table_name);
		$editItem = $cache_helper->get($cache_key);
		if($editItem === false){
			$error_msg = "该key不存在。";
			$editItem = null;
		}else{
			if(is_array($editItem) && is_array(current($editItem))){
				$error_msg = "该key的缓存值是二维数组，不能编辑。";
				$editItem = null;
			}
		}
	}else{
		$error_msg = "缓存key不能为空。";
	}
} elseif($_POST['editItem']){
	$cache_key = getGPC('cache_key','string');
	$expire_time = getGPC('expire_time');
	if(!empty($cache_key)){
		$uid = $_POST ['uid'];
		if (!empty($_POST ['uid'])) {
			$gameuid = get_gameuid_from_uid(trim($_POST['uid']));
		} else {
			$gameuid = $_POST['gameuid'];
			$uid = get_uid_from_gameuid($gameuid);
		}
		$table_name = $_POST['table_name'];
		$cache_helper = get_cache_instance($gameuid, $table_name);
		if($_POST['editscalar']){
			$modifyItem = getGPC('modifyItem','string');
			$cache_helper->set($cache_key,$modifyItem,$expire_time);
		}
		else{
			$modifyItem = getGPC('modifyItem','array');
			$cache_helper->set($cache_key,$modifyItem,$expire_time);
		}
		$op_msg = "更新完成。";
	}
	else{
		$error_msg = "缓存key不能为空。";
	}
}
if(!isset($expire_time)){
	$expire_time = 0;
}
?>