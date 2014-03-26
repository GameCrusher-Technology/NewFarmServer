<?php
include_once GAMELIB.'/model/UserFactoryManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class OpenFormula extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$method = $this->getParam("method",'int');
		$item_id = $this->getParam("id",'string');
		
		$user_info = $this->user_account_mgr->getUserAccount($gameuid);
		$fac_mgr = new UserFactoryManager();
		$fac_info = $fac_mgr->getUserFac($gameuid);
		$owned_formulas = $fac_info['other'];
		
		$id = intval($item_id) - 72000;
		$cur_item = get_xml_def($item_id);
		if(empty($cur_item)){
			$this->throwException("no next extend id".$id."gameuid :".$gameuid,GameStatusCode::DATA_ERROR);
		}
		if ($cur_item['type'] != 'special'){
			$this->throwException("formula can not buy ".$id."gameuid :".$gameuid,GameStatusCode::DATA_ERROR);
		}
		$new_formulasArr = array();
		if (!empty($owned_formulas)){
			$new_formulasArr = explode(":",$owned_formulas);
			if (in_array($id,$new_formulasArr)){
				$this->throwException("already has formula ".$id."gameuid :".$gameuid,GameStatusCode::DATA_ERROR);
			}
		}
		array_push($new_formulasArr,$id);
		$new_formulaStr = implode(":",$new_formulasArr);
		$change =array();
		$fac_change = array('other'=>$new_formulaStr);
		if($method == MethodType::METHOD_COIN){
			$player_level = StaticFunction::expToGrade($user_info['exp']);
			if($cur_item['level'] > $player_level){
				$this->throwException("coin buy extend need level".$cur_item['level'],GameStatusCode::DATA_ERROR);
			}
			if($user_info['coin']<$cur_item['coinPrice']){
				$this->throwException("coin buy extend need coin".$cur_item['coinPrice'],GameStatusCode::DATA_ERROR);
			}
			$change['coin'] = - $cur_item['coinPrice'];
		}else{
			if($user_info['gem']<$cur_item['gemPrice']){
				$this->throwException("gem buy extend need gem".$cur_item['gemPrice'],GameStatusCode::DATA_ERROR);
			}
			$change['gem'] =  - $cur_item['gemPrice'];
		}
		$this->user_account_mgr->updateUserStatus($gameuid,$change);
		$fac_mgr->updateUserFac($gameuid,$fac_change);
		
		$result = array();
		$new_fac_info = $fac_mgr->getUserFac($gameuid);
		$new_fac_info['workTimeIndex'] = $fac_mgr->getFormulaIndex($gameuid);
		$result['formula'] = $new_fac_info;
		
		return $result;
		
	}
}
?>