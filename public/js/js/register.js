alert(11);
$(function() {
	//解决输入框被软键盘遮挡
	$(document).ready(function(){

　　$('body').height($('body')[0].clientHeight);//处理软键盘弹出的影响
            $("#txtCustomsCode").keydown(function(e){//给所需要处理的文本框处理keydown事件
              e=e||window.event;
                var code = e.keyCode||e.which;
                if((code>=96&&code<=105)||(code>=65&&code<=90)||(code>=48&&code<=57)){//可以输入大小写字母、数字（包括小键盘）
                  var userkeydown = $(this).val().substr(14);
                    var reg = /\*/;
                    $(this).val($(this).val().replace(reg,userkeydown));
                    $(this).val($(this).val().substr(0,14))
                }
                else
                   $(this).val($(this).val().substr(0,14))
            })
       })

	//报错提示
	function markmsg(err) {
		$(".markmsg").html(err).css({
			"opacity": "1",
			"z-index": "100"
		});

		setTimeout(function() {
			$(".markmsg").css({
				"opacity": "0",
				"z-index": "-1"
			})
		}, 2000)
	}

	//注册界面的提示
	//用户手机号提示
	$("#phone").blur(function() {
		var name = $(this).val().trim();
		var reg = /^1\d{10}$/;
		if(name == "") {
			markmsg("请输入手机号");
		} else if(reg.test(name) == false) {
			markmsg("手机号格式错误");
		}
		//判断账号是否已经存在
		else{
			//验证手机号是否注册
			$.ajax({
				type: "post",
				url: url('checkPhone'),
				dataType: "json",
				data: {
					phone: name
				},
				success: function(re){
					alert(re.msg);
					if(re.status == 0){
						alert(re.msg);
					}
				}

			})
		}
	})

	//昵称
	$("#name").blur(function(){
		var name = $(this).val().trim();
		if(name == ""){
			markmsg("请输入昵称")
		}
	})

	//密码
	$("#password").blur(function() {
		var name = $(this).val().trim();
		var reg = /^\w{6,16}$/;
		if(name == "") {
			markmsg("请输入密码");
		} else if(reg.test(name) == false) {
			markmsg("密码格式错误");
		}
	})

	//推荐人
	$("#tjname").blur(function() {
		var name = $(this).val().trim();
		var reg = /^1\d{10}$/;

		if(name == "") {
			markmsg("请输入推荐人手机号");
		} else if(reg.test(name) == false) {
			markmsg("推荐人手机号格式错误");
		}
		//判断账号是否已经存在

	})

	//获取短信验证码
	$(".passcode").click(function yanz() {
		var name = $("#phone").val().trim();
		var reg = /^1\d{10}$/;
		if(name == "") {
			return markmsg("请输入手机号");
		} else if(reg.test(name) == false) {
			return markmsg("手机号格式错误");
		} else {
			//发送短信验证码
			$.ajax({
				url: httpUrl + "getverify",
				type: "POST",
				dataType: "json",
				data: {
					phone: name,
					type: 1
				},
				success: function(data) {
					return markmsg(data.msg);
				}
			})

			//重发验证码
			var num = 60;
			var numCode = setInterval(function() {
				$(".passcode").off("tap", yanz);
				num--;
				$(".passcode").css({
					"color": "#666"
				}).html("重发验证码" + num + "s")
				if(num < 0) {
					clearInterval(numCode);
					$(".passcode").css({
						"color": "#ff3535"
					}).html("获取短信验证码").on("tap", yanz)
				}
			}, 1000)

		}
	})

	//同意用户协议前的小图标
	$(".gou").click(function() {
		var img = $(this).find("img");
		if(img.attr("src") == "img/pic-gou.png1") {
			img.attr("src","{{asset('img/pic-quan.png1')}}");
		} else {
			img.attr("src","{{asset('img/pic-gou.png1')}}");
		}
	});

	//点击
	$(".zcbtn").click(function() {

		var phone = $("#phone").val().trim();
		var name = $("#name").val().trim();
		var password1 = $("#password").val().trim();
		var tjname = $("#tjname").val().trim();
		var code = $("#code").val().trim();
		var imgSrc = $(".gou img").attr("src");
		var reg = /^1\d{10}$/;
		var reg1 = /^\w{6,16}$/;
		var falg4 = false;

		if(phone == "") {
			markmsg("请输入手机号");
			$("#phone").focus();
		} else if(reg.test(phone) == false) {
			markmsg("手机号格式错误");
			$("#phone").focus();
		} else if(name == "") {
			markmsg("请输入您的昵称");
			$("#name").focus();
		} else if(password1 == "") {
			markmsg("请输入密码");
			$("#password").focus();
		} else if(reg1.test(password1) == false) {
			markmsg("密码格式错误");
			$("#password").focus();
		} else if(tjname == "") {
			markmsg("请输入推荐人手机号");
			$("#tjname").focus();
		} else if(reg.test(tjname) == false) {
			markmsg("手机号格式错误");
			$("#tjname").focus();
		} else if(code == "") {
			markmsg("验证码不能为空");
			$("#code").focus();
		} else if(imgSrc != "img/pic-gou.png") {
			markmsg("同意用户协议后可注册");
		} else {
			$.ajax({
				url: httpUrl + "save",
				type: "post",
				dataType: "json",
				data: {
					phone: phone,
					nickname:name,
					password:password1,
					pphone:tjname,
					code: code
				},
				success: function(data) {
					console.log(data);
					if(data.status == 1) {
						markmsg("注册成功");
						// location.href = "login.html?flag=" + phone;
					} else {
						//alert(data.msg);
						return markmsg(data.msg);
					}
				}
			})
		}
	})
})