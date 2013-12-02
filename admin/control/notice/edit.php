<?php
if(!defined('IN_GAME')) exit('Access Denied');

require_once GAMELIB.'/extends/SystemNotice.class.php';

$SystemNotice = new SystemNotice();

$id = getGPC("id");


$notice = $SystemNotice->getNotice();

$result = array();
foreach($notice as $tmp){
	if($tmp['id'] == $id){
		$result = $tmp;
		break;
	}
}

?>