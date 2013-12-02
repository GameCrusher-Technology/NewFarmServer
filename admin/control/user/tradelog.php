<?php
if(!defined('IN_GAME')) exit('Access Denied');

global $limitvalue, $admin_logger;

ini_set('mbstring.internal_encoding','UTF-8');
$action_get=getGPC("getUserLog",'string');
$action_getTotal=getGPC("getTotal",'string');
if (empty($action_get)&&empty($action_getTotal))	return ;
if (isset ( $_POST ['gameuid'] )) {
	try {
		$gameuid = trim ( $_POST ['gameuid']);
		$date = getGPC('date', 'string');
		if (!empty($action_get)){
			if(isset($_POST['success'])){
				$success = 1;
			}else{
				$success = 0;
			}
			$limit = intval($_POST['limit']);
			$offset = intval($_POST['offset']);
			if(empty($limit)){
				$limit = 20;
			}
			$result = _get_tradelogs($gameuid,$success,$offset,$limit,$date);
		}else {
			$result = _get_total($gameuid,$date);
		}
		
		if(!empty($result) && !is_array($result[0]))
			$results[] = $result;
		else 
			$results = $result;
	} catch ( Exception $e ) {
		$admin_logger->writeError($e->getTraceAsString());
		$error_msg = $e->getMessage ();
	}
}
function _get_tradelogs($gameuid,$success,$offset,$limit,$date){
	$db_helper = get_app_config($gameuid)->getTableServer("trade_log")->getDBHelperInstance();
	$sql = "SELECT * FROM trade_log ";
	$whereclause ;
	if( !empty($gameuid )){
		$whereclause = " WHERE gameuid = $gameuid";
	}
	if( $success ==1 ){
		if(empty($whereclause))
			$whereclause = " WHERE status = 1";
		else 
			$whereclause .=" AND status = 1";
	}
	if (!empty($date)){
		$start_time = strtotime($date);
		$end_time = strtotime('+1 day', $start_time);
		if(empty($whereclause))
			$whereclause = " WHERE create_time > $start_time AND create_time < $end_time";
		else 
			$whereclause .=" AND create_time > $start_time AND create_time < $end_time";
	}
	if( !empty($whereclause)){
		$sql .= $whereclause;
	}
	
	$sql .= " LIMIT $offset,$limit";	
	return $db_helper->getAll($sql);
}
function _get_total($gameuid,$date){
	$db_helper = get_app_config($gameuid)->getTableServer("trade_log")->getDBHelperInstance();
	$sql="select sum(amount) as total from trade_log where status=1";
	if (!empty($gameuid)){
		$sql.=" and gameuid=$gameuid";
	}
	if (!empty($date)){
		$start_time = strtotime($date);
		$end_time = strtotime('+1 day', $start_time);
		$sql.=" and create_time > $start_time and create_time < $end_time";
	}
	$result=$db_helper->getOne($sql);
	$result['total']=intval($result['total']);
	if (!empty($gameuid)){
		$result['gameuid']=$gameuid;
	}
	if (!empty($date)){
		$result['date']=$date;
	}
	return $result;
}
?>