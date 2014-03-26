<?php
include_once GAMELIB.'/model/UserFactoryManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class HarvestFormulas extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		
		$fac_mgr = new UserFactoryManager();
		$fac_info = $fac_mgr->getUserFac($gameuid);
		$fac_index = $fac_mgr->getFormulaIndex($gameuid);
		$user_formulas = $fac_info['formulas'];
		$work_time = $fac_info['workTime'];
		
		
		$item_mgr = new UserGameItemManager($gameuid);
		
		if(empty($user_formulas)){
			return TRUE;
		}
		$formulas_arr = explode(":",$user_formulas);
		$total_time = $work_time;
		$startIndex = $fac_index;
		$new_workTime = time();
		$add_items = array();
		$lastCount = count($formulas_arr);
		$result = array();
		foreach ($formulas_arr as $key=>$value){
			$item_xml = get_xml_def($value);
			$reward_item_str = $item_xml['product'];
			$reward_arr = explode(":",$reward_item_str);
			$reward_id = $reward_arr[0];
			$reward_count = $reward_arr[1];
			if($key >= $startIndex){
				if (($total_time + $item_xml['workTime'])<=$new_workTime){
					$item_mgr->addItem($reward_id,$reward_count);
					array_push($add_items,array('id'=>$reward_id,'count'=>$reward_count));
					$total_time += $item_xml['workTime'];
					unset($formulas_arr[$key]);
				}else{
					$new_workTime = $total_time;
					break;
				}
			}else{
				unset($formulas_arr[$key]);
				$item_mgr->addItem($reward_id,$reward_count);
				array_push($add_items,array('id'=>$reward_id,'count'=>$reward_count));
			}
		}
		
		$add_exp = $lastCount-count($formulas_arr);
		$this->user_account_mgr->updateUserExperience($gameuid,$add_exp);
		$new_formula_str = implode(":",$formulas_arr);
		$item_mgr->commitToDB();
		
		$fac_mgr->updateUserFac($gameuid,array('workTime'=>$new_workTime,'formulas'=>$new_formula_str));
		//重置
		$fac_mgr->setFormulaIndex($gameuid,0);
		
		$result['exp'] = $add_exp;
		$fac_info['workTime'] = $new_workTime;
		$fac_info['formulas'] = $new_formula_str;
		$fac_info['workTimeIndex'] = 0;
		$result['formula'] = $fac_info;
		$result['addItems'] =$add_items;
		return $result;
	}
}
?>