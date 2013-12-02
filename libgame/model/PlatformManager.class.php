<?php
abstract class PlatformManager extends ManagerBase {
	protected $sig_api_key = null;
	protected $sig_secret = null;
	protected $sig_user = 0;
	protected $platform_handler = null;
	public function __construct() {
		parent::__construct();
		$platform_config = get_app_config()->getSection("platform");
		$this->sig_api_key = $platform_config['sig_api_key'];
		$this->sig_secret = $platform_config['sig_secret'];
		$this->logger = $GLOBALS['platform_logger'];
	}
	/**
	 * 根据模块名返回相应的平台操作对象.
	 * 该对象负责平台用户信心和好友的获取
	 *
	 * @param string $mod 平台的名称
	 * @param array $platform_params 和当前登录用户相关的会话信息
	 * @return PlatformManager 平台的操作对象
	 */
	public static function getPlatformHandler($mod, $platform_params = null) {
		if (empty($mod)) return null;
		$logger = $GLOBALS['platform_logger'];
		if ($logger->isDebugEnabled()) {
			$logger->writeDebug("tring to get platform handler:$mod, params=".print_r($platform_params,true));
		}
		if ($mod != 'admin_viewland') {
			 require_once FRAMEWORK."/platform/plugins/$mod.php";
		}
		switch ($mod) {
			case "facebook":
				return new FacebookHandler($platform_params);
			case "thinksns":
				return new ThinkSnsHandler($platform_params);
			case "admin_viewland":
				return new EmptyHandler($platform_params);
			default:
				return null;
		}
	}
	public function getApiKey() {
		return $this->sig_api_key;
	}
	public function preProcess() {}
	public function getPlatformParams() {return array();}
	public function validateUser() {
		$logged_in_user = $this->getLoggedInUser();
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug("tring to validate login user[user_from_session=$logged_in_user,user_from_params=".$this->sig_user."]");
		}
		if ($this->sig_user == $logged_in_user) return true;
		return false;
	}
	public function getUserInfo($uids) {
		if (is_string($uids)) $uids = explode(",",$uids);
		if (empty($uids)) return array();
		
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug("tring to get user info from platform[uids=".implode(',',$uids)."]");
		}
		
		$user_info_keys = array();
		foreach ($uids as $uid) {
			$user_info_keys[] = sprintf(CacheKey::CACHE_KEY_PLATFORM_USER_INFO, $uid);
		}
		$user_infos = $this->getFromCache($user_info_keys);
		$valid_uids = array();
		$ret = array();
		foreach ($user_infos as $key=>$user_info) {
			$uid = intval(substr($key, strrpos($key, "_")+1));
			$valid_uids[] = $uid;
			$ret[$uid] = $user_info;
		}
		$expires = array_diff($uids, $valid_uids);
		if (count($expires) > 0) {
			$start = microtime(true);
			$remote_infos = $this->getUserInfoFromPlatform(implode(",",$expires));
			if ($this->logger->isDebugEnabled()) {
				$this->logger->writeDebug("get user info from platform takes ".(microtime(true) - $start)."sec to finish.");
			}
			foreach ($remote_infos as $remote_info) {
				$this->setToCache(sprintf(CacheKey::CACHE_KEY_PLATFORM_USER_INFO, $remote_info['uid']), $remote_info, null, GameConstCode::PLATFORM_INFO_EXPIRE_TIME);
				$ret[$remote_info['uid']] = $remote_info;
			}
		}
		return $ret;
	}
	public function getUserFriends($uid) {
		if (empty($uid)) return "";
		$user_friends = $this->getFromCache(sprintf(CacheKey::CACHE_KEY_PLATFORM_USER_FRIENDS,$uid));
		if ($user_friends === false) {
			$start = microtime(true);
			$user_friends = $this->getUserFriendsFromPlatform($uid);
			if ($this->logger->isDebugEnabled()) {
				$this->logger->writeDebug("get user friends from platform takes ".(microtime(true) - $start)."sec to finish.");
			}
			if (empty($user_friends)) {
				if ($this->logger->isDebugEnabled()) {
					$this->logger->writeDebug("can not retrieve user friends[uid=$uid] from platform, check it.");
				}
				return "";
			}
			$this->setToCache(sprintf(CacheKey::CACHE_KEY_PLATFORM_USER_FRIENDS, $uid), $user_friends, null, GameConstCode::PLATFORM_INFO_EXPIRE_TIME);
		}
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug("retrieved user friends[uid=$uid,friends=$user_friends]");
		}
		return $user_friends;
	}
	public function refreshUserFriends($uid) {
		if (empty($uid)) return "";
		$start = microtime(true);
		$user_friends = $this->getUserFriendsFromPlatform($uid);
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug("get user friends from platform takes ".(microtime(true) - $start)."sec to finish.");
		}
		$this->setToCache(sprintf(CacheKey::CACHE_KEY_PLATFORM_USER_FRIENDS, $uid), $user_friends, null, GameConstCode::PLATFORM_INFO_EXPIRE_TIME);
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug("refreshed user friends[uid=$uid,friends=$user_friends]");
		}
		return $user_friends;
	}
	/**
	 * 发送feed，feed和notification发送需要控制，一天只能发送一次feed或者是notification
	 *
	 * @param 触发操作的用户的gameuid $gameuid
	 * @param 模版的id $template_id
	 * @param 模版标题中的变量数据，为json格式 $title_data
	 * @param 模版内容中的变量数据，为json格式 $body_data
	 * @return 发送成功返回true，否则返回false
	 */
	public function sendFeed($gameuid, $template_id, $title_data, $body_data) {
		require_once GAMELIB.'/model/XmlManager.class.php';
		$action_def_mgr = new ActionDefManager();
		if (!$action_def_mgr->canActionHappen($gameuid, ActionCode::ACTION_SEND_FEED)) {
			if ($this->logger->isDebugEnabled()) {
				$this->logger->writeDebug("the user[gameuid=$gameuid] has already sended feed today. will not send feed[template_id=$template_id]");
			}
			return false;
		}
		$start = microtime(true);
		$this->sendFeedThroughPlatform($template_id, $title_data, $body_data);
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug("send feed takes ".(microtime(true) - $start)."sec to finish.");
		}
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug(
				"sended feed to platform[gameuid=$gameuid,template_id=$template_id,title_data=$title_data,body_data=$body_data]");
		}
		return true;
	}
	/**
	 * 发送notification，feed和notification发送需要控制，一天只能发送一次feed或者是notification
	 *
	 * @param 触发操作的用户的gameuid $gameuid
	 * @param 发送通知的目的uids，为逗号分隔的字符串 $to_uids
	 * @param 通知的内容 $content
	 * @return 发送成功返回true，否则返回false
	 */
	public function sendNotification($gameuid, $to_uids, $content) {
		require_once GAMELIB.'/model/XmlManager.class.php';
		$action_def_mgr = new ActionDefManager();
		if (!$action_def_mgr->canActionHappen($gameuid, ActionCode::ACTION_SEND_NOTIFICATION)) {
			if ($this->logger->isDebugEnabled()) {
				$this->logger->writeDebug("the user[gameuid=$gameuid] has already sended notification today. will not send notification[content=$content]");
			}
			return false;
		}
		$start = microtime(true);
		$this->sendNotificationThroughPlatform($to_uids, $content);
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug("send notification takes ".(microtime(true) - $start)."sec to finish.");
		}
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug(
				"sended notification to platform[gameuid=$gameuid,to_uids=$to_uids,content=$content]");
		}
		return true;
	}
	protected function getTableName() {
		return "platform_cache";
	}
	abstract public function getLoggedInUser();
	abstract protected function getUserInfoFromPlatform($uids);
	abstract protected function getUserFriendsFromPlatform($uid);
	abstract protected function sendFeedThroughPlatform($template_id, $title_data, $body_data);
	abstract protected function sendNotificationThroughPlatform($to_uids, $content);
}
/**
 * 这个类是private的，不要直接使用，
 * 要通过PlatformManger::getPlatformHandler('facebook', $platform_params)来获取操作句柄
 */
class FacebookHandler extends PlatformManager {
	private $fb_params = null;
	public function __construct($platform_params = null) {
		parent::__construct();
		if (!isset($_POST['fb_sig_session_key']) && !isset($_GET['fb_sig_session_key'])) {
			foreach ($platform_params as $name=>$value) {
				$_POST[$name]=$value;
			}
		}
		$this->platform_handler = new Facebook($this->sig_api_key, $this->sig_secret);
		$this->sig_user = $this->platform_handler->user;
	}
	public function preProcess() {
		$this->platform_handler->require_login();
	}
	public function getPlatformParams() {
		if ($this->fb_params === null) {
			$this->fb_params = $this->_get_valid_fb_params($_POST, 'fb_sig');
			if (!$this->fb_params) {
		      $fb_params = $this->_get_valid_fb_params($_GET, 'fb_sig');
		      $fb_post_params = $this->_get_valid_fb_params($_POST, 'fb_post_sig');
		      $this->fb_params = array_merge($fb_params, $fb_post_params);
		    }
		}
		return $this->fb_params;
	}
	private function _get_valid_fb_params($params, $namespace='fb_sig') {
        $fb_params = array();
        if (empty($params)) return array();
        foreach ($params as $name => $val) {
	        if (strpos($name, $namespace) === 0) {
	        	$fb_params[$name] = $val;
	        }
        }
        return $fb_params;
	}
	public function getLoggedInUser() {
		return $this->sig_user;
	}
	protected function getUserInfoFromPlatform($uids) {
		if (empty($uids)) return array();
		$ret = array();
		try {
			$remote_infos = $this->platform_handler->api_client->users_getInfo($uids, "uid,name,profile_url,pic_square");
		} catch (FacebookRestClientException $e) {
			$this->logger->writeError("exception while get user info[$uids] from facebook rest server:".$e->getMessage());
			return array();
		}
		foreach ($remote_infos as $remote_info) {
			$ret[] = array("uid"=>$remote_info['uid'],"name"=>$remote_info['name'],"profile_url"=>$remote_info['profile_url'],"head_pic"=>$remote_info['pic_square']);
		}
		return $ret;
	}
	protected function getUserFriendsFromPlatform($uid) {
		if (empty($uid)) return '';
		try {
			$app_friends = $this->platform_handler->api_client->fql_query(
					"SELECT uid,name,profile_url,pic_square FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=$uid) AND is_app_user");
		} catch (FacebookRestClientException $e) {
			$this->logger->writeError("exception while get user friends[$uid] from facebook rest server:".$e->getMessage());
			return '';
		}
		$user_friends = "";
		if (is_array($app_friends) && count($app_friends) > 0) {
			foreach ($app_friends as $app_friend) {
				$user_friends .= $app_friend['uid'].',';
				$this->setToCache(sprintf(CacheKey::CACHE_KEY_PLATFORM_USER_INFO,$app_friend['uid']), array("name"=>$app_friend['name'],"profile_url"=>$app_friend['profile_url'],"head_pic"=>$app_friend['pic_square']), 
					null, GameConstCode::PLATFORM_INFO_EXPIRE_TIME);
			}
			$user_friends = trim($user_friends, ',');
		}
		return $user_friends;
	}
	protected function sendFeedThroughPlatform($template_id, $title_data, $body_data){}
	protected function sendNotificationThroughPlatform($to_uids, $content){}
}
class ThinkSnsHandler extends PlatformManager {
	public function __construct($platform_params = null) {
		parent::__construct();
		$this->platform_handler = new ThinkSns($this->sig_api_key, $this->sig_secret);
		$this->platform_handler->api_client->set_session_key($platform_params['sig_session_key']);
		$this->sig_user = $platform_params['sig_user'];
	}
	public function getLoggedInUser() {
		$self = $this->platform_handler->api_client->user_getLoggedInUser();
		if (!empty($self['error_code'])) {
			$this->logger->writeError("retrieving logged user failed from think sns:" . $self['error_msg']);
			return false;
		}
		if (empty($self['data'])) {
			$this->logger->writeError("can not retrieve user uid from think sns. it is empty");
			return false;
		}
		return $self['data'];
	}
	protected function getUserInfoFromPlatform($uids) {
		$ret = array();
		$remote_infos = $this->platform_handler->api_client->user_getInfo($uids, "id,name,pic_small");
		$remote_infos = $remote_infos['data'];
		foreach ($remote_infos as $remote_info) {
			$ret[] = array("uid"=>$remote_info['id'],"name"=>$remote_info['name'],"head_pic"=>$remote_info['pic_small']);
		}
		return $ret;
	}
	protected function getUserFriendsFromPlatform($uid) {
		$app_friends = $this->platform_handler->api_client->friends_get();
		$app_friends = $app_friends['data'];
		return implode(',', $app_friends);
	}
	protected function sendFeedThroughPlatform($template_id, $title_data, $body_data){
		$app_name = get_app_config()->getGlobalConfig('app_name');
		$this->platform_handler->api_client->notifications_send($app_name."_feed_$template_id", $title_data, $body_data);
	}
	protected function sendNotificationThroughPlatform($to_uids, $content){
		include_once FRAMEWORK .'/json/JSON.php';
    	$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    	$data = $json->decode($content);
    	$template_id = $data['tpl_id'];
    	if (empty($template_id)) return;
    	$title_data = $data['title_data'];
    	$body_data = $data['body_data'];
    	$app_name = get_app_config()->getGlobalConfig('app_name');
    	$this->platform_handler->api_client->notifications_send($app_name."_notification_$template_id", $title_data, $body_data, $to_uids);
	}
}
class EmptyHandler extends PlatformManager {
	public function __construct($platform_params = null) {
		$this->sig_user = $platform_params['sig_user'];
	}
	public function getLoggedInUser() {
		return $this->sig_user;
	}
	public function validateUser() {
		return true;
	}
	public function getUserInfo($uids) {
		return array(
			$this->sig_user=>array(
				'name'=>'无名',
				'profile_url'=>'',
				'head_pic'=>'http://home.elex-tech.com/public/themes/blue/images/pic2.gif',
			)
		);
	}
	public function getUserFriends($uid) {
		return '';
	}
	public function refreshUserFriends($uid) {
		return '';
	}
	public function sendFeed($gameuid, $template_id, $title_data, $body_data) {
		return true;
	}
	public function sendNotification($gameuid, $to_uids, $content) {
		return true;
	}
	protected function getUserInfoFromPlatform($uids) { }
	protected function getUserFriendsFromPlatform($uid) { }
	protected function sendFeedThroughPlatform($template_id, $title_data, $body_data){ }
	protected function sendNotificationThroughPlatform($to_uids, $content){ }
}
?>