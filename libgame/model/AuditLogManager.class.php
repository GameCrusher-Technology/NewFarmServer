<?php
require_once GAMELIB.'/model/ManagerBase.class.php';
class AuditLogManager extends ManagerBase {
	const ACTION_LOGIN = 1;
	const ACTION_CHANGE_STATUS = 2;
	const ACTION_ADD_ITEM = 3;
	const ACTION_DELETE_ACCOUNT = 4;
	const ACTION_ADD_ADMIN = 5;
	const ACTION_DELETE_ADMIN = 6;
	const ACTION_CHANGE_PASSWORD = 7;
	const ACTION_DELETE_LAND = 8;
	const ACTION_DELETE_ANIMAL = 9;
	const ACTION_FIX_LAND = 10;
	public function __construct() {
		$this->logger = $GLOBALS['admin_logger'];
	}
	public function write(AuthenticationResponse $login_user,$target_uid,$action,$detail,$changes = null){
		try{
			$req = RequestFactory::createInsertRequest(get_app_config());
			$req->setTable($this->getTableName());
			$columns = "uid,username,target_uid,ip,action_type,action_detail,create_time";
			$values = array(
				$login_user->uid, $login_user->username, $target_uid,
				get_ip(), $action, $detail, time());
			if (!empty($changes)) {
				foreach($changes as $key => $v){
					$columns .= ",$key";
					$values[] = $v;
				}
			}
			$req->setColumns($columns);
			$req->addValues($values);
			$req->execute();
		}catch(Exception $e){
			$error_msg = 'write audit log fail. Error:' . $e->getMessage();
			$this->logger->writeError($error_msg);
		}
	}
	protected function getTableName() {
		return "admin_audit_log";
	}
}

?>