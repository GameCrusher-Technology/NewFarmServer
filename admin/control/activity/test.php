<?php
if($_POST){
	$activity_id = MooGetGPC("activity_id","string","P");
	$uid = MooGetGPC("uid","integer","P");
	if(isset($activity_id) && isset($uid)){
		require_once API."model/cache/ActivityCache.class.php";
		$activity = new ActiovityCache();
		
		$result = $activity->getAward($uid,$activity_id);

		print $result;	

	}else{
		print "activity_id or uid not set!";
	}
}
?>