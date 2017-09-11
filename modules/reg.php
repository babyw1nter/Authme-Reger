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
$iClientProfile = DefaultProfile::getProfile("cn-hangzhou", $setting['aliyun']['Access_Key_ID'], $setting['aliyun']['Access_Key_Secret']);
$client = new DefaultAcsClient($iClientProfile);

// 判断表单提交主函数
if (!$mysql_con){
	die('数据库连接失败.'); // 连接失败输出错误信息
} else {
	if(isset($_POST['submit'])){
		
		// 接收表单数据并存入变量
		$f_ = array(); // 数组初始化
		$f_['username'] = $_POST['username'];
		$f_['username_s'] = strtolower($f_['username']); // 取小写用户名
		$f_['password'] = $_POST['password'];
		$f_['email'] = $_POST['email'];
		$f_['emkey'] = $_POST['emailkey']; // 取表单邮箱验证码
		$f_['key'] = $_POST['fkey'];
		$f_['ip'] = getIP();
		$f_['date'] = date("Y-m-d H:i:s");
		$f_['date_unix'] = getUnix(); // 取Unix13位时间戳
		$f_['fastreg_time'] = date_count(date("Y-m-d H:i:s"),'-'.$reg_time,'hour'); // 计算防多次注册范围时间
		$f_['sscode'] = $_POST['session_code']; // 取表单隐藏域Session
		// 取表单云盾风控SSID & SIG等参数
		$f_['cssid'] = $_POST['csessionid'];
		$f_['sig'] = $_POST['sig'];
		$f_['token'] = $_POST['token'];
		$f_['scene'] = $_POST['scene'];
			
 		// Session判断表单是否重复提交
		if(!isset($f_['sscode']) || $f_['sscode'] != $_SESSION['code']){
			die('表单重复提交.');
		} else {
			unset($_SESSION['code']);
		}
		
		// 云盾风控校验
		$request = new Jaq\AfsCheckRequest(); // 创建阿里云盾风控对象
		$request->setSession($f_['cssid']);
		$request->setSig($f_['sig']);
		$request->setToken($f_['token']);
		$request->setScene($f_['scene']);
		$request->setPlatform(3); // 必填参数, 请求来源: 1.Android端 2.iOS端 3.PC端及其他
		$response = $client->doAction($request); // 提交校验
		$response_type = json_encode(object_array($response)); // 将返回对象转换为str	
		if(strstr($response_type,'success') == false){
			die('尚未完成风险验证.');
		}
		
		// 判断邮箱验证码
 		if(!isset($f_['emkey']) || $f_['emkey'] != $_SESSION[$f_['email']]['em_key']){
			die('邮箱验证码错误.');
		} else {
			unset($_SESSION[$f_['email']]['em_key']);
		} 

		// 判断同IP是否在一定时间内重复注册
		$sql_text = "select * from `".$setting['mysql']['webreg_db']."` where ip = '".$f_['ip']."' and `time` between '".$f_['fastreg_time']."' and '".$f_['date']."' limit 1;";
		$sql_fastreg_return = mysqli_query($mysql_con, $sql_text);			
		if(is_array(mysqli_fetch_row($sql_fastreg_return))){	
			$url = $setting['web']['Web_Url']."/template/".$setting['web']['Web_Url_Msg']."?s=fail_ip";
			echo $setting['web']['Web_Url_Script'].$url."';</script>"; // 跳转报错
			mysqli_close($mysql_con);
			die('您的IP暂时不能注册.');
		}
		
		// 取各个变量长度
		$un_len = strlen($f_['username']);
		$pw_len = strlen($f_['password']);
		$em_len = strlen($f_['email']);
		$fkey_len = strlen($f_['key']);
		
		// 正则判断各个变量是否符合格式, 不符合格式立即die(); 安全守则: 永远不要相信用户的输入..
		if ($un_len <= 4 || $un_len > 10 || !preg_match("/^[a-zA-Z][a-zA-Z0-9_]*$/", $f_['username'])){
			die('用户名格式错误.');
		} elseif ($pw_len <= 5 || $pw_len > 16 || !preg_match("/^[\x21-\x7E]*$/", $f_['password'])){
			die('密码格式错误.');
		} elseif ($em_len <= 6 || $em_len > 30 || !preg_match("/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/", $f_['email'])){
			die('邮箱格式错误.');
		} else {	

			// 查询用户名是否存在
			$sql_text = "select 1 from ".$setting['mysql']['authme_db']." where ".$setting['authme']['mySQLColumnName']." = '".$f_['username_s']."' limit 1;";
			$sql_return = mysqli_query($mysql_con, $sql_text);
			if(is_array(mysqli_fetch_row($sql_return))){
				die('用户名已存在.'); 			 
			}
			
			// 查询邮箱是否存在
			$sql_text = "select 1 from ".$setting['mysql']['authme_db']." where ".$setting['authme']['mySQLColumnEmail']." = '".$f_['email']."' limit 1;";
			$sql_return = mysqli_query($mysql_con, $sql_text);
			if(is_array(mysqli_fetch_row($sql_return))){
				die('邮箱已存在.'); 
			}
			
			// 查询邀请码是否存在
			if($setting['fkey']['enabled']){
				if($fkey_len <= $setting['fkey']['minlen'] || $fkey_len > $setting['fkey']['maxlen'] || !preg_match("/^[A-Za-z0-9]+$/", $f_['key'])){
					$url = $setting['web']['Web_Url']."/template/".$setting['web']['Web_Url_Msg']."?s=fail"; // 格式错误跳转报错
						echo $setting['web']['Web_Url_Script'].$url."';</script>";
						mysqli_close($mysql_con);
						die('邀请码格式错误.');
				} else {
					$sql_text = "select * from ".$setting['mysql']['fkey_db']." where fkey = '".$f_['key']."' limit 1;";
					$sql_key_return = mysqli_query($mysql_con, $sql_text);
					$sql_key_Array = mysqli_fetch_row($sql_key_return);
					if(is_array($sql_key_Array)){ // 邀请码是否存在
						if(isset($sql_key_Array[2])){ // 邀请码已被使用
							$url = $setting['web']['Web_Url']."/template/".$setting['web']['Web_Url_Msg']."?s=fail";
							echo $setting['web']['Web_Url_Script'].$url."';</script>"; // 已被使用跳转报错页面
							mysqli_close($mysql_con);
							die('邀请码已被使用.');
						} else { // 修改邀请码使用者字段
							$sql_text = "update ".$setting['mysql']['fkey_db']." set `usedate` = '".$f_['date']."', username = '".$f_['username']."'" . " where fkey = '" .$f_['key']."';";
							$sql_key_return = mysqli_query($mysql_con, $sql_text); if($sql_key_return == false){ if($setting['system']['debug']){ die('<b>插入邀请码数据表失败</b>: ' . mysqli_error($mysql_con)); } else { die(); } }
						}
					} else {
							$url = $setting['web']['Web_Url']."/template/".$setting['web']['Web_Url_Msg']."?s=fail";
							echo $setting['web']['Web_Url_Script'].$url."';</script>";
							mysqli_close($mysql_con);
							die('邀请码不存在.');
					}
				}
			}
			
			// 密码加密,判断算法
			if($setting['authme']['pw_enc'] == 'SHA256'){
				$f_pwd_sha = SHA256Salt($f_['password'], $setting['authme']['pw_enc_salt_len'] * 2);
			} // TODO else if (){ ...
			
			// 插入记录至 webreg 数据表
			$sql_text = "INSERT INTO ".$setting['mysql']['webreg_db']." (`username`, `password`, `email`, `fkey`, `ip`, `time`) VALUES ('".$f_['username']."', '".$f_pwd_sha."', '".$f_['email']."', '".$f_['key']."', '".$f_['ip']."', '".$f_['date']."')";
			$sql_web_return = mysqli_query($mysql_con, $sql_text); if($sql_web_return == false){ if($setting['system']['debug']){ die('<b>插入网页数据表失败</b>: ' . mysqli_error($mysql_con)); } else { die(); } }
			
			// 插入记录至 authme 数据表
			$sql_text = "INSERT INTO ".$setting['mysql']['authme_db']." (`".$setting['authme']['mySQLColumnId']."`, `".$setting['authme']['mySQLColumnName']."`, `".$setting['authme']['mySQLColumnPassword']."`, `".$setting['authme']['mySQLColumnIp']."`, `".$setting['authme']['mySQLColumnLastLogin']."`, `".$setting['authme']['mySQLlastlocX']."`, `".$setting['authme']['mySQLlastlocY']."`, `".$setting['authme']['mySQLlastlocZ']."`, `".$setting['authme']['mySQLlastlocWorld']."`, `".$setting['authme']['mySQLColumnEmail']."`, `".$setting['authme']['mySQLColumnLogged']."`, `".$setting['authme']['mySQLRealName']."`, `".$setting['authme']['mySQLlastlocYaw']."`, `".$setting['authme']['mySQLlastlocPitch']."`) VALUES (NULL, '".$f_['username_s']."', '".$f_pwd_sha."', '".$f_['ip']."', '".$f_['date_unix']."', '0', '0', '0', '".$setting['authme']['spawn_world']."', '".$f_['email']."', '0', '".$f_['username']."', NULL, NULL)";
			$sql_atm_return = mysqli_query($mysql_con, $sql_text); if($sql_atm_return == false){ if($setting['system']['debug']){ die('<b>插入Authme数据表失败</b>: ' . mysqli_error($mysql_con)); } else { die(); } }
			
			$url = $setting['web']['Web_Url']."/template/".$setting['web']['Web_Url_Msg']."?s=ok";				
			
		}
		
		// 程序结束跳转
		echo $setting['web']['Web_Url_Script'].$url."';</script>";
		mysqli_close($mysql_con);
		die('注册成功.');

	} else {
		mysqli_close($mysql_con);
		die('非法提交.');
	}
}

?>
