<?php
include_once GAMELIB.'/model/UserFactoryManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class AddFormula extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$id = $this->getParam("id",'string');
		
		$fac_mgr = new UserFactoryManager();
		$fac_info = $fac_mgr->getUserFac($gameuid);
		$fac_index = $fac_mgr->getFormulaIndex($gameuid);
		
		$user_formulas = $fac_info['formulas'];
		$work_time = $fac_info['workTime'];
		$fac_expand = $fac_info['expand'];
		$total_expand = GameModelConfig::FACTORY_TILES + $fac_expand;
		//删除 需求
		$item_mgr = new UserGameItemManager($gameuid);
		$item_formula = get_xml_def($id);
		$material = $item_formula['material'];
		$material_arr = explode("|",$material);
		foreach ($material_arr as $m_value){
			$item_m_arr = explode(":",$m_value);
			$item_id = $item_m_arr[0];
			$item_count = $item_m_arr[1];
			$item_mgr->subItem($item_id,$item_count);
		}
		$item_mgr->checkReduceItemCount();
		
		
		$change = array();
		if (empty($user_formulas)){
			$change = array('formulas'=>$id,'workTime'=>time());
		}else {
			$formulas_arr = explode(":",$user_formulas);
			$total_time = 0;
			$startIndex = $fac_index;
			if (count($formulas_arr) >= $total_expand){
				$this->throwException("no more tiles in factory gameuid".$gameuid,GameStatusCode::DATA_ERROR);
			}
			foreach ($formulas_arr as $key=>$value){
				if($key >= $startIndex){
					$item_xml = get_xml_def($value);
					$total_time += $item_xml['workTime'];
				}
			}
			if (($work_time+$total_time) >=time()){
				//不需要改时间和 index
				$change = array('formulas'=>$user_formulas.":".$id);
			}else{
				//改时间和 index
				$change = array('formulas'=>$user_formulas.":".$id,'workTime'=>time());
				$index = count($formulas_arr);
				$fac_mgr->setFormulaIndex($gameuid,$index);
			}
		}
		
		$fac_mgr->updateUserFac($gameuid,$change);
		//提交item
		$item_mgr->commitToDB();
		
		$new_fac_info = $fac_mgr->getUserFac($gameuid);
		$new_fac_info['workTimeIndex'] = $fac_mgr->getFormulaIndex($gameuid);
		$result['formula'] = $new_fac_info;
		
		return $result;
	}
}
?>