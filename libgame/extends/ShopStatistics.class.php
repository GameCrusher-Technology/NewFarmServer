<?php
require_once GAMELIB.'/model/ManagerBase.class.php';

require_once FRAMEWORK . '/cache/MemCounter.class.php';

/**
 * 用户统计用户购买商店物品的计数器。
 *
 */
class ShopStatistics extends ManagerBase{
	/**
	 * 用于统计卖出动作的计数器
	 * @var ActionCounter
	 */
	protected $counter = null;
	
	static protected $prefix = "ranch_buy_item_stat_"; 
	static protected $prefix1 = "ranch_pay_item_stat_"; 
	
	public function __construct(){
		parent::__construct();
		$cache_instance = $this->getCacheInstance();
		$this->counter = new MemCounter($cache_instance);
	}
	
	/**
	 * @see 增加统计
	 *
	 * @param  $item_id
	 * @param  $item_count
	 * @param  $buy_method c - credits  m - cash ||buy_task A=coin=>3000金币，B=coupon=>2点卷，C=money=>1N币,
	 *	D=money=>2N币，E=money=>4N币,F=money=>5N币,G=money=>8N币,H=money=>10N币.
	 * */
	public function addItemCount($item_id,$item_count = 1,$buy_method = 'c'){
		$key = self::$prefix . $buy_method . $item_id;
		$now = time();
		$today = mktime(0,0,0);
		$last_commit = $this->getFromCache($key.'_commit');
		
		//如果昨天的记录没有提交完毕，提交数据库
		if($last_commit === false || $last_commit < $today){
			$this->commitCounter($item_id,$buy_method,$last_commit);
			$this->setToCache($key.'_commit',$now,null,259200);
		}
		
		return $this->counter->increateCount($key,$item_count);
	}
	
	/**
	 * @see 增加充值用户统计
	 *
	 * @param  $item_id
	 * @param  $item_count
	 * @param  $buy_method c - credits  m - cash  ||buy_task A=coin=>3000金币，B=coupon=>2点券
	 * 
	 */
	public function addItemCount1($item_id,$item_count = 1,$buy_method = 'm'){
		$key = self::$prefix1 . $buy_method . $item_id;
		$now = time();
		$today = mktime(0,0,0);
		$last_commit = $this->getFromCache($key.'_commit');
		
		//如果昨天的记录没有提交完毕，提交数据库
		if($last_commit === false || $last_commit < $today){
			$this->commitCounter1($item_id,$buy_method,$last_commit);
			$this->setToCache($key.'_commit',$now,null,259200);
		}
		
		return $this->counter->increateCount($key,$item_count);
	}
	
	public function addTempCount($type,$level,$count = 1){
		$key = 'ranch_'.$type.'_count_level_'.$level;
		
		return $this->counter->increateCount($key,$count);
	}
	
//	public function addPacksCount($pack_id,$count = 1,$buy_method = 'c'){
//		return $this->counter->increase(self::$prefix . 'pack_' . $buy_method . $pack_id,$count);
//	}
	
	public function addItemsCount($items){
		if(empty($items) || !is_array($items)){
			return false;
		}
		$val = 0;
		foreach ($items as $item) {
			$val += $this->addItemCount($item['item_id'],$item['item_count']);
		}
		return $val;
	}
	
	protected function commitCounter($item_id,$buy_method,$last_commit_time){	
		if($last_commit_time <= 0)
			$last_commit_time = strtotime("-1 day");
			
		$stat_date = date('Y-m-d',$last_commit_time);
		
		$key = self::$prefix . $buy_method . $item_id;
		
		$count = $this->counter->getCount($key);
		if($count === false)
			return false;
		
		$now = time();
		$sql = "insert into shop_statistics(stat_date,item_id,count,is_packs,buy_method,update_time)
		 values('$stat_date', $item_id, $count, 0, '$buy_method', $now) on duplicate key
		  update count = count + values(count)";
		
		$this->getDBHelperInstance()->execute($sql);
		
		$this->counter->decreateCount($key,$count);
		
		return true;
	}
	
	protected function commitCounter1($item_id,$buy_method,$last_commit_time){	
		if($last_commit_time <= 0)
			$last_commit_time = strtotime("-1 day");
			
		$stat_date = date('Y-m-d',$last_commit_time);
		
		$key = self::$prefix1 . $buy_method . $item_id;
		
		$count = $this->counter->getCount($key);
		if($count === false)
			return false;
		
		$now = time();
		$sql = "insert into shop_statistics_pay(stat_date,item_id,count,is_packs,buy_method,update_time)
		 values('$stat_date', $item_id, $count, 0, '$buy_method', $now) on duplicate key
		  update count = count + values(count)";
		
		$this->getDBHelperInstance()->execute($sql);
		
		$this->counter->decreateCount($key,$count);
		
		return true;
	}
	
	protected function getTableName(){
		return "system_statistics";
	}
	
}

?>