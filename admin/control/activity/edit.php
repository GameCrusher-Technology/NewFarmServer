<?php
$activity_id = MooGetGPC("activity_id","integer","R");
require_once API."model/cache/ActivityCache.class.php";
$activity = new ActiovityCache();
$msg = "";
if($_POST){
	$setarr = array();
	$setarr['activity_id'] = $activity_id;
	$setarr['title'] = MooGetGPC("title","string","R");
	$setarr['description'] = MooGetGPC("description","string","R");
	$setarr['start_time'] = MooGetGPC("start_time","integer","R");
	$setarr['stop_time'] = MooGetGPC("stop_time","integer","R");
	$setarr['is_public'] = MooGetGPC("is_public","integer","R");
	$setarr['exp'] = MooGetGPC("exp","integer","R");
	$setarr['coin'] = MooGetGPC("coin","integer","R");
	$setarr['money'] = MooGetGPC("money","integer","R");
	$setarr['items'] = MooGetGPC("items","string","R");
	$setarr['member'] = MooGetGPC("member","integer","R");
	$setarr['success_message'] = MooGetGPC("success_message","string","R");
	
	$activity->edit($setarr);
	$msg = "<script>alert('修改完成');</script>";
}


$result = $activity->getItem($activity_id);

$activity_items = $activity->resolveItems($result['items']);
?>