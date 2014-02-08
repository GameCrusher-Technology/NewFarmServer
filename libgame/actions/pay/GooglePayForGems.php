<?php 
include_once GAMELIB.'/model/TradeLogManager.class.php';
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
		$receipt_str = $this->getParam("receipt",'string');
		$receipt = json_decode($receipt_str,true);
		
		$account = $this->user_account_mgr->getUserAccount($gameuid);

		$payLog->writeInfo($gameuid." || ".$account['gem']." || ".$receipt_str." || ".time());
		if (empty($receipt)) {
			return array('status'=>'error');
		}
		$signature = base64_decode($receipt['signature']);
		$signed_data = $receipt['signedData'];
		$pub_key = <<<EOF
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnGq+mkH8cFacOY9UoWyi1tmAxa55pdmTpoexuMVKbOjbpsY8jwzBOxTO3VBsu7HSibYDTrn79t0uFj0YMsQ/wGK1sO/Ab08DlGEYqV7m5+QsqMcAtQ8UNUER+sGnQxnzTmr3Uq9izMkk69NXzkZRaO5lp8f4gbfRx3KT2JweWihjOyFhWdlWmHRBAJE81Wn2iFJzNGNr50XIC4VDOlt+ljcUD3vu9bZmqmgMryKwn4WtxV2o4UwT5RehpyGHAyQ6YX2jmDSfoR6z2UgajCedxGK5bfmnPZXj75DC4P08O+SlBCGhEq62o/I0sDNtdWdSVnb+HM7IcqqaEMEd6taZEwIDAQAB
-----END PUBLIC KEY-----
EOF;
		$pub_k = openssl_pkey_get_public($pub_key);
			
//			if (openssl_verify($signed_data, $signature, $pub_k) !== 1) {
//				return array('status'=>'error');
//			}
			
//			$signed_data = json_decode($signed_data, true);
			$request_orders = $signed_data['orders'];
			if (empty($request_orders)) {
				return array('status'=>'error');
			}
			$tradeManager = new TradeLogManager();
			
			foreach ($request_orders as $request_order) {
				$purchase_state = $request_order['purchaseState'];
				$purchasetime = $request_order['purchaseTime'];
				$product_id = $request_order['productId'];
				$transactionid = $request_order['orderId'];
				if (empty($transactionid)) {
					continue;
				}
							
				if ($purchase_state == 0)
				{
					if ($product_id == "sunny_farm.littlefarmgem"){
						$change['gem'] = 100;
					}elseif ($product_id == "sunnyfarm.largefarmgem"){
						$change['gem'] = 600;
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
		}
		$new_account = $this->user_account_mgr->getUserAccount($gameuid);
		return array("gem"=>$new_account['gem']);
	}
}
?>
