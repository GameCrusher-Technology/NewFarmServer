<?php
set_time_limit(0);
ignore_user_abort(true);
$error_msg = '';
$op_msg = '';
function getMaxFarmuid(){
	require_once API . '/model/IDSequence.class.php';
	$id_seq = new IDSequence('user_account','farmuid',true);
	$max = $id_seq->getCurrentId();
	return $max;
}

$opObject = getGPC('opObject');
if($opObject == '0'){
	$start_farmuid = 1;
	$end_farmuid = getMaxFarmuid();
}
elseif($opObject == '1'){
	$start_farmuid = getGPC('start_farmuid');
	if(empty($start_farmuid)){
		$start_farmuid = 1;
	}
	$end_farmuid = getMaxFarmuid();
	$end_fuid = getGPC('end_farmuid');
	if($end_fuid < $start_farmuid){
		$error_msg = "范围指定错误。";
	}
	elseif($end_fuid < $end_farmuid ){
		$end_farmuid = $end_fuid;
	}
}
$all_user = getGPC('all_user','bool');
if(!isset($_POST['opObject'])){
	$opObject = 1;
}
if(isset($_POST['batch_update']) && empty($error_msg)){
	require_once APP_ROOT . '/admin/include/BatchProcessor.class.php';
	$processor = new BatchProcessor();
	$processor->setProcessAllUser($all_user);
	try{
		if($opObject == '2'){
			$op_msg = $processor->processUserFromDB();
		}
		else{
			$op_msg = $processor->batchProcessUser($start_farmuid,$end_farmuid);
		}
	}
	catch (Exception $e){
		$error_msg = $e->getMessage();
	}
}
?>