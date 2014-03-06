<?php
include_once GAMELIB.'/model/FarmDecorationManager.class.php';
class CreatweedCommand extends GameActionBase{
	
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$data_id = $this->getParam("data_id",'int');
		$posx = $this->getParam("x",'int');
		$posy = $this->getParam("y",'int');
		
		$deco_mgr = new FarmDecorationManager();
		
		$lastCreatTime = $deco_mgr->getWeedCacheTime($gameuid);
		if (!empty($lastCreatTime)) {
			$r_time = time()-$lastCreatTime;
			if ($r_time<3600){
				return TRUE;
			}
		}
		$deco_mgr->setWeedCacheTime($gameuid);
		
		$weedArr = array(50013,50014,50015,50016,50017,50018,50002,50003,50004,50005,50006,50007,50008);
		$rateArr = array(100,90,80,70,60,50,50,0,40,35,30,0,20);
		
		$key = StaticFunction::getOneByRate($rateArr);
		$item_id = $weedArr[$key];
		
		
		
		//panduan
		$old_dec = $deco_mgr->getDecoration($gameuid,$data_id);
		if (!empty($old_dec)){
			$decos = $deco_mgr->getDecorations($gameuid);
			$maxId = 1000;
			foreach ($decos as $dec) {
				$maxId = max($maxId,$dec['data_id']);
			}
			$data_id = $maxId++;
		}
		$deco = array('data_id'=>$data_id,'positiony'=>$posy,'positionx'=>$posx,"item_id"=>$item_id);
		$deco_mgr->addDeco($gameuid,$deco);
		return array("weed"=>$deco);
	}
}
?>