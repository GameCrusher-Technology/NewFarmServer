<?php
error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);

define('APP_ROOT', realpath(dirname(__FILE__) . '/../'));
define('ADMIN_ROOT', APP_ROOT . '/admin');
define('FRAMEWORK', APP_ROOT . '/framework');
define('GAMELIB', APP_ROOT . '/libgame');

//note 定义Cache、Block、Templates等缓存数据的目录
define('TEMPLATE_DATA_DIR', ADMIN_ROOT.'/cache/templates');
//note 定义Templates模板路径
define('TEMPLATE_DIR', ADMIN_ROOT.'/view');
define ('TEMPLATE_EXT','.htm');
define ('BASE_URL', 'http://gaozhiyong/happyranch/');
define ('MOD', 'admin_viewland');
define('IN_GAME',true);
define("VERSION","1.0");

//错误码定义
define("STATUS_USER_NOT_EXISTS", 5001);
define("WRONG_PASSWORD", 5001);

require_once GAMELIB . '/config/GameConfig.class.php';
require_once FRAMEWORK . '/log/LogFactory.class.php';
require_once FRAMEWORK . '/db/RequestFactory.class.php';
require_once GAMELIB . '/GameConstants.php';
include_once FRAMEWORK. '/template/Template.class.php';
include_once GAMELIB. '/common.func.php';
include_once ADMIN_ROOT. '/common.func.php';

require_once GAMELIB . '/model/AdminAccountManager.class.php';
require_once GAMELIB . '/model/AuditLogManager.class.php';
include_once FRAMEWORK . '/session/Session.class.php';

date_default_timezone_set(get_app_config()->getTimeZone());
$GLOBALS['cache_helper'] = get_app_config()->getDefaultCacheInstance();
$GLOBALS['session'] = Session::getInstance('memcache',array('cache' => $GLOBALS['cache_helper'], 'expire' => 10800));
$GLOBALS['audit_logger'] = new AuditLogManager();
$GLOBALS['admin_account_mgr'] = new AdminAccountManager($GLOBALS['session']);
// 获取所有的缓存服务器地址
$GLOBALS['cache_servers'] = array();
if (file_exists(APP_ROOT."/etc_all/".GameConfig::getPlatForm()."/user_partitions.ini")) {
	$user_partition = parse_ini_file(APP_ROOT."/etc_all/".GameConfig::getPlatForm()."/user_partitions.ini");
	$config_files = array_keys($user_partition);
	$config_files[] = "default";
	foreach ($config_files as $config_file) {
		$config_file = APP_ROOT."/etc_all/".GameConfig::getPlatForm()."/$config_file.ini";
		if (!file_exists($config_file)) continue;
		$root_config = parse_ini_file($config_file,true);
		foreach ($root_config as $section_config) {
			foreach ($section_config as $k=>$v) {
				$v = trim($v);
				if (empty($v)) continue;
				if (strpos($k, "cache_server") !== false) {
					$host_configs = explode(',', $v);
					foreach ($host_configs as $host_config) {
						if (in_array($host_config, $GLOBALS['cache_servers'])) continue;
						$GLOBALS['cache_servers'][] = $host_config;
					}
				}
			}
		}
	}
}
?>