<?php
include_once GAMELIB.'/model/UserMessageManager.class.php';
class LeaveMessage extends GameActionBase{
	protected function _exec()
	{
		//在好友家 留言
		$addMes = $this->getParam("addMes",'array');
		$delMes = $this->getParam("delMes",'array');
		
		$mes_mgr = new UserMessageManager();
		foreach ($addMes as $mesInfo){
			$gameuid = $mesInfo["gameuid"];
			$f_gameuid = $mesInfo["f_gameuid"];
			$message = $mesInfo["message"];
			$type = $mesInfo["type"];
			$data_id = $mesInfo["data_id"];
			
			$merge = array();
			$merge['gameuid'] = $gameuid;
			$merge['f_gameuid']=$f_gameuid;
			$merge['message'] = $message;
			$merge['type'] = $type;
			$merge['data_id'] = $data_id;
			$merge['updatetime'] = time();
			
			$mes_mgr->addMessage($gameuid,$merge);
		}
		foreach ($delMes as $delMesInfo){
			$gameuid = $delMesInfo["gameuid"];
			$data_id = $delMesInfo["data_id"];
			$mes_mgr->delMessage($gameuid,$data_id);
		}
		
		return TRUE;
	}
}
?>