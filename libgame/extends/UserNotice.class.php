<?php
require_once GAMELIB.'/model/ManagerBase.class.php';

/**
 * 系统活动或补偿领取
 *
 */
class UserNotice extends ManagerBase{ 
	
	public function hasDrawReward($gameuid,$notice_id){
		$notice_id = intval($notice_id);
		
		$data = $this->getUserNotice($gameuid);
		
		if(isset($data[$notice_id]) && $data[$notice_id] == 1)
			return true;
			
		return false;
	}
	
	public function drawReward($gameuid, $notice_id, $uid = ""){
		$data = $this->getUserNotice($gameuid);
		
		$data[$notice_id] = 1;
		
		$rewards = array_keys($data);
		$rewards = implode(",",$rewards);
		
		$this->setUserNoticeToDb($gameuid,$rewards,$uid);
		
		$this->setToCache($this->getMemKey($gameuid),$data,null,259200);
	}
	
	
	public function getUserNotice($gameuid){
		$key = $this->getMemKey($gameuid);
		$data = $this->getFromCache($key);
		if($data === false){
			$rewards = $this->getUserNoticeFromDb($gameuid);
			if(empty($rewards) || $rewards == "")
				return array();
				
			$rewards = explode(",",$rewards);
			foreach ($rewards as $id){
				$data[$id] = 1;
			}
			
			$this->setToCache($key,$data,null,2592000);
		}
		
		return $data;
	}
	
	private function getUserNoticeFromDb($gameuid){
		$sql = "select rewards from user_notice where gameuid = $gameuid";
		$result = $this->getDBHelperInstance()->getAll($sql);
		
		if(isset($result[0]))
			return $result[0]['rewards'];
		
		return "";
	}
	
	private function setUserNoticeToDb($gameuid,$rewards,$uid = ""){
		$now = time();
		$sql = "insert into user_notice(gameuid,uid,rewards,update_time) values(%d,'%s','%s',%d) on duplicate key
		  update rewards = values(rewards), update_time = values(update_time)";
		
		if($uid != ""){
			$sql .= ", uid = values(uid)";
		}
		
		$sql = sprintf($sql,$gameuid,addslashes($uid),$rewards,$now);
		
		$this->getDBHelperInstance()->execute($sql);
	}
	
	private function getMemKey($gameuid){
		return "ranch_user_".$gameuid."_notice";
	}
	
	protected function getTableName(){
		return "user_notice";
	}
	
}

?>