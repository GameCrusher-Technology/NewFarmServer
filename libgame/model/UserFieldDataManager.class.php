<?php
include_once GAMELIB.'/model/UserGrowableItemManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class UserFieldDataManager extends UserGrowableItemManager 
{
	protected $waste_modify=array (
				"item_id"=>0, "item_type"=>0, "status"  =>-1,
				"leavings" =>0, "output" =>0, "min_output"=>0,
				'plant_time'=>0, 'grownup_time'=>0, 'pest'=>0,
				'weed'=>0, 'thief' =>'', 'harvest_time' => '',
				'reserve_1'=>0);
	protected $hoe_status=array(
			'item_id' => 0, 'item_type' => 0, 'grownup_time' => 0,
			'thief' => '', 'weed' => 0, 'pest' => 0, 'output' => 0,
			'leavings' => 0, 'min_output'=>0, 'plant_time' => 0,
			'is_good' => 0, 'given_gameuid' => '', 'status' => -1,
			'harvest_time'=>'','reserve_1'=>0);
	public function loadFarm($gameuid){
		// 获得用户拥有的田地
		$user_fields = $this->getList($gameuid);
		if (empty($user_fields)) return array();
		return $user_fields;
	}
	
	private function getStage($field_data) {
		$item = get_xml_def($field_data['item_id'], XmlDbType::XMLDB_ITEM);
		$plant_time = $field_data['plant_time'];
		$now = time();
		$time_passed = $now - $plant_time;
		$time_remain = $field_data['grownup_time'] - $time_passed;
		if($time_remain <= 0){
			return 5;
		}
		$detail = $item['detail'];
		$total_stages = count($detail);
		$current_stage = 1;
		for($i = $total_stages - 1;$i >= 0; --$i) {
			$stage_time = $detail[$i] * 3600;
			if($time_remain - $stage_time <= 0){
				$current_stage = $i;
				break;
			}
			else{
				$time_remain -= $stage_time;
			}
		}
		return $current_stage;
	}
	
	public function getOutput($gameuid,$crop){
		$output=parent::getOutput($gameuid,$crop);
		if ($crop['item_type']==ItemType::ITEM_TYPE_CROP){
			//获取植物的生长跨度,注意解决作物生长周期跨年的bug
			$harvest_time = time()+intval($crop['life_cycle'] * 3600);
			$day_count=date('z',$harvest_time)-date('z')+1;
			if ($day_count<0) $day_count+=366;
			//获取植物的生长周期内的天气变化
			include_once GAMELIB.'/model/UserAccountManager.class.php';
			$acc_mgr=new UserAccountManager();
			$weathers=$acc_mgr->refreshWeather($gameuid);
			$effect=0;
			for ($j=0;$j<$day_count;$j++){
				$effect+=weather::getWeatherEffect($weathers[$j],$crop['weather_effect']);
			}
			$effect=$effect/$day_count;
			$output= ceil($output*$effect);
		}
		return $output;
	}
	public function cureCrop($field,&$modify){
		if (empty($field)||empty($field['item_id'])||$field['reserve_1']==0) return false;
		$crop_def=get_xml_def($field['item_id'],XmlDbType::XMLDB_ITEM);
		//判断作物是否已经成熟
		$can_harvest=$this->canHarvest($field,$crop_def);
		if (!$can_harvest) return false;
		$now=time();
		$withered_time=$this->getWiltTime($crop_def);
		
		$modify['reserve_1']=$withered_time+$now;
		
		return true;
	}
	public function harvestCrop($field,&$modify,&$items,&$result){
		if (empty($field)||empty($field['item_id'])) return false;
		$crop_def=get_xml_def($field['item_id'],XmlDbType::XMLDB_ITEM);
		//判断作物是否已经成熟
		$can_harvest=$this->canHarvest($field,$crop_def);
		if (!$can_harvest) return false;
		$harvest_times = $field['harvest_time'];
		$now = time();
		//检查作物是否已经枯萎
		if ($field['reserve_1']>0&&$field['reserve_1']<$now) return false;
		
		if($crop_def['sub_type'] > 1 
			&& count($harvest_times)+1 < $crop_def['sub_type']){
			// 多季作物
			$harvest_times[] = $now;
			$relife_cycle = intval($crop_def['relife_cycle']*3600);
			$modify1=$this->getPlantModify($relife_cycle,$field['output'],$crop_def);
			$modify1['harvest_time']=$harvest_times;
		}else{
			// 单季作物
			$modify1 = $this->waste_modify;
		}
		$items=array('item_id'=>$crop_def['depend_itemid'],'count'=>intval($items['count'])+$field['leavings']);
		$modify=array_merge($modify,$modify1);
		
		$result['experience']+=$crop_def['experience'];
		return true;
	}
	public function hoeCrop($field,&$modify){
		if (empty($field)||$field['status']==0) return false;
		$modify1=$this->hoe_status;
		$modify=array_merge($modify,$modify1);
		return true;
	}
	public function waterCrop($field,&$modify,$friend_gameuid){
		if (empty($field)||empty($field['item_id'])) return false;
		$now = time();
		//检查作物是否已经枯萎
		if ($field['reserve_1']>0&&$field['reserve_1']<$now) return false;
		$crop_def=get_xml_def($field['item_id'],XmlDbType::XMLDB_ITEM);
		
		//判断作物是否已经成熟
		$can_harvest=$this->canHarvest($field,$crop_def);
		if (!$can_harvest){
			$friend_skill_level=get_current_level($friend_gameuid,JobConst::JOB_CAREFIELD,KeyInLevel::KEY_JOB);
			$id = 5000 + JobConst::JOB_CAREFIELD * 10 + $friend_skill_level+90000;
			$action_def=get_xml_def($id,XmlDbType::XMLDB_ITEM);
			$fertilize_time=intval($action_def['life_cycle']*3600);
			$modify['grownup_time']=($field['grownup_time']-$fertilize_time)>0?($field['grownup_time']-$fertilize_time):0;
		}
		return true;
	}
	
	
	
	//使用道具增加爆出体力值和双倍产物的几率的道具
	public function setHarvestRand($gameuid,$item_def){
		$time=time()+intval($item_def['life_cycle']*3600+30);
		$value=array('item_id'=>$item_def['item_id'],'expire_time'=>$time,'hitRate'=>$item_def['hitRate']);
		$mem_key=sprintf(CacheKey::CACHE_KEY_HARVEST_RAND_GET_FLAG,$gameuid);
		$this->deleteFromCache($mem_key,$gameuid);
		$this->setToCache($mem_key,$value,$gameuid,$time);
		return $value;
		
	}
	
	public function getHarvestRand($gameuid){
		$flag=array();
		$mem_key=sprintf(CacheKey::CACHE_KEY_HARVEST_RAND_GET_FLAG,$gameuid);
		$flag=$this->getFromCache($mem_key,$gameuid);
		return $flag;
	}
	
	
	
	public function delPlantsun($gameuid){
		$mem_key=sprintf(CacheKey::CACHE_KEY_PLANT_SUN_GET_FLAG,$gameuid);
		$this->deleteFromCache($mem_key,$gameuid);
	}

	
	public function add_item($gameuid,$item_id,$hammer_def){
		$result=array();
		$special_arr=array();
		$item_def=get_xml_def($item_id,XmlDbType::XMLDB_ITEM);
		$user_item=new UserGameItemManager($gameuid);
		if(!empty($item_def['cost'])){
			$result['coin']=$item_def['cost'];
			$user_pick->setPickUpCache($gameuid,'coin');
		}
		if(!empty($item_def['coupon'])){
			$result['coupon']=$item_def['coupon'];
		}
		if(!empty($item_def['special_bind'])){
			$special_bind=explode(',',$item_def['special_bind']);
			foreach ($special_bind as $k=>$v){
				$special=explode(':',$v);
				$rand=!empty($hammer_def)?$special[1]+$hammer_def['hitRate']:$special[1];
				if(can_happen_random_event($rand)){
					$user_item->addItem($special[0],1);
					$user_item->commitToDB();
					$arr=array('item_id'=>$special[0],'count'=>1);
					array_push($special_arr,$arr);
				}
			}
			if(!empty($special_arr)){
				$result['add_items']=$special_arr;
			}
		}
		return $result;
	}
	protected function getTableName() {
		return "user_data";
	}
	
}
?>