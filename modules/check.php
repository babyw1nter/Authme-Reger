<?php

// 初始化session
session_start();

include 'config.inc.php';
include 'api.php';
require_once 'smtp.class.php';

// 禁止浏览器直接打开
$fromurl = $Web_Url; //跳转往这个地址。
if( $_SERVER['HTTP_REFERER'] == "" )
{
header("Location:".$fromurl); exit;
}

// 判断数据库是否已连接
if (!$mysql_con){
	die('<b>Error</b>: ' . mysqli_error()); // 连接失败输出错误信息
}

// 判断用户名是否可用
if($_GET['action'] == 'checkid'){
	usleep(500); //此处usleep用于缓解数据库查询压力
	$chk_username = $_GET['username'];
		$chk_un_len = strlen($chk_username);
		// 正则判断是否符合格式.也是为了防Sql注入
		if ($chk_un_len <= 4 || $chk_un_len > 10 || !preg_match("/^[a-zA-Z][a-zA-Z0-9_]*$/", $chk_username)){
			die();
		}
	$sql_text = "select 1 from " . $authme_tablename . " where username = '" . $chk_username . "' limit 1;";
	$sql_return = mysqli_query($mysql_con, $sql_text);
    if(is_array(mysqli_fetch_row($sql_return))){
		echo '1'; 
    }else{
		echo '0'; 
    }
}

// 判断邮箱是否可用
if($_GET['action'] == 'checkem'){
	usleep(500); // 作用同上
	$chk_email = $_GET['email'];
	$chk_em_len = strlen($chk_email);
		// 作用同上
		if ($chk_em_len <= 6 || $chk_em_len > 30 || !preg_match("/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/", $chk_email)){
			die();
		}
	$sql_text = "select 1 from " . $authme_tablename . " where email = '" . $chk_email . "' limit 1;";
	$sql_return = mysqli_query($mysql_con, $sql_text);
    if(is_array(mysqli_fetch_row($sql_return))){
		echo '1'; 
    }else{
		echo '0'; 
    }
}

// Smtp发送邮件
if($_GET['action'] == 'sendemkey'){
	unset($_SESSION['em_key']); // 清空session
	$_SESSION['em_key'] = mt_rand(0,1000000); // 生成随机session
	$send_email = $_GET['email'];
		$em_title = "菜市场小镇服务器--邮箱身份验证";
		$em_text = "您正在进行邮箱验证，请在邮箱验证输入框中输入此次验证码：<br><b><font size='5' color=red>".$_SESSION['em_key']."</font><b><br>如非本人操作，请忽略此邮件，由此给您带来的不便请谅解！<br><br><br><br><br>我的世界菜市场服务器<br>2017年06月19日";
	$smtp = new Smtp($smtp_server, $smtp_port, true, $smtp_username, $smtp_password); // 创建smtp对象
	$smtp -> debug = false; // 是否显示发送的调试信息
	$smtp -> sendmail($send_email, $smtp_username_em, $em_title, $em_text, "HTML");
}

// 判断邮箱验证码是否和session一致
if($_GET['action'] == 'checkemkey'){
	//usleep(5500); // 作用同上
	$chk_emailkey = $_GET['key'];
	$chk_emkey_len = strlen($chk_emailkey);
		// 作用同上, 单纯判断数字而已
		if ($chk_emkey_len <= 5 || $chk_emkey_len > 6 || !preg_match("/^[0-9]*$/", $chk_emailkey)){
			die();
		}
    if(!isset($_SESSION['em_key']) || $chk_emailkey == $_SESSION['em_key']){
		echo '0'; 
    }else{
		echo '1'; 
    }
}

//mysql_close($mysql_con);

?>