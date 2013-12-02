<?php
if($_POST){
	//添加活动
	$addarr = array();
	$addarr['title'] = MooGetGPC("title","string","P");
	$addarr['description'] = MooGetGPC("description","string","P");
	$addarr['start_time'] = MooGetGPC("start_time","string","P");
	$addarr['stop_time'] = MooGetGPC("stop_time","string","P");
	$addarr['is_public'] = MooGetGPC("is_public","integer","P");
	$addarr['exp'] = MooGetGPC("exp","integer","P");
	$addarr['coin'] = MooGetGPC("coin","integer","R");
	$addarr['money'] = MooGetGPC("money","integer","P");
	$addarr['items'] = MooGetGPC("items","string","P");
	$addarr['member'] = MooGetGPC("member","integer","P");
	$addarr['success_message'] = MooGetGPC("success_message","string","P");
	
	require_once API."model/cache/ActivityCache.class.php";
	$activity = new ActiovityCache();
	$activity->insert($addarr);
	print "<script>alert('添加成功');location.href='admincp.php?mod=activity&act=list';</script>";
}
?>