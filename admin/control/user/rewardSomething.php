<?php
	if(!defined('IN_GAME')) exit('Access Denied');
	global $limitvalue, $admin_logger;
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
	set_time_limit(0);
	if ($limitvalue!=1&&$limitvalue!=2){
			$error_msg = "没有执行权限。";
			return;
	}
	
	$password=$_POST['password'];
	$reward_uid_from_file=$_POST['reward_uid_from_file'];
	$reward_uid_from_page=$_POST['reward_uid_from_page'];
	$operator=$_POST['operator'];
	$item_with_count_list=$_POST['item_with_count_list'];
	
	try {
		if (!empty($password)&&!empty($item_with_count_list)){
			$today = date("Ymd"); 
			if(intval($password)!=intval($today)){
				echo "password error";
				die();
			}
			
			if(empty($operator)){
				echo "请填写操作人一栏";
				die();
			}
			
			if(empty($reward_uid_from_file)&&empty($reward_uid_from_page)){
				echo "无法获取奖励用户的uid";
				die();
			}	
			
			$log_file=$today."_reward.log";
			$log_fp=fopen($log_file, 'a');
			if(empty($log_fp)){
				die("cannot open log file");
			}
			
			if (!defined('APP_ROOT')) define('APP_ROOT',realpath(dirname(__FILE__)));
			if (!defined('GAMELIB')) define('GAMELIB', APP_ROOT . '/libgame');
			if (!defined('FRAMEWORK')) define('FRAMEWORK', APP_ROOT . '/framework');

			require_once GAMELIB . '/config/GameConfig.class.php';
			require_once FRAMEWORK . '/log/LogFactory.class.php';
			require_once FRAMEWORK . '/db/RequestFactory.class.php';
			require_once GAMELIB . '/GameConstants.php';
			require_once GAMELIB.'/model/ManagerBase.class.php';
			require_once GAMELIB.'/model/UserAccountManager.class.php';
			require_once GAMELIB.'/model/UidGameuidMapManager.class.php';
			require_once GAMELIB.'/model/UserGameItemManager.class.php';
			require_once GAMELIB.'/model/TradeLogManager.class.php';
			require_once GAMELIB.'/common.func.php';
			
			//$reward=array("$reward_item_id"=>$reward_count,"coupon"=>10);
			
			if(!empty($item_with_count_list)){
				$item_array_index=0;
				$colon ="/[:]/";
				$enter="/[\n\r]/";
				while(true){
					$temp_item_array=preg_split($enter, $item_with_count_list,2);
					$temp_item_array[0]=trim($temp_item_array[0]);
					$temp_item_array[1]=trim($temp_item_array[1]);
					$item_with_count_array[$item_array_index]=preg_split($colon,$temp_item_array[0],2);			
					if(!empty($temp_item_array[1])){
						$item_with_count_list=$temp_item_array[1];
						$item_array_index++;
					}else{
						break;
					}			
				}
			}
			foreach($item_with_count_array as $item_with_count_index => $subarr){
				$reward_array[$item_with_count_index]=array("$subarr[0]"=>$subarr[1],"coupon"=>10);
			}
			
			if(!empty($reward_uid_from_page)){
				$list_index_1=0;
				$enter="/[\n\r]/";
				$splitter="/[\n\r\t ]/";
				while(true){
					$temp_reward_uid_array=preg_split($enter, $reward_uid_from_page,2);
					$temp_reward_uid_array[0]=trim($temp_reward_uid_array[0]);
					$temp_reward_uid_array[1]=trim($temp_reward_uid_array[1]);
					$temp_uid_array=preg_split($splitter, $temp_reward_uid_array[0],2);
					reward($list_index_1,$temp_uid_array[0],$reward_array,$log_fp,$operator);			
					if(!empty($temp_reward_uid_array[1])){
						$reward_uid_from_page=$temp_reward_uid_array[1];
						$list_index_1++;
					}else{
						break;
					}			
				}
				$list_index_2=$list_index_1+1;
			}else{
				$list_index_2=0;
			}
			
			if(!empty($reward_uid_from_file)){
				$reward_uid_file=$reward_uid_from_file.'.dat';
				$data_fp=fopen(APP_ROOT.'/admin/'.$reward_uid_file,'r');
				if(empty($data_fp)){
					die("cannot open data file");
				}
				$pattern="/[\n\r\t ]/";			
				while(!feof($data_fp)){
					$each_line=fgets($data_fp);
					$each_line=trim($each_line);
					if(!empty($each_line)){
						$tmp_arr=preg_split($pattern, $each_line);
						if($tmp_arr!=null&&count($tmp_arr)>0){
							reward($list_index_2,$temp_uid_array[0],$reward_array,$log_fp,$operator);
						}
						$list_index_2++;
					}
				}
				fclose($data_fp);
			}
			echo 'end';
			fclose($log_fp);
		}
	}catch (Exception $e){
		$error_msg = $e->__toString();
		$admin_logger->writeError("exception while reward \n".$e->getTraceAsString());
	}
	function reward($line_index,$uid,$reward_array,$log_fp,$operator){
				$uid=trim($uid);				
				$line_number=$line_index+1;
				if(!empty($uid)){
					try {
						$gameuid_mapper=new UidGameuidMapManager();
						$account_mgr=new UserAccountManager();
						$gameuid=$gameuid_mapper->getGameuid($uid);
						if(!empty($gameuid)){
							$change=array();
							$user_item_mgr=new UserGameItemManager($gameuid);
							foreach ($reward_array as $sub_reward_array){
								foreach ($sub_reward_array as $item_id=>$count){
									if(intval($item_id)!=0){
										$user_item_mgr->addItem($item_id, intval($count), "extra_add");
										fwrite($log_fp, "line ".$line_number."\tuid: ".$uid."\t\tReward Item id: ".$item_id."\tReward Count:".$count."\t Done by ".$operator."\r\n");
									}else{
										$change[$item_id]=intval($change[$item_id])+intval($count);
									}
								}
							}
							$user_item_mgr->commitToDB();							
							if(count($change)>0){
								$account_mgr->updateUserStatus($gameuid, $change);
							}
						}else{
							fwrite($log_fp, "line ".$line_number."\tuid: ".$uid."\t\tThe gameuid not exist."."\r\n");
						}
					}catch (Exception $e){
						fwrite($log_fp, "line ".$line_number."\terror: ".$e->getMessage()."\r\n");
					}
				}else{
					fwrite($log_fp, "line ".$line_number."\tuid: empty"."\r\n");				
				}
	}
?>