<?php
if(!defined('IN_GAME')) exit('Access Denied');

require_once ADMIN_ROOT.'/modules/AdminManager.class.php';
require_once GAMELIB.'/extends/SystemStatistics.class.php';

$columns = SystemStatistics::$column;

$AdminManager = new AdminManager();
$op = trim($_REQUEST['op']);
try{
	$monthes = $AdminManager->getDbInstance()->getAll('select distinct DATE_FORMAT(stat_date, "%Y-%m") as month from system_statistics order by stat_date desc');
	$dates = $AdminManager->getDbInstance()->getAll('select distinct stat_date from retention_detail order by stat_date desc');
	array_unshift($dates,array('stat_date'=>date("Y-m-d")));

	if($op == "stat_month" || $op == "stat_all"){
		$search_month = getGPC('search_month','string');
		if($op == "stat_month" && $search_month == "")
			throw new Exception("请输入查找的月份");
			
//		$sql = 'SELECT * FROM system_statistics';
//		if($op == "stat_month")
//			$sql .=  ' where DATE_FORMAT(stat_date, "%Y-%m") = "'.$search_month.'"';
//		$sql .= ' order by stat_date asc';
//		$search_data = $AdminManager->getDbInstance()->getAll($sql);
		
		$sql = 'SELECT stat_date,col,count FROM system_statistics';
		if($op == "stat_month")
			$sql .=  ' where DATE_FORMAT(stat_date, "%Y-%m") = "'.$search_month.'"';
		$sql .= ' order by stat_date asc';
		$search_data1 = $AdminManager->getDbInstance()->getAll($sql);
		
		$search_data = array();
		foreach($search_data1 as $d){
			if(!isset($search_data[$d['stat_date']]))
				$search_data[$d['stat_date']]['stat_date'] = $d['stat_date'];
			$search_data[$d['stat_date']][$d['col']] = $d['count'];
		}
		
		$lines = array();
		$js_lines = "";
		$js_parm = "";
		$js_parm1 = "";
		$js_label = "";
		$sum_user = 0;
		
		$show_all = true;
		foreach ($columns as $tmp){
			if(isset($_POST[$tmp]))
				$show_all = false;
		}
		
		$oa = 0;
		$ta = 0;
		foreach($search_data as $data){
			if($oa == 0)
				$oa = $data['install'];
			if($ta == 0)
				$ta = $oa;
			foreach($data as $k=>$v){
				if($k == "stat_date" || $k == "update_time")
					continue;
				if(!isset($lines[$k]))
					$lines[$k] = "[";
					
				if($k == 'retention'){
					$v = sprintf("%0.2f",$v*100/$oa);
				}
				$lines[$k] .= "['".$data['stat_date']."',".$v."],";
			}
//			if(!isset($lines['install_all']))
//					$lines['install_all'] = "[";
			$sum_user += intval($data['install']);
//			$lines['install_all'] .= "['".$data['stat_date']."',".$sum_user."],";
			$ta = $oa;
			$oa = $data['install'];
		}
		
		foreach($lines as $col=>&$line){
			if(!$show_all && !isset($_POST[$col]))
				continue;
			$line = rtrim($line,",") . "]";
			$js_lines .= "stat_$col = $line; \r\n";
			$js_parm .= "stat_$col,";
			if($col == 'activity' || $col == 'install_all')
				$js_label .= "{label:'".$col."',yaxis:'y2axis'},";
			elseif($col == 'retention')
				$js_label .= "{label:'".$col."',yaxis:'y3axis'},";
			else
				$js_label .= "{label:'".$col."'},";
		}
		$js_parm = rtrim($js_parm,",");
		$js_label = rtrim($js_label,",");
		
		
	}
	
	if($op == "stat_today"){
		$columns = SystemStatistics::$column;
		$keys = array();
		foreach ($columns as $tmp){
			$keys[] = SystemStatistics::$prefix . $tmp;
		}
		$search_data = array();
		$result = $AdminManager->getCacheInstance()->get($keys);
		foreach ($columns as $tmp){
			if(isset($result[SystemStatistics::$prefix.$tmp]))
				$search_data[$tmp] = $result[SystemStatistics::$prefix.$tmp];
			else
				$search_data[$tmp] = 0;
		}
	}
	
	if($op == "retention_detail"){
		$search_date = getGPC('search_date','string');
		if($search_date == date("Y-m-d")){
			for($i=0;$i<100;$i++){
				$key2 = SystemStatistics::$prefix . "retention_2"."_".$i;
				$count2 = $AdminManager->getCacheInstance()->get($key2);
				
				if($count2 > 0){
					$search_data['yestoday'][] = array('level'=>$i,'count'=>$count2);
				}
			}
			
			$s_date = date("Y-m-d",strtotime($search_date)-86400);
			$sql  = "select stat_date,type,level,count from retention_detail WHERE stat_date = '$s_date' and type = 1";
			$data = $AdminManager->getDbInstance()->getAll($sql);
			foreach ($data as $tmp){
				if($tmp['type'] == 1)
					$search_data['today'][] = array('level'=>$tmp['level'],'count'=>$tmp['count']);
			}
			
		}else{
			$s_date = date("Y-m-d",strtotime($search_date)-86400);
			$sql  = "select stat_date,type,level,count from retention_detail WHERE (stat_date = '$search_date' AND type = 2)
			 OR (stat_date = '$s_date' AND type = 1)";
			$data = $AdminManager->getDbInstance()->getAll($sql);
			foreach ($data as $tmp){
				if($tmp['type'] == 1)
					$search_data['today'][] = array('level'=>$tmp['level'],'count'=>$tmp['count']);
				if($tmp['type'] == 2)
					$search_data['yestoday'][] = array('level'=>$tmp['level'],'count'=>$tmp['count']);
			}
			
		}
	}
}catch(exception $e){
	$error_msg = $e->getMessage();
}


?>