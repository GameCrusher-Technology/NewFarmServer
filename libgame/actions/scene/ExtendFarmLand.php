<?php
class ExtendFarmLand extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$method = $this->getParam("method",'int');
		
		$user_info = $this->user_account_mgr->getUserAccount($gameuid);
		$cur_extend = $user_info['crop_extend'];
		
		$next_extend = get_xml_def(41000+$cur_extend+1);
		if(empty($next_extend)){
			$this->throwException("no next extend id".$next_extend."gameuid :".$gameuid,GameStatusCode::DATA_ERROR);
		}
		
		$change =array();
		if($method == MethodType::METHOD_COIN){
			$player_level = StaticFunction::expToGrade($user_info['exp']);
			if($next_extend['level'] > $player_level){
				$this->throwException("coin buy extend need level".$next_extend['level'],GameStatusCode::DATA_ERROR);
			}
			$change['crop_extend'] = $cur_extend+1;
			if($user_info['coin']<$next_extend['coinPrice']){
				$this->throwException("coin buy extend need coin".$next_extend['coinPrice'],GameStatusCode::DATA_ERROR);
			}
			$change['coin'] = - $next_extend['coinPrice'];
		}else{
			$change['crop_extend'] = $cur_extend+1;
			if($user_info['gem']<$next_extend['gemPrice']){
				$this->throwException("gem buy extend need gem".$next_extend['gemPrice'],GameStatusCode::DATA_ERROR);
			}
			$change['gem'] =  - $next_extend['gemPrice'];
		}
		$this->user_account_mgr->updateUserStatus($gameuid,$change);
		
		return TRUE;
	}
}
?>