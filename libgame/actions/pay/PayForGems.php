<?php
@ini_set("display_errors","On");
error_reporting(E_ALL);
require_once FRAMEWORK . '/net/HttpRequest.class.php';
require_once PATH_DATAOBJ .'TradeLogManager.class.php';
require_once PATH_CACHE . 'AccountCache.php';

class PayForGems extends BaseCommand
{
	protected function executeEx($params)
	{
		if(!isset($this->uid))
		{
			$this->throwException('uid is null',StatusCode::DATABASE_ERROR);
		}
		$key = $params['key'];
		
		$verify_url = "https://sandbox.itunes.apple.com/verifyReceipt";
//		$verify_url = 'https://buy.itunes.apple.com/verifyReceipt';
    	$verify_postfields = json_encode(array("receipt-data"=> base64_encode($key)));
    	$res = HttpRequest::post($verify_url, $verify_postfields);
    	
    	$resData = json_decode($res['data'],true);
    	if($resData == NULL)
    	{
    		return array('status'=>'error');
    	}
    	if($resData["status"] != 0){
    		return array('status'=>'error');
    	}
    	$receipt = $resData["receipt"];
    	$gameuid = $this->gameuid;
    	$accountMC = new AccountCache($gameuid,$this->uid);
		$accountinfo = $accountMC->getAccount();
		$id = $receipt['transaction_id'];
		
		$tradeManager = new TradeLogManager($gameuid,$this->uid);
		$oldPay = $tradeManager->get($gameuid,$id);
		if($oldPay != FALSE){
			return array('status'=>'error');
		}
		if($receipt['product_id'] == "tableTest01"){
			$change['redGemsNum'] = $accountinfo['redGemsNum'] + 100;
			$pId= 1;
		}elseif($receipt['product_id'] == "tableTest02"){
			$change['redGemsNum'] = $accountinfo['redGemsNum'] + 1200;
			$pId= 2;
		}
		$accountMC->updateAccount($change,$accountinfo);
		$accountMC->deleteBuygemsCache($gameuid);
    	$tradeinfo = array();
		$tradeinfo['id'] = $id;
		$tradeinfo['gameuid'] = $gameuid;
		$tradeinfo['product_id'] = $pId;
		$tradeinfo['create_time'] = time();
		$tradeinfo['ConsumeStreamId'] = $receipt['unique_identifier'];
		$tradeinfo['CooOrderSerial'] = $receipt['bvrs'];
		$tradeinfo['status'] = 1;
		$tradeManager->insert($tradeinfo,$gameuid);
		
		$accountinfo = $accountMC->getAccount();
		return $accountinfo;
	}
}
?>