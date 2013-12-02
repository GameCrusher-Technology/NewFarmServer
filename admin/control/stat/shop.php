<?php
if(!defined('IN_GAME')) exit('Access Denied');

require_once ADMIN_ROOT.'/modules/AdminManager.class.php';

$AdminManager = new AdminManager();

$op = trim($_REQUEST['op']);

require_once GAMELIB.'/model/XmlManager.class.php';
$item_handler = new XmlManager();
$item_list = $item_handler->getList('item');
$task_list = $item_handler->getList('task');

try{
	$dates = $AdminManager->getDbInstance()->getAll('select distinct stat_date from shop_statistics order by stat_date desc');
	//每日统计
	if($op == "search_daily"){
		$search_date = getGPC('search_date','string');
		$d_index = get_date_index($dates,$search_date);
		$search_date_end = getGPC('search_date_end','string');
		$d_index_end = get_date_index($dates,$search_date_end);
		if($d_index === false||$d_index_end===false||$d_index<$d_index_end){
			$error_msg = "没有记录111";
		}else{
			$sql = "SELECT * FROM shop_statistics 
				WHERE (stat_date BETWEEN '$search_date' AND '$search_date_end')";
			if($_POST['stat_pay'] == '1')
				$sql = "SELECT * FROM shop_statistics_pay 
					WHERE (stat_date BETWEEN '$search_date' AND '$search_date_end')";
			
			$sort_field = get_sort_field();
			$sort_order = 'desc';
			if(isset($_POST['sort_order'])){
				$sort_order = 'asc';
			}
			$sql .= " order by $sort_field $sort_order";
			$search_data = $AdminManager->getDbInstance()->getAll($sql);
			
			set_task_name($search_data,$task_list);
			set_item_name($search_data,$item_list);
//			set_package_name($search_data);

			//画统计图
//			$pie_credits = "[";
//			$pie_cash = "[";
//			foreach($search_data as $d){
//				if($d['buy_method'] == "c")
//					$pie_credits .= "['".($d['name']==""?$d['item_id']:addslashes($d['name']))."',".$d['count']."],";
//				else
//					$pie_cash .= "['".($d['name']==""?$d['item_id']:addslashes($d['name']))."',".$d['count']."],";
//			}
//			$pie_credits = rtrim($pie_credits,",") . "]";
//			$pie_cash = rtrim($pie_cash,",") . "]";
		}
	}
	
	//当日统计
	if($op == "search_today"){
		$prefix = "ranch_buy_item_stat_";
		if($_POST['stat_pay'] == '1')
			$prefix = "ranch_pay_item_stat_";
		$keys = array();
		foreach($item_list as $item){
			$keys[] = $prefix."m".$item['item_id'];
			$keys[] = $prefix."c".$item['item_id'];
			$keys[] = $prefix."d".$item['item_id'];
			$keys[] = $prefix."t".$item['item_id'];
		}
		$result = $AdminManager->getCacheInstance()->get($keys);
		$search_data = array();
		$date = date("Y-m-d");
		$sort_keys = array();
		$amount_sum = 0;
		foreach($item_list as $item){
			if(isset($result[$prefix."m".$item['item_id']])){
				$amount = 0;
				
				$amount = $item['money'] * $result[$prefix."m".$item['item_id']];
//				if(isset($item['expire_time'])){
//					$saleEndTime = strtotime($item['expire_time']);
//					if($saleEndTime >= strtotime($date) && isset($item['price3']))
//						$amount = $item['price3'] * $result[$prefix."m".$item['item_id']];
//				}
				
				$search_data[] = array('stat_date'=>$date,'item_id'=>$item['item_id'],
				'count'=>$result[$prefix."m".$item['item_id']],'is_packs'=>0,'buy_method'=>"<font color=\"#0B6138\">农币</font>",'name'=>$item['name'],'amount'=>$amount);
				$sort_keys[] = $amount;
				$amount_sum += $amount;
			}
			if(isset($result[$prefix."c".$item['item_id']])){
				//$amount = $result[$prefix."c".$item['item_id']] * $item['price'];
				$amount = 0;
				$search_data[] = array('stat_date'=>$date,'item_id'=>$item['item_id'],
				'count'=>$result[$prefix."c".$item['item_id']],'is_packs'=>0,'buy_method'=>"<font color=\"#868A08\">金币</font>",'name'=>$item['name'],'amount'=>$amount);
				$sort_keys[] = $amount;
				$amount_sum += $amount;
			}
			if(isset($result[$prefix."d".$item['item_id']])){
				//$amount = $result[$prefix."c".$item['item_id']] * $item['price'];
				$amount = 0;
				$search_data[] = array('stat_date'=>$date,'item_id'=>$item['item_id'],
				'count'=>$result[$prefix."d".$item['item_id']],'is_packs'=>0,'buy_method'=>"<font color=\"#FF00FF\">点券</font>",'name'=>$item['name'],'amount'=>$amount);
				$sort_keys[] = $amount;
				$amount_sum += $amount;
			}
		}
		foreach($task_list as $item){
			if(isset($result[$prefix."t".$item['task_id']])){
				//$amount = $result[$prefix."c".$item['item_id']] * $item['price'];
				$amount = 0;
				$search_data[] = array('stat_date'=>$date,'item_id'=>$item['task_id'],
				'count'=>$result[$prefix."t".$item['task_id']],'is_packs'=>0,'buy_method'=>"<font color=\"#0000C6\">任务</font>",'name'=>$item['cname'],'amount'=>$amount);
				$sort_keys[] = $amount;
				$amount_sum += $amount;
			}
		}
		array_multisort($sort_keys,SORT_NUMERIC,SORT_DESC,$search_data);
	}
	
	//临时统计
	if($op == "search_bylevel" || $op == "clear_search_bylevel"){
		$keys = array();
		$type = trim($_POST['level_type']);
		for($i=0;$i<100;$i++){	
			$keys[] = 'ranch_'.$type.'_reset_count_level'.$i;
		}
		foreach($item_list as $item){
			$keys[] = 'ranch_'.$type.'_reset_count_level'.$item['item_id'];
		}
		if($op == "clear_search_bylevel"){
			foreach($keys as $k){
				$AdminManager->getCacheInstance()->delete($k);
			}
			$op_msg = "操作成功"." \r\n";
		}
			
		
		$result = $AdminManager->getCacheInstance()->get($keys);
		
		for($i=0;$i<100;$i++){	
			if(isset($result['ranch_'.$type.'_reset_count_level'.$i]))
				$search_data[$i] = array('item_id'=>$i,'name'=>$type,'count'=>$result['ranch_'.$type.'_reset_count_level'.$i]);	
			
		}
		foreach($item_list as $item){
			if(isset($result['ranch_'.$type.'_reset_count_level'.$item['item_id']]))
				$search_data[$item['item_id']] = array('item_id'=>$item['item_id'],'name'=>$type,'count'=>$result['ranch_'.$type.'_reset_count_level'.$item['item_id']]);
		}
	}
	
	//单个物品统计
	if($op == "search_item"){
		$start_date = getGPC('start_date','string');
		$end_date = getGPC('end_date','string');
		$item_id = getGPC('item_id','string');
		
		$sql = "SELECT * FROM shop_statistics 
				WHERE (stat_date BETWEEN '$start_date' AND '$end_date')";
				
		if($item_id != "all")
			$sql .= " AND item_id = $item_id";
			
		$sql .= " order by stat_date asc";
		$search_data = $AdminManager->getDbInstance()->getAll($sql);
		set_item_name($search_data,$item_list);
//		set_package_name($search_data);
		
		$lines = array();
		$js_lines = "";
		$js_parm = "";
		$js_label = "";
		foreach($search_data as $d){
			if(!isset($lines[$d['item_id']]))
				$lines[$d['item_id']] = "[";
			
			$lines[$d['item_id']] .= "['".$d['stat_date']."',".$d['count']."],";
		}
		foreach($lines as $it_id=>&$line){
			$line = rtrim($line,",") . "]";
			$js_lines .= "item_$it_id = $line; \r\n";
			$js_parm .= "item_$it_id,";
			$js_label .= "{label:'".($item_list[$it_id]['name'] ==""?$item_list[$it_id]['id']:addslashes($item_list[$it_id]['name']))."'},";
		}
		$js_parm = rtrim($js_parm,",");
		$js_label = rtrim($js_label,",");
		
	}
}catch(exception $e){
	$error_msg = $e->getMessage();
}

function set_item_name(&$data,$item_list=null){
	foreach($data as &$d){
		if($d['is_packs']){
			continue;
		}
		if($d['buy_method'] == 't'){
			$d['buy_method'] = "<font color=\"#0000C6\">任务</font>";
			continue;
		}
		$d['name'] = $item_list[$d['item_id']]['name'];
		$d['amount'] = 0;
		if($d['buy_method'] == 'm'){
			$d['amount'] = $item_list[$d['item_id']]['money'] * $d['count'];
//			if(isset($item_list[$d['item_id']]['expire_time'])){
//				$saleEndTime = strtotime($item_list[$d['item_id']]['expire_time']);
//				if($saleEndTime >= strtotime($d['stat_date']) && isset($item_list[$d['item_id']]['price3']))
//					$d['amount'] = $item_list[$d['item_id']]['price3'] * $d['count'];
//			}
			$d['buy_method'] = "<font color=\"#0B6138\">农币</font>";
		}elseif($d['buy_method'] == 'd'){
			$d['amount'] = $item_list[$d['item_id']]['coupon'] * $d['count'];
			$d['buy_method'] = "<font color=\"#FF00FF\">点券</font>";
		}else{
			$d['amount'] = $item_list[$d['item_id']]['cost'] * $d['count'];
			$d['buy_method'] = "<font color=\"#868A08\">金币</font>";
		}
	}
}

function set_task_name(&$data,$item_list=null){
	foreach($data as &$d){
		if($d['is_packs']){
			continue;
		}
		if($d['buy_method'] == 't'){
			$d['name'] = $item_list[$d['item_id']]['cname'];
			$d['amount'] = 0;
		}
	}
}

//function set_package_name(&$data){
//	require_once MODEL .'/ItemPackage.class.php';
//	$packages = new ItemPackage();
//	$pkg_list = $packages->getPackageList();
//	foreach($data as &$d){
//		if(!$d['is_package']){
//			continue;
//		}
//		$d['name'] = $pkg_list[$d['item_id']]['cname'];
//	}
//}

function get_date_index($dates,$date){
	foreach($dates as $idx => $d){
		if($d['stat_date'] == $date){
			return $idx;
		}
	}
	return false;
}

function get_sort_field(){
	$sort_field = getGPC('sort_field','string');
	switch($sort_field){
		case 1:
			return 'count';
		case 2:
			return 'buy_method';
	}
	return 'count';
}