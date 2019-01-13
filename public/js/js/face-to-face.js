$(function(){
	function GetQueryString(name){
		var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);
		if(r!=null)return  unescape(r[2]); return null;
	}
	var phone = GetQueryString("phone");
	var shop_name = GetQueryString("shop_name");
	alert(phone+shop_name);
	$("#merchatName").val(shop_name);
	$("#seller_phone").val(phone);
});