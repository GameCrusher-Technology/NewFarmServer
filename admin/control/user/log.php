<?php
if(!defined('IN_GAME')) exit('Access Denied');
include_once ADMIN_ROOT . 'common.func.php';
require_once GAMELIB . '/model/UserEventLogManager.class.php';
require_once GAMELIB . '/model/UserAccountManager.class.php';
require_once GAMELIB . '/model/AuditLogManager.class.php';
global $limitvalue, $audit_logger, $login_user, $admin_logger;

ini_set('mbstring.internal_encoding','UTF-8');

$selected_week = getGPC('action_log_week','string');
$current_week = date('W');
if (empty($selected_week)){
	$selected_week = $current_week;
}
$week_list = range(1,$current_week);
$all_action = array('19,20' => '买卖',
'20' => '卖出',
'19' => '买入',
'11' => '收获',
'12' => '扩地',
'21' => '充值',
'25' => '放置动物',
'26' => '喂动物',
'41' => '删除土地',
'43' => '升级技能',
'96' => '获取复活节彩蛋奖励',
);
try {
	$uid = $_POST ['uid'];
	if (!empty($uid)) {
		$gameuid = get_gameuid_from_uid(trim($_POST['uid']));
	} else {
		$gameuid = $_POST['gameuid'];
	}
	$user_account_mgr = new UserAccountManager($gameuid);
	$userAccount = $user_account_mgr->getUserAccount($gameuid);
	$user_level = $user_account_mgr->getUserLevel($userAccount['experience']);
	$offset = getGPC('offset');
	$limit = getGPC ('limit');
	if($limit < 1){
		$limit = 100;
	}
	if (! empty ( $_POST ['exp'] ) ||
	! empty ( $_POST ['coin'] ) ||
	! empty ( $_POST ['money'] )||! empty ( $_POST ['charm'] )) {
		$change = array();
		$change_msg = '';
		if(!empty($_POST['coin'])){
			$change['coin'] = intval($_POST['coin']);
			$change_msg .= ' coin:' . $change['coin'];
		}
		if(!empty($_POST['exp'])){
			$change['experience'] = intval($_POST['exp']);
			$change_msg .= ' exp:' . $change['experience'];
		}
		if(!empty($_POST['money'])){
			$change['money'] = intval($_POST['money']);
			$change_msg .= ' money:' . $change['money'];
		}
		if(!empty($_POST['charm'])){
			$change['charm'] = intval($_POST['charm']);
			$change_msg .= ' charm:' . $change['charm'];
		}
		if($limitvalue){
			// add file log
			$msg = sprintf('user %s(uid=%s) change player(uid=%s,gameuid=%d) for %s',
			$login_user->username,$login_user->uid,$uid,$gameuid,$change_msg);
			$GLOBALS['admin_logger']->writeInfo($msg);
			$user_account_mgr->updateUserStatus($gameuid,$change,true);
			// add audit log to database
			$audit_logger->write($login_user,$uid,AuditLogManager::ACTION_CHANGE_STATUS,$msg,$change);
		}
		else{
			$error_msg = '没有执行权限。';
		}
	}
	if(isset($_POST['getUserLog'])){
			$w = $selected_week;
			if(empty($w)){
				$w = $current_week;
			}
			$log_action_type = getGPC('log_action_type','string');
			$action_cond = '';
			if(!empty($log_action_type)) {
				$action_cond = " and action_type IN($log_action_type) ";
			}
			if(!empty($gameuid)){
				$action_logs = _get_actionlogs($gameuid,$action_cond,$w,$offset,$limit);
//				$event_logs = _get_eventlogs($gameuid,$offset,$limit);
			}
			else{
				$error_msg = '用户gameuid不能为空。';
			}
		}
	} catch ( Exception $e ) {
		$admin_logger->writeError($e->getTraceAsString());
		$error_msg = $e->getMessage ();
	}
?>