<?php
class UserTotalManager extends ManagerBase {
	
    protected function getTableName(){
    	return  "user_total";
    }
    
    /**
     * @see 更新用户统计数据
     *
     * @param  $gameuid
     * @param  $change
     */
    public function updateDate($gameuid,$change){
    	if(empty($change)) return false;
    	$no_db = true;
    	$data = $this->getUserTotal($gameuid);
    	if(empty($data)){
    		$this->insertDB(array("gameuid"=>$gameuid));
    	}
    	$mem_key = sprintf(CacheKey::CACHE_KEY_ACCUMULATION_FLAG, $this->getTableName(), $gameuid);
    	$acc_flag = $this->getFromCache($mem_key, $gameuid);
    	foreach ($change as $k => $v){
    		if($no_db){
    			$acc_flag = intval($acc_flag) + abs($v);
    		}
    		
    		if($no_db && $acc_flag > 100){
    			$no_db = false;
    			$acc_flag = 0;
    		}
    		
    		if(!isset($data[$k])){
    			$data[$k] = intval($v);
    		}else{
    			$data[$k] += intval($v);
    		}
    	}
//    	error_log($acc_flag . "db:".$no_db,3,APP_ROOT. "/log/log.log");
    	$this->updateDB($gameuid,$data,array('gameuid'=>$gameuid),$no_db);
    	
    	$this->setToCache($mem_key, $acc_flag, $gameuid, 0);
    	
    	return true;
    }
    
    /**
     * @see 获得用户统计数据
     *
     * @param  $gameuid
     * @return array
     */
    public function getUserTotal($gameuid){
    	if (empty($gameuid) || intval($gameuid) <= 0) return false;
    	$data = $this->getFromDb($gameuid,array('gameuid'=>$gameuid));
    	return $data;
    }
}
?>