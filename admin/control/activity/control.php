<?php
$m = MooGetGPC("m","string","R");
$activity_id = MooGetGPC("activity_id","integer","R");

require_once API."model/cache/ActivityCache.class.php";
$activity = new ActiovityCache();
	
switch ($m){
	case "del":
		delete($activity_id,$activity);
		break;
	case "updatecache":
		$result = updatecache($activity_id,$activity);
	default:
		break;
}
//删除活动
function delete($activity_id,&$activity){
	$activity->delete($activity_id);
}
//更新单个item缓存
function updatecache($activity_id,&$activity){
	$activity->updateCacheItem($activity_id);
	return $activity->getItemFromCache($activity_id);
}
?>