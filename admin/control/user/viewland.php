<?php
if(!defined('IN_GAME')) exit('Access Denied');
$view_land=getGPC("view_land", "string");
$flash_config_dir="testApp/flash_xml/flash.xml";
/*
 * plateform_info数组说明
 * plateform_info=array(
 * 	"平台名称"=>array(
 * 		"game_client"=>"该平台主文件侦听的地址",
 * 		"flash_config"=>"该平台对应的配置文件的路径",//其中的文件名称应该是配置的平台名称),
 * )
 * 平台名称：在/libgame/config/config.php文件中定义的PLATFORM
 */
$plateform_info=array(
"orkut_pt"=>array(
	"web_base"=>"http://ok.farm3.pt.337.com/happyranch/",
	"host"=>"http://img.337.com/farm/orkut_farm3/orkut/swf/",
	"main_path"=>"main/orkut_pt.swf",
),
"meinvz_de"=>array(
	"web_base"=>"http://mz.farm3.de.337.com/happyranch/",
	"host"=>"http://de.img.337.com/farm3/meivz/swf/",
	"main_path"=>"main/meinvz.swf",
),
"facebook_tw"=>array(
	"web_base"=>"http://tw.farm3.337.com/happyranch/",
	"host"=>"http://tw.farm3.337.com/happyranch/swf_1.0.0507/",
	"main_path"=>"facebook.swf",
),
"yahoo_tc"=>array(
	"web_base"=>"http://yh.farm3.mt.337.com/yahoo_tc/",
	"host"=>"http://de.img.337.com/farm3/yahoo/yahoo_tc/",
	"main_path"=>"yahoo_tc.swf",
),
"test"=>array(
	"web_base"=>"http://10.1.1.65/big_scene/",
	"host"=>"http://10.1.2.86/happyranch_bigscene/swf/",
	"main_path"=>"xiaoneiranch.swf?v=aldjfhlasjf",
),
);
if (empty($view_land)) return ;
try {
	$uid = $_POST ['uid'];
	if (!empty($uid)) {
		$gameuid = get_gameuid_from_uid(trim($_POST['uid']));
	} else {
		$gameuid = $_POST['gameuid'];
		$uid = get_uid_from_gameuid($gameuid);
	}
	$platform_name=_get_plateform();
	if (!array_key_exists($platform_name,$plateform_info)){
		$error_msg = "配置信息不存在";
		return;
	}
	createFlashXml($platform_name);
	$flash_config=$flash_config_dir;
	$game_client=createMainUrl($platform_name);
	if ($gameuid > 0) {
		require_once GAMELIB.'/model/UserAccountManager.class.php';
		$user_account_mgr = new UserAccountManager();
		$user_account = $user_account_mgr->getUserAccount($gameuid);
		if (empty($user_account)) {
			$error_msg = "虽然找到了[uid=$uid ]所对应的gameuid[$gameuid ]，但是无法找到相应的user_account对象，请检查该用户";
			return;
		}
	} else {
		$error_msg = "[$uid]所对应的用户不存在";
	}
}catch (Exception $e){
	echo print_r($e,true);
}
function createFlashXml($platform_name){
	global $flash_config_dir;
	global $plateform_info;
	$config=array(
		"web_base"=>$plateform_info[$platform_name]["web_base"],
		"host"=>$plateform_info[$platform_name]["host"],
		"database"=>$plateform_info[$platform_name]["host"],
		"sns_call_type"=>"back_end",
		"mod"=>"facebook",
		"menu"=>"false",
		"wmode"=>"opaque",
		"allowFullScreen"=>"true",
		"allowScriptAccess"=>"always",
		"scale"=>"noscale",
		"config_version"=>strtotime(date("Y-m-d H:i:s"))
	);
	file_put_contents($flash_config_dir,"<?xml version=\"1.0\" encoding=\"UTF-8\" ?> \r\n");
	file_put_contents($flash_config_dir,"<config> \r\n",FILE_APPEND);
	file_put_contents($flash_config_dir,"\t<game> \r\n",FILE_APPEND);
	foreach ($config as $key=>$v){
		$str="\t\t<$key>$v</$key> \r\n";
		file_put_contents($flash_config_dir,$str,FILE_APPEND);
	}
	file_put_contents($flash_config_dir,"\t</game> \r\n",FILE_APPEND);
	file_put_contents($flash_config_dir,"</config>",FILE_APPEND);
}
function createMainUrl($platform_name){
	global $plateform_info;
	$url=$plateform_info[$platform_name]["host"].$plateform_info[$platform_name]["main_path"];
	return $url;
}
?>