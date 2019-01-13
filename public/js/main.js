document.documentElement.style.fontSize = document.documentElement.clientWidth / 7.5 + 'px';

Zepto(function($){

	var urlfirst = "app.labaca.cn";
	var re01 = /^1[3|4|5|7|8][0-9]{9}$/,
		re02 = /^[0-9A-Za-z\u4e00-\u9fa5]{2,10}$/,
		re03 = /^\w{6,16}$/,
		re04 = /^[0-9a-zA-Z]{6,18}$/,
		re05 = /^[0-9]{4}$/;

	$(".errCue").tap(function(){
		$(this).children().hide();
		$(this).hide();
	});
	$(".errCue").children().tap(function(){
		$(this).children().hide();
		$(this).hide();
	});


	$(".ipt a.check").tap(function(){
		var i=$(this).attr("data");
		if (i==1) {
			$(this).attr("data",0).removeClass("active");
		} else{
			$(this).attr("data",1).addClass("active");
		}
	});


//	报错提示
	function markmsg(err){
		$(".markmsg").text(err).css({"opacity":"1","z-index":"100"});
		$(".mark").show();
		setTimeout(function(){
			$(".markmsg").text(err).css({"opacity":"0","z-index":"-1"});
			$(".mark").hide();
		},2000);
	}


	$("#reg #msgTest").tap(function yanz() {
//		var phone = $("#reg #phone").val();
		if ($("#reg #phone").val()=="") {
    		return markmsg("手机号码不能为空！");
    	}
		if (!re01.test($("#reg #phone").val())) {
			return markmsg("请输入格式正确的11位手机号码！");
		}else{
			$.ajax({
		     	url:"http://"+urlfirst+"/getverify",
		     	type:"post",
		     	dataType:"json",
		     	data:{
		     		phone:$("#reg #phone").val(),
		     		type:1
		     	},
		     	success:function(data){
		     		// console.log(data);
		     		return markmsg(data.msg);
		     	}
		    });
		    var num = 60;
	        var numCode = setInterval(function () {
	        	$("#reg #msgTest").off('tap',yanz);
	        	num--;
	        	$("#reg #msgTest").css("backgroundColor","#e6e6e6").html("已发送"+num+"s");
	            if (num < 0) {
	                clearInterval(numCode);
	                $("#reg #msgTest").css("backgroundColor","#fff").html("发送验证码").on('tap',yanz);
	            }
	        }, 1000);
		}
	})

	//点击获取验证码 出现倒计时 且按钮禁用

	$("#reg a.button").on("tap",function reg(){
		// if ($("#reg #phone").val()=="") {
  //   		return markmsg("手机号码不能为空！");
  //   	}
    	if (!re01.test($("#reg #phone").val())) {
			return markmsg("请输入格式正确的11位手机号码");
		}
		if ($("#reg #name").val().length==0) {
			return markmsg("姓名不能为空！");
		}
		if (!re02.test($("#reg #name").val())) {
			return markmsg("请检查格式，只能输入中文字母数字，2到10位！");
		}
		if ($("#reg #password").val().length==0) {
			return markmsg("密码不能为空！");
		}
		if (!re03.test($("#reg #password").val())) {
			return markmsg("密码长度6位以上16位以内");
		}
		if ($("#reg #passwordAg").val().length==0) {
			return markmsg("请确认密码！");
		}
		if (!re03.test($("#reg #passwordAg").val())) {
			return markmsg("密码长度6位以上16位以内！");
		}
		if ($("#reg #passwordAg").val()!=$("#reg #password").val()) {
			return markmsg("2次输入密码不一致！");
		}
		if (!$("#reg a.check").hasClass("active")){
			return markmsg("请勾选阅读并同意《相关协议》！");
		}
		if ($("#reg #iphoneTj").val().length==0){
			return markmsg("推荐人不能为空");
		}
		else{
			$.ajax({
				type:"post",
				url:"http://"+urlfirst+"/save",
				dataType:"json",
				data:{
					phone:$("#reg #phone").val(),
					code:$("#verification").val(),
					realname:$("#reg #name").val(),
					passwords:$("#reg #password").val(),
					recommend_code:$("#iphoneTj").val(),
					type:1
				},
				success:function(data){
					//console.log(data);
					if(data.status==1){
//		     			登录成功 跳转首页
		     			markmsg("注册成功，即刻登录");
		     			location.href="login.html";
		     		}else{
		     			return markmsg(data.msg);
		     		}
				}
			});
		}
	});


//	跳转修改密码页
	$("#login .forget").on("tap",function(){
		location.href = "reg.html";
	});

//	跳转注册页
	$("#login .forget").on("tap",function(){
		location.href = "reg.html";
	});

	$("#login a.button").on("tap",function log(){
		if ($("#login #phone").val()=="") {
			return markmsg("手机号码不能为空！");
		}
		if (!re01.test($("#login #phone").val())) {
			return markmsg("请输入格式正确的11位手机号码！");
		}
		if ($("#login #password").val()=="") {
			return markmsg("密码不能为空！");
		}
		if (!re03.test($("#login #password").val())) {
			return markmsg("密码长度6位以上16位以内！");
		}
		else{
			//登录提交
			$.ajax({
				type:"post",
				url:"http://"+urlfirst+"/login",
				data:{
					tel:$("#login #phone").val(),
					password:$("#login #password").val()
				},
				dataType:'json',
				success:function(data){
					console.log(data);
					if(data.status == 1){
						//保存用户id
						localStorage.setItem('userid',data.id);
						//保存用户角色  消费者还是商家
						localStorage.setItem('role',data.role);
						//保存token值  用于验证是否异地登录以及登录是否超时
						localStorage.setItem('token',data.token);
						//登录成功后跳转
						location.href="index.html";
					}else if(data.status < 1){
						//登录失败，弹出失败原因
						return markmsg(data.msg);
					}
				}
			});
		}
	});

//	news
	$('.news-nav li').tap(function(){
		$(this).addClass('select').siblings().removeClass('select');

		var index = $(this).index();
		if(index == 0){
			$('.pay-part1').css('display','block');
			$('.pay-part2,.pay-part3,.pay-part4').css('display','none');
		}
		if(index == 1){
			$('.pay-part2').css('display','block');
			$('.pay-part1,.pay-part3,.pay-part4').css('display','none');
		}
		if(index == 2){
			$('.pay-part3').css('display','block');
			$('.pay-part2,.pay-part1,.pay-part4').css('display','none');
		}
		if(index == 3){
			$('.pay-part4').css('display','block');
			$('.pay-part2,.pay-part3,.pay-part1').css('display','none');
		}
	});
});