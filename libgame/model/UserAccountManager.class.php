<?php

class UserAccountManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
 	 * 返回数据为gameuid对应的数据
 	 *
 	 * @param int $gameuid the gameuid to be retrieved.
 	 * @return array the user info of gameuid.
 	 */
    public function getUserAccount($gameuid){
    	if (empty($gameuid) || intval($gameuid) <= 0) return false;
    	
    	//account表中数据
		$account = $this->getFromDb($gameuid,array('gameuid'=>$gameuid));
		if (empty($account))	return array();
		return $account;
    }
    
	/**
	 * 获取用户当前的体力值
	 *
	 * @param unknown_type $gameuid
	 * @return unknown
	 */
	public function getStrength($gameuid){
		$user_account = $this->getUserAccount($gameuid);
		$strength=intval($user_account['strength']);
		
		//获取当前等级的体力值上限
    	$level = $user_account['level'];
    	$max_strength = $this->getMaxStrength($gameuid,$level);
    	if ($strength<$max_strength){
    		$strength_time_flag = $this->getStrengthTime($gameuid);
    		
    		$strength_add = ceil((time() - $strength_time_flag) / $this->getStrenghAddCD($gameuid));
    		$strength+=$strength_add;
    		if ($strength>$max_strength) $strength=$max_strength;
    	}
    	$strength=$strength>0?$strength:0;
    	return $strength;
    }
    
    private function getStrenghAddCD($gameuid){
		include_once GAMELIB.'/model/UserGameItemManager.class.php';
		$del_cd = 0;
		$item_mgr=new UserGameItemManager($gameuid);
		$item_info=$item_mgr->getItem('35018');
		if(!empty($item_info)&&$item_info['count']>0){
			$del_cd = $item_info['count'];
		}
		return GameConstCode::STRENGTH_TO_TIME - $del_cd;
    }
	/**
     * @see 修改体力值
     *
     * @param 需要修改体力值的用户 $gameuid
     * @param 改变的值 $delta 为0,实现将体力值持久化的功能
     * @return 更新后的体力值
     */
    public function updateStrength($gameuid,$delta) {
    	$user_account = $this->getUserAccount($gameuid);
		$strength=intval($user_account['strength']);
		$now=time();
		//获取当前等级的体力值上限
    	$level = intval($user_account['level']);
    	$max_strength = $this->getMaxStrength($gameuid,$level);
    	//将已经可以转化成整个体力值的时间转化为体力值存储，将不满一点体力值的时间继续存储在缓存中
    	if ($strength>=$max_strength){
    		$strength=$strength+$delta;
    		$strength_time_flag=$now;
    	}else {
    		$strength_time_flag=$this->getStrengthTime($gameuid);
    		$strength_add = intval(($now - $strength_time_flag) / $this->getStrenghAddCD($gameuid));
    		$strength+=$strength_add;
    		if ($strength>=$max_strength){
    			$strength=$max_strength+$delta;
    			$strength_time_flag=$now;
    		}else {
    			$strength=$strength+$delta;
    			$strength_time_flag=$strength_time_flag+$strength_add*$this->getStrenghAddCD($gameuid);
    		}
    	}
    	//体力值为负的情况的处理方式
    	if ($strength<0){
    		$flag=abs($strength)>1?1:abs($strength);
    		$strength_time_flag=$strength_time_flag+$flag*$this->getStrenghAddCD($gameuid);
    		$strength=0;
    	}
    	
    	$this->updateUserStatus($gameuid,array('strength'=>$strength));
    	return $this->getStrength($gameuid);
    }

    /**
     * @see 更新用户连续登录时间
     *
     * @param  $gameuid
     * @param  $rows
     */
    public function updateLoginRow($gameuid, $rows = 1){
    	
    	$this->updateUserStatus($gameuid, array('login_row'=>$rows));
    	
    	return $rows;
    }
    
    /**
     * @see 更新用户雇佣的cd时间
     *
     * @param  $gameuid
     * @param  $time
     * @param  $now
     */
    public function updateEmployeeCD($gameuid, $time = 0, $now = null){
    	if($now == null){
    		$now = time();
    	}
    	
    	$cd = $now + $time;
    	
    	$this->updateUserStatus($gameuid, array('employee_cd'=>$cd));
    	
    	return $cd;
    }
    
 	/**
 	 * 更新用户 coin
 	 *
 	 * @param int $gameuid
 	 * @param int $coin
 	 */
    public function updateUserCoin($gameuid,$coin) {
    	$change = array('coin' => $coin);
    	$this->updateUserStatus($gameuid,$change);
    	
    	return true;
    }
    
    /**
 	 * 更新用户 coupon
 	 *
 	 * @param int $gameuid
 	 * @param int $coupon
 	 */
    public function updateUserCoupon($gameuid,$coupon) {
    	$change = array('coupon' => $coupon);
    	$this->updateUserStatus($gameuid,$change);
    	
    	return true;
    }
    
	/**
 	 * 更新用户 money
 	 *
 	 * @param int $gameuid
 	 * @param int $money
 	 */
    public function updateUserMoney($gameuid,$money) {
    	$change = array('gem' => $money);
    	$this->updateUserStatus($gameuid,$change);
    	
    	return true;
    }
    
	/**
 	 * 更新用户 exp
 	 *
 	 * @param int $gameuid
 	 * @param int $experience
 	 */
    public function updateUserExperience($gameuid,$experience) {
    	$change = array('exp' => $experience);
    	$this->updateUserStatus($gameuid,$change);
    	
    	return true;
    }
    
	/**
 	 * 更新用户 experience
 	 *
 	 * @param int $gameuid
 	 * @param int $coin
 	 * @param int $experience
 	 */
    public function updateUserCoinAndExp($gameuid,$coin,$experience) {
    	$change = array('coin'=>$coin,'exp' => $experience);
    	$this->updateUserStatus($gameuid,$change);
    	
    	return true;
    }
    
    /**
 	 * 用户升级
 	 *
 	 * @param int $gameuid
 	 * @return int $new_level
 	 */
    public function upgradeLevel($gameuid){
    	$account = $this->getUserAccount($gameuid);
    	$old_level = intval($account['level']);
    	
    	$new_level = $this->getUserLevel($account['exp']);
    	
    	if($new_level <= $old_level){
    		$this->throwException('exp not enough.',GameStatusCode::EXP_NOT_ENOUGH);
    	}
    	// 
    	if(!get_app_config()->getGlobalConfig("debug_mode")){
    		$new_level = $old_level + 1;
    	}
    	
    	
    	
    	$this->updateUserStatus($gameuid,array('level'=>$new_level),false);
    	
    	return $new_level;
    }
    
	/**
     * 更新用户状态信息
     *
     * @param array $change 数据库字段为key，改变的值为value的数组
     * 
     * 
     */
	public function updateUserStatus($gameuid, $change, $no_db=true,$is_self=true) {
		if (empty($change))	return false;
		$old_account=$this->getUserAccount($gameuid);
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug("update user status:".print_r($change, true));
		}
		
		$total = array();
		//记录交易金额
		if(!empty($change['coin'])){
			if($change['coin'] < 0){
				$total['expense']= abs($change['coin']);
			}else {
				$total['income'] = $change['coin'];
			}	
		}
		//统计money的花销,本地数据库中还没有相关的文件，故不能够记录
		if(!empty($change['gem'])){
			if($change['gem'] < 0){
				$total['money_expense']= abs($change['gem']);
			}else {
				$total['money_income'] = $change['gem'];
			}	
		}

		
		//设置更新模式
		if ($is_self){
			if (!empty($change['coin']) || !empty($change['exp'])){
				$mem_key = sprintf(CacheKey::CACHE_KEY_ACCUMULATION_FLAG,'user_account',$gameuid);
				$flag = $this->getFromCache($mem_key,$gameuid);
				$flag += abs($change['coin'])+abs($change['exp']);
				if ($flag > 50){
					$no_db = false;
					$this->deleteFromCache($mem_key,$gameuid);
				}else {
					$this->setToCache($mem_key, $flag, null, 0);
				}
			}
			if ($no_db){
				if (!empty($change['gem'])){
					$no_db=false;
				}
			}
		}
		
		//获取更新数组
		$modify=array();
		$modify_extra = array();
		$modify_cache = array();
		foreach ($change as $field => $value) {
			switch ($field) {
				case 'exp':
				case 'coin':
				case 'gem':
				case 'love':
					if (intval($value)!=0){
						$modify[$field] = $old_account[$field] + $value;
					}
					break;
				case 'strength':
					$modify[$field]=$value>=0?$value:0;
					break;
				default:
					$modify[$field] = $value;
				    break;
			}
		}
		if (empty($modify) && empty($modify_extra) && empty($modify_cache)) return false;
		
		if ($this->logger->isDebugEnabled()){
			$this->logger->writeDebug("user[$gameuid] modify:".print_r(array_merge($modify,$modify_extra),true));
		}
		//调用父类中的方法
		if(!empty($modify)){
			$this->updateDB($gameuid,$modify,array('gameuid'=>$gameuid),$no_db);
		}
//		if(!empty($total)){
//			require_once GAMELIB.'/model/UserTotalManager.class.php';
//			$UserTotalMgr = new UserTotalManager();
////			error_log(print_r($total,true),3,APP_ROOT."/log/log.log");
//			$UserTotalMgr->updateDate($gameuid,$total);
//		}
		
		return true;
	}
	
	/*
	 * 将用户充值的农币减去，同时将用户的农币的入账信息进行修改
	 */
	public function bannedUser($gameuid,$money){
		$old_account=$this->getUserAccount($gameuid);
		if (empty($old_account)||$money===0) return false;
		$modify=array();
		$modify['gem']=$old_account['gem']-abs($money);
//		$modify['money_income']=$old_account['money_income']-abs($money);
//		if ($modify['money_income']<0) $modify['money_income']=0;
//		$modify['update_time']=time();
		$this->updateDB($gameuid,$modify,array('gameuid'=>$gameuid));
		return true;
	}
	
	
	
	public static function getUserLevel($exp){
		return StaticFunction::expToGrade($exp);	
	}
	
	/**
	 * 创建用户
	 *
	 * @param 被创建用户的gameuid $gameuid
	 * @return 返回被创建用户的gameuid
	 */
	public function createUserAccount($gameuid,$new_account) {
		$new_account['gameuid'] = $gameuid;
		$this->insertDB($new_account);
		if ($this->logger->isDebugEnabled()) {
			$this->logger->writeDebug("created new empty user[gameuid=$gameuid]");
		}
		return $gameuid;
	}
	
	/**
	 * 获取用户可以拥有的最多的土地数量
	 *
	 * @param int $gameuid
	 * @return int 用户可以拥有的土地的最大数量
	 */
	public function getFieldMaxAllowedCount($gameuid){
		$account=$this->getUserAccount($gameuid);
		$add_field=intval($account['add_field']);
		return GameConstCode::FIELD_BASE_COUNT + 2*$add_field;
	}
	
	//初始成就信息
	public function creatAchieveInfo(){
		return  "00000000000000000000000000000000000000000000000000|00000000000000000000";
	}
	
	
	protected function getTableName(){
    	return  "farm_account";
    } 
}
?>
