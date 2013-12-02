<?php
require_once ADMIN_ROOT . '/modules/AdminModel.class.php';
require_once API . '/model/Users.class.php';
$admin = new AdminModel();
$cache = $admin->getCacheManager();
$op_msg = '';
require_once ADMIN_ROOT . '/include/package.php';
try{
	$itemlist = getItemList();
	$checked = false;
	if(!empty($_REQUEST['package_id'])){
		$package_id = getGPC('package_id');
	}
	$package = $_POST['package'];
	$itemPackage = new ItemPackage();
	if(isset($_POST['getPackages'])){
		if(empty($package_id)){
			$packages = $itemPackage->getPackageList(null,true);
		}
	}
	if(isset($_POST['getPackageItems'])){
		$package_item_list = $itemPackage->getPackageItems($package_id,true);
	}
	if(isset($_POST['addPackage'])){
		$package_id = addPackage();
	}
	if(isset($_POST['addPackageItem'])){
		addPackageItem();
		
	}
	if(isset($_POST['editPackage'])){
		if(empty($package_id)){
			$error_msg = '礼包id不能为空。';
		}
		else{
			updatePackage();
		}
	}
	if(!empty($package_id)){
		$package = $itemPackage->getPackageById($package_id,true);
		$package_item_desc = $itemPackage->getItemsDescription($package['package_items']);
	}
}catch (Exception $e){
	$error_msg = $e->__toString();
}
?>