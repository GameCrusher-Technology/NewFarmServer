<?php
require_once GAMELIB.'/model/ManagerBase.class.php';
class AccountLogManager extends ManagerBase {
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
	
	public function write_account_log(AuthenticationResponse $login_user,$target_uid,$content){
		
		try{
			$req = RequestFactory::createInsertRequest(get_app_config());
			$req->setTable($this->getTableName());
			$columns = "uid,user,target_uid,content,create_time";
			$values = array(
				$login_user->uid, $login_user->username, $target_uid,
				$content , time());
			
			$req->setColumns($columns);
			$req->addValues($values);
			$req->execute();
	
		}catch(Exception $e){
			$error_msg = 'write user_account log fail. Error:' . $e->getMessage();
			$this->logger->writeError($error_msg);
		}
	}
	protected function getTableName() {
		return "admin_change_user_account_log";
	}
	
	public function delete($gameuid){
		 //$dbhelper=$this->getDBHelperInstance();
		// $sql="delete from admin_change_user_account_log  where target_uid=$gameuid";
		 $this->deleteFromDb($gameuid,array('target_uid'=>$gameuid));
	}
	public function getchangeList(){
		$dbhelper=$this->getDBHelperInstance();
		$sql="select * from admin_change_user_account_log";
		$result=$dbhelper->getAll($sql);
		return $result;
	}
}








?>
