<?php
if(!defined('IN_GAME')) exit('Access Denied');
include_once ADMIN_ROOT . 'common.func.php';
require_once GAMELIB . '/model/UserEventLogManager.class.php';
require_once GAMELIB . '/model/UserAccountManager.class.php';
require_once GAMELIB . '/model/AuditLogManager.class.php';
global $limitvalue, $audit_logger, $login_user, $admin_logger;

$log_levels = array('fatal','error','info','debug');
$log_modules = array(
	LogFactory::LOG_MODULE_AMF_ENTRY,
	LogFactory::LOG_MODULE_MODEL,
	LogFactory::LOG_MODULE_ACTIONS,
	LogFactory::LOG_MODULE_WEB_ENTRY,
	LogFactory::LOG_MODULE_FRAMEWORK,
	LogFactory::LOG_MODULE_ADMIN,
	LogFactory::LOG_MODULE_DATABASE,
	LogFactory::LOG_MODULE_CACHE,
	LogFactory::LOG_MODULE_PLATFORM,
	LogFactory::LOG_MODULE_OTHER,
);
$type_get = getGPC('get','string');
$type_download = getGPC('download','string');
$type_truncate = getGPC('truncate','string');
if (!isset($type_get) && !isset($type_download) && !isset($type_truncate)) return;

$cache_helper = get_system_log_cache_helper();
$log_conn = get_system_log_db_conn();
$key = 'ck_system_log';
	
if (isset($type_get)) {
	$gameuid = getGPC('gameuid');
	$date = getGPC('date', 'string');
	$current_log_level = getGPC('log_level', 'string');
	$current_log_module = getGPC('log_module', 'string');
	$offset = getGPC('offset') <= 0 ? 0 : getGPC('offset');
	$limit = getGPC ('limit');
	if($limit < 1) $limit = 100;
	
	// 先提交缓存里边的日志信息
	commit_log();
	
	$cond = '';
	if ($gameuid > 0) {
		$cond .= " AND gameuid=$gameuid";
	}
	if (!empty($current_log_level)) {
		$cond .= " AND level='$current_log_level'";
	}
	if (!empty($current_log_module)) {
		$cond .= " AND module='$current_log_module'";
	} else if ($limitvalue != 1) {
		$cond .= " AND module!='".LogFactory::LOG_MODULE_ADMIN."'";
	}
	if (!empty($date)) {
		$start_time = strtotime($date);
		$end_time = strtotime('+1 day', $start_time);
		$cond .= " AND create_time > $start_time AND create_time < $end_time";
	}
	$sql = 'SELECT * FROM log WHERE 1=1'.$cond." LIMIT $limit OFFSET $offset";
	$result = @mysql_query($sql, $log_conn);
	$logs = array();
	if (@mysql_num_rows($result) > 0) {
		do {
			$row = @mysql_fetch_assoc($result);
			if ($row) $logs[] = $row;
		} while ($row);
	}
	@mysql_close($log_conn);
}
if(isset($type_download)){
	$download_dir = "/tmp/happyranch_system_log/";
	//$download_dir = "D:/temp/system_log/";
	// 先将缓存中的log日志提交到数据库
	commit_log();
	if (!file_exists($download_dir)) {
		mkdir($download_dir, 0777, true);
	}
	$logs_file_name = 'logs.tar.gz';
	$archive_file_name = $download_dir.$logs_file_name;
	unlink($archive_file_name);
	
	$file_names = array();
	foreach ($log_modules as $log_module) {
		$module_log_file = $download_dir.$log_module.".log";
		unlink($module_log_file);
		if ($log_module == 'admin' && $limitvalue != 1) continue;
		/* the db server and web server are not on the same machine. can't use the following way.
		 * $res = @mysql_query("SELECT msg INTO OUTFILE '$module_log_file' FIELDS ESCAPED BY '' FROM log WHERE module='$log_module'");
		if ($res === false) {
			echo "can not dump log of $log_module";
			continue;
		}*/
		dump_log($log_module, $module_log_file);
		$file_names[] = $log_module.".log";
	}
	@mysql_close($log_conn);
	
	// the zlib php extension not include. can not use
	// zip_files($archive_file_name, $file_names, $download_dir);
	exec("cd $download_dir;tar -czvf $archive_file_name -C $download_dir *.log");
	header("Content-type: application/x-gzip");
	header("Content-Disposition: attachment; filename=$logs_file_name");
	header("Pragma: no-cache");
	header("Expires: 0");
	readfile($archive_file_name);
	exit;
}
if (isset($type_truncate)) {
	$cache_helper->delete($key);
	$res = @mysql_query("TRUNCATE TABLE log");
	if ($res === false) {
		echo "execute truncate sql failed.";
	}
	@mysql_close($log_conn);
	$op_msg = "删除log日志成功";
}
function zip_files($archive_file_name,$file_names,$file_path)
{
	//create the object
	$zip = new ZipArchive();
	//create the file and throw the error if unsuccessful
	if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE ) !== true) {
		echo "can not open archive file : $archive_file_name";
		return false;
	}
	
	//add each files of $file_name array to archive
	foreach($file_names as $file_name)
	{
		$zip->addFile($file_path.$file_name, $file_name);
	}
	$zip->close();
	return true;
}
function commit_log() {
	global $cache_helper, $log_conn, $key, $error_msg;
	$logs_from_cache = $cache_helper->get($key);
	if (is_array($logs_from_cache) && count($logs_from_cache) > 0) {
		$sql = '';
		foreach ($logs_from_cache as $log) {
			if (isset($sql[0])) $sql .= ',';
			$sql .= "('".implode("','", array_map("elex_addslashes", $log))."')";
		}
		$sql = sprintf(
			'INSERT INTO %s (%s) values', 
			'log', 
			'level,gameuid,module,msg,create_time').$sql;
		$res = @mysql_query($sql, $log_conn);
		if ($res === false) {
			$error_msg = "tring to commit logs in cache failed.".@mysql_error($log_conn);
			return;
		}
		$affected_rows = @mysql_affected_rows($log_conn);
		if ($affected_rows > 0) {
			// 将缓存清空
			$logs = array();
			$cache_helper->set(CACHE_KEY_LOG_DATABASE_STORAGE, $logs, 0);
		}
	}
}
function dump_log($module, $module_log_file) {
	$loop_count = 100;
	$count = get_one("SELECT count(*) AS count FROM log WHERE module='$module'");
	$count = $count['count'];
	$loop = ($count % $loop_count == 0) ? $count / $loop_count : floor($count / $loop_count) + 1;
	for ($i = 0; $i < $loop; $i++) {
		$result = get_all("SELECT msg FROM log WHERE module='$module' ORDER BY create_time LIMIT $loop_count OFFSET ".$loop_count*$i);
		foreach ($result as $entry) {
			file_put_contents($module_log_file, $entry['msg'], FILE_APPEND);
		}
		unset($result);
	}
}
function get_all($sql) {
	global $log_conn;
	$result = @mysql_query($sql, $log_conn);
	$rows = array();
	if (@mysql_num_rows($result) > 0) {
		do {
			$row = @mysql_fetch_assoc($result);
			if ($row) $rows[] = $row;
		} while ($row);
	}
	if (empty($rows)) return false;
	return $rows;
}
function get_one($sql) {
	global $log_conn;
	$result = @mysql_query($sql, $log_conn);
	$row = array();
	if (@mysql_num_rows($result) > 0) {
		do {
			$row = @mysql_fetch_assoc($result);
			if ($row) $rows[] = $row;
		} while ($row);
	}
	if (empty($rows)) return false;
	return $rows[0];
}
?>