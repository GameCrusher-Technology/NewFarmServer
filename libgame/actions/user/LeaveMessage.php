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
			$merge['gameuid'] = $f_gameuid;
			$merge['f_gameuid']=$gameuid;
			$merge['message'] = $message;
			$merge['type'] = $type;
			$merge['data_id'] = $data_id;
			$merge['updatetime'] = time();
			
			return $mes_mgr->addMessage($f_gameuid,$merge);
		}
		
		foreach ($delMes as $delMesInfo){
			$f_gameuid = $delMesInfo["f_gameuid"];
			$data_id = $delMesInfo["data_id"];
			$mes_mgr->delMessage($f_gameuid,$data_id);
		}
		
		
	}
}
?>