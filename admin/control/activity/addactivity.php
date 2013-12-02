<?php
if($_POST){
	$activityArr = array();
	$activityArr['name'] = getGPC("name", "string",'P');
	$activityArr['count'] = getGPC("count", "string",'P');
	//$activityArr['start_time'] = getGPC("start_time", "string",'P');
	//$activityArr['stop_time'] = getGPC("stop_time", "string",'P');	
	$action_get = getGPC("get","string",'P');
	$action_insert = getGPC("insert","string",'P');
	global $admin_logger;
	if (empty($activityArr['name'])){
		$error_msg="name is empty";
		return ;
	}
	if (empty($action_get)&&empty($action_insert)){
		$error_msg="action is empty";
		return ;
	}
	try {
		include_once GAMELIB.'/model/ActivityManager.class.php';
		$activity_mgr = new ActivityManager();
		if (!empty($action_insert)){
			if (empty($activityArr['count'])){
				$error_msg="count is empty";
				return ;
			}
			$activity_def = $activity_mgr->getActivityDefByName($activityArr['name']);
			if (!empty($activity_def)){
				$error_msg = "activity has exist";
				return ;
			}
			$activity_mgr->creatActivity($activityArr['name'],$activityArr['count']);
			$op_msg = "insert success!";
		}
		$activity_def = $activity_mgr->getActivityDefByName($activityArr['name']);
		$heads = array_keys($activity_def);
		return ;
	}catch (Exception $e){
		$admin_logger->writeError("exception while get definiton.".$e->getMessage());
		$error_msg = $e->getMessage ();
	}
}
?>