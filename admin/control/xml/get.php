<?php
if(!defined('IN_GAME')) exit('Access Denied');
require_once GAMELIB.'/model/XmlManager.class.php';
global $admin_logger;
$xml_names = array('farmSceneData','actionDef','database','missionData','configuration',"giftbox","collection",'login_reward','composition');
$xml_name = $_POST['xml_name'];
switch ($xml_name){
	case 'database':
		$xml_mgr = new ItemManager();
		break;
	case 'missionData':
		$xml_mgr = new TaskInfoManager();
		break;
	case 'farmSceneData':
		$xml_mgr = new SceneActionManager();
		break;
	case 'actionDef':
		$xml_mgr = new ActionDefManager();
		break;
	case 'configuration':
		$xml_mgr = new ConfigurationManager();
		break;		
	case 'giftbox':
		$xml_mgr = new GiftboxManager();
		break;
	case 'login_reward':
		$xml_mgr = new LoginRewardManager();
		break;
	case 'collection':
		$xml_mgr = new Target_ItemManager();
		break;
	case 'composition':
		$xml_mgr = new CompositionManager();
		break;
}
$action_get = getGPC('get','string');
if (empty($action_get)) return ;
try {
	if (!empty($action_get)){
		$defs = $xml_mgr->getDefList();
		$item_id = $_POST['id'];
	    if (empty($item_id)){
	    	$err_msg = "id is empty";
	    	return ;
	    }
	    $result = $xml_mgr->getDef($item_id);
	    if (empty($result)){
	    	$err_msg = "defination of [$item_id] is empty";
	    	return ;
	    }
	    foreach ($result as $key=>$value){
	    	if (is_array($value)){
	    		$result[$key] = implode_string($value);
	    	}
	    }
	}
}catch (Exception $e){
	$admin_logger->writeError("exception while get definiton.".$e->getMessage());
	$error_msg = $e->getMessage ();
}
?>