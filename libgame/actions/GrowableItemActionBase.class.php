<?php
require_once GAMELIB.'/actions/DataidActionBase.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
include_once GAMELIB.'/model/UserPickUpBonusManager.class.php';
require_once GAMELIB.'/model/UserFieldDataManager.class.php';
include_once GAMELIB.'/model/activity/UserGetMonsterNian.class.php';

abstract class GrowableItemActionBase extends DataidActionBase {
	/**
	 * 浇水的动作
	 *
	 * @return unknown
	 */
	protected function water(){
		$friend_gameuid=$this->getParam('friend_gameuid');

		// 更新土地状态
		$field_mgr = $this->getDataManager();
        //浇水
		$field = $this->getData();
		$target_gameuid=$this->getTargetGameuid();
		$data_id=$this->getParam('data_id');
		if (empty($field)||$field['gameuid']!=$target_gameuid){
			$this->throwException("data[$data_id] of user[$target_gameuid] not exist",GameStatusCode::DATA_NOT_EXISTS);
		}

		$next_water_time=$field_mgr->water($field);
		$result = array('next_water_time'=>$next_water_time);
		if ($friend_gameuid > 0) {
			$uid=$this->getParam('uid','string');
			$name=$this->getParam('name','string');
			$result['event_log_params'] = array(
				'gameuid'=>$friend_gameuid,
				'action_id'=>ActionCode::ACTION_EVENT_DO_HELP,
				'params'=>array('uid'=>$uid,'name'=>$name)
			);
		}
		return $result;
	}
	/**
	 * 收获的动作
	 *
	 * @return unknown
	 */
	protected function harvest($byVehicle = 0){
		$useSkill = false;
		$gameuid=$this->getParam('gameuid');
		$data_id=$this->getParam('data_id');
		$greenhouse_mgr=$this->getDataManager();
		$data=$this->getData();
		if (empty($data)||$data['gameuid']!=$gameuid||empty($data['item_id'])){
			$this->throwException("data[$data_id] of user[$gameuid] not exist",GameStatusCode::DATA_NOT_EXISTS);
		}
		$crop=get_xml_def($data['item_id'],XmlDbType::XMLDB_ITEM);

		if(!$greenhouse_mgr->canHarvest($data,$crop)){
			$this->throwException("crop[$data_id] not immatual.", GameStatusCode::IMMATUAL_CROP);
		}
		//添加记录日常收获作物的次数
		include_once GAMELIB.'/model/UserDailyTaskManager.class.php';
	    $user_daily_task_mgr = new UserDailyTaskManager();
		if($crop['resource'] == 'Flowers_seeds'){
			$user_daily_task_mgr->updateTaskCount($gameuid,5);
		}else{
			$user_daily_task_mgr->updateTaskCount($gameuid,1);
		}
	    //这里添加2012女王种植大赛的额外获取奖章
		include_once GAMELIB.'/model/activity/UserQueenPlantComp2012Manager.class.php';
	    $name    = $this->getParam("name","string");
		$user_queen_plant_mgr = new UserQueenPlantComp2012Manager();
		$tmp_medal = $user_queen_plant_mgr->plantToGetMedal($gameuid,$name,$data['item_id']);
		//删除地鼠数据
		require_once GAMELIB.'/model/UserFieldDataManager.class.php';
		$field=new UserFieldDataManager();
		$field->deletemole($gameuid,$data_id);
		$user_pick=new UserPickUpBonusManager();
		//产品放入仓库
		$product_item_id=$greenhouse_mgr->getProductItemId($data['item_id']);
		$item_mgr=new UserGameItemManager($gameuid);
		if($data['leavings']>0){
			include_once GAMELIB.'/model/UserHoroscopeManager.class.php';
			$horo_mgr = new UserHoroscopeManager();
			//收获时产量降低{10%}   75112
			$reduce_leavings = $horo_mgr->addBuffOnUser($gameuid,75112);
			$data['leavings'] = round($data['leavings']*(1-$reduce_leavings));
			if ($product_item_id!=49815){
				$item_mgr->addItem($product_item_id,$data['leavings']);
			}
			
			$user_pick->setPickUpCache($gameuid,$product_item_id);
			//检查产物是否可以双倍爆出
	
			$flag=$field->getHarvestRand($gameuid);
			$extra_rewards=array();
			
			//收获时{10%}几率产量翻倍  75111
			$double_leavings = $horo_mgr->addBuffOnUser($gameuid,75111);
			if(!empty($flag)){
				$double_leavings += GameConstCode::ADD_TWINCE_PRODUCTION+$flag['hitRate'];
			}else{
				$double_leavings += GameConstCode::ADD_TWINCE_PRODUCTION;
			}
			if($double_leavings > 1) $double_leavings = 1;
			
			if ($product_item_id!=49815){
				if (can_happen_random_event($double_leavings)){
					$item_mgr->addItem($product_item_id,$data['leavings']);
					$extra_rewards[]=array('item_id'=>$product_item_id,'count'=>$data['leavings']);
					//添加捡东西的验证信息
					$user_pick->setPickUpCache($gameuid,$product_item_id);
				}
			}
			$leavings = 0;//金牛座新技能产量
			if($byVehicle != 1){
				//增加金牛座新技能
				$skills = array(1=>65,2=>20,3=>10,5=>5);
				$more_rate = StaticFunction::getOneByRate($skills);
				$tmp_star_skill = array();
				$star_skills = $horo_mgr->getNewSkills($gameuid);
				if(!empty($star_skills)){
					foreach ($star_skills as &$star_skill){
						if($star_skill['id'] == 75001 && $star_skill['use_able'] == 1){
							$star_skill['use_able'] = 0;
							$star_skill['multiple'] = $more_rate;
							$horo_mgr->setNewSkill($gameuid,$star_skill);
							$tmp_star_skill = $star_skills;
							if ($product_item_id == 49815){
								// 判断是否已枯萎
								if (time()>intval($data['reserve_1'])){
									$product_item_id = $crop['withered_depend_itemid'];
									$data['leavings'] = 1;
								}else {
									$data['leavings'] = 2;
								}
							}
							$leavings = $data['leavings']*$more_rate;
							$item_mgr->addItem($product_item_id,$leavings);
							break;
						}elseif ($star_skill['id'] == 75002 && $star_skill['use_able'] == 1) {
							// 双子座技能:收获人参果可以无视枯萎时间，必定收获到2个双子人参果
							if ($data['item_id']==9815){
								$star_skill['use_able'] = 0;
								$horo_mgr->setNewSkill($gameuid,$star_skill);
								$tmp_star_skill = $star_skills;
								
								$product_item_id = 49815;
								$item_mgr->addItem($product_item_id,2);
								$data['leavings']=2;
								$useSkill = true;
								
								break;
							}
						}
					}
				}
			}
			if($data['item_id']==9806){
				$gif=array(2160=>5,2161=>10,2162=>10,2163=>10,2164=>10,2165=>10,2166=>10,2167=>10,2168=>10,2169=>5,2170=>5,2171=>5);
				$rand=mt_rand(1,100);
				$count=0;
				foreach ($gif as $k=>$v){
					$count+=$v;
					if($rand<=$count){
						$item_id=$k;
						$item_mgr->addItem($item_id,1);
						$extra_rewards[]=array('item_id'=>$item_id,'count'=>1);
						$user_pick->setPickUpCache($gameuid,$item_id);
						break;
					}
				}
			}
			
			// 收获人参果
			if ($data['item_id']==9815){
				if ($useSkill === false){
					// 判断是否已枯萎
					if (time()>intval($data['reserve_1'])){
						// 产出人参果
						$product_item_id = $crop['withered_depend_itemid'];
						$item_mgr->addItem($product_item_id, 1);
						$data['leavings']=1;
					}else {
						// 产出双子人参果
						$item_mgr->addItem($product_item_id, 2);
						$data['leavings']=2;
					}
				}
			}
			$item_mgr->commitToDB();
		}

		//修改玩家的数据
		$item_def=get_xml_def($product_item_id,XmlDbType::XMLDB_ITEM);
		$result=$greenhouse_mgr->harvest($data,$crop);
		$result['item_id']=$product_item_id;
		$result['count']=$leavings+$data['leavings'];
		
		$result['experience']=intval($item_def['experience']);
		//添加捡东西验证标示
		$user_pick->setPickUpCache($gameuid,'exp',intval($item_def['experience']));
		$result['extra_rewards']=$extra_rewards;
		$result['energy']=add_extral_energy($gameuid,$flag);
		//这里添加
	    if(!empty($tmp_medal)){
			$result["special"] = $tmp_medal["count"];
		}
	    //星座新技能，返给前台
		if(!empty($tmp_star_skill)){
		    $result["starsign_skills"] = $tmp_star_skill;
		}
//		//这里添加年兽
//		$monsterManager = new UserGetMonsterNian();
//		$result["monster"] = $monsterManager->setNomalSmallMonster($gameuid);

		return $result;
	}
	/**
	 * 批量收获
	 *
	 * @return unknown
	 */
	protected function Volumeharvest($gameuid,$data_def){
		$greenhouse_mgr=$this->getDataManager();
		$crop=get_xml_def($data_def['item_id'],XmlDbType::XMLDB_ITEM);
		//产品放入仓库
		$item_mgr=new UserGameItemManager($gameuid);
		if($data_def['leavings']>0){
			$item_mgr->addItem($crop['depend_itemid'],$data_def['leavings']);
		}
		$item_mgr->commitToDB();
		//添加捡东西验证
		$user_pick=new UserPickUpBonusManager();
		$user_pick->setPickUpCache($gameuid,$crop['depend_itemid']);
		$result['harvest']=$greenhouse_mgr->harvest($data_def,$crop,1);
		return $result;
	}



	/**
	 * 施肥的动作
	 *
	 * @return unknown
	 */
	protected function fertilize(){
		$gameuid=$this->getParam('gameuid');
		$data_id=$this->getParam('data_id');
		$item_id=$this->getParam('item_id');

		$field_mgr = $this->getDataManager();
        //施肥
		$field = $this->getData();
		if (empty($field)||$field['gameuid']!=$gameuid){//施肥不允许好友进行操作
			$this->throwException("data[$data_id] of user[$gameuid] not exist",GameStatusCode::DATA_NOT_EXISTS);
		}
		//判断是否是化肥
		$fertilize_item = get_xml_def($item_id, XmlDbType::XMLDB_ITEM);
		if (strpos($fertilize_item['name'],'Fertilize')===false){
			$this->throwException("the item[$item_id,name=".$fertilize_item['name']."] is not fertilizer.",GameStatusCode::INCORRECT_ITEM_TYPE);
		}
		//有活动的作物禁止用化肥
		$item_def=get_xml_def($field['item_id'],XmlDbType::XMLDB_ITEM);
		if(!empty($item_def['switch_itemid'])){
			$this->throwException("the data[$data_id] is not do fertilizer.",GameStatusCode::CANNT_USE);
		}
		//检查化肥数量
		$item_mgr=new UserGameItemManager($gameuid);
		$item_mgr->subItem($item_id,1);
		$item_mgr->checkReduceItemCount();

		//植物的生长时间
		$grw_time=$field_mgr->fertilize($field,$fertilize_item['life_cycle']);

		//减少化肥数量
		$item_mgr->commitToDB();

		$result = array();
		//TODO:给自己施肥这里是否需要加入eventlog

		$result['data_id'] = $data_id;
		$result['grownup_time'] = $grw_time;
		$result['item_id'] = $item_id;
		$result['item']=$field['item_id'];
		return $result;
	}
	/**
	 * 偷东西的动作
	 *
	 * @return unknown
	 */
	protected function steal(){
		$gameuid=$this->getParam('gameuid','int');
		$data_id=$this->getParam('data_id','int');
		$friend_gameuid=$this->getParam('friend_gameuid');
		//判断是否是活力之星家
		if(isofficial($friend_gameuid)===true)
		{
			$this->throwException("user official cant do this action",GameStatusCode::ACTION_MAX_ALLOWED);
		}
		//判断是否超出了一天的最大值
		require_once GAMELIB.'/model/XmlManager.class.php';
		$action_def_mgr = new ActionDefManager();
		if (!$action_def_mgr->canActionHappen($gameuid, ActionCode::ACTION_FRIEND_STEAL)) {
			$this->throwException("user[$gameuid] cant do this action today",GameStatusCode::ACTION_MAX_ALLOWED);
		}
		//-------------------计算是否偷到，并且更新field的数据---------------
		$field_mgr = $this->getDataManager();
		$field = $this->getData();
		if (empty($field)||$field['gameuid']!=$friend_gameuid){
			$this->throwException("data[$data_id] of user[$friend_gameuid] not exist",GameStatusCode::DATA_NOT_EXISTS);
		}
		$change=$field_mgr->steal($gameuid,$field);
		$modify=$change['modify'];
		$lost_coin=$change['lost_coin'];
		$lost_strengh=$change['lost_strengh'];
		$thief=$modify['thief'];
		$stolen_count = 1;
		//-----------------------------------------------------------------------
		$uid=$this->getParam('uid','string');
		$name=$this->getParam('name','string');
		if($lost_coin !== false || $lost_strengh !== false){
	    	// 将丢失的金币加到被偷的人上
	    	if ($lost_coin>$this->user_account['coin']) $lost_coin=$this->user_account['coin'];
	    	$this->user_account_mgr->updateUserStatus($friend_gameuid, array('coin'=>$lost_coin));
	    	$action_logger = new UserActionLogManager($friend_gameuid);
	    	$action_logger->writeLog(ActionCode::ACTION_STEAL_CATCH, array('coin'=>$lost_coin));
	    	//统计抓获的次数
	    	UserActionCountManager::updateActionCount($friend_gameuid, ActionCode::ACTION_STEAL_CATCH);

			include_once GAMELIB . '/model/UserAccountManager.class.php';
			$userAccManager = new UserAccountManager();
			$strength_del = $lost_strengh===false?0:1;
			$strength = $userAccManager->getStrength($gameuid);
			if($strength > $strength_del){
				$strength = $userAccManager->updateStrength($gameuid, -$strength_del);
			}
	    	$result = array(
	    		'coin' => -$lost_coin,
	    		'strength' => $strength,
	    		'thief'=>implode(',', $thief),
	    		'event_log_params' =>  array(
						'gameuid'=>$friend_gameuid,
						'action_id'=>ActionCode::ACTION_STEAL_CATCH,
						'params'=>array('uid'=>$uid,'name'=>$name,'coin'=>$lost_coin)
	    	));
	    } else {
    		//将偷到的物品放到用户的仓库中
    		$product_item_id=$field_mgr->getProductItemId($field['item_id']);
    		$item_mgr=new UserGameItemManager($gameuid);
    		$item_mgr->addItem($product_item_id,$stolen_count);
    		$item_mgr->commitToDB();
 			//统计偷窃成功的次数
 			UserActionCountManager::updateActionCount($gameuid, ActionCode::ACTION_FRIEND_STEAL);
 			$result = array(
 				'data_id' => $data_id,
				'gameuid' => $gameuid,
 				'thief'=>implode(',', $thief),
				'leavings' => $modify['leavings'] ,
				'item_id' => $product_item_id,
				'count' => $stolen_count,
 				'event_log_params' => array(
					'gameuid'=>$friend_gameuid,
					'action_id'=>ActionCode::ACTION_FRIEND_STEAL,
					'params'=>array('uid'=>$uid,'name'=>$name,'item_id'=>$field['item_id'],'count'=>1)
 			));
	    }
	    recordSteal($friend_gameuid,$gameuid);
	    return $result;
	}
	protected function plant($field,$flag){
		$gameuid = $this->getParam('gameuid');
		$item_id = $this->getParam('item_id');
		$method = $this->getParam('method');
		$crop_method=$this->getParam('crop_method');
		$field_mgr=$this->getDataManager();
		if ($method != 1 && $method != 2){
			$this->throwException('not have seed', GameStatusCode::CANT_SEED);
		}
		//种植作物, 如果item_id>0，那么这块地不能再播种了
		if ($field['item_id'] > 0) {
			$this->throwException("has crop on field[".$field['data_id']."]",GameStatusCode::CANT_SEED);
		}

		$crop = get_xml_def($item_id, XmlDbType::XMLDB_ITEM);

//		if ($crop['item_type']!=ItemType::ITEM_TYPE_CROP&&$crop['item_type']!=ItemType::ITEM_TYPE_FLOWER){
//			$this->throwException("crop[$item_id] type error",GameStatusCode::ITEM_ERROR);
//		}

		//获取植物等级
		$crop_level = $field_mgr->getItemLevel($gameuid,$item_id);
		$result = array();
		if ($method == 2) {
			//检查用户的种子是否够
			$item_mgr=new UserGameItemManager($gameuid);
			$item_mgr->subItem($item_id,1);
			$item_mgr->checkReduceItemCount();
		} else {
			//当使用金钱来种植时要检查这种作物能不能买
			if (!empty($crop['visible'])&&$crop['visible']==1){
				$this->throwException("user[$gameuid] can not buy item[$item_id]",GameStatusCode::CANT_BUY);
			}
			if ($crop_level < 1){
				$this->throwException("level of item[$item_id] not enough,current_level[$crop_level]",
					GameStatusCode::ITEM_LEVEL_ERROR);
			}
			//如果设定的有cost字段则减少金币，否则减少农币
			if(!empty($crop_method)){
				if($crop_method==1){
					if (isset($crop['cost'])&&$crop['cost']>0){
						$user_field = 'coin';
						$item_def_field = 'cost';
						$status_code = GameStatusCode::COIN_NOT_ENOUGH;
						$buy_method = 'c';
					}
				}
				elseif($crop_method==2){
					if (isset($crop['money'])&&$crop['money']>0) {
						$user_field = 'money';
						$item_def_field = 'money';
						$status_code = GameStatusCode::MONEY_NOT_ENOUGH;
						$buy_method = 'm';
					}
				}
				elseif($crop_method==3){
					if (isset($crop['coupon'])&&$crop['coupon']>0){
					$user_field = 'coupon';
					$item_def_field = 'coupon';
					$status_code = GameStatusCode::COUPON_NOT_ENOUGH;
					$buy_method = 'd';
					}
				}elseif ($crop_method==4){
					$user_pick=new UserPickUpBonusManager();
					$is_buy=$user_pick->getItemCount($item_id);
					$user_buy=$user_pick->getUserBuy($gameuid,$item_id);
					$maxbuy=$crop['offSaleItemMax']+$crop['offSaleItemBuffer'];
					if(intval($maxbuy)<=0){
						$this->throwException("cant buy item[$item_id] is not offsale",GameStatusCode::CANT_BUY);
					}
					if(intval($is_buy)>=intval($maxbuy)){
						$this->throwException("cant buy item[$item_id] is max $maxbuy",GameStatusCode::CANT_BUY);
					}
					$user_buy=$user_pick->getUserBuy($gameuid,$item_id);
					if(intval($user_buy)>=intval($crop['offSaleBuyMax'])){
						$this->throwException("the user[$gameuid]cant buy item[$item_id] is max 5",GameStatusCode::CANT_BUY);
					}
					$user_field =$crop['offSaleType'];
					$item_def_field ='offSaleCost';
					$buy_method = 'c';
				}
				else {
					$this->throwException("this item[$item_id] cant plant by coin or money",GameStatusCode::ITEM_ERROR);
				}
			}else{
				if (isset($crop['cost'])&&$crop['cost']>0){
					$user_field = 'coin';
					$item_def_field = 'cost';
					$status_code = GameStatusCode::COIN_NOT_ENOUGH;
					$buy_method = 'c';
				}elseif (isset($crop['money'])&&$crop['money']>0) {
					$user_field = 'money';
					$item_def_field = 'money';
					$status_code = GameStatusCode::MONEY_NOT_ENOUGH;
					$buy_method = 'm';
				}elseif (isset($crop['coupon'])&&$crop['coupon']>0){
					$user_field = 'coupon';
					$item_def_field = 'coupon';
					$status_code = GameStatusCode::COUPON_NOT_ENOUGH;
					$buy_method = 'd';
				}else{
					$this->throwException("this item[$item_id] cant plant by coin or money",GameStatusCode::ITEM_ERROR);
				}

			}
			$cost=get_current_price($gameuid,$crop[$item_def_field],$crop['discount']);
			if ($this->user_account[$user_field]<$cost){
				$this->throwException("$user_field of user[$gameuid] not enough",$status_code);
			}
			if($crop_method==4){
				$user_pick=new UserPickUpBonusManager();
				$user_pick->setUserBuy($gameuid,$item_id,1);
				$user_pick->setItemCount($item_id,1);
				$result['is_buy']=array('item_id'=>$item_id,'count'=>intval($is_buy)+1);
				$result['user_buy']=array('item_id'=>$item_id,'count'=>intval($user_buy)+1);
			}
			$result[$user_field]=-$cost;
			addShopStat($item_id,1,$buy_method);
			
			//添加记录日常照顾动物的次数
			if($user_field == 'money'){
				include_once GAMELIB.'/model/UserDailyTaskManager.class.php';
				$user_daily_task_mgr = new UserDailyTaskManager();
				$user_daily_task_mgr->updateTaskCount($gameuid,17);
			}
		}

		$farm=$field_mgr->plant($field,$crop,$flag);
		if ($method==2){
			$item_mgr->commitToDB();
		}
		$result['farm']=$this->implodeRow($farm);
		if(isset($farm['starsign_skills'])){
			$result['starsign_skills']=$farm['starsign_skills'];
		}
		//做玫瑰的成就
		if ($item_id==19002||$item_id==19007){
			UserActionCountManager::updateActionCount($gameuid,ActionCode::ACTION_PLANT_ROSE,1);
		}
		return $result;
	}
}
?>
