<?php
if(!defined('IN_GAME')) exit('Access Denied');
require_once GAMELIB . '/model/AccountLogManager.class.php';
global $limitvalue, $admin_logger;
$app_config = get_app_config(1);
$fields_defs = $app_config->getSection(AppConfig::TABLE_FIELDS);//??
$exclude_tables = array(
	"uid_gameuid_mapping","user_event_log","user_action_log","trade_log","action_trade_log","action_records",
	"admin_users","admin_audit_log","user_close","user_account_deleted","lottery","uid_gameuid_mapping_deleted","user_plateform_gift"
);
$packed_handler_mapping = array(
	"user_background"=>"UserBackgroundManager",
	"user_employee"=>"UserEmployeeManager",
	"user_owned"=>"UserOwnedManager",
	"user_owned_tasks"=>"UserTasksManager",
	"user_warehouse"=>"UserWarehouseManager",
);
//定义类型为TCRequest::CACHE_KEY_LIST的表
$list_cache_tables = array(
	"user_data","user_data_animal","user_decoration","user_greenhouse","user_furniture","user_action_count","user_achievement",
	"user_mail","user_feed","user_house","user_scene","user_pet","user_item","user_planttrees","user_plateform_gift1"
);
$uid_tables=array("user_plateform_gift");
$assoc_list_cache_tables=array(
	"user_mail","user_feed","user_item","user_action_count","user_scene","user_plateform_gift1","user_planttrees","user_pet",
	"user_data","user_data_animal","user_decoration","user_greenhouse","user_furniture"
);
$assoc_list_dataId_cache_tables=array(
	"user_scene","user_plateform_gift1","user_planttrees","user_pet",
	"user_data","user_data_animal","user_decoration","user_greenhouse","user_furniture"
);
$packed_fields_defs = $app_config->getSection(AppConfig::TABLE_PACK_FIELDS);//??
$table_names = array_diff(array_keys($fields_defs), $exclude_tables);//fields_defs-exclude_tables
$primery_key_def=$app_config->getSection(AppConfig::TABLE_PRIMARY_KEY);

$action_get = getGPC("get", "string");
$action_insert = getGPC("insert", "string");
$action_modify = getGPC("modify", "string");
if (empty($action_get) && empty($action_insert) && empty($action_modify)) return;
try {
	$table_name = $_POST['table_name'];
	if (empty($table_name)) {
		$error_msg = "数据库表不能为空";
		return;
	}
	if (empty($action_insert) || $table_name != 'user_account') {
		$uid = $_POST ['uid'];
		if (!empty($uid)) {
			$gameuid = get_gameuid_from_uid(trim($_POST['uid']));
		} else {
			$gameuid = $_POST['gameuid'];
			$uid = get_uid_from_gameuid($gameuid);
		}
		if (intval($gameuid) <= 0) {
			$error_msg = "用户信息不存在[uid=$uid,gameuid=$gameuid]";
			return;
		}
		$user_account = get_user_account($gameuid);
		if (empty($user_account)) {
			$error_msg = "用户信息不存在[uid=$uid,gameuid=$gameuid]";
			return;
		}
	}//获取用户gameuid和uid，根据gameuid获取用户信息。如果操作是插入数据或要编辑的是account表则不必进行该操作
	$packed_fields_def = $packed_fields_defs[$table_name];  //获得该数据表中进行PACK的字段名
	if (!empty($action_modify)) {//如果修改数据
		if ($limitvalue!=1&&$limitvalue!=2){//检查用户的权限
			$error_msg = "没有执行权限。";
			return;
		}
		if (in_array($table_name,$uid_tables)){
			$entries = _get_datas_by_uid($table_name,$uid);
		}else {
			$entries = _get_datas($table_name, $gameuid);  //获取数据表数据
		}
		$primery_key=$primery_key_def[$table_name];
		foreach ($entries as $entry) {
			$id=_get_id_value($table_name,$entry);
			$action_type = getGPC($table_name.'_'.$id, "string");
			if ($action_type == 'delete') {
				if ($table_name == 'user_account') {
					$error_msg = "user_account表不能删除，请用<a href=\"".BASE_URL."admin/admincp.php?mod=user&act=deleteaccount\">删除用户</a>管理功能";
				} else {
					_del_data($table_name, $id, $gameuid);
					$op_msg = "成功删除数据";
				}
			} elseif ($action_type == 'modify') {
				foreach ($entry as $k=>$v) {
					$modifed_value = getGPC($table_name.'_'.$id.'_'.$k, "string");
					if (isset($modifed_value)) {
						if (is_array($v)) {
							if (in_array($k, array_keys($packed_fields_def))) {//如果修改的数据是PACK的字段
								$sub_fields = $packed_fields_def[$k]['sub_fields'];
								 //把PACK字段的数据，由字符串转为数组
								if (count($sub_fields) == 1){//说明只是一层数组，就像complete_task字段中的内容一样
									$modifed_value = explode(',',$modifed_value);
								}else {
									$modifed_value = parse_string(            
									$modifed_value, 
									array_keys($sub_fields),
									'/[:,]/',
									count(array_keys($sub_fields)));
								}
							}
						}
						$entry[$k] = $modifed_value;
					}
				}
			$arr2=array($entry);
				 $array=($_SESSION['entries']);
			     $res=(array_diff_assoc2_deep($array, $arr2));
			     
			
			    // print_r($res);
			 foreach($res as $v){
                     foreach($v as $key=>$value){
                     $arr3[$key]=$value;
                    
                    }
			 }
             foreach($arr3 as $key=>$value){
            	if($key=='complete_task'||$key=='extra_award_count'){
            		unset($arr3[$key]);
            		//$arr5[$key]= intval($entry[$key])-intval($arr3[$key]);
            	}                    
               }
               
                foreach($arr3 as $key=>$value){
                	
                	if(is_numeric($arr3[$key])){
                	$arr5[$key]= intval($entry[$key])-intval($arr3[$key]);
                		}
                	else {
                		$arr5[$key]=$arr3[$key];
                		$arr5[$key.'now']=$entry[$key];
                	}
                }
               
     
            foreach($arr5 as $key=>$value){
            	
                $str.=$key.":".$value.",";
            	
                 }
              // echo $str;
           //$str=strstr($str,",");
             // echo $str;
               $login_user = $admin_account_mgr->isLogin();
              // echo "login_user:".var_export($login_user,true)."<br/>";
              if($table_name=='user_account'){
              	if($str!==''){		  
			      $add_account_log=new AccountLogManager();
			      $add_account_log->write_account_log($login_user,$gameuid,$str);
            	  }
              }
				_update_data($table_name, $id, $entry, $gameuid);
				$op_msg = "成功修改数据";
			}
		}
	} elseif (!empty($action_insert)) {//如果插入数据
		if ($limitvalue!=1&&$limitvalue!=2){//检查用户的权限
			$error_msg = "没有执行权限。";
			return;
		}
		$entry = array();
		if (in_array($table_name, $list_cache_tables)&&!in_array($table_name,$assoc_list_cache_tables)) {
			require_once FRAMEWORK.'/database/IDSequence.class.php';
			$sequence_handler = new IDSequence($table_name, 'data_id');
			$data_id = $sequence_handler->getNextId();
			if (intval($data_id) <= 0) {
	    		$error_msg = "无法从IDSequence获取data_id";
	    		return;
	    	}
	    	$entry['data_id'] = $data_id;
		}else if (in_array($table_name,$assoc_list_dataId_cache_tables)){
			$mem_key=sprintf("ranch_id_sequence_%s_%d",$table_name,$gameuid);
			$cache_mgr= get_cache_instance($gameuid,$table_name);
			$value=$cache_mgr->increment($mem_key);
			if ($value<1000){
				$list=_get_datas($table_name,$gameuid);
				$max_id=0;
				foreach ($list as $scene){
					if ($max_id<$scene['data_id']) $max_id=$scene['data_id'];
				}
				if ($max_id<1000)	$max_id=1000;
				$cache_mgr->set($mem_key,$max_id,0);
				$value=$cache_mgr->increment($mem_key);
			}
			$entry['data_id'] = $value;
		}
		if ($table_name == 'user_account') {
			$uid = $_POST['uid'];
			if (empty($uid)) {
	    		$error_msg = "必须要指定平台的uid";
	    		return;
	    	}
			require_once FRAMEWORK.'/database/IDSequence.class.php';
			$sequence_handler = new IDSequence($table_name, 'gameuid');
			$gameuid = $sequence_handler->getNextId();
			if (intval($gameuid) <= 0) {
	    		$error_msg = "无法从IDSequence获取gameuid";
	    		return;
	    	}
	    	require_once GAMELIB.'/model/UidGameuidMapManager.class.php';
	    	$mapping_handler = new UidGameuidMapManager();
	    	$mapping_handler->createMapping($uid, $gameuid);
		}
    	$fields_def = $fields_defs[$table_name];
		$field_names = array_keys($fields_def);
		foreach ($field_names as $field_name) {
			$field_value = getGPC($table_name.'_'.$field_name, "string");
			if (empty($field_value)) continue;
			if (in_array($field_name, array_keys($packed_fields_def))) {
				$sub_fields = $packed_fields_def[$field_name]['sub_fields'];
				if (count($sub_fields) == 1){
					$field_value = explode(',',$field_value);
				}else {
					$field_value = parse_string(
					$field_value, 
					array_keys($sub_fields),
					'/[:,]/',
					count(array_keys($sub_fields)));
				}
			}
			$entry[$field_name] = $field_value;
		}
		_insert_data($table_name, $entry, $gameuid);
		$op_msg = "成功添加数据";
	}
	// 管理员给用户添加物品，需要在用户的user_event_log将这个事件记录下来，便于用户的小秘书通知TA
	$event_msg = getGPC("event_msg", 'string');
	if (!empty($event_msg)) {
		require_once GAMELIB . '/model/UserEventLogManager.class.php';
		$event_logger = new UserEventLogManager($gameuid);
		$event_logger->writeLog(ActionCode::ACTION_LITERAL_EVENT, $event_msg);
	}
	if (in_array($table_name,$uid_tables)){
		$entries = _get_datas_by_uid($table_name,$uid);
	}else {
		$entries = _get_datas($table_name, $gameuid);  //获取数据表数据
		setcookie("entries", $entries); 
		session_start();
		$_SESSION['entries']= $entries; 
	}
	
	if (is_array($entries) && count($entries) > 0) {
		$header = array_keys($fields_defs[$table_name]);
		if(empty($header)){
			$header = array_keys(current($entries));
		}
		$rows = $entries;
		$column_count = count($header)+1;
	}
	if (isset($packed_handler_mapping[$table_name])) {
		require_once GAMELIB.'/model/'.$packed_handler_mapping[$table_name].".class.php";
		$packed_handler = new $packed_handler_mapping[$table_name]($gameuid);
		$entriesPacked = $packed_handler->getFormattedUnpacked();
		$extra_info_packed = "缓存键值:".$table_name."_".$gameuid;
		if (!empty($entriesPacked['extra_info'])) {
			$extra_info_packed .= ','.strval($entriesPacked['extra_info']);
		}
		$entriesPacked['extra_info'] = $extra_info_packed;
		$headerPacked = $entriesPacked['header'];
		$rowsPacked = $entriesPacked['data'];
		$extra_info_packed = strval($entriesPacked['extra_info']);
		$column_count_packed = count($headerPacked)+1;
	}
} catch ( Exception $e ) {
	$admin_logger->writeError("exception while manage memcache.".$e->getMessage());
	$error_msg = $e->getMessage ();
}
function _get_datas($table_name, $gameuid) {
	global $list_cache_tables;
	$req=RequestFactory::createGetRequest(get_app_config($gameuid));
	$req->setKey('gameuid',$gameuid);
	$req->setTable($table_name);
	$req->addKeyValue("gameuid", $gameuid);
	$result = $req->getFromCache();
	if($result === false){
		if (in_array($table_name, $list_cache_tables)) {
			$req->setCacheType(TCRequest::CACHE_KEY_LIST);
			return $req->fetchAll();
		} else {
			$res = $req->fetchOne();
			if ($res === false) return $res;
			return array($res);
		}
	}
	return $result;
}
function _get_datas_by_uid($table_name, $uid) {
	$req=RequestFactory::createGetRequest(get_app_config());
	$req->setTable($table_name);
	$req->addKeyValue("uid", $uid);
	$res = $req->fetchOne();
	if ($res === false) return $res;
	return array($res);
}
function _del_data($table_name, $id, $gameuid) {
	global $primery_key_def;
	$req = RequestFactory::createDeleteRequest(get_app_config($gameuid));
	$req->setKey("gameuid", $gameuid);
	$req->setTable($table_name);
	//获取键值
	$primery_key=$primery_key_def[$table_name];
	$primery_key=explode(',',$primery_key);
	$id=explode('_',$id);
	foreach ($primery_key as $k=>$key){
		$req->addKeyValue($key,$id[$k]);
	}
	$req->execute();
    //删除缓存数据
	if(!$req->getNoCache()){
		$req->deleteFromCache();
	}
}
function _update_data($table_name, $id, $entry, $gameuid) {
	global $primery_key_def;
	$req = RequestFactory::createUpdateRequest(get_app_config($gameuid));
	$req->setKey("gameuid", $gameuid);
	$req->setTable($table_name);
	$req->setModify($entry);
	//获取键值
	$primery_key=$primery_key_def[$table_name];
	$primery_key=explode(',',$primery_key);
	$id=explode('_',$id);
	foreach ($primery_key as $k=>$key){
		$req->addKeyValue($key,$id[$k]);
	}
	$req->execute();
}
function _insert_data($table_name, $entry, $gameuid) {
	global $list_cache_tables;
	global $fields_defs;
	$fields_def=$fields_defs[$table_name];
	if (key_exists('gameuid',$fields_def)){
		$entry['gameuid'] = $gameuid;
	}
	$req = RequestFactory::createInsertRequest(get_app_config($gameuid));
	$req->setKey("gameuid", $gameuid);
	$req->setTable($table_name);
	//获取键值
	$req->setColumns(implode(',', array_keys($entry)));
	$req->addValues(array_values($entry));
	if (in_array($table_name, $list_cache_tables)) {
		$req->setCacheType(TCRequest::CACHE_KEY_LIST);
	}
	$req->execute();
}
function _get_id_value($table_name,$entry){
	global $primery_key_def;
	$id_field_name = $primery_key_def[$table_name];
	$id_field_name=explode(',',$id_field_name);
	$id_array=array();
	foreach ($id_field_name as $one){
		$id_array[]=$entry[$one];
	}
	$id_value=join('_',$id_array);
	return $id_value;
}
function _get_primery_key_field($table_name){
	global $primery_key_def;
	$id_field_name = $primery_key_def[$table_name];
	$id_field_name=explode(',',$id_field_name);
	return $id_field_name;
}
function getTableName() {
		return "admin_audit_log";
	}
function array_diff_assoc2_deep($array1, $array2) {
$ret = array();
foreach ($array1 as $k => $v) {
if (!isset($array2[$k])) $ret[$k] = $v;
else if (is_array($v) && is_array($array2[$k])) $ret[$k] = array_diff_assoc2_deep($v, $array2[$k]);
else if ((string)$v != (string)$array2[$k]) $ret[$k] = $v;
}
return $ret;
}
	
?>