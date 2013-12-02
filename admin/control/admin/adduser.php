<?php
global $limitgroup, $audit_logger, $login_user, $admin_logger;
foreach($limitgroup as $gkey=>$limit){
	$groups[$gkey] = $limit;
}
$user['group_id'] = 11111;
$adminuid = getGPC('adminuid','string');
if(!empty($adminuid)){
	$user = $admin_account_mgr->getUserByUid($adminuid);
}
if(isset($_POST['addadmin'])){
	$admin_info = array();
	
	$admin_info['email'] = getGPC('email','string','P');
	$admin_info['group_id'] = getGPC('group_id','string','P');
	
	try{
		if(!empty($adminuid)){
			$admin_info['adminuid'] = $adminuid;
			if($admin_account_mgr->updateUser($admin_info)){
				$user['group_id'] = $admin_info['group_id'];
				$op_msg = "编辑成功.";
			}
		}
		else{
			$admin_info['username'] = getGPC('username','string','P');
			$admin_info['password'] = getGPC('password','string','P');
			$new_uid = $admin_account_mgr->createNew($admin_info);
			$audit_logger->write($login_user,$new_uid,AuditLogManager::ACTION_ADD_ADMIN,'add admin ' . $admin_info['username']);
			$op_msg = '添加管理员成功。';
		}
	} catch (Exception $e){
		$admin_logger->writeError("exception while save admin to db.".$e->getMessage());
	}
}
?>