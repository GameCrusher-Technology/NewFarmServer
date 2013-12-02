<?php
require_once GAMELIB.'/model/ManagerBase.class.php';
require_once FRAMEWORK . '/auth/Authentication.class.php';
require_once FRAMEWORK . '/auth/AuthProviderFactory.class.php';
define("LOGIN_FAIL_COUNT_CACHE_KEY", "game_admin_login_fail_%s");
class AdminAccountManager extends ManagerBase {
	protected $session;
	protected $crypt_func = 'sha1';
	public function __construct($session){
		$this->logger = $GLOBALS['admin_logger'];
		$this->session = $session;
	}
	
	/**
	 * 转义一个字符串
	 * @param string $str
	 * @return string
	 */
	private function quote($str){
		if(!get_magic_quotes_gpc()){
			return addslashes($str);
		}
		return $str;
	}
	/**
	 * 获得新的额外加密信息
	 * @param string $add 额外的附加信息
	 * @return string 长度为6的字符串
	 */
	private function getNewSalt($add,$len = 6){
		return substr(md5(time() . $add),10,$len);
	}
	
	public function createNew($credit){
		$username = $credit['username'];
		$adminuid = md5(time() . $username);
		$crypt_func = $this->crypt_func;
		$salt = $this->getNewSalt($username);
		$pass = $crypt_func($crypt_func($credit['password']) . $salt);
		$now = time();
		$expire_time = 0;
		if(isset($credit['expire_time'])){
			$expire_time = $credit['expire_time'];
		}
		$group_id = $credit['group_id'];
		$email = $credit['email'];
		
		$req = RequestFactory::createInsertRequest(get_app_config());
		$req->setTable($this->getTableName());
		$req->setColumns("adminuid,username,password,email,salt,create_time,expire_time,group_id");
		$req->addValues(array($adminuid,$username,$pass,$email,$salt,$now,$expire_time,$group_id));
		$req->execute();
		return $adminuid;
	}
	
	public function updateUser($credit){
		$adminuid = $credit['adminuid'];
		if(empty($adminuid)){
			return false;
		}
		$email = $credit['email'];
		$group_id = $credit['group_id'];
		
		$req=RequestFactory::createUpdateRequest(get_app_config());
		$req->setTable($this->getTableName());
		$req->setModify(array("email"=>$email,"group_id"=>$group_id));
		$req->addKeyValue('adminuid', $adminuid);
		$req->execute();
		return true;
	}
	
	public function updatePassword($credit){
		$username = $credit['username'];
		$password = $credit['password'];
		
		$req = RequestFactory::createGetRequest(get_app_config());
		$req->setTable($this->getTableName());
		$req->addKeyValue("username", $username);
		$user = $req->fetchOne();
		if(empty($user)){
			$this->throwException('该用户不存在',STATUS_USER_NOT_EXISTS);
		}
		$crypt_func = $this->crypt_func;
		$pass = $crypt_func($crypt_func($credit['oldpass']) . $user['salt']);
		if($pass != $user['password']){
			$this->throwException('密码错误。', WRONG_PASSWORD);
		}
		$uid = $user['adminuid'];
		$salt = $this->getNewSalt($username);
		$new_pass = $crypt_func($crypt_func($password) . $salt);
		
		$req=RequestFactory::createUpdateRequest(get_app_config());
		$req->setTable($this->getTableName());
		$req->setModify(array("password"=>$new_pass,"salt"=>$salt));
		$req->addKeyValue('adminuid', $uid);
		$req->execute();
	}
	
	public function getUserByUid($uid){
		$uid = $uid;
		$req = RequestFactory::createGetRequest(get_app_config());
		$req->setTable($this->getTableName());
		$req->addKeyValue("adminuid", $uid);
		$user = $req->fetchOne();
		if(empty($user)){
			$this->throwException('该用户不存在。', STATUS_USER_NOT_EXISTS);
		}
		return $user;
	}
	
	public function getUserByName($username){
		$username = $username;
		$req = RequestFactory::createGetRequest(get_app_config());
		$req->setTable($this->getTableName());
		$req->addKeyValue("username", $username);
		$user = $req->fetchOne();
		if(empty($user)){
			$this->throwException('该用户不存在。', STATUS_USER_NOT_EXISTS);
		}
		return $user;
	}
	
	public function getUserList() {
		$req = RequestFactory::createGetRequest(get_app_config());
		$req->setTable($this->getTableName());
		$users = $req->execute();
		return $users;
	}
	
	public function deleteUserList($adminuids) {
		$req = RequestFactory::createDeleteRequest(get_app_config());
		$req->setTable($this->getTableName());
		$req->addKeyValue("adminuid", $adminuids);
		$req->execute();
	}
	
	public function resetPassword($credit){
		$uid = $credit['uid'];
		$password = $credit['password'];
		$user = $this->getUserByUid($uid);
		if(empty($user)){
			$this->throwException('该用户不存在。',STATUS_USER_NOT_EXISTS);
		}
		$crypt_func = $this->crypt_func;
		
		$salt = $this->getNewSalt($user['username']);
		$new_pass = $crypt_func($crypt_func($password) . $salt);
		
		$req=RequestFactory::createUpdateRequest(get_app_config());
		$req->setTable($this->getTableName());
		$req->setModify(array("password"=>$new_pass,"salt"=>$salt));
		$req->addKeyValue('adminuid', $uid);
		$req->execute();;
	}
	/**
	 * 进行用户登录，如果失败返回false，否则返回用户登陆信息
	 * @return AuthenticationResponse
	 */
	public function login(){
		$resp = $this->isLogin();
		if(!empty($resp)){
			return $resp;
		}
		$credit = array();
		$credit['password'] = trim(getGPC('password','string'));
		$username = trim(getGPC('username','string'));
		// 用户名和密码不能为空
		if(empty($username) || empty($credit['password'])){
			$resp = new AuthenticationResponse();
			$resp->status = ELEX_AUTHENTICATE_STATUS_FAILURE;
			$resp->error_message = 'empty username or password';
			return $resp;
		}
//		// 验证登陆失败次数
//		$fail_times = $this->getFailTimes($username);
//		if($fail_times == 5){
//			$resp = new AuthenticationResponse();
//			$resp->status = ELEX_AUTHENTICATE_STATUS_FAILURE;
//			$resp->fail_times = $fail_times;
//			$this->logger->writeError("user $username login fail too many times.");
//			return $resp;
//		}

		$credit['username'] = $this->quote($username);
		$this->logger->writeInfo("user %s try to login.",$username);
		$options = array('dbo' => $this->getDBHelperInstance());
		$provider = AuthProviderFactory::createProvider('mysql',$options);
		$auth = new Authentication();
		$auth->setProvider($provider);
		try{
			$resp = $auth->authenticate($credit);
			if($resp->status == ELEX_AUTHENTICATE_STATUS_SUCCESS){
				$this->session->set('login_user',$resp);
				$this->clearLoginFail($username);
				$this->logger->writeInfo("user $username login success.");
				return $resp;
			} else {
				$count = $this->increaseLoginFail($username);
				if($count !== false){
					$resp->fail_times = $count;
				}
				$this->logger->writeError("user $username login fail $count time(s). Error:%s",$resp->error_message);
				return $resp;
			}
		} catch (Exception $e){
			$this->logger->writeError($e->getTraceAsString());
		}
		return false;
	}
	
	protected function increaseLoginFail($username){
		$key = sprintf(LOGIN_FAIL_COUNT_CACHE_KEY, $username);
		$count = $this->getFromCache($key);
		if($count === false){
			$this->setToCache($key,1);
			return 1;
		} elseif($count < 5){
			++$count;
			$this->setToCache($key,$count);
			return $count;
		}
		return false;
	}
	
	protected function getFailTimes($username){
		$key = sprintf(LOGIN_FAIL_COUNT_CACHE_KEY, $username);
		return $this->getFromCache($key);
	}
	
	protected function clearLoginFail($username){
		$key = sprintf(LOGIN_FAIL_COUNT_CACHE_KEY, $username);
		$this->deleteFromCache($key);
	}
	
	/**
	 * 判断用户是否已经登陆，如果已经登陆，则返回登录信息
	 * @return AuthenticationResponse
	 */
	public function isLogin(){
		$user = $this->session->get('login_user');
		if(empty($user)){
			return false;
		}
		return $user;
	}
	/**
	 * 取得登录的管理员用户
	 * @return AuthenticationResponse
	 */
	public function getLoginUser(){
		return $this->session->get('login_user');
	}
	
	public function logout(){
		$this->session->clear('login_user');
	}
	
	protected function getTableName(){
    	return  "admin_users";
    } 
}
?>
