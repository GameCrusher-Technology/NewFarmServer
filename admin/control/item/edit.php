<?php
require_once MODEL . '/Items.class.php';

$items = new Items();

if(isset($_POST['getItem'])){
$item_id = trim($_POST['item_id']);
	$editItem = $items->selectItem($item_id);
}elseif(isset($_POST['editItem']) && isset($_POST['modifyItem'])){
	if($limitvalue){
		//print_r($_POST['modifyItem']);
		$item_id = trim($_POST['item_id']);
		$items->updateItem($item_id,$_POST['modifyItem']);
		$op_msg = '更新成功。';
	}else{
		$op_msg = '没有权限。';
	}
}
$itemlist = $items->getItemList();