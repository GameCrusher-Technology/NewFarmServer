<?php
try{
	$dates = $adminModel->getAll('select distinct stat_date from shop_statistics order by stat_date desc');
	if(isset($_POST['search'])){
		$search_date = getGPC('search_date','string');
		$d_index = get_date_index($dates,$search_date);
		if($d_index === false){
			$error_msg = "查询的日期销售数据不存在。";
		}else{
			$sql = "SELECT t1.*,IFNULL(t1.sell_count - t2.sell_count,0) daily_count
					FROM shop_statistics t1
					LEFT JOIN shop_statistics t2
					ON t2.item_id = t1.item_id
					AND t2.is_package = t1.is_package
					AND t2.buy_method = t1.buy_method
					AND t2.stat_date = '%s'
					WHERE t1.stat_date = '%s' ";
			$date_before = '0';
			if($d_index < count($dates) - 1){
				$date_before = $dates[$d_index + 1]['stat_date'];
			}
			$sql = sprintf($sql,$date_before,$search_date);
			if(isset($_POST['package_only'])){
				$sql .= ' and t1.is_package = 1';
			}
			$sort_field = get_sort_field();
			$sort_order = 'desc';
			if(isset($_POST['sort_order'])){
				$sort_order = 'asc';
			}
			$sql .= " order by t1.$sort_field $sort_order";
			$search_data = $adminModel->getAll($sql);
			set_item_name($search_data);
			set_package_name($search_data);
			//$op_msg = "查询完成。";
		}
	}
}catch(exception $e){
	$error_msg = $e->getMessage();
}

function set_item_name(&$data){
	require_once MODEL . '/Items.class.php';
	$items = new Items();
	$item_list = $items->getItemList();
	foreach($data as &$d){
		if($d['is_package']){
			continue;
		}
		$d['name'] = $item_list[$d['item_id']]['cname'];
	}
}

function set_package_name(&$data){
	require_once MODEL .'/ItemPackage.class.php';
	$packages = new ItemPackage();
	$pkg_list = $packages->getPackageList();
	foreach($data as &$d){
		if(!$d['is_package']){
			continue;
		}
		$d['name'] = $pkg_list[$d['item_id']]['cname'];
	}
}

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
			return 'sell_count';
		case 2:
			return 'buy_method';
	}
	return 'sell_count';
}
include renderTemplate('default/sysstat/shop');