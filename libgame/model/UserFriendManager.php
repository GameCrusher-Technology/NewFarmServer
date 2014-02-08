<?php
/**
 * 该类主要是处理好友逻辑
 */
class UserFriendManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	//获取好友
	public function getFriends($gameuid)
	{
		if (empty($gameuid) || intval($gameuid) <= 0) return false;
    	
    	//account表中数据
		$friends = $this->getFromDb($gameuid,array('gameuid'=>$gameuid));
		if (empty($friends))	return array();
		return $friends;
	}
	
	//更新好友列表
	public function updateFriends($change,$gameuid){
		$this->updateDB($gameuid,$change,array('gameuid'=>$gameuid));
	}
	public function creatFriends($change){
		$this->insertDB($change,TCRequest::CACHE_PRIMARY_KEY);
	}
	
	//获取 好友简单数据
	public function getfriendinfo($friend_gameuid)
	{
		$key = sprintf(CacheKey::CACHE_KEY_ACCOUNT_LIMIT,$friend_gameuid);
		$cacheValue = $this->getFromCache($key);
		if(empty($cacheValue)){
			require_once GAMELIB.'/model/UserAccountManager.class.php';
			$user_mgr = new UserAccountManager();
			$friend_account = $user_mgr->getUserAccount($friend_gameuid);
			if(empty($friend_account)){
				return NULL;
			}else{
				$friendInfo = $this->mergeFriendInfo($friend_account);
				$this->setToCache($key ,$friendInfo,null,172800);
				return $friendInfo;
			}
		}else{
			return $cacheValue;
		}
	
	}
	
	public function getHelpFriendTag($gameuid,$f_gameuid)
	{
		$memKey = $this->getHelpFriendCacheKey($gameuid,$f_gameuid);
		$helpTime = $this->getFromCache($memKey);
		if(empty($helpTime))
		{
			$helpTime = 0;
		}
		return $helpTime;
		
	}
	public function setHelpFriendTag($gameuid,$f_gameuid)
	{
		$memKey = $this->getHelpFriendCacheKey($gameuid,$f_gameuid);
		$this->setToCache($memKey,time(),$gameuid,86400);
	}
	private function mergeFriendInfo($friendAccount)
	{
		$friendInfo = array("gameuid"=>$friendAccount['gameuid'],
							"exp"=>$friendAccount['exp'],
							"sex"=>$friendAccount['sex'],
							"name"=>$friendAccount['name'],
							"title"=>$friendAccount['title'],
							"achieve"=>$friendAccount['achieve']
								);
								return  $friendInfo;
	}
	
	private function getHelpFriendCacheKey($gameuid,$f_gameuid){
		return "farm_help_".$gameuid.'_'.$f_gameuid;
	}
	protected function getTableName(){
		return "user_friends";
	}
}
?>