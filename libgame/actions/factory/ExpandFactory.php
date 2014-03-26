<?php
include_once GAMELIB.'/model/UserFactoryManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class ExpandFactory extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$method = $this->getParam("method",'int');
		
		$user_info = $this->user_account_mgr->getUserAccount($gameuid);
		
		$fac_mgr = new UserFactoryManager();
		$fac_info = $fac_mgr->getUserFac($gameuid);
		
		$cur_extend = $fac_info['expand'];
		
		$next_extend = get_xml_def(42000+$cur_extend+1);
		if(empty($next_extend)){
			$this->throwException("no next extend id".$next_extend."gameuid :".$gameuid,GameStatusCode::DATA_ERROR);
		}
		
		$change =array();
		$fac_change = array();
		if($method == MethodType::METHOD_COIN){
			$player_level = StaticFunction::expToGrade($user_info['exp']);
			if($next_extend['level'] > $player_level){
				$this->throwException("coin buy extend need level".$next_extend['level'],GameStatusCode::DATA_ERROR);
			}
			$fac_change['expand'] = $cur_extend+1;
			if($user_info['coin']<$next_extend['coinPrice']){
				$this->throwException("coin buy extend need coin".$next_extend['coinPrice'],GameStatusCode::DATA_ERROR);
			}
			$change['coin'] = - $next_extend['coinPrice'];
		}else{
			$fac_change['expand'] = $cur_extend+1;
			if($user_info['gem']<$next_extend['gemPrice']){
				$this->throwException("gem buy extend need gem".$next_extend['gemPrice'],GameStatusCode::DATA_ERROR);
			}
			$change['gem'] =  - $next_extend['gemPrice'];
		}
		$this->user_account_mgr->updateUserStatus($gameuid,$change);
		$fac_mgr->updateUserFac($gameuid,$fac_change);
		return TRUE;
		
	}
}
?>