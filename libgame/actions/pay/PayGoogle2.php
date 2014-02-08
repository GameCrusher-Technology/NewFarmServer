<?php 
require_once PATH_DATAOBJ . '/BaseModel.php';
require_once PATH_DATAOBJ . '/log/PayLog.php';
class PayGoogle2 extends BaseCommand
{
	
	protected function executeEx($params){
		$act = $params['Act'];
//		file_put_contents('/home/elex/nginx/data/ipad_server/www/amf/services/log/paygoogle.log', $msg.' params='.print_r($params,true)."\r\n",FILE_APPEND);
			
		if($act == 'checkpaysuccess'){
			$msg = sprintf("[%s]",date('Y-m-d H:i:s'));
 file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.'uid='.$this->uid.' params='.print_r($params,true)."\r\n",FILE_APPEND);
//测试支付信息start
//$file = '/home/elex/nginx/data/server_gp/command/payg2.log';
//$handle = @fopen($file, "r");
//if($handle){
//	while(!feof($handle)){
//		$string = fgets($handle, 4096);
//		$a = json_decode($string,true);
//		if($a){
//			break;
//		}
//	}
//}
//			file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.' a='.$a."\n",FILE_APPEND);
//			$t = $a['receipt'];
//			file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.' t='.$t."\n",FILE_APPEND);
//			$receipt = json_decode($t,true);
//测试支付信息end
			$receipt = json_decode($params['receipt'],true);
		file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.'step1'."\n",FILE_APPEND);	
			if (empty($receipt)) {
				return array('code'=>1, 'msg'=>'Emptyparams-receipt');
			}
			file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.' receipt='.print_r($receipt,true)."\n",FILE_APPEND);
						
			$signature = base64_decode($receipt['signature']);
			$signed_data = $receipt['signedData'];
			$pub_key = <<<EOF
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1iiaK352KzbZfPLJn9AD
S8of8PtPSGyWxfP5bcwumb/gyn0aLWPvL+Yg88vvbFVPFRY75PFphk9psBuc6/2H
yiIgcx2dDLnDdVII4QtYUFjcLqo+aK/LvXnEmXQdMmYzQFJC/H/0ZT5u552+0sgq
C5w5m2GFDBaycU+3lZV7PwLIAcXlUTCXXilh3t4YuzVXWWdA9X+m0DFgSAR3Cv/G
bc1VTPgM8Y6XBU0G7AP/VgUdy9JHwORFOmG4StW7o5KElDN5ntzG9MkFDx6KSFTS
URXHJuT3VbDsKnkOj7nn3Bu2q3eKB1C9zT9CgUUqr4JWYCFYbR8FVMhmZyyGmvB+
yQIDAQAB
-----END PUBLIC KEY-----
EOF;
			$pub_k = openssl_pkey_get_public($pub_key);
			if (openssl_verify($signed_data, $signature, $pub_k) !== 1) {
				file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.' '.$this->uid.' signature_error'."\n",FILE_APPEND);
				return array('code'=>1, 'msg'=>'verify error');
			}
			$signed_data = json_decode($signed_data, true);
			file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.' signed_data='.print_r($signed_data,true)."\n",FILE_APPEND);
			$request_orders = $signed_data['orders'];
			if (empty($request_orders)) {
				return array('code'=>1, 'msg'=>'no-orders');
			}
			
			$success_count = 0;
			$amount_total = 0;
			foreach ($request_orders as $request_order) {
				$purchase_state = $request_order['purchaseState'];
				if ($purchase_state !== 0) {
					continue;
				}
				$product_id = $request_order['productId'];
				file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.' '.$this->uid.' product_id='.$product_id."\n",FILE_APPEND);
				
				$channel_trans_id = $request_order['orderId'];
				if (empty($channel_trans_id)) {
					continue;
				}			
				else {
					$transactionid = $channel_trans_id;					
					if(empty($transactionid)){
						file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.' '.$this->uid.' trans_id_empty '."\n",FILE_APPEND);
						continue;
					}
					
					$payinfoid = intval(substr($product_id, -1));
					
					$baseinfo = array('1'=>'500,4.99','2'=>'1200,9.99','3'=>'2500,19.99','4'=>'6500,49.99,','5'=>'14000,99.99');
					$payinfo = explode(',', $baseinfo[$payinfoid]);
					file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg." trsid=$transactionid".' orderid='.$payinfoid.' uid='.$this->uid."\r\n",FILE_APPEND);
					$accountMC = $this->createAccountModel('',$this->uid);
					$account = $accountMC->getAccount();
					$gameuid = $account['gameuid'];
					$payMD = new PayLog($gameuid,$this->uid);
					$payMD->useHandlerSocket = false;
					
					$amount = intval($payinfo['0']);
					if(empty($amount)){
						continue;
					}
					$cash = $payinfo['1'];
					
					$exist = $payMD->read(array('transaction_id'=>$transactionid));
					if($exist){
						$success_count++;
						$amount_total += $amount;
						file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg." trans_id exist:trans_id=$transactionid".' uid='.$this->uid."\r\n",FILE_APPEND);
						continue;
					}
			
				//验证ok，给玩家加上充值的内容
				if(empty($account)){
					file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.'no this uid='.$this->uid."\r\n",FILE_APPEND);
					continue;
		        }else{
		        	file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/paygoogle.log', $msg.'uid='.$this->uid.' amount='.$amount."\r\n",FILE_APPEND);
					$change = array('points'=>$amount);
		    	    $accountMC->updateAccount($change);
		    	    //写入log
		    	    $payMD->setAmount($amount);
		    	    $payMD->setTransactionId($transactionid);

		    	    $payMD->setCash($cash);
		    	    $payMD->setLocale('google');
		    	    $payMD->setStatus('1');
					$payMD->add();
					$success_count++;
				    $amount_total += $amount;
					
		        }
		}
		}
		try {
			if($transactionid && $this->uid && $amount_total && $cash){
				$this->add_payelex_log($transactionid, $this->uid, $amount_total, $cash);	
			}
		} catch (Exception $e) {
			continue;
		}
		return array('code'=>0,'points'=>$amount_total);
		}
	}

	public function add_payelex_log($transaction_id,$uid,$amount,$cash){
		$msg = sprintf("[%s]",date('Y-m-d H:i:s'));
		$params = array();
		$params['channelTransId'] = $transaction_id;//交易ID
		$params['uid'] = $uid;//用户UID(gameuid)
		//支付渠道 
		$params['channel'] = 'googlecheckout';
		//交易状态，成功是APP_SUCCESS失败是 APP_FAILED退款是APP_REFUNDED
		$params['status'] = 'APP_SUCCESS';
		//应用ID xingcloud 平台上使用的fhw支付key
		$params['appId'] = '6aea90c004b1013082ba782bcb1b6cfd';
		//虚拟币数量，只能是数字
		$params['amount'] = $amount;
		//货币类型
//		$params['currency'] = 'USD';
		//交易金额,只能是数字
		$params['gross'] = $cash;
		//税费
		$params['fee'] = $cash*0.3;
		$params['timestamp'] = time();
		//然后将以上参数生成token
		$api_secret = 'e6WyOz,PnAuvDd7JYjO,';
		ksort($params);
		$sign_str = $api_secret;
		foreach($params as $k=>$v){
		 	$sign_str .= $k.$v;
		}
		$str=strtoupper ( md5 ( $sign_str ) );
		$url="http://pay.337.com/payelex/api/mobile/mobile_transaction.php";
		$parstr='timestamp='.$params['timestamp'].'&uid='.$params['uid'].'&channel='.$params['channel'].'&status='.
		$params['status'].'&appId='.$params['appId'].'&amount='.$params['amount']./*'&currency='.$params['currency'].*/'&gross='.$params['gross'].
		'&fee='.$params['fee'].'&channelTransId='.$params['channelTransId'];
		$all=$url.'?'.$parstr.'&token='.$str;	
		file_put_contents('/home/elex/nginx/data/server_gp/www/amf/services/log/checkout.log', $msg.' parstr='.$parstr.' token='.$str."\n",FILE_APPEND);
		$out=file_get_contents($all);
	}


}

?>
