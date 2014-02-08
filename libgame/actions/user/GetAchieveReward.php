<?php
include_once GAMELIB.'/model/UserActionCountManager.class.php';
class GetAchieveReward extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$id = $this->getParam("id",'int');
		$step = $this->getParam("step",'int');
		$account = $this->user_account_mgr ->getUserAccount($gameuid);
		
		$achieve = $account['achieve'];
		
		if(empty($achieve)){
			$achieve = $this->user_account_mgr->creatAchieveInfo();
		}
		$achieveArrs = explode("|",$achieve);
		$crop_achieves = $achieveArrs[0];
		$tree_achieves = $achieveArrs[1];
		
		$achieve_def = get_xml_def($id,XmlDbType::XMLDB_ITEM);
		if($achieve_def['type']=='Crop'){
			$achieve_index = $id - 30000;
			if($achieve_index >= strlen($crop_achieves)){
				throw $this->throwException("gameuid:".$gameuid." not this achieve",
					GameStatusCode::DATA_ERROR);
			}
			$curLevel = substr($crop_achieves,$achieve_index,1);
			$crop_achieves = substr_replace($crop_achieves,$step,$achieve_index,1);
		}elseif ($achieve_def['type']=='Tree'){
			$achieve_index = $id - 34000;
			if($achieve_index >= strlen($tree_achieves)){
				throw $this->throwException("gameuid:".$gameuid." not this achieve",
					GameStatusCode::DATA_ERROR);
			}
			$curLevel = substr($tree_achieves,$achieve_index,1);
			$tree_achieves = substr_replace($tree_achieves,$step,$achieve_index,1);
		}
		
		if($step <= $curLevel){
			throw $this->throwException("gameuid:".$gameuid." has accept this achieve",
				GameStatusCode::DATA_ERROR);
		}
		//审核 是否达到
		$action_mgr = new UserActionCountManager();
		$achieve_count_info = $action_mgr->getEntry($gameuid,$id);
		if(empty($achieve_count_info)){
			$achieve_count_info = array("action_id"=>$id,"count"=>0);
		}
		$countArr = explode("|",$achieve_def['levels']);
		$needcount = $countArr[$step-1];
		
		if($achieve_count_info["count"]<$needcount){
			throw $this->throwException("gameuid:".$gameuid."achieve id:".$id." count is not enough",
				GameStatusCode::DATA_ERROR);
		}
		$change = array();
		$reward_arr = explode("|",$achieve_def['rewards']);
		$change['gem'] = $reward_arr[$step-1];
		$change['achieve'] = $crop_achieves."|".$tree_achieves;
		$this->user_account_mgr->updateUserStatus($gameuid,$change);
		return $change;
	}
}
?>