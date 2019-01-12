document.documentElement.style.fontSize = document.documentElement.clientWidth / 7.5 +'px';
var urlfirst = "http://dounile.tumujinhua.com/";
function plusReady(){

	// Android处理返回键
	var pageUrl=window.location.href;
	plus.key.addEventListener('backbutton',function(){
	//判断是否返回到首页，是->退出,否则返回上一页
	if(pageUrl.indexOf('index') < 0){
		history.back();
		scan.close();
		document.getElementById('index_page').style.left = '0';
		document.getElementById('footer').style.left = '0';
		document.getElementById('header_top').style.left = '0';
		document.getElementById('scan_page').style.left = '100%';

	}else{
	//if(confirm('确认退出？')){
		plus.runtime.quit();
	//}
	}
	},false);
}
//扩展API是否准备好，如果没有则监听“plusready"事件
if(window.plus){
plusReady();
}else{
document.addEventListener('plusready',plusReady,false);
}

function UnixToDate(unixTime, isFull, timeZone) {
    if (typeof (timeZone) == 'number')
    {
        unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
    }
    var time = new Date(unixTime * 1000);
    var ymdhis = "";
    ymdhis += time.getFullYear() + "-";
    ymdhis += (time.getMonth()+1) + "-";
    ymdhis += time.getDate();
//              if (isFull === true)
//              {
        ymdhis += " " + time.getHours() + ":";
        ymdhis += time.getMinutes() + ":";
        ymdhis += time.getSeconds();
//              }
    return ymdhis;
}


//$("input[type='text']").tap(function(){
//	footerFix(this);
//});
//$("input[type='number']").tap(function(){
//	footerFix(this);
//});
//$("input[type='password']").tap(function(){
//	footerFix(this);
//});
//
//function footerFix(event){
//	$(event).focus(function(){
//		$("#footer").css("position","static");
//	});
//	$(event).blur(function(){
//		$("#footer").css("position","fixed");
//	});
//}