<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="static/style.css" rel="stylesheet" type="text/css">
<title><?php echo $title;?></title>
<script type="text/javascript">
function checkLogin(){
	var username = $("#username").val();
	if(!jQuery.trim(username)){
		alert("用户名不能为空。");
		$("#username").val("");
		$("#username").focus();
		return false;
	}
	var password = jQuery.trim($("#password").val());
	if(password.length < 4){
		alert("密码长度不能小于4个字符。");
		$("#password").val("");
		$("#password").focus();
		return false;
	}
	return true;
}
</script>
</head>
<body>

<br>
<br>
<br>
	<form id="form" name="form_login" method="post" action="admincp.php?mod=admin&act=index">
	<table width="60%" border="0" align="center" cellpadding="10" cellspacing="1" bgcolor="#CCCCCC">
	  <tr>
	    <td colspan="2" align="center" bgcolor="#000000" class="title"><?php echo $title;?></td>
	  </tr>
	  <?php if($error_msg) { ?>
	  <tr>
	    <td style="font-size:14px;" align="center" colspan="2" bgcolor="#FFFFFF"><?php include renderTemplate('error_msg'); ?></td>
	  </tr>
	  <?php } ?>
	  <tr>
	    <td width="36%" align="right" bgcolor="#FFFFFF">用户:</td>
	    <td width="64%" bgcolor="#FFFFFF"><input name="username" type="text" id="username" size="30" maxlength="50"/></td>
	  </tr>
	  <tr>
	    <td align="right" bgcolor="#FFFFFF">密码:</td>
	    <td bgcolor="#FFFFFF"><input name="password" type="password" id="password" size="30" maxlength="50"/></td>
	  </tr>
	  <tr>
	    <td colspan="2" align="center" bgcolor="#FFFFFF">
		<input name="user_login" type="submit" value="登录" onclick="return checkLogin()"/>
		</td>
	    </tr>
	</table>
	</form>
<script language="javascript" src="static/jquery-1.2.6.min.js"></script>
</body>
</html>