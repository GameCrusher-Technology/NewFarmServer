<?php
include_once GAMELIB.'/model/UserMessageManager.class.php';
include_once GAMELIB.'/model/UserFieldDataManager.class.php';
include_once GAMELIB.'/model/UserFriendManager.php';
include_once GAMELIB.'/model/UserPetManager.class.php';
class HelpFriend extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$f_gameuid = $this->getParam("target",'string');
		$speedArr = $this->getParam("speed","array");
		$data_id = $this->getParam("data_id","int");
		
		$fri_mgr = new UserFriendManager();
		$last_help_time = $fri_mgr->getHelpFriendTag($gameuid,$f_gameuid);
		if ((time()- $last_help_time) < 86000){
			$this->throwException($gameuid."has helped friend ,[".$f_gameuid."] ",
					GameStatusCode::DATA_ERROR);
		}
		$fri_mgr->setHelpFriendTag($gameuid,$f_gameuid);
		
		$mes_mgr = new UserMessageManager();
		$type = MethodType::MESSTYPE_HELP ;
		$merge = array();
		$merge['gameuid'] = $f_gameuid;
		$merge['f_gameuid']=$gameuid;
		$merge['type'] = $type;
		$merge['updatetime'] = time();
		$merge['data_id'] = $data_id;
		$mes_mgr->addMessage($f_gameuid,$merge);
		
		if (empty($speedArr)|| count($speedArr)==0){
			$this->user_account_mgr->updateUserStatus($f_gameuid,array('coin'=>100));
		}else{
			$filed_mgr = new UserFieldDataManager();
			foreach ($speedArr as $speed_id){
				$cache_crop = $filed_mgr->get($f_gameuid,$speed_id);
				if (empty($cache_crop)){
					$this->throwException("no field,[".$speed_id."] gameuid:".$f_gameuid,
						"help friend");
				}
				$plant_time = $cache_crop['plant_time'] - GameConstCode::WATER_TIME;
				$modify = array("plant_time"=>$plant_time);
				$filed_mgr->update($f_gameuid, $speed_id, $modify,false);
			}
		}
		
		$pet_Mgr = new UserPetManager();
		$myPet = $pet_Mgr->getPet($gameuid,"100000");
		$dogChange = array();
		if (!empty($myPet)){
			$levelArr = explode("|",$myPet['skillLevel']);
			$searchLevel = 1;
			foreach ($levelArr as $skillStr){
				$skillM = explode(":",$skillStr);
				if($skillM[0] == "110000"){
					$searchLevel = $skillM[1];
					break;
				}
			}
			
			$coinCount = rand(10,200)* $searchLevel;
			
			$friendPet = $pet_Mgr ->getPet($f_gameuid,"100001");
			if(!empty($friendPet)){
				$levelArr = explode("|",$friendPet['skillLevel']);
				$DefenceLevel = 1;
				foreach ($levelArr as $skillStr){
					$skillM = explode(":",$skillStr);
					if($skillM[0] == "110001"){
						$DefenceLevel = $skillM[1];
						break;
					}
				}
				
				$robInt = 50 + ($DefenceLevel - $searchLevel)*5;
				$hasRob = FALSE;
				if (rand(0,100) <= $robInt){
					$this->user_account_mgr->updateUserStatus($f_gameuid,array('coin'=>$coinCount));
					
					$rob_merge = array();
					$rob_merge['gameuid'] = $f_gameuid;
					$rob_merge['f_gameuid']=$gameuid;
					$rob_merge['type'] = MethodType::MESSTYPE_ROBBER;
					$rob_merge['updatetime'] = time();
					$rob_merge['data_id'] = $data_id+1;
					$rob_merge['message'] = $coinCount;
					$mes_mgr->addMessage($f_gameuid,$rob_merge);
		
					$hasRob = TRUE;
				}else{
					$this->user_account_mgr->updateUserStatus($gameuid,array('coin'=>$coinCount));
					$hasRob = FALSE;
				}
				$dogChange = array("coin"=>$coinCount,"rob"=>$hasRob,"step"=>1,"Id"=>$gameuid);
			}else{
				$this->user_account_mgr->updateUserStatus($gameuid,array('coin'=>$coinCount));
				$dogChange = array("coin"=>$coinCount,"rob"=>FALSE,"step"=>2);
			}
		}
		
		$this->user_account_mgr->updateUserStatus($gameuid,array('love'=>1));
		
		return array("dog"=>$dogChange);
		
	}
	
}
?>