<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="static/style.css" rel="stylesheet" type="text/css">
<title><?php echo $title;?></title>
</head>
<body>

<table width="100%" border="0" cellspacing="5" cellpadding="0">
  <tr>
    <td width="20%" valign="top"><?php include renderTemplate('menu'); ?></td>
    <td width="80%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td>您好,<?php echo $login_user->username;?>！<?php if($last_login_time > 0 ) { ?>最后登陆时间：<?php echo $last_login_time;?><?php } ?></td>
  </tr>
  <tr>
  <td>
  <table height="100%" width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#CCCCCC">
        <tr>
          <td bgcolor="#FFFFFF">
  

      