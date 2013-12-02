<?php

if(!defined('IN_GAME')) exit('Access Denied');

require_once GAMELIB.'/extends/SystemNotice.class.php';

$op = trim($_REQUEST['op']);

$SystemNotice = new SystemNotice();

try{
	$id = getGPC("id");
	$name = getGPC("name","string");
	$start_time = strtotime(getGPC("start_time","string"));
	$end_time = strtotime(getGPC("end_time","string"));
	$reward = getGPC("reward","string");
	switch ($op){
		case "add":
			$SystemNotice->addNotice($name,$start_time,$end_time,$reward);
			$op_msg = "新增成功";
			break;
			
		case "edit":	
			$SystemNotice->updateNotice($id,$name,$start_time,$end_time,$reward);
			$op_msg = "编辑成功";
			break;
		
		case "delete":
			$SystemNotice->deleteNotice($id);
			$op_msg = "删除成功";
			break;
		default:
			break;
	}
	
}catch (Exception $e){
	$err_msg = "error:".$e->getMessage();
}

$result = $SystemNotice->getNotice();
?>