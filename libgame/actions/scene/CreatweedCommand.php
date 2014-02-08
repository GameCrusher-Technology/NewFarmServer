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
		
		$weedArr = array(50013,50014,50015,50016,50017,50018,50002,50003,50004,50005,50006,50007,50008);
		$rateArr = array(100,90,80,70,60,50,50,0,40,35,30,0,20);
		
		$key = StaticFunction::getOneByRate($rateArr);
		$item_id = $weedArr[$key];
		$deco = array('data_id'=>$data_id,'positiony'=>$posy,'positionx'=>$posx,"item_id"=>$item_id);
		$deco_mgr->addDeco($gameuid,$deco);
		
		return array("weed"=>$deco);
		
		
	}
}
?>