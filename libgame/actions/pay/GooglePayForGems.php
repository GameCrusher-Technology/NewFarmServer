<?php 
include_once GAMELIB.'/model/TradeLogManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
require_once FRAMEWORK . '/log/LogFactory.class.php';
class GooglePayForGems extends GameActionBase 
{
	protected function _exec()
	{
		$payLog = LogFactory::getLogger(array(
			'prefix' => LogFactory::LOG_MODULE_PLATFORM,
			'log_dir' => APP_ROOT.'/log/payment/', // 文件所在的目录
			'archive' => ILogger::ARCHIVE_YEAR_MONTH, // 文件存档的方式
			'log_level' => 1
		));
	//	$str = 
	//{"GameVersion":"1.2.1",
//			"order_id":0,
//			"Act":"checkpaysuccess",
//			"local":"en_US",
//			"receipt":"{\"startId\":31,
//						\"signedData\":\"{\\\"nonce\\\":-1192671730504895324,
//										  \\\"orders\\\":[{\\\"notificationId\\\":\\\"5436123142856017636\\\",
//										  					\\\"orderId\\\":\\\"12999763169054705758.1306563549370746\\\",
//										  					\\\"packageName\\\":\\\"air.com.elextech.happyfarm\\\",
//										  					\\\"productId\\\":\\\"sapphire_0\\\",
//										  					\\\"purchaseTime\\\":1365709235000,
//										  					\\\"purchaseState\\\":0,
//										  					\\\"purchaseToken\\\":\\\"muctppyltxuprjtvrlkvgtwy\\\"
//															}]
//										} \",
//						\"signature\":\"JbaXNai3opyd+aEYrmRTyw5j2zmCz5u09c3BuKP1Aj\/6TkgDPrkbaySowac0e+uLeE1U+kacoDlF58fg01bMiaoGwnCEkKdMfx4ebjeC+e9MzKNEGhUAnOuL5gZbDIEd3hLnhCQQWgqcQ6rsxyPN6rMAuLws7ZVPBuNOIRns0v4\/UbCt9asA4dnMXmqNHLKZlyDvDS9sIc0be81IAUQfesAykpGciaV\/w20CYl9HjX+rTDS5hiLvFAY8SujiRq0moGh4f\/PywqBrPQGB9O8ujomVl70qzN8Z8TOi\/5VYdP2oigzdp\/D5dptwb0rcid\/gWO2gUu8dbiy163iosZVgMg==\"
//						}",
//			"ChannelId":"googleplay"
//	}

		$gameuid = $this->getParam("gameuid",'int');
		$receipt = $this->getParam("receipt",'array');
		$receipt_str = $this->getParam("receiptStr",'string');
		$buytype = $this->getParam("buytype",'string');
		
		$account = $this->user_account_mgr->getUserAccount($gameuid);
		$payLog->writeInfo($gameuid." || ".$account['gem']." || ".json_encode($receipt) );
		
		if (empty($receipt)) {
			return array('status'=>'error');
		}
		$new_rec = array();
		foreach ($receipt as $key=>$value){
			$new_rec[$key] = $value;
		}
		$signature = $new_rec['signature'];
		$signed_data = $new_rec["signedData"];
		
		$keyStr=  "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnGq+mkH8cFacOY9UoWyi1tmAxa55pdmTpoexuMVKbOjbpsY8jwzBOxTO3VBsu7HSibYDTrn79t0uFj0YMsQ/wGK1sO/Ab08DlGEYqV7m5+QsqMcAtQ8UNUER+sGnQxnzTmr3Uq9izMkk69NXzkZRaO5lp8f4gbfRx3KT2JweWihjOyFhWdlWmHRBAJE81Wn2iFJzNGNr50XIC4VDOlt+ljcUD3vu9bZmqmgMryKwn4WtxV2o4UwT5RehpyGHAyQ6YX2jmDSfoR6z2UgajCedxGK5bfmnPZXj75DC4P08O+SlBCGhEq62o/I0sDNtdWdSVnb+HM7IcqqaEMEd6taZEwIDAQAB";
		$KEY_PREFIX = "-----BEGIN PUBLIC KEY-----\n";
	    $KEY_SUFFIX = '-----END PUBLIC KEY-----';
		$pub_key = $KEY_PREFIX . chunk_split($keyStr, 64, "\n") . $KEY_SUFFIX;
		$pub_k = openssl_get_publickey($pub_key);
		
		$r = openssl_verify($signed_data, base64_decode($signature), $pub_k);
		if ($r !== 1 && $buytype!="localTest") {
			$payLog->writeError($gameuid." || ".$r );
			return array('status'=>'error');
		}
		$signed_data = json_decode($signed_data, true);
		$new_signed_data = array();
		foreach ($signed_data as $key=>$value){
			$new_signed_data[$key] = $value;
		}
		$request_orders = $new_signed_data['orders'];
		if (empty($request_orders)) {
			return array('status'=>'error');
		}
		$tradeManager = new TradeLogManager();
		
		$cached_orders = $tradeManager->getOrderCache($gameuid);
		if (empty($cached_orders)){
			$cached_orders = array();
		}
		foreach ($request_orders as $orderKey => $request_order) {
				$new_request_order = array();
				foreach ($request_order as $orderK=>$orderV) {
					$new_request_order[$orderK]= $orderV;
				}
				
				
				$purchase_state = $new_request_order['purchaseState'];
				$purchasetime = $new_request_order['purchaseTime'];
				$product_id = $new_request_order['productId'];
				$transactionid = $new_request_order['orderId'];
				
				$notification_id = "t".$new_request_order['notificationId'];
				if (empty($transactionid)) {
					continue;
				}
				if (in_array($notification_id,$cached_orders)){
					continue;
				}
				$rewards = InitUser::$treasure_activity;
				if ($purchase_state == 0)
				{
					if ($product_id == "sunny_farm.littlefarmgem"){
						$change['gem'] = 200;
						$item = $this->addReward($gameuid,$rewards['littleFarmGem']);
					}elseif ($product_id == "sunny_farm.largefarmgem"){
						$change['gem'] = 1100;
						$item = $this->addReward($gameuid,$rewards['largeFarmGem']);
					}else{
						$this->throwException("wrong product_id :".$product_id,GameStatusCode::PARAMETER_ERROR);
					}
					$this->user_account_mgr->updateUserStatus($gameuid,$change);
				}
				$tradeinfo = array();
				$tradeinfo['gameuid'] = $gameuid;
				$tradeinfo['product_id'] = $product_id;
				$tradeinfo['platform'] = "andriod";
				$tradeinfo['orderId'] = $transactionid;
				$tradeinfo['purchaseState'] = $purchase_state;
				$tradeinfo['purchasetime'] = $purchasetime;
				$tradeinfo['status'] = 1;
				$tradeManager->insert($tradeinfo);
				array_push($cached_orders,$notification_id);
		}
		$tradeManager->setOrderCache($gameuid,$cached_orders);
		$new_account = $this->user_account_mgr->getUserAccount($gameuid);
		$payLog->writeInfo($gameuid." || ".$new_account['gem'] );
		if (empty($item)){
			return array("gem"=>$new_account['gem']);
		}else {
			return array("gem"=>$new_account['gem'],"items"=>$item);
		}
	}
	
	private function addReward($gameuid,$rewards)
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
}
?>
