<?php
require_once ADMIN_ROOT . '/include/package.php';
$itemPackage = new ItemPackage();
$packages = $itemPackage->getPackageList(null,true);
if(isset($_REQUEST[''])){
	try{
		if(deletePackage() == true){
			$op_msg = "删除成功。";
		}
	}
	catch (Exception $e){
		$error_msg = $e->getMessage();
	}
}
?>