<?php
if(!defined('IN_GAME')) exit('Access Denied');
global $limitvalue;
	if ($limitvalue==0){
		$menu = array(
		"user_data" => array("name" => "用户数据管理模块"),
		"memcache" => array("name" => "缓存数据管理"),
		"user_log" => array("name"=>"用户日志管理模块"),
		"stat"=>array("name"=>"系统统计数据"),
		);
		// 超级管理员
		$menu["user_data"]["sub_menu"] = array(
			array("name"=>"用户数据管理",
						"href"=>"admincp.php?mod=user&act=databasemgr"),
		);
		$menu["memcache"]["sub_menu"] = array(
			array("name"=>"用户缓存数据",
						"href"=>"admincp.php?mod=system&act=getmemcache"),
		);
		$menu["user_log"]["sub_menu"] = array(
			array("name"=>"查看动作日志",
						"href"=>"admincp.php?mod=user&act=log"),
			array("name"=>"查看事件日志",
						"href"=>"admincp.php?mod=user&act=eventlog"),
		);
		$menu["stat"]["sub_menu"] = array(
			array("name"=>"系统统计",
						"href"=>"admincp.php?mod=stat&act=system"),
			array("name"=>"支付统计",
					"href"=>"admincp.php?mod=stat&act=pay"),
			array("name"=>"商店统计",
					"href"=>"admincp.php?mod=stat&act=shop"),
			array("name"=>"临时统计",
					"href"=>"admincp.php?mod=stat&act=temp")
		);
	}elseif ($limitvalue == 2){
		$menu = array(
		"user_data" => array("name" => "用户数据管理模块"),
		"memcache" => array("name" => "缓存数据管理"),
		"admin" => array("name" => "管理用户管理"),
		"user_log" => array("name"=>"用户日志管理模块"),
		);
		$menu["user_log"]["sub_menu"] = array(
			array("name"=>"查看动作日志",
						"href"=>"admincp.php?mod=user&act=log"),
			array("name"=>"查看事件日志",
						"href"=>"admincp.php?mod=user&act=eventlog"),
		);
		// 高级管理员只能够修改密码
		$menu["admin"]["sub_menu"] = array(
			array("name"=>"修改密码",
						"href"=>"admincp.php?mod=admin&act=changepasswd"),
			);
		$menu["memcache"]["sub_menu"] = array(
			array("name"=>"用户缓存数据",
						"href"=>"admincp.php?mod=system&act=getmemcache"),
			array("name"=>"ItemList缓存数据",
						"href"=>"admincp.php?mod=system&act=deleteItemListMemcache"),
			array("name"=>"缓存key删除",
					"href"=>"admincp.php?mod=system&act=deletememcache"),
		);
		$menu["user_data"]["sub_menu"] = array(
			array("name"=>"用户数据管理",
						"href"=>"admincp.php?mod=user&act=databasemgr"),
			array("name"=>"删除用户",
						"href"=>"admincp.php?mod=user&act=deleteaccount"),
			array("name"=>"查看已删用户",
					"href"=>"admincp.php?mod=user&act=getdeleteaccount"),
			array("name"=>"用户奖励",
					"href"=>"admincp.php?mod=user&act=rewardSomething"),
		);
	}elseif ($limitvalue == 1){
		$menu = array(
		"user_data" => array("name" => "用户数据管理模块"),
		"memcache" => array("name" => "缓存数据管理"),
		"admin" => array("name" => "管理用户管理"),
		"user_log" => array("name"=>"用户日志管理模块"),
		"system_log" => array("name"=>"系统日志管理模块"),
	    "xml"=>array("name"=>"xml管理模块"),
		"stat"=>array("name"=>"系统统计数据"),
		"notice"=>array("name"=>"系统公告和补偿"),
		);
		// 超级管理员能够管理管理员用户
		$menu["admin"]["sub_menu"] = array(
			array("name"=>"查看管理员",
						"href"=>"admincp.php?mod=admin&act=edit"),
			array("name"=>"添加管理员",
						"href"=>"admincp.php?mod=admin&act=adduser"),
			array("name"=>"修改密码",
						"href"=>"admincp.php?mod=admin&act=changepasswd"),
			array("name"=>"重置密码",
						"href"=>"admincp.php?mod=admin&act=resetpasswd")
		);
		// 超级管理员可以查看交易日志
		$menu["user_log"]["sub_menu"] = array(
			array("name"=>"查看动作日志",
						"href"=>"admincp.php?mod=user&act=log"),
			array("name"=>"查看交易日志",
						"href"=>"admincp.php?mod=user&act=tradelog"),
			array("name"=>"查看事件日志",
						"href"=>"admincp.php?mod=user&act=eventlog"),
			array("name"=>"查看管理日志",
						"href"=>"admincp.php?mod=admin&act=change_log"),
			array("name"=>"查看封号日志",
						"href"=>"admincp.php?mod=admin&act=close_log"
			)
		);
		//超级管理员可以对xml的内容进行修改
		$menu["xml"]["sub_menu"] = array(
			array("name"=>"修改xml内容",
						"href"=>"admincp.php?mod=xml&act=modify"),
			array("name"=>"获取xml内容",
					"href"=>"admincp.php?mod=xml&act=get"),
		);
		$menu["stat"]["sub_menu"] = array(
			array("name"=>"系统统计",
						"href"=>"admincp.php?mod=stat&act=system"),
			array("name"=>"支付统计",
					"href"=>"admincp.php?mod=stat&act=pay"),
			array("name"=>"商店统计",
					"href"=>"admincp.php?mod=stat&act=shop"),
			array("name"=>"临时统计",
					"href"=>"admincp.php?mod=stat&act=temp")
		);
		$menu["system_log"]["sub_menu"] = array(
			array("name"=>"查看系统日志",
						"href"=>"admincp.php?mod=system&act=log"),
		);
		$menu["memcache"]["sub_menu"] = array(
			array("name"=>"用户缓存数据",
						"href"=>"admincp.php?mod=system&act=getmemcache"),
			array("name"=>"ItemList缓存数据",
						"href"=>"admincp.php?mod=system&act=deleteItemListMemcache"),
			array("name"=>"缓存key删除",
						"href"=>"admincp.php?mod=system&act=deletememcache"),
			array("name"=>"缓存监控",
						"href"=>"admincp.php?mod=system&act=memcache")
			
		);
		$menu["notice"]["sub_menu"] = array(
			array("name"=>"公告列表",
						"href"=>"admincp.php?mod=notice&act=list"),
			array("name"=>"新增公告",
						"href"=>"admincp.php?mod=notice&act=add")
			
		);
		$menu["user_data"]["sub_menu"] = array(
			array("name"=>"用户数据管理",
						"href"=>"admincp.php?mod=user&act=databasemgr"),
			array("name"=>"删除用户",
						"href"=>"admincp.php?mod=user&act=deleteaccount"),
			array("name"=>"查看已删用户",
					"href"=>"admincp.php?mod=user&act=getdeleteaccount"),
			array("name"=>"viewland",
					"href"=>"admincp.php?mod=user&act=viewland"),
			array("name"=>"用户奖励",
					"href"=>"admincp.php?mod=user&act=rewardSomething"),
		);
	}elseif ($limitvalue==3) {
		$menu = array(
		"user_data" => array("name" => "用户数据管理模块"),
		"user_log" => array("name"=>"用户日志管理模块"),
		);
		$menu["user_data"]["sub_menu"] = array(
			array("name"=>"获取用户gameuid",
						"href"=>"admincp.php?mod=user&act=getgameuid"),
		);
		$menu["user_log"]["sub_menu"] = array(
			array("name"=>"查看交易日志",
						"href"=>"admincp.php?mod=user&act=tradelog"),
		);
	}

?>