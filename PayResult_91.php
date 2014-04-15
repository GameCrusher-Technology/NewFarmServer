<?php
error_reporting(100);
set_time_limit(0);
/**
 * PHP SDK for  OpenAPI
 *
 * @version 1.0
 * @author dev.91.com
 */
if (!defined('APP_ROOT')) define('APP_ROOT',realpath(dirname(__FILE__)));
if (!defined('GAMELIB')) define('GAMELIB', APP_ROOT . '/libgame');
if (!defined('FRAMEWORK')) define('FRAMEWORK', APP_ROOT . '/framework');

require_once GAMELIB . '/config/GameConfig.class.php';
require_once FRAMEWORK . '/log/LogFactory.class.php';
include_once GAMELIB.'/model/TradeLogManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
include_once GAMELIB . '/model/UserAccountManager.class.php';
require_once GAMELIB.'/model/UidGameuidMapManager.class.php';

header("Content-type: text/html; charset=utf-8");
if (!function_exists('json_decode')){
	exit('您的PHP不支持JSON，请升级您的PHP版本。');
}
/**
 * 应用服务器接收91服务器端发过来支付购买结果通知的接口DEMO
 * 当然这个DEMO只是个参考，具体的操作和业务逻辑处理开发者可以自由操作
 */
/*
 * 这里的MyAppId和MyAppKey是我们自己做测试的
 * 开发者可以自己根据自己在dev.91.com平台上创建的具体应用信息进行修改
 */
$MyAppId = 113200; 
$MyAppKey = 'ca03fa81271804b6a300fd69d1d708c4505ca0477938dfe2';

echo "test";
//$Res = pay_result_notify_process($MyAppId,$MyAppKey);

/**
 * 此函数就是接收91服务器那边传过来传后进行各种验证操作处理代码
 * @param int $MyAppId 应用Id
 * @param string $MyAppKey 应用Key
 * @return json 结果信息
 */
function pay_result_notify_process($MyAppId,$MyAppKey){
	$tradeManagerTest = new TradeLogManager();
	$Result = array();//存放结果数组
	//
	$payLog = LogFactory::getLogger(array(
			'prefix' => LogFactory::LOG_MODULE_PLATFORM,
			'log_dir' => APP_ROOT.'/log/payment/', // 文件所在的目录
			'archive' => ILogger::ARCHIVE_YEAR_MONTH, // 文件存档的方式
			'log_level' => 1
		));
	$payLog->writeInfo(print_r($_GET,TRUE));

	
	if(empty($_GET)||!isset($_GET['AppId'])||!isset($_GET['Act'])||!isset($_GET['ProductName'])||!isset($_GET['ConsumeStreamId'])
		||!isset($_GET['CooOrderSerial'])||!isset($_GET['Uin'])||!isset($_GET['GoodsId'])||!isset($_GET['GoodsInfo'])||!isset($_GET['GoodsCount'])||!isset($_GET['OriginalMoney'])
		||!isset($_GET['OrderMoney'])||!isset($_GET['Note'])||!isset($_GET['PayStatus'])||!isset($_GET['CreateTime'])||!isset($_GET['Sign'])){
		$Result["ErrorCode"] =  "0";//注意这里的错误码一定要是字符串格式
		$Result["ErrorDesc"] =  urlencode("接收失败");
		$tradeManagerTest->setCache($_GET);
		$Res = json_encode($Result);
		return urldecode($Res);
	}
	$AppId 				= $_GET['AppId'];//应用ID
	$Act	 			= $_GET['Act'];//操作
	$ProductName		= $_GET['ProductName'];//应用名称
	$ConsumeStreamId	= $_GET['ConsumeStreamId'];//消费流水号
	$CooOrderSerial	 	= $_GET['CooOrderSerial'];//商户订单号
	$Uin			 	= $_GET['Uin'];//91帐号ID
	$GoodsId		 	= $_GET['GoodsId'];//商品ID
	$GoodsInfo		 	= $_GET['GoodsInfo'];//商品名称
	$GoodsCount		 	= $_GET['GoodsCount'];//商品数量
	$OriginalMoney	 	= $_GET['OriginalMoney'];//原始总价（格式：0.00）
	$OrderMoney		 	= $_GET['OrderMoney'];//实际总价（格式：0.00）
	$Note			 	= $_GET['Note'];//支付描述
	$PayStatus		 	= $_GET['PayStatus'];//支付状态：0=失败，1=成功
	$CreateTime		 	= $_GET['CreateTime'];//创建时间
	$Sign		 		= $_GET['Sign'];//91服务器直接传过来的sign
	
	
	//因为这个DEMO是接收验证支付购买结果的操作，所以如果此值不为1时就是无效操作
	if($Act != 1){
		$Result["ErrorCode"] =  "3";//注意这里的错误码一定要是字符串格式
		$Result["ErrorDesc"] =  urlencode("Act无效");
		$Res = json_encode($Result);
		$tradeManagerTest->setCache(array("Act无效"));
		return urldecode($Res);
	}
	
	//如果传过来的应用ID开发者自己的应用ID不同，那说明这个应用ID无效
	if($MyAppId != $AppId){
		$Result["ErrorCode"] =  "2";//注意这里的错误码一定要是字符串格式
		$Result["ErrorDesc"] =  urlencode("AppId无效");
		$tradeManagerTest->setCache(array("AppId无效"));
		$Res = json_encode($Result);
		return urldecode($Res);
	}
	$mapping_handler = new UidGameuidMapManager();
	$gameuid = $mapping_handler->getGameuid($Uin);
	$user_account_mgr = new UserAccountManager();
	$account_info = $user_account_mgr->getUserAccount($gameuid);
	
	$tradeManager = new TradeLogManager($gameuid,$Uin);
	$tradeinfo = array();
	$tradeinfo['gameuid'] = $gameuid;
	$tradeinfo['product_id'] = $GoodsId;
	$tradeinfo['platform'] = "91andriod";
	$tradeinfo['orderId'] = $CooOrderSerial;
	$tradeinfo['purchaseState'] = $PayStatus;
	$tradeinfo['purchasetime'] = $CreateTime;
	$rewards = InitUser::$treasure_activity;
			
	//按照API规范里的说明，把相应的数据进行拼接加密处理
	$sign_check = md5($MyAppId.$Act.$ProductName.$ConsumeStreamId.$CooOrderSerial.$Uin.$GoodsId.$GoodsInfo.$GoodsCount.$OriginalMoney.$OrderMoney.$Note.$PayStatus.$CreateTime.$MyAppKey);
	if($sign_check == $Sign){//当本地生成的加密sign跟传过来的sign一样时说明数据没问题
		
		/*
		 * 
		 * 开发者可以在此处进行订单号是否合法、商品是否正确等一些别的订单信息的验证处理
		 * 相应的别的错误用不同的代码和相应说明信息，数字和信息开发者可以自定义（数字不能重复）
		 * 如果所有的信息验证都没问题就可以做出验证成功后的相应逻辑操作
		 * 不过最后一定要返回上面那样格式的json数据
		 * 
		 */
		
		if ($GoodsId == "sunny_farm.littlefarmgem"){
			$change['gem'] = 200;
			$item = addReward($gameuid,$rewards['littleFarmGem']);
		}elseif ($GoodsId == "sunny_farm.largefarmgem"){
			$change['gem'] = 1100;
			$item = addReward($gameuid,$rewards['largeFarmGem']);
		}
		$user_account_mgr->updateUserStatus($gameuid,$change);
			
		$tradeinfo['status'] = 1;
		$Result["ErrorCode"] =  "1";//注意这里的错误码一定要是字符串格式
		$Result["ErrorDesc"] =  urlencode("接收成功");
		$Res = json_encode($Result);
	}else{
		$tradeinfo['status'] = 0;
		$Result["ErrorCode"] =  "5";//注意这里的错误码一定要是字符串格式
		$Result["ErrorDesc"] =  urlencode("Sign无效");
		$Res = json_encode($Result);
	}
	$tradeManager->insert($tradeinfo);
	return urldecode($Res);
}
function addReward($gameuid,$rewards)
{
	$item_mgr = new UserGameItemManager($gameuid);
	foreach ($rewards as $value){
		if ($value['id'] == 'coin'){
			$this->user_account_mgr->updateUserCoin($gameuid,$value['count']);
		}else if($value['id']== 'exp'){
			$this->user_account_mgr->updateUserExperience($gameuid,$value['count']);
		}else {
			$item_mgr->addItem($value['id'],$value['count']);
		}
	}
	$item_mgr->commitToDB();
	return $rewards;
}

?>