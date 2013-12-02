<?php
try{
	$pay_today = date('Y-m-d');
	$monthes = $adminModel->getAll('select distinct month from pay_statistics order by month desc');
	if(isset($_POST['search'])){
		$search_month = getGPC('search_month','string');
		$d_index = get_date_index($monthes,$search_month);
		if($d_index === false){
			$error_msg = "查询的日期销售数据不存在。";
		}else{
			$sql = "SELECT * FROM pay_statistics
					WHERE month = '$search_month' ";
			$sort_field = get_sort_field();
			$sort_order = 'desc';
			if(isset($_POST['sort_order'])){
				$sort_order = 'asc';
			}
			$sql .= " order by $sort_field $sort_order";
			$search_data = $adminModel->getAll($sql);
			//$op_msg = "查询完成。";
		}
	}elseif(isset($_POST['create_day_stat'])){
		require_once ADMIN_ROOT . '/include/pay.php';
		try{
			$date = getGPC('create_stat_date','string','P');
			$amount = create_daily_pay_stat($date);
			
			$op_msg = "$date 的支付数量是:$amount";
		}catch(exception $e){
			$error_msg = $e->getMessage();
		}
	}
}catch(exception $e){
	$error_msg = $e->getMessage();
}
function get_date_index($dates,$date){
	foreach($dates as $idx => $d){
		if($d['month'] == $date){
			return $idx;
		}
	}
	return false;
}

function get_sort_field(){
	$sort_field = getGPC('sort_field','string');
	switch($sort_field){
		case 1:
			return 'stat_date';
		case 2:
			return 'money';
	}
	return 'sell_count';
}
include renderTemplate('default/sysstat/pay');