<?php

// 定义网站Url信息, 结尾不带斜杠
$Web_Url = 'http://www.yourwebsite.com/reg';
$Web_Url_Msg = 'message.html';

// 定义数据库信息
$ip = 'localhost';
$username = 'root';
$password = '123456';

// 定义数据库名
$sqlname = 'mcserver';

// 定义数据库表名
$authme_tablename = 'authme';
$web_tablename = 'webreg';
$web_fkey_tablename = 'webreg_fkey';

// 定义注册设置
$ban_id = '*admin*,*fuck*,*op*'; // 注册保留关键字
$reg_time = 8; // 同IP多少小时内只能注册1个ID


// 定义禁止访问地区 & 禁止模式
// 0 为 ban 市区 , 1 为 ban 省区
$ban_mode = '0';
$ban_province = '江苏';
$ban_city = '常州';

// 定义Stmp邮件服务器信息
$smtp_server = 'smtp.exmail.qq.com';
$smtp_port = 25;
$smtp_username_em = 'your@email.com'; // 发件地址
$smtp_from_username = 'Admin'; // 发件人
$smtp_username = 'your@email.com';
$smtp_password = 'password';
//$smtp_emtitle = '';

// 定义云盾KEY
$Access_Key_ID = '';
$Access_Key_Secret = '';

// 定义authme密码加密算法
$pw_enc = 'sha256';
$pw_enc_salt_len = 8;

?>
