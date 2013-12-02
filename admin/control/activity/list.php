<?php
require_once API."model/cache/ActivityCache.class.php";
$activity = new ActiovityCache();
$result = $activity->get();
?>