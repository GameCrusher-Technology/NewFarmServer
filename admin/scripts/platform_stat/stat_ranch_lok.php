<?php
set_time_limit(0);
ignore_user_abort(true);
error_reporting(0);

date_default_timezone_set("Europe/Berlin");

$platform_name = "LOK";
$mail_to = "salina@elex-tech.com,zhangqinglai@elex-tech.com,luyi@elex-tech.com";
$mail_cc = "zhouyu@elex-tech.com,wangwenjing@elex-tech.com";

global $dsn;
$dsn = array(
	'host' => "10.32.11.170",
	'username' => "happyranch",
	'password' => "nealf6KkuSwzSLFonyplW7mL29Aop49E"
);

writeLog("start stat.");
$stat_start = date("Y-m-d",strtotime("-7 day"));
$stat_end = date("Y-m-d");
writeLog("start:".strtotime($stat_start).", end:".strtotime($stat_end));

//register num
$sql = "select count(*) as reg_num from elex_game_global.uid_gameuid_mapping where create_time < '".date("Y-m-d H:i:s",strtotime($stat_end))."'
 and create_time > '".date("Y-m-d H:i:s",strtotime($stat_start))."' and uid > 0";

try{
	$result = getAll($sql);
	if(isset($result[0])){
		$register_num = $result[0]['reg_num'];
		writeLog("get reg_num successfully. reg_num:".$register_num);
	}
	else 
		throw new Exception("result empty when get the register_num.");
}catch (Exception $e){
	writeErrorLog($e->getMessage());
}

//active user 2种方式 只分表 只分库
$active_num = 0;

$sql = "";
$conj = "";
//只分表方式(union 最多只能输出35个结果，奇怪?)
for($j=0;$j<4;$j++){
	for($i=25*$j;$i<25*($j+1);$i++){
		$sql .= " $conj (select count(*) as active_num from user_account.user_account_$i t1 join elex_game_global.uid_gameuid_mapping t2 on t1.gameuid = t2.gameuid and t2.uid > 0 and t1.load_time > ".strtotime($stat_start).")";
		$conj = "union";
	}
	
	//只分库方式
	//for($i=0;$i<10;$i++){
	//	$sql .= " $conj (select count(*) as active_num from user_account_$i.user_account t1 join elex_game_global.uid_gameuid_mapping t2 on t1.gameuid = t2.gameuid and t2.uid > 0 and t1.load_time > ".strtotime($stat_start).")";
	//	$conj = "union";
	//}
	
	//$sql .= " where load_time > ".strtotime($stat_start);
	try{
		$result = getAll($sql);
		if(!isset($result[0])){
			throw new Exception("result empty when get the active_num.");
		}
		
		foreach ($result as $tmp){
			$active_num += intval($tmp['active_num']);
		}
		if($j == 3)
			writeLog("get active_num successfully. active_num:".$active_num);
	}catch (Exception $e){
		writeErrorLog($e->getMessage());
	}
}

//pay user num
$sql = "select count(distinct t1.gameuid) as pay_user from trade_log.trade_log t1 join elex_game_global.uid_gameuid_mapping t2
 on t1.gameuid = t2.gameuid and t2.uid > 0 and t1.create_time > ".strtotime($stat_start);

try{
	$result = getAll($sql);
	if(isset($result[0])){
		$pay_user = $result[0]['pay_user'];
		writeLog("get pay_user successfully. pay_user:".$pay_user);
	}
	else 
		throw new Exception("result empty when get the pay_user.");
}catch (Exception $e){
	writeErrorLog($e->getMessage());
}

//pay money
$sql = "select sum(amount) as pay_money from trade_log.trade_log t1 join elex_game_global.uid_gameuid_mapping t2
 on t1.gameuid = t2.gameuid and t2.uid > 0 and t1.create_time > ".strtotime($stat_start);

try{
	$result = getAll($sql);
	if(isset($result[0])){
		$pay_money = $result[0]['pay_money'];
		writeLog("get pay_money successfully. pay_money:".$pay_money);
	}
	else 
		throw new Exception("result empty when get the pay_money.");
}catch (Exception $e){
	writeErrorLog($e->getMessage());
}

writeLog("start send mail.");
//send mail
$mail_subject = "The ".date("W")."th week $platform_name platform Happyranch statistics";
$mail_message = 
	"<html>
		<head>
		<title>$platform_name Happyranch [".$stat_start." 至 ".date("Y-m-d",strtotime("-1 day"))."] 统计数据</title>
		</head>
		<body>
			<center>
			<h3>$platform_name Happyranch [".$stat_start." 至 ".date("Y-m-d",strtotime("-1 day"))."] 统计数据</h3>
			<br />
			<table align='center' cellpadding='2' cellspacing='0' border='1' style='border: 1px'>
				<tr>
					<td>注册人数</td>
				  	<td>活跃用户数</td>
				 	<td>付费用户数</td>
				 	<td>平均每个用户付费情况</td>
				</tr>
				<tr>
				  	<td>".$register_num."</td>
				  	<td>".$active_num."</td>
				 	<td>".$pay_user."</td>
				 	<td>".sprintf("%.2f",$pay_money/$active_num)."</td>
				</tr>
			</table>
			</center>
		</body>
	</html>
	<br /><br />";
$mail_from = "Ranch Stat <happyranch@elex-tech.com>";

if(send_mail($mail_to,$mail_subject,$mail_message,$mail_from,$mail_cc)){
	writeLog("send mail successfully to:".$mail_to);
	writeLog("stat end.");
}
else 
	writeLog("send mail failure.");


function writeErrorLog($msg){
	$log_path = "log";
	if(file_exists($log_path))
		mkdir($log_path);
		
	$msg = "[".date("Y-m-d H:i:s")."]" . $msg . "\r\n";
	file_put_contents($log_path."/error.log",$msg,FILE_APPEND);
}

function writeLog($msg){
	$log_path = "log";
	if(!file_exists($log_path))
		mkdir($log_path);
		
	$msg = "[".date("Y-m-d H:i:s")."]" . $msg . "\r\n";
	file_put_contents($log_path."/info.log",$msg,FILE_APPEND);
}

function getAll($sql){
	global $dsn;
	$conn = mysql_connect($dsn['host'],$dsn['username'],$dsn['password'],true);
	
	if($conn === false){
		writeErrorLog("could not connect to database.");
		return false;
	}
	
	$result = mysql_query($sql,$conn);
	
	if($result === false){
		writeErrorLog("sql error.sql:".$sql);
		return false;
	}
	
	$return = array();
	
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$return[] = $row;
	}
	
	mysql_free_result($result);
	mysql_close($conn);
	
	return $return;
	
}

function query($sql){
	global $dsn;
	$conn = mysql_connect($dsn['host'],$dsn['username'],$dsn['password'],true);
	
	if($conn === false){
		writeErrorLog("could not connect to database.");
		return false;
	}
	
	$result = mysql_query($sql,$conn);
	
	if($result === false){
		writeErrorLog("sql error.sql:".$sql);
		return false;
	}
	
	mysql_free_result($result);
	mysql_close($conn);
	
	return true;
	
}

function send_mail($to,$subject,$message="",$from=null,$cc=null,$bcc=null){
	
	$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

	if($from != null)
		$headers .= 'From: ' . $from . "\r\n";
	if($cc != null)
		$headers .= 'Cc: ' . $cc . "\r\n";
	if($bcc != null)
		$headers .= 'Bcc: ' . $bcc . "\r\n";
	
	return mail($to,$subject,$message,$headers);
}
?>