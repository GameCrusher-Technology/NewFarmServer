<?php
require_once GAMELIB.'/model/ManagerBase.class.php';

/**
 * 系统活动或补偿领取
 *
 */
class SystemNotice extends ManagerBase{
	
	public function getSystemNotice($id){
		$key = $this->getMemKey($id);
		$data = $this->getFromCache($key);
		if($data === false){
			$res = $this->getNotice();

			if(!isset($res[$id]) || empty($res[$id]))
				return array();
				
			$data = $res[$id];
			
			if($data['reward'] != ""){
				$array = explode("|",$data['reward']);
				$data['reward'] = array();
				foreach($array as $tmp){
					if($tmp == "")continue;
					$tmp = explode(":",$tmp);
					$item['item_id'] = $tmp[0];
					$item['count'] = $tmp[1];
					$item['type'] = $tmp[2];
					$data['reward'][] = $item;
				}
			}else{
				$data['reward'] = array();
			}

			$this->setToCache($key,$data,null,2592000);
		}
		
		return $data;
	}
	
	public function getNotice(){
		$sql = "select * from system_notice";
		$res = $this->getDBHelperInstance()->getAll($sql);
		
		$result = array();
		foreach($res as $tmp){
			$result[$tmp['id']] = $tmp;
		}
		
		return $result;
	}
	
	public function addNotice($name,$start_time,$end_time,$reward){
		$sql = "insert into system_notice(name,start_time,end_time,reward,create_date) values('%s',%d,%d,'%s','%s')";
		
		$sql = sprintf($sql,addslashes($name),$start_time,$end_time,$reward,date("Y-m-d H:i:s"));
		
		return $this->getDBHelperInstance()->execute($sql);
	}
	
	public function updateNotice($id,$name,$start_time,$end_time,$reward){

		$sql = "insert into system_notice(id,name,start_time,end_time,reward) values(%d,'%s',%d,%d,'%s') on duplicate key
		  update name = values(name), start_time = values(start_time), end_time = values(end_time), reward = values(reward)";
		
		$sql = sprintf($sql,$id,addslashes($name),$start_time,$end_time,$reward);
		
		$this->deleteFromCache($this->getMemKey($id));
		
		return $this->getDBHelperInstance()->execute($sql);
	}
	
	public function deleteNotice($id){
		$sql = "delete from system_notice where id = $id";
		
		$this->deleteFromCache($this->getMemKey($id));
		
		return $this->getDBHelperInstance()->execute($sql);
	}
	
	private function getMemKey($id){
		return "ranch_system_notice_$id";
	}
	
	protected function getTableName(){
		return "system_notice";
	}
	
}

?>