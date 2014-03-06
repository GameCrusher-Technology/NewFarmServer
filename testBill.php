<?php
error_reporting(E_ALL);
set_time_limit(0);
if (!defined('APP_ROOT')) define('APP_ROOT',realpath(dirname(__FILE__)));
if (!defined('GAMELIB')) define('GAMELIB', APP_ROOT . '/libgame');
if (!defined('FRAMEWORK')) define('FRAMEWORK', APP_ROOT . '/framework');

require_once GAMELIB . '/config/GameConfig.class.php';
require_once FRAMEWORK . '/log/LogFactory.class.php';
require_once FRAMEWORK . '/db/RequestFactory.class.php';
require_once GAMELIB . '/GameConstants.php';

require_once GAMELIB.'/model/ManagerBase.class.php';
require_once GAMELIB.'/model/UserAccountManager.class.php';
require_once GAMELIB.'/model/UidGameuidMapManager.class.php';
require_once GAMELIB.'/model/XmlManager.class.php';
require_once GAMELIB.'/model/UserGameItemManager.class.php';
include_once GAMELIB.'/model/TradeLogManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
require_once FRAMEWORK . '/log/LogFactory.class.php';


//[info][2014-02-25 20:03:53-48350000] 1 || 2023719 || {"startId":8,"signature":"MlaguT03ZwiL0xRNfPwTb9ifhgp3VyMOBjk+9OK55algYgLIaf4PvTnt4HLsTqK8BiZIYhinZb4DWluI5M+9g2joOEg4j2gx9CkaVEzJ4QKMteJG7WN2nJaVLAbaFjP9cELAE34bjPihfRdRGUhcH3O5GiSUN/gwsZzQG9mZsq6ntKsBAuARAjeOTJcr9KLcQRKXhJQQRf0uvLoW2km724aNG+6ZAk9CqbQG2e9+ko3El2r6nUdslFF7eqDE1a8f6HxQKzWTauCwoMTIhgb52RP882l7e85a6HuFAbBHQcKOezTMEFE/7jvCvviONuuDPz0yXhjD5rGqtmM5rmeSIw==","signedData":"{\"nonce\":-5465853498583976000,\"orders\":[{\"purchaseTime\":1392002287294,\"packageName\":\"air.Farmland.andriod\",\"orderId\":\"12999763169054705758.1324177654896095\",\"notificationId\":\"-6441531684346654540\",\"purchaseState\":0,\"productId\":\"sunny_farm.littlefarmgem\",\"purchaseToken\":\"anvtjlveifgtpmxvncnxjeua\"}]}"} || {"startId":8,"signature":"MlaguT03ZwiL0xRNfPwTb9ifhgp3VyMOBjk+9OK55algYgLIaf4PvTnt4HLsTqK8BiZIYhinZb4DWluI5M+9g2joOEg4j2gx9CkaVEzJ4QKMteJG7WN2nJaVLAbaFjP9cELAE34bjPihfRdRGUhcH3O5GiSUN\/gwsZzQG9mZsq6ntKsBAuARAjeOTJcr9KLcQRKXhJQQRf0uvLoW2km724aNG+6ZAk9CqbQG2e9+ko3El2r6nUdslFF7eqDE1a8f6HxQKzWTauCwoMTIhgb52RP882l7e85a6HuFAbBHQcKOezTMEFE\/7jvCvviONuuDPz0yXhjD5rGqtmM5rmeSIw==","signedData":"{\"nonce\":-5465853498583976000,\"orders\":[{\"purchaseTime\":1392002287294,\"packageName\":\"air.Farmland.andriod\",\"orderId\":\"12999763169054705758.1324177654896095\",\"notificationId\":\"-6441531684346654540\",\"purchaseState\":0,\"productId\":\"sunny_farm.littlefarmgem\",\"purchaseToken\":\"anvtjlveifgtpmxvncnxjeua\"}]}"}
//
//		$receipt= array(
//					'signedData'=>"{\"nonce\":413141968697409363,\"orders\":[{\"notificationId\":\"-9205864378282155079\",\"orderId\":\"3827263939721300580\",\"packageName\":\"air.Farmland.andriod\",\"productId\":\"sunny_farm.largefarmgem\",\"purchaseTime\":1391278298911,\"purchaseState\":0,\"purchaseToken\":\"dptjykwbgsegszqmewxdzzif.wnK6fO5P9Q64uZO3NcByFXAF9FTHbGb3Xymdmr3po-jfK4u7CJyednYcT3iSoPVl30J3SVx1ZHJ-K3eLvkWQNeAKCdNx3k-fQnNrgKTGUJZYWE7F5HU-sgM\"}]}",
//					'startId'=>8,
//					'signature'=>"HL+MHU+N3SpINm8pMBsdwj6shENzlMkDE/R5PPqlZ6R4P7G1niHTbdKu48+amyHqLVcVYjFlJuZCmGx7qkaatzO3zRSUtFnMZ6bMwpuj41KlqGa0fJV1j3hm+siZYQ+bcwB+DZSu/oRA8NAxEGJQhO3y9ksu+eXmntR1YR/jzTvdnhP0+VHcDMUKhgwgfPbEob5heHjQzrbYRP0z1wCPeN1Yu92P5yB0GyU6Vwl/hk/Q1YmyY/0YMdNnCiG8nPBnZ9utGZgVK+h1vSY7EUjhRRae5OYm0UNBwjRPnbHm0wuH1ZvnUA+0hHn6lHHFzDBxkVNzLhFlLDe9QiH4E04JSA"
//					);
		$receipt= array(
					'signedData'=>"{\"nonce\":-5465853498583976126,\"orders\":[{\"notificationId\":\"-6441531684346654540\",\"orderId\":\"12999763169054705758.1324177654896095\",\"packageName\":\"air.Farmland.andriod\",\"productId\":\"sunny_farm.littlefarmgem\",\"purchaseTime\":1392002287294,\"purchaseState\":0,\"purchaseToken\":\"anvtjlveifgtpmxvncnxjeua\"}]}",
					'startId'=>3,
					'signature'=>"MlaguT03ZwiL0xRNfPwTb9ifhgp3VyMOBjk+9OK55algYgLIaf4PvTnt4HLsTqK8BiZIYhinZb4DWluI5M+9g2joOEg4j2gx9CkaVEzJ4QKMteJG7WN2nJaVLAbaFjP9cELAE34bjPihfRdRGUhcH3O5GiSUN/gwsZzQG9mZsq6ntKsBAuARAjeOTJcr9KLcQRKXhJQQRf0uvLoW2km724aNG+6ZAk9CqbQG2e9+ko3El2r6nUdslFF7eqDE1a8f6HxQKzWTauCwoMTIhgb52RP882l7e85a6HuFAbBHQcKOezTMEFE/7jvCvviONuuDPz0yXhjD5rGqtmM5rmeSIw=="
					);
//		$receipt= array(
//					'signedData'=>"{\"nonce\":-4845357686530827055,\"orders\":[{\"notificationId\":\"-995132349464726235\",\"orderId\":\"12999763169054705758.1300808982867287\",\"packageName\":\"air.Farmland.andriod\",\"productId\":\"sunny_farm.littlefarmgem\",\"purchaseTime\":1393156021303,\"purchaseState\":0,\"purchaseToken\":\"cexvbznzqxwzszivrnympjbd\"}]}",
//					'startId'=>2,
//					'signature'=>"NjpclvsGUU1wUP46HdjQPguIXJX8iokHjWv4hABRWQlADf46vVCg+qe4gBKaXeAQD/q4s5bGb+tmWgJFVAmOqcbPJaG4Vu7HvcUh5uHI8aqEaqeFLpx2FIw57wmIP3xH350S8h1CSUIRfUz1gtx2InsO3at8BmxY8bwbvADIfayktwI2Xi+cCU0ShV2B9A+g+ah6zY0xgYdZ7mN15zmXMBY90IZVSWDKWhVifysg8bruaZFdZOy8O5T5mh+eDaTsHxXMvVoyytAYzkUmebSe7HoUPAsC0e65lYE5f24ISnXqMOtfRsyPK/2xlbPJabOOfSyVuOl+FJuVexemuL4h6w=="
//					);
								
								
		
		$new_rec = array();
		foreach ($receipt as $key=>$value){
			$new_rec[$key] = $value;
		}
		$signature = $new_rec['signature'];
		$signed_data = $new_rec["signedData"];
		
		print_r($signed_data);
		echo '<br />';
		echo '<br />';
		
//		$signed_data = json_decode($signed_data);
//		print_r($signed_data);
//		echo '<br />';
		
		$keyStr=  "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnGq+mkH8cFacOY9UoWyi1tmAxa55pdmTpoexuMVKbOjbpsY8jwzBOxTO3VBsu7HSibYDTrn79t0uFj0YMsQ/wGK1sO/Ab08DlGEYqV7m5+QsqMcAtQ8UNUER+sGnQxnzTmr3Uq9izMkk69NXzkZRaO5lp8f4gbfRx3KT2JweWihjOyFhWdlWmHRBAJE81Wn2iFJzNGNr50XIC4VDOlt+ljcUD3vu9bZmqmgMryKwn4WtxV2o4UwT5RehpyGHAyQ6YX2jmDSfoR6z2UgajCedxGK5bfmnPZXj75DC4P08O+SlBCGhEq62o/I0sDNtdWdSVnb+HM7IcqqaEMEd6taZEwIDAQAB";
		$KEY_PREFIX = "-----BEGIN PUBLIC KEY-----\n";
	    $KEY_SUFFIX = '-----END PUBLIC KEY-----';
		$pub_key = $KEY_PREFIX . chunk_split($keyStr, 64, "\n") . $KEY_SUFFIX;
		$pub_k = openssl_get_publickey($pub_key);
		
		
		print_r($pub_k);
		echo '<br />';
		
//		$pub_k = openssl_pkey_get_public($pub_key);
		
		$r = openssl_verify($signed_data, base64_decode($signature), $pub_k);
		if ($r !== 1) {
			print_r( array('status'=>$r));
		}else{
			print_r( array('suce'=>$r));
		}
?>