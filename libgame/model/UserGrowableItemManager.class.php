<?php
include_once GAMELIB.'/model/UserListDataManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class UserGrowableItemManager extends UserAssocListDataManager {
	protected $waste_modify=array();
	public function canHarvest($grow_data,$item_def){
		if(empty($grow_data['item_id'])) return false;
		//获取该季作物生长的起始时间
		$start_time = $grow_data['plant_time'];
		//多季作物才应该这么做
		if (!empty($grow_data['harvest_time'])&&$item_def['sub_type']>1){
			$start_time= array_pop($grow_data['harvest_time']);
		}
		// 如果系统时间-种植的时间 < 作物的生长时间，则表示没有成熟
		$grownup_time=$grow_data['grownup_time'];
		if(time() - $start_time  <  $grownup_time-60){
			return false;
		}
		return true;
	}
	public function fertilize($gameuid,$crop_data){
		$data_id = $crop_data['data_id'];
		$cache_crop = $this->get($gameuid,$data_id);
		if (empty($cache_crop)){
			$this->throwException("no field,[".$data_id."] gameuid:".$gameuid,
				GameStatusCode::NOT_OWN_FIELD);
		}
		
		$plant_time = $cache_crop['plant_time'] - GameConstCode::WATER_TIME;
		$modify = array("plant_time"=>$plant_time);
		$this->update($gameuid, $data_id, $modify,false);
		
		$result = array();
		$result['delItem'] = array("20001"=>1);
		return $result;
		
	}
	public function move($gameuid,$crop_data){
		$data_id=$crop_data['data_id'];
		
		$cache_crop = $this->get($gameuid,$data_id);
		if (empty($cache_crop)){
			$this->throwException("no field,[".$data_id."] gameuid:".$gameuid,
				GameStatusCode::NOT_OWN_FIELD);
		}
		
		$modify = array('positionx' => $crop_data['positionx'], 'positiony' => $crop_data['positiony']);
		$this->update($gameuid, $data_id, $modify,false);

		return TRUE;
	}
	
	public function harvest($gameuid,$crop_data){
		$data_id=$crop_data['data_id'];
		
		$cache_crop = $this->get($gameuid,$data_id);
		if (empty($cache_crop)){
			$this->throwException("no field,[".$data_id."] gameuid:".$gameuid,
				GameStatusCode::NOT_OWN_FIELD);
		}
		$item_id = $cache_crop['item_id'];
		$plant_time = $cache_crop['plant_time'];
		if (!isset($item_id)){
			$this->throwException("no field item,[".$data_id."] gameuid:".$gameuid,
				GameStatusCode::HAS_HARVESTED);
		}
		$crop_itemspec = get_xml_def($item_id, XmlDbType::XMLDB_ITEM);
		$growtimeArr = explode(":",$crop_itemspec['growtime']);
		$growtime = 0;
		foreach ($growtimeArr as $t){
			$growtime += $t;
		}
		// 判断植物有没有成熟
		
		$leftTime = (time() - $plant_time) - $growtime*60;
		if ($leftTime < 0) {
			$this->throwException("crop[$data_id] not immatual. time : ".$leftTime, GameStatusCode::IMMATUAL_CROP);
		}
		
		//增加 收获成就统计次数
		include_once GAMELIB.'/model/UserActionCountManager.class.php';
		$action_mgr = new UserActionCountManager();
		$action_mgr->updateActionCount($gameuid,$item_id+20000,1);
		//判断树 不会死掉
		$addItem = array();
		if($crop_itemspec['type'] =="Tree"){
			$modify = array('plant_time' => time());
			$addItem = array((intval($item_id)+10000)=>10);
		}else{
			$modify = array('item_id' => 0, 'plant_time' => 0);
			$addItem = array($item_id=>2);
		}
		$this->update($gameuid, $data_id, $modify,false);
		
		$result = array();
		$result['exp']=$crop_itemspec['exp'];
		$result['addItem'] = $addItem;
		return $result;
	}
	public function plant($gameuid,$crop){
		$item_id = $crop['item_id'];
		$modify = array();
		$modify["item_id"] = $item_id;
		$modify["plant_time"]=$crop['plant_time'];
		$modify["output"] = 2;
		$this->update($gameuid,$crop['data_id'],$modify,false);
		
		$result = array();
		$result['delItem'] = array($item_id=>1);
		return $result;
	}
	public function water($grow_data){
		$now=time();
		if($now - $grow_data['next_water_time'] < 0){
			$this->throwException("no need to water, next_water_time of field[".$grow_data['data_id']."]:".$grow_data['next_water_time'],
				GameStatusCode::NO_NEED_TO_WATER);
		}
		$modify=array(
			'health' => $grow_data['health']+1 > 100 ? 100 : $grow_data['health']+1,
			'next_water_time' => $now+GameConstCode::WATER_TIME);
		$this->update($grow_data['gameuid'], $grow_data['data_id'], $modify);
		return $now+GameConstCode::WATER_TIME;
	}
	public function steal($gameuid,$grow_data){
		$data_id=$grow_data['data_id'];
		$friend_gameuid=$grow_data['gameuid'];
		$crop_item = get_xml_def($grow_data['item_id'], XmlDbType::XMLDB_ITEM);

		// 判断植物有没有成熟
		if (!$this->canHarvest($grow_data,$crop_item)) {
			$this->throwException("crop[$data_id] not immatual.", GameStatusCode::IMMATUAL_CROP);
		}
		//获取植物的最小产出
		$min_output=ceil($grow_data['output'] * $crop_item['min_output']);
		if ($grow_data['leavings'] <= $min_output){
			$this->throwException("field[$data_id] of gameuid[$friend_gameuid] nothing to stolen",GameStatusCode::NOTHING_TO_STOLEN);
		}
		$thief = $grow_data['thief'];
		if (in_array($gameuid, $thief)){
			$this->throwException("user[$gameuid] have already stolen field[$data_id]", GameStatusCode::HAS_STOLEN);
		}

		$thief[] = $gameuid;
		$product_item_id=$this->getProductItemId($grow_data['item_id']);
		$product_item_def=get_xml_def($product_item_id,XmlDbType::XMLDB_ITEM);
		$lost_coin = is_caught_by_dog($gameuid,$friend_gameuid, $product_item_def['sale']);
		$lost_strengh = is_caught_by_monster($friend_gameuid, $product_item_def['sale']);
		// 没有被抓住
		if($lost_coin === false && $lost_strengh === false) {
			$stolen_count = 1;
			$modify = array(
				'leavings' => $grow_data['leavings'] - $stolen_count,
				'thief' => $thief, 'update_time' => time());
		} else {
			$modify = array('thief' => $thief,'update_time' => time());
		}
		$this->update($friend_gameuid, $data_id,$modify);
		return array('modify'=>$modify,'lost_coin'=>$lost_coin,'lost_strengh'=>$lost_strengh);
	}
	public function getOutput($gameuid,$crop){
		return 2;
	}
	/**
	 * 获取作物的枯萎时间
	 *
	 * @param int $grownup_time 作物生长的时间段
	 * @return int
	 */
	public function getWiltTime($item_def){
		$time=intval($item_def['rotTime']*3600);
		return $time>GameConstCode::MIN_WITHERED_TIME?$time:GameConstCode::MIN_WITHERED_TIME;
	}
	public function getProductItemId($seed_id){
		$seed_def=get_xml_def($seed_id,XmlDbType::XMLDB_ITEM);
		return intval($seed_def['depend_itemid'])==0?$seed_id:intval($seed_def['depend_itemid']);
	}

/*	//试用可以种出增产道具的道具
	public function setPlantSun($gameuid,$item_def){
		$time=time()+intval($item_def['life_cycle']*3600);
		$value=array('item_id'=>$item_def['item_id'],'expire_time'=>$time,'hitRate'=>$item_def['hitRate'],'upRate'=>$item_def['upRate']);
		$mem_key=sprintf(CacheKey::CACHE_KEY_PLANT_SUN_GET_FLAG,$gameuid);
		$this->deleteFromCache($mem_key,$gameuid);
		$this->setToCache($mem_key,$value,$gameuid,$time);
		return $value;
	}
	public function getPlantSun($gameuid){
		$flag=array();
		$mem_key=sprintf(CacheKey::CACHE_KEY_PLANT_SUN_GET_FLAG,$gameuid);
		$flag=$this->getFromCache($mem_key,$gameuid);
		return $flag;
	}*/
	protected function getTableName(){}
}
?>