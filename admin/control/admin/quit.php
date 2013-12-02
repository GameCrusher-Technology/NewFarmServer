<?php
if(!defined('IN_GAME')) exit('Access Denied');
global $login_user, $admin_account_mgr, $admin_logger;
$admin_logger->writeInfo('user %s logged out.',$login_user->username);
$admin_account_mgr->logout();
header("Location:admincp.php?mod=admin&act=login");
?>