/* ----------------------------------------
 * Index页面控制js(需要jQuery支持)	  
 * 编写: sc0utzz 创建日期: 2017.6.14
 * 06-18新增: 邮箱验证码检测
 * 06-19新增: 阿里云盾滑动验证码
 * 09-03修改: 重写部分function
 * 09-06修复: 邮箱验证码部分BUG
 * ----------------------------------------
*/

var namecheck = false, pwcheck = false, apwcheck = false, emcheck = false, emkeycheck = false, keycheck = false, afs_ = false;
var isCD = false;
var email_ = null;

// 判断是否开启邀请码
if($("#fkey-li").css("display") == "none"){
	keycheck = true;
} else {
	keycheck = false;
}

// 设置焦点
$("#id").focus();

// 禁用邮件发送btn
$("#sdem").addClass('btn-disabled');
$("#sdem").attr("disabled", true);

// 检测用户名
$("#id").focus(function (){
	$("#id").css("color","#666");
	$("#i-tt1").hide();
});

$("#id").blur(function (){
	var regtext = /^[a-zA-Z][a-zA-Z0-9_]*$/; //正则表达式
	var userid = $("#id").val(); // 取input内容
	if (userid.length <= 4 || !regtext.test(userid)) {
		$("#i-tt1").html("<span><i class='ion-span ion-android-alert'></i>5-10个英文+数字+下划线组合</span>");
		if(userid.length == 0){
			$("#i-tt1").html("<span><i class='ion-span ion-android-alert'></i>请输入用户名</span>");
		}
		namecheck = false;
	} else {
		$("#id").css("color","#666");
		checkun(); // Get提交检测用户名是否存在
	}
	$("#i-tt1").show();
});

// 检测邮箱
$("#em").focus(function (){
	$("#em").css("color","#666");
	$("#i-tt2").hide();
	if(isCD == false){
		$("#sdem").addClass('btn-disabled');
		$("#sdem").attr("disabled", true);
	}
});

$("#em").blur(function (){
	var emtext = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
	var em = $("#em").val();
	if(isCD == false){
		// 按钮禁用
		$("#sdem").addClass('btn-disabled');
		$("#sdem").attr("disabled", true);
	}
	if (em.length <= 6 || !emtext.test(em)) {
		$("#i-tt2").html("<span><i class='ion-span ion-android-alert'></i>请输入正确的邮箱格式</span>");
		if(em.length == 0){
			$("#i-tt2").html("<span><i class='ion-span ion-android-alert'></i>请输入邮箱</span>");
		}
		emcheck = false;
	} else {
		$("#em").css("color","#666");
		if(email_ != null && email_ != $('#em').val()){
			$("#i-tt3").html("<span><i class='ion-span ion-android-alert'></i>验证码错误！</span>");
			$("#i-tt3").show();
			emkeycheck = false;
		} else {
			checkem(); // Get提交检测
		}
	}
	$("#i-tt2").show();
});

// 检测邮箱验证码
$("#emkey").focus(function (){
	$("#emkey").css("color","#666");
	$("#i-tt3").hide();
});

$("#emkey").blur(function (){
	var emkeytext = /^[0-9]*$/;
	var emkey = $("#emkey").val();
	if (emkey.length <= 5 || emkey.length > 6 || !emkeytext.test(emkey)) {
		$("#i-tt3").html("<span><i class='ion-span ion-android-alert'></i>请输入正确的邮箱验证码格式（4个以上的数字）</span>");
		if(emkey.length == 0){
			$("#i-tt3").html("<span><i class='ion-span ion-android-alert'></i>请输入邮箱验证码</span>");
		}
		$("#i-tt3").show();
		emkeycheck = false;
	} else {
		$("#emkey").css("color","#666");
		checkemkey(); // Get提交检测
	}
});

// 邮箱发送验证码按钮点击事件
var countdown = 0;  
function sendem(){
	if(emcheck){
		$("#sdem").addClass('btn-disabled');
		$("#sdem").attr("disabled", true);
		countdown = 60;
		settime();
		var getUrl = "./modules/check.php?action=sendemkey&email=" + $('#em').val();
		$.get(getUrl,function(str){ console.log(str); });
		$('#emkey').val("");
		$("#emkey").focus();
		$("#i-tt3").html("<span style='color: #63da5c;'><i class='ion-span ion-ios-checkmark'></i>验证码已发送至邮箱</span>");
		$("#i-tt3").show();
		email_ = $("#em").val(); // 记录邮箱
		emkeycheck = false;
	} 
}

function settime() {  
    if(countdown == 0) {  
        $("#sdem").html('获取验证码');
		$("#sdem").removeClass('btn-disabled');
		$("#sdem").attr("disabled", false);
		clearTimeout(tmt); 
		isCD = false;
		return;
    } else {    
        $("#sdem").html("重新发送(" + countdown + ")");
		$("#sdem").addClass('btn-disabled');
		$("#sdem").attr("disabled", true);
		isCD = true;
        countdown--;  
    }  
    var tmt = setTimeout(settime, 1000);
}

// 检测密码
$("#pw").focus(function (){
	$("#pw").css("color","#666");
	$("#i-tt4").hide();
});

$("#pw").blur(function (){
	//var pwtext = /^[a-zA-Z][a-zA-Z0-9_]*$/;
	var pwtext = /^[\x21-\x7E]*.{5,15}$/;
	var pw = $("#pw").val();
	if (pw.length <= 5 || !pwtext.test(pw)) {
		$("#i-tt4").html("<span><i class='ion-span ion-android-alert'></i>请输入6-16个（英文，数字，符号）</span>");
		if(pw.length == 0){
			$("#i-tt4").html("<span><i class='ion-span ion-android-alert'></i>请输入密码</span>");
		}
		pwcheck = false;
	} else {
		$("#i-tt4").html("<span style='color: #63da5c;'><i class='ion-span ion-ios-checkmark'></i></span>");
		pwcheck = true;
		$("#pw").css("color","#666");
	}
	$("#i-tt4").show();
});

// 检测重复密码
$("#apw").focus(function (){
	$("#apw").css("color","#666");
	$("#i-tt5").hide();
});

$("#apw").blur(function (){
	var pw = $("#pw").val();
	var apw = $("#apw").val();
	if (pw != apw || apw.length == 0) {
		$("#i-tt5").html("<span><i class='ion-span ion-android-alert'></i>两次输入的密码不一致</span>");
		if(apw.length == 0){
			$("#i-tt5").html("<span><i class='ion-span ion-android-alert'></i>请输入确认密码</span>");
		}
		apwcheck = false;
	} else {
		$("#i-tt5").html("<span style='color: #63da5c;'><i class='ion-span ion-ios-checkmark'></i></span>");
		apwcheck = true;
		$("#apw").css("color","#666");
	}
	$("#i-tt5").show();
});


// 检测邀请码
$("#fkey").focus(function (){
	$("#fkey").css("color","#666");
	$("#i-tt5plus").hide();
});

$("#fkey").blur(function (){
	var fkeytext = /^[A-Za-z0-9]+$/; //正则表达式
	var fkey = $("#fkey").val(); // 取input内容
	if (fkey.length < 4 || !fkeytext.test(fkey)) {
		if(fkey.length == 0){
			$("#i-tt5plus").html("<span><i class='ion-span ion-android-alert'></i>请输入邀请码</span>");
		} else {
			$("#i-tt5plus").html("<span><i class='ion-span ion-android-alert'></i>请输入4-11位邀请码（英文，数字）</span>");			
		}
		keycheck = false;
	} else {
		$("#i-tt5plus").html("<span style='color: #63da5c;'><i class='ion-span ion-ios-checkmark'></i></span>");
		keycheck = true;
	}
	$("#i-tt5plus").show();

});


// 判断表单可否提交
function check(){
	if(namecheck && emcheck && emkeycheck && pwcheck && apwcheck && keycheck && afs_){
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
			$("#i-tt1").html("<span><i class='ion-span ion-android-alert'></i>该用户名已被注册！</span>");
		} else {
			$("#i-tt1").html("<span style='color: #63da5c;'><i class='ion-span ion-ios-checkmark'></i></span>");
			namecheck = true;
		}
		$('#i-tt1').show();
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
			$("#i-tt2").html("<span><i class='ion-span ion-android-alert'></i>该邮箱已被注册！</span>");
		} else {
			$("#i-tt2").html("<span style='color: #63da5c;'><i class='ion-span ion-ios-checkmark'></i></span>");
			if(isCD == false){
				// 按钮解禁
				$("#sdem").removeClass('btn-disabled');
				$("#sdem").attr("disabled", false);
			}
			emcheck = true;
		}
		$('#i-tt2').show();
	})
}

// Get判断邮箱验证码是否正确
function checkemkey(){
	emkeycheck = false;
	var getEm = $('#em').val();
	var getEmkey = $('#emkey').val();
	var getUrl = "./modules/check.php?action=checkemkey&email=" + getEm + "&key=" + getEmkey; // 现在邮箱也一并提交供后端判断
	$.get(getUrl,function(str){ 
		//console.log(str);
		if(str == '1' && email_ == $('#em').val()){
			$("#i-tt3").html("<span style='color: #63da5c;'><i class='ion-span ion-ios-checkmark'></i></span>");
			emkeycheck = true;
		} else {
			$("#i-tt3").html("<span><i class='ion-span ion-android-alert'></i>验证码错误！</span>");
		}
		$('#i-tt3').show();
	})
}

// 创建阿里云盾风控滑动验证条
var nc = new noCaptcha();
var nc_appkey = 'FFFF0000000001750EFC';  // 应用标识，请勿修改
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
		$("#i-tt6").html("<span style='color: #63da5c;'><i class='ion-span ion-ios-checkmark'></i></span>");
		$('#i-tt6').show();
		afs_ = true;
	}
};
nc.init(nc_option);

function ckboxOnClick(){
	if($("input[type='checkbox']").is(':checked')){
		$('#rbtn').removeClass('btn-disabled');
		$("#rbtn").attr("disabled", false);
		$("#i-tt7").hide();
	} else {
		$('#rbtn').addClass('btn-disabled');
		$("#rbtn").attr("disabled", true);
		$("#i-tt7").html("<span style='top: 1px; left: 103px;'><i class='ion-span ion-android-alert'></i>瞅啥呢？把这个勾上！</span>");
		$("#i-tt7").show();
	}
}

// 注册按钮点击事件
function btnclick(){
	if(check()){
		$('#rbtn').html('正在注册...');
		//$('#rbtn').addClass('btn-disabled');
		//$("#rbtn").attr("disabled", true);
	} /* else if(namecheck == false) {
		$("#i-tt1").show();
	} else if(emcheck == false) {
		$("#i-tt2").show();
	} else if(emkeycheck == false) {
		$("#i-tt3").show();
	} else if(pwcheck == false) {
		$("#i-tt4").show();
	} else if(apwcheck == false) {
		$("#i-tt5").show();
	}  */else if(afs_ == false) {
		$("#i-tt6").html("<span style='top: 1px; left: 103px;'><i class='ion-span ion-android-alert'></i>请完成滑动验证</span>");
		$("#i-tt6").show();
	}
}


