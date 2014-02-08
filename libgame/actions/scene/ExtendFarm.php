<?php
class ExtendFarm extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$method = $this->getParam("method",'int');
		
		$user_info = $this->user_account_mgr->getUserAccount($gameuid);
		$cur_extend = $user_info['extend'];
		
		$next_extend = get_xml_def(40000+$cur_extend+1);
		if(empty($next_extend)){
			$this->throwException("no next extend id".$next_extend."gameuid :".$gameuid,GameStatusCode::DATA_ERROR);
		}
		
		$change =array();
		if($method == MethodType::METHOD_COIN){
			$player_level = StaticFunction::expToGrade($user_info['exp']);
			if($next_extend['level'] > $player_level){
				$this->throwException("coin buy extend need level".$next_extend['level'],GameStatusCode::DATA_ERROR);
			}
			$change['extend'] = $cur_extend+1;
			$change['coin'] =  - $next_extend['coinPrice'];
		}else{
			$change['extend'] = $cur_extend+1;
			$change['gem'] = - $next_extend['gemPrice'];
		}
		
		$this->user_account_mgr->updateUserStatus($gameuid,$change);
		
		return TRUE;
	}
}
?>