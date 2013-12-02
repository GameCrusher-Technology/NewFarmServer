<?php
require_once GAMELIB.'/model/ManagerBase.class.php';

require_once FRAMEWORK . '/cache/MemCounter.class.php';

class SystemStatistics extends ManagerBase {
	
	protected $counter = null;
	
	static public $column = array('install','firstload','retention','activity','login_times','selCharacter','visit_friend');
	
	static public $prefix = "ranch_system_stat_";
	
	public function __construct(){
		parent::__construct();
		$cache_instance = $this->getCacheInstance();
		$this->counter = new MemCounter($cache_instance);
	}
	
	/**
	 * @see 增加统计
	 *
	 * @param  $col
	 * @param  $count
	 */
	public function addCount($col,$count = 1){
		if(!in_array($col,self::$column))
			return false;
		$key = self::$prefix . $col;
		$now = time();
		$today = mktime(0,0,0);
		$last_commit = $this->getFromCache($key.'_commit');
		
		//如果昨天的记录没有提交完毕，提交数据库
		if($last_commit === false || $last_commit < $today){
			
			$this->commitCounter($col,$last_commit);
			
			$this->setToCache($key.'_commit',$now,null,259200);
		}
		
		return $this->counter->increateCount($key,$count);
	}
	
	/**
	 * @see 提交计数器数据到数据库
	 *
	 * @param  $col
	 * @param  $last_commit_time
	 * @return 
	 */
	protected function commitCounter($col,$last_commit_time){
		if($last_commit_time <= 0)
			$last_commit_time = strtotime("-1 day");
			
		$stat_date = date('Y-m-d',$last_commit_time);
		
		$key = self::$prefix . $col;
		
		$count = $this->counter->getCount($key);
		if($count === false)
			return false;
		
		$now = time();
		
		$sql = "insert into system_statistics(stat_date,col,count,update_time)
		 values('$stat_date','$col', $count, $now) on duplicate key
		  update count = count + values(count),update_time = values(update_time)";
		
		$this->getDBHelperInstance()->execute($sql);
		
		$this->counter->decreateCount($key,$count);
		
		return true;
	}
	
	/**
	 * @增加retention计数
	 *
	 * @param  $type
	 * @param  $level
	 * @param  $count
	 * @return 
	 */
	public function addRetentionCount($type,$level,$count=1){
		$key = self::$prefix . "retention_".$type."_".$level;
		$now = time();
		$today = mktime(0,0,0);
		$last_commit = $this->getFromCache(self::$prefix.'retention_commit');
		
		//如果昨天的记录没有提交完毕，提交数据库
		if($last_commit === false || $last_commit < $today){
			$this->commitRetention($last_commit);
			$this->setToCache(self::$prefix.'retention_commit',$now,null,259200);
		}
		
		if($count > 0)
			return $this->counter->increateCount($key,$count);
		else
			return $this->counter->decreateCount($key,abs($count));
	}
	
	protected function commitRetention($last_commit_time){
		if($last_commit_time <= 0)
			$last_commit_time = strtotime("-1 day");
			
		$stat_date = date('Y-m-d',$last_commit_time);
		
		$values = "";
		for($i=0;$i<100;$i++){
			$key1 = self::$prefix . "retention_1"."_".$i;
			$count1 = $this->counter->getCount($key1);
			$key2 = self::$prefix . "retention_2"."_".$i;
			$count2 = $this->counter->getCount($key2);
			if($count1 > 0){
				$values .= "('$stat_date', 1, $i, $count1),";
			}
			if($count2 > 0){
				$values .= "('$stat_date', 2, $i, $count2),";
			}
			$this->counter->decreateCount($key1,$count1);
			$this->counter->decreateCount($key2,$count2);
		}
		
		$values = rtrim($values,",");
		
		if($values == "")
			return false;
		
		$sql = "insert into retention_detail(stat_date,type,level,count)
		 values".$values." on duplicate key
		  update count = count + values(count)";
		
		$this->getDBHelperInstance()->execute($sql);
		
		return true;
	}
	
	protected function getTableName(){
		return "system_statistics";
	}
	
}
?>