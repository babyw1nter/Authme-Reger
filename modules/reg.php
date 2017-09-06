<?php

// 初始化session
session_start();

// 引入头文件
include './config.inc.php';
include 'api.php';
	// 阿里云盾风控SDK
	include_once 'aliyun-php-sdk-core/Config.php';
	use Jaq\Request\V20161123 as Jaq;

// 设置 date() 时区为中国时区
// 解决比系统时间差 6 小时的问题.
date_default_timezone_set('PRC');

// 创建阿里云盾风控对象
$iClientProfile = DefaultProfile::getProfile("cn-hangzhou", $Access_Key_ID, $Access_Key_Secret);
$client = new DefaultAcsClient($iClientProfile);

// 判断表单提交主函数
if (!$mysql_con){
	die('数据库连接失败.'); // 连接失败输出错误信息
} else {
	if(isset($_POST['submit'])){
		
		// 接收表单数据并存入变量
		$f_username = $_POST['username'];
			$f_username_s = strtolower($f_username);
		$f_password = $_POST['password'];
		$f_email = $_POST['email'];
		$f_emkey = $_POST['emailkey']; // 取表单邮箱验证码
		$f_key = $_POST['fkey'];
		$f_ip = getIP();
		$f_date = date("Y-m-d H:i:s");
			$f_date_unix = getUnix(); // 取Unix13位时间戳
			$f_fastreg_time = date_count(date("Y-m-d H:i:s"),'-'.$reg_time,'hour'); // 计算防多次注册范围时间
		$f_sscode = $_POST['session_code']; // 取表单隐藏域Session
			// 取表单云盾风控SSID & SIG等参数
			$f_cssid = $_POST['csessionid'];
			$f_sig = $_POST['sig'];
			$f_token = $_POST['token'];
			$f_scene = $_POST['scene'];
		
		// 云盾风控校验
		$request = new Jaq\AfsCheckRequest();
		$request->setSession($f_cssid);// 必填参数，从前端获取，不可更改
		$request->setSig($f_sig);// 必填参数，从前端获取，不可更改
		$request->setToken($f_token);// 必填参数，从前端获取，不可更改
		$request->setScene($f_scene);// 必填参数，从前端获取，不可更改
		$request->setPlatform(3);//必填参数，请求来源： 1：Android端； 2：iOS端； 3：PC端及其他
		$response = $client->doAction($request); // 提交校验
		$response_type = json_encode(object_array($response)); // 将返回对象转换为str	
		if(strstr($response_type,'success') == false){
			die('尚未完成风险验证.');
		}
			
 		// Session判断表单是否重复提交
		if(!isset($f_sscode) || $f_sscode != $_SESSION['code']){
			die('表单重复提交.');
		} else {
			unset($_SESSION['code']);
		}
		
		// 判断邮箱验证码
 		if(!isset($f_emkey) || $f_emkey != $_SESSION[$f_email]['em_key']){
			die('邮箱验证码错误.');
		} else {
			unset($_SESSION[$f_email]['em_key']);
		} 

		// 判断同IP是否在一定时间内重复注册
		$sql_text = "select * from `".$web_tablename."` where ip = '".$f_ip."' and `time` between '".$f_fastreg_time."' and '".$f_date."' limit 1;";
		$sql_fastreg_return = mysqli_query($mysql_con, $sql_text);			
		if(is_array(mysqli_fetch_row($sql_fastreg_return))){	
			$url = $Web_Url."/template/".$Web_Url_Msg."?s=fail_ip"; // 跳转报错
			echo "<script language='javascript' type='text/javascript'>window.location.href = '$url';</script>";  
			mysqli_close($mysql_con);
			die('您的IP暂时不能注册.');
		}
		
		// 取各个变量长度
		$un_len = strlen($f_username);
		$pw_len = strlen($f_password);
		$em_len = strlen($f_email);
		$fkey_len = strlen($f_key);
		
		// 正则判断各个变量是否符合格式, 不符合格式立即die();
		if ($un_len <= 4 || $un_len > 10 || !preg_match("/^[a-zA-Z][a-zA-Z0-9_]*$/", $f_username)){
			die('用户名格式错误.');
		} elseif ($pw_len <= 5 || $pw_len > 16 || !preg_match("/^[a-zA-Z][a-zA-Z0-9_]*$/", $f_password)){
			die('密码格式错误.');
		} elseif ($em_len <= 6 || $em_len > 30 || !preg_match("/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/", $f_email)){
			die('邮箱格式错误.');
		} else {	

			// 查询用户名是否存在
			$sql_text = "select 1 from " . $authme_tablename . " where ".$mySQLColumnName." = '" . $f_username . "' limit 1;";
			$sql_return = mysqli_query($mysql_con, $sql_text);
			if(is_array(mysqli_fetch_row($sql_return))){
				die('用户名已存在.'); 			 
			}
			
			// 查询邮箱是否存在
			$sql_text = "select 1 from " . $authme_tablename . " where ".$mySQLColumnEmail." = '" . $f_email . "' limit 1;";
			$sql_return = mysqli_query($mysql_con, $sql_text);
			if(is_array(mysqli_fetch_row($sql_return))){
				die('邮箱已存在.'); 
			}
			
			// 查询邀请码是否存在
			if($fkey_enabled){
				if($fkey_len <= $fkey_minlen || $fkey_len > $fkey_maxlen || !preg_match("/^[A-Za-z0-9]+$/", $f_key)){
					$url = $Web_Url."/template/".$Web_Url_Msg."?s=fail"; // 格式错误跳转报错
						echo "<script language='javascript' type='text/javascript'>window.location.href = '$url';</script>";
						mysqli_close($mysql_con);
						die('邀请码格式错误.');
				} else {
					$sql_text = "select * from " . $web_fkey_tablename . " where fkey = '" . $f_key . "' limit 1;";
					$sql_key_return = mysqli_query($mysql_con, $sql_text);
					$sql_key_Array = mysqli_fetch_row($sql_key_return);
					if(is_array($sql_key_Array)){ // 邀请码是否存在
						if(isset($sql_key_Array[2])){ // 邀请码已被使用
							$url = $Web_Url."/template/".$Web_Url_Msg."?s=fail"; // 已被使用跳转报错页面
							echo "<script language='javascript' type='text/javascript'>window.location.href = '$url';</script>";
							mysqli_close($mysql_con);
							die('邀请码已被使用.');
						} else { // 修改邀请码使用者字段
							$sql_text = "update " . $web_fkey_tablename . " set `usedate` = '". $f_date ."', username = '" . $f_username . "'" . " where fkey = '" . $f_key . "';";
							$sql_key_return = mysqli_query($mysql_con, $sql_text); if($sql_key_return == false){ die(); }
						}
					}
				}
			}
			
			// 密码加密,判断算法
			if($pw_enc == 'SHA256'){
				$f_pwd_sha = SHA256Salt($f_password, $pw_enc_salt_len * 2);
			} // TODO else if (){ ...
			
			// 插入记录至 web 数据表
			$sql_text = "INSERT INTO " . $web_tablename . " (`username`, `password`, `email`, `fkey`, `ip`, `time`) VALUES ('".$f_username."', '".$f_pwd_sha."', '".$f_email."', '".$f_key."', '".$f_ip."', '".$f_date."')";
			$sql_web_return = mysqli_query($mysql_con, $sql_text); if($sql_web_return == false){ die(); }
			
			// 插入记录至 authme 数据表
			$sql_text = "INSERT INTO " . $authme_tablename . " (`".$mySQLColumnId."`, `".$mySQLColumnName."`, `".$mySQLColumnPassword."`, `".$mySQLColumnIp."`, `".$mySQLColumnLastLogin."`, `".$mySQLlastlocX."`, `".$mySQLlastlocY."`, `".$mySQLlastlocZ."`, `".$mySQLlastlocWorld."`, `".$mySQLColumnEmail."`, `".$mySQLColumnLogged."`, `".$mySQLRealName."`) VALUES (NULL, '".$f_username_s."', '".$f_pwd_sha."', '".$f_ip."', '".$f_date_unix."', '0', '0', '0', NULL, '".$f_email."', '0', '".$f_username."')";
			$sql_atm_return = mysqli_query($mysql_con, $sql_text); if($sql_atm_return == false){ die(); }
			
			$url = $Web_Url."/template/".$Web_Url_Msg."?s=ok";				
			
		}
		
		// 程序结束跳转
		echo "<script language='javascript' type='text/javascript'>window.location.href = '$url';</script>";  
		mysqli_close($mysql_con);
		die('注册成功.');

	}
}

?>
