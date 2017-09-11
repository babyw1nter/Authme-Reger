<?php

$setting = array(); // 初始化数组

// ----------------------------  WEB页面信息  ----------------------------- //
$setting['web']['Web_Url'] = 'http://www.website.com/Authme-Reger';	// 站点URL
$setting['web']['Web_Url_Msg'] = 'message.html';
$setting['web']['Web_Url_Script'] = "<script language='javascript' type='text/javascript'>window.location.href = '"; // 页面跳转js, 请勿修改.

// ----------------------------  注册设置项S  ----------------------------- //
// 条件设置
// TODO $setting['web']['ban_key'] = '*admin*,*fuck*,*op*'; 		// 注册保留关键字
$setting['web']['reg_time'] = 8; 					// 同IP多少小时内只能注册1个ID
// 禁止访问设置
$setting['web']['ban_mode'] = '0';					// 0 为 ban 市区 , 1 为 ban 省区
$setting['web']['ban_province'] = '江苏';				// 省区
$setting['web']['ban_city'] = '常州';					// 市区

// ----------------------------  数据库设置项  ----------------------------- //
$setting['mysql']['ip'] = 'localhost'; 					// 数据库IP
$setting['mysql']['username'] = 'root'; 				// 数据库用户
$setting['mysql']['password'] = 'password'; 				// 数据库密码
$setting['mysql']['sqlname'] = 'mcserver'; 				// 数据库名
$setting['mysql']['authme_db'] = 'authme'; 				// Authme表
$setting['mysql']['webreg_db'] = 'webreg'; 				// 网页表
$setting['mysql']['fkey_db'] = 'webreg_fkey'; 				// 邀请码表

// ----------------------------  Authme设置项  ----------------------------- //
// 字段设置
$setting['authme']['mySQLColumnId'] = 'id';
$setting['authme']['mySQLColumnName'] = 'username';
$setting['authme']['mySQLRealName'] = 'realname';
$setting['authme']['mySQLColumnPassword'] = 'password';
$setting['authme']['mySQLColumnEmail'] = 'email';
$setting['authme']['mySQLColumnLogged'] = 'isLogged';
$setting['authme']['mySQLColumnIp'] = 'ip';
$setting['authme']['mySQLColumnLastLogin'] = 'lastlogin';
$setting['authme']['mySQLlastlocX'] = 'x';
$setting['authme']['mySQLlastlocY'] = 'y';
$setting['authme']['mySQLlastlocZ'] = 'z';
$setting['authme']['mySQLlastlocWorld'] = 'world';
$setting['authme']['mySQLlastlocYaw'] = 'yaw';
$setting['authme']['mySQLlastlocPitch'] = 'pitch';
// 插件设置
$setting['authme']['pw_enc'] = 'SHA256'; 				// 现在改成大写了
$setting['authme']['pw_enc_salt_len'] = 8;				// Salt长度
$setting['authme']['spawn_world'] = 'world'; 				// 默认出生世界

// ----------------------------  SMTP邮件设置项  ----------------------------- //
$setting['smtp']['server'] = 'smtp.exmail.qq.com'; 			// SMTP地址
$setting['smtp']['port'] = 25; 						// SMTP端口
$setting['smtp']['from_email'] = 'admin@qq.cn'; 			// 发信人地址
$setting['smtp']['from_username'] = 'Admin'; 				// 发件人名称
$setting['smtp']['username'] = 'admin@qq.cn'; 				// SMTP用户名
$setting['smtp']['password'] = 'password';				// SMTP密码
$setting['smtp']['emtitle'] = '我的世界--邮箱身份验证'; 			// 邮件标题

// ----------------------------  阿里云滑动设置  ----------------------------- //
$setting['aliyun']['Access_Key_ID'] = '';
$setting['aliyun']['Access_Key_Secret'] = '';

// ----------------------------  邀请码功能设置  ----------------------------- //
$setting['fkey']['enabled'] = false; 					// true 开启 false 关闭
$setting['fkey']['minlen'] = 4;						// 最小长度
$setting['fkey']['maxlen'] = 11;					// 最大长度

// ----------------------------  服务条款设置项  ----------------------------- //
$setting['contract']['name'] = '《同性交友网服务条款》';			// 条款标题
$setting['contract']['url'] = '#';					// 条款URL

// ----------------------------  页脚版权设置项  ----------------------------- //
// TODO ...

// ----------------------------  系统功能设置项  ----------------------------- //
$setting['system']['debug'] = false;					// 是否显示错误信息

?>
