<?php

// 引入头文件
// include 'config.inc.php';

// 连接数据库 & 选择数据库
$mysql_con = mysql_connect($ip, $username, $password); // 连接数据库
$mysql_sql = mysql_select_db($sqlname ,$mysql_con); // 选择数据库

// 取客户端IP地址函数
function getIP(){
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

/* 
// 判断IP地址是否禁止访问
function CheckIP($_ip){
	$ipAddress = GetIpLookup($_ip);
	if(!isset($ipAddress)){
		return true;
	}
	if($ban_mode == '0'){
		if($ipAddress['city'] == $ban_city){
			return true;
		} else {
			return false;
		}
	} elseif($ban_mode == '1'){
		if($ipAddress['province'] == $ban_province){
			return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}
*/

// IP地址取地名函数-新浪接口
function GetIpLookup($ip = ''){  
	if(empty($ip)){  
	return 'Error'; 
	die();
	}  
	$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);  
	if(empty($res)){ return false; }  
	$jsonMatches = array();  
	preg_match('#\{.+?\}#', $res, $jsonMatches);  
	if(!isset($jsonMatches[0])){ return false; }  
	$json = json_decode($jsonMatches[0], true);  
	if(isset($json['ret']) && $json['ret'] == 1){  
	$json['ip'] = $ip;  
	unset($json['ret']);  
	}else{  
	return false;  
	}  
	return $json;  
} 


// 对象转数组
function object_array($array) {  
    if(is_object($array)) {  
        $array = (array)$array;  
     } if(is_array($array)) {  
         foreach($array as $key=>$value) {  
             $array[$key] = object_array($value);  
             }  
     }  
     return $array;  
}


// SHA256 Salt 算法
function SHA256Salt($Str = '', $Salt_len = 0){
	if(!$Str || $Salt_len == 0) { return 'Error'; }
	$RStr =  getRStr($Salt_len);  
	$pw_hash = hash('sha256', $Str, false);
	$pw_salt_hash = hash('sha256', $pw_hash.$RStr, false);
	return '$SHA$'.$RStr.'$'.$pw_salt_hash;
}


// 生成指定长度随机字符串
function getRStr($len){  
    $chars = array(  
        "a", "b", "c", "d", "e", "f", "0", "1", "2",    
        "3", "4", "5", "6", "7", "8", "9"  
    );  
    $charsLen = count($chars) - 1;  
    shuffle($chars);    // 将数组打乱   
        
    $output = "";  
    for ($i=0; $i<$len; $i++)  
    {  
        $output .= $chars[mt_rand(0, $charsLen)];  
    }  
    return $output;  
}  


// 取当前时间13位Unix时间戳
function getUnix(){ 
	list($t1, $t2) = explode(' ', microtime()); 
	return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000); 
} 

// 日期加减
function date_count($date_,$count_,$unit_){
	return date("Y-m-d H:i:s",strtotime($date_." ".$count_." ".$unit_));
}

?>