/* ----------------------------------------
 * Index页面控制js(需要jQuery支持)	  
 * 编写: Anan 创建日期: 2017.6.14
 * 06-18新增: 邮箱验证码检测
 * 06-19新增: 阿里云盾滑动验证码
 * ----------------------------------------
*/

var namecheck = false, pwcheck = false, apwcheck = false, emcheck = false, emkeycheck = false, keycheck = false, afs_ = false;

// 设置焦点
$("#id").focus();

// 检测用户名
$("#id").focus(function (){
	$("#id").css("color","#555");
	$("#i-tt1").hide();
});

$("#id").blur(function (){
	var regtext = /^[a-zA-Z][a-zA-Z0-9_]*$/; //正则表达式
	var userid = $("#id").val(); // 取input内容
	if (userid.length <= 4 || !regtext.test(userid)) {
		$("#i-tt1").html("昵称不合法");
		if(userid != ""){
			namecheck = false;
			$("#i-tt1").show();
		}
	} else {
		$("#id").css("color","#555");
		$("#i-tt1").hide();
		checkun(); // ajax提交检测用户名是否存在
	}
});

// 检测密码
$("#pw").focus(function (){
	$("#pw").css("color","#555");
	$("#i-tt2").hide();
});

$("#pw").blur(function (){
	var pwtext = /^[a-zA-Z][a-zA-Z0-9_]*$/;
	var pw = $("#pw").val();
	if (pw.length <= 5 || !pwtext.test(pw)) {
		if(pw != ""){
			pwcheck = false;
			$("#i-tt2").show();
		}
	} else {
		pwcheck = true;
		$("#pw").css("color","#555");
		$("#i-tt2").hide();
	}
});

// 检测重复密码
$("#apw").focus(function (){
	$("#apw").css("color","#555");
	$("#i-tt3").hide();
});

$("#apw").blur(function (){
	var pw = $("#pw").val();
	var apw = $("#apw").val();
	if (pw != apw) {
		if(apw != ""){
			apwcheck = false;
			$("#i-tt3").show();
		}
	} else {
		apwcheck = true;
		$("#apw").css("color","#555");
		$("#i-tt3").hide();
	}
});

// 检测邮箱
$("#em").focus(function (){
	$("#em").css("color","#555");
	$("#i-tt4").hide();
	$('#em').popover('show');
});

$("#em").blur(function (){
	var emtext = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
	var em = $("#em").val();
	if (em.length <= 6 || !emtext.test(em)) {
		$('#i-tt4').html('邮箱不正确');
		if(em != ""){
			emcheck = false;
			$("#i-tt4").show();
		}
	} else {
		$("#em").css("color","#555");
		$("#i-tt4").hide();
		checkem(); // Ajax提交检测
	}
	$('#em').popover('hide');
});

// 检测邮箱验证码
$("#emkey").focus(function (){
	$("#emkey").css("color","#555");
	$("#i-tt5").hide();
});

$("#emkey").blur(function (){
	var emkeytext = /^[0-9]*$/;
	var emkey = $("#emkey").val();
	if (emkey.length <= 3 || !emkeytext.test(emkey)) {
		if(emkey != ""){
			emkeycheck = false;
			$("#i-tt5").show();
		}
	} else {
		$("#emkey").css("color","#555");
		$("#i-tt5").hide();
		checkemkey(); // Ajax提交检测
	}
});

// 检测邀请码
$("#fkey").focus(function (){
	$("#fkey").css("color","#555");
	$("#i-tt6").hide();
	$('#fkey').popover('show');
});

$("#fkey").blur(function (){
	var fkeytext = /^[A-Za-z0-9]+$/; //正则表达式
	var fkey = $("#fkey").val(); // 取input内容
	if (fkey.length <= 4 || !fkeytext.test(fkey)) {
		if(fkey != ""){
			keycheck = false;
			$("#i-tt6").show();
		}
	} else {
		keycheck = true;
		$("#fkey").css("color","#555");
		$("#i-tt6").hide();
	}
	$('#fkey').popover('hide');
});


// 判断表单可否提交
function check(){
	if(namecheck && pwcheck && apwcheck && emcheck && emkeycheck && keycheck && afs_){
		return true;
	} else {
		return false;
	}
}


// Get判断用户名是否存在
function checkun(){
	namecheck = false;
	var getUn = $('#id').val();//获取文本框内容
	var getUrl = "./modules/check.php?action=checkid&username=" + getUn; 
	$.get(getUrl,function(str){ 
		if(str == '1'){
			$('#i-tt1').show();
			$('#i-tt1').html('该昵称已被注册');
		} else {
			$('#i-tt1').hide();
			namecheck = true;
		}
	})
}

// Get判断邮箱是否存在
function checkem(){
	emcheck = false;
	var getEm = $('#em').val();//获取文本框内容
	var getUrl = "./modules/check.php?action=checkem&email=" + getEm; 
	$.get(getUrl,function(str){ 
		//console.log(str);
		if(str == '1'){
			$('#i-tt4').show();
			$('#i-tt4').html('邮箱已被使用');
		} else {
			$('#i-tt4').hide();
			emcheck = true;
		}
	})
}

// Get判断邮箱验证码是否正确
function checkemkey(){
	emkeycheck = false;
	var getEmkey = $('#emkey').val();//获取文本框内容
	var getUrl = "./modules/check.php?action=checkemkey&key=" + getEmkey; 
	$.get(getUrl,function(str){ 
		//console.log(str);
		if(str == '1'){
			$('#i-tt5').show();
		} else {
			$('#i-tt5').hide();
			emkeycheck = true;
		}
	})
}

// 邮箱按钮点击事件
var countdown = 0;  
function sendem(){
	if(emcheck){
		$("#sdem").addClass('disabled');
		$("#sdem").attr("disabled", true);
		countdown = 60;
		settime();
		var getUrl = "./modules/check.php?action=sendemkey&email=" + $('#em').val();
		$.get(getUrl,function(str){ console.log(str); });
		$('#emkey').val("");
		emkeycheck = false;
	} 
}

function settime() {  
    if(countdown == 0) {  
        $("#sdem").html('发送验证码');
		$("#sdem").removeClass('disabled');
		$("#sdem").attr("disabled", false);
		$('#i-tt5').css('right','104px');
        //countdown = 60; 
		clearTimeout(tmt); 
		return;
    } else {    
        $("#sdem").html("重新发送(" + countdown + ")");
		$("#sdem").addClass('disabled');
		$("#sdem").attr("disabled", true);
        countdown--;  
    }  
		if(countdown < 9){
			$('#i-tt5').css('right','110px');
		} else {
			$('#i-tt5').css('right','116px');
		}
    var tmt = setTimeout(settime, 1000);
}  

// 创建阿里云盾风控滑动验证条
var nc = new noCaptcha();
var nc_appkey = 'FFFF0000000000000000';  // 应用标识
var nc_scene = 'register';  // 场景
var nc_token = [nc_appkey, (new Date()).getTime(), Math.random()].join(':');
var nc_option = {
	renderTo: '#afs',// 渲染到该DOM ID指定的Div位置
	appkey: nc_appkey,
	scene: nc_scene,
	token: nc_token,
	callback: function (data) { // 校验成功回调
		// 存入表单隐藏域
		$('#csessionid').val(data.csessionid);
		$('#sig').val(data.sig);
		$('#token').val(nc_token);
		$('#scene').val(nc_scene);
		afs_ = true;
	}
};
nc.init(nc_option);

// 注册按钮点击事件
function btnclick(){
	if(check()){
		$('#rbtn').html('正在注册...');
		$('#rbtn').addClass('disabled');
		//$("#rbtn").attr("disabled", true);
	}
}


