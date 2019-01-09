//tab页面，需要判断，其他页面不需要
var authorization = storage.getItem('authorization');
var url = baseurl+"/creditorderinfo/list",
page = 1,
pagesize= 10;
var data1,data2;

data1 = {
	page: page,
	pagesize: pagesize,
	handelStatus: 0
}
//审核中
//getOrderList(data1,auditfunc);

data2 = {
	page: page,
	pagesize: pagesize,
	handelStatus: 1
}
//已完成
//getOrderList(data2,donefunc);

/*
//下拉刷新
pulldown(url,{
	page: page,
	pagesize: pagesize,
	handelStatus: 0
},"#audit",$(".audit_list"),getOrderList,auditfunc);
$(".nav-tabs").find("li").on("click",function(){
	if($(this).siblings().hasClass("active")){
		if($(this).index() == 0){
			pulldown(url,data1,"#audit",$(".audit_list"),getOrderList,auditfunc);//审核中
		}else if($(this).index() == 1){
			
			pulldown(url,data2,"#done",$(".done_list"),getOrderList,donefunc);//已完成
			
		}
	}
})

*/
//下拉刷新回调函数

var html = ''

function getOrderList(data,func){
	var data = {
			page: page,
			pagesize: pagesize,
			handelStatus: data.handelStatus
	}
	$.ajax({
		   type : 'POST',  
		   contentType : 'application/json',  
		   url:baseurl+"/creditorderinfo/list",
		   headers: {    
			   authorization : authorization
		   },
		   data: JSON.stringify(data),  
		   dataType : 'json',  
		   success : function(response) { 
			   console.log(response)
			   if (response.code =='0000') {
				   var res = response.data.list;
				   var len = res.length;
				   if (len > 0) {
					   func(res,len);
					   page++;
				   }
			   }
			  
		   },  
		   error : function(data) {  
			   
		   }  
	});
}

function auditfunc(res,len){
	for(var i = 0; i < len; i++){
		  var bankpinyin=getbankPinyin(res[i].bankId);
		  var createTime = timestampToTime(res[i].createTime);
		  html += '<div class="order_item">';
		  html += '<div class="order_no">';
		  html += '<text class="order_item_title order_no_title">订单编号</text>';
		  html += '<text class="order_item_detail">' + res[i].orderNo +'</text>';
		  html += '</div>';
		  html += '<div class="card_detail">';
		  html += '<div class="order_img">';
		  html += '<img src="http://cre-card.oss-cn-beijing.aliyuncs.com/images/mine/order/' + bankpinyin+ '.png" />';
		  html += '</div>';
		  html += '<div class="order_info">';
		  html += '<div class="order_info_item">';	
		  html += '<text class="order_item_title">申请人：</text>';
		  html += '<text class="order_item_detail">'+res[i].merName+'</text>';
		  html += '</div>';
		  html += '<div class="order_info_item">';
		  html += '<text class="order_item_title">手机号：</text>';
		  html += '<text class="order_item_detail">'+res[i].merMobile+'</text>';
		  html += '</div>';
		  html += '<div class="order_info_item">';
		  html += '<text class="order_item_title">申请时间：</text>';
		  html += '<text class="order_item_detail">'+createTime+'</text>';
		  html += '</div>';
		  html += '</div>';
		  html += '</div>';
		  html += '</div>';
	  }
	  $(".audit_list").append(html)
	  html = "";
}
function donefunc(res,len){
	for(var i = 0; i < len; i++){
		 	var bankpinyin=getbankPinyin(res[i].bankId);
		  var createTime = timestampToTime(res[i].createTime);
		  var completeTime = timestampToTime(res[i].completeTime);
		  html += '<div class="order_item">';
		  html += '<div class="order_no">';
		  html += '<text class="order_item_title order_no_title">订单编号</text>';
		  html += '<text class="order_item_detail">' + res[i].orderNo +'</text>';
		  html += '</div>';
		  html += '<div class="card_detail">';
		  html += '<div class="order_img">';
		  html += '<img src="http://cre-card.oss-cn-beijing.aliyuncs.com/images/mine/order/' + bankpinyin+ '.png" />';
		  html += '</div>';
		  html += '<div class="order_info">';
		  html += '<div class="order_info_item">';	
		  html += '<text class="order_item_title">申请人：</text>';
		  html += '<text class="order_item_detail">'+res[i].merName+'</text>';
		  html += '</div>';
		  html += '<div class="order_info_item">';
		  html += '<text class="order_item_title">手机号：</text>';
		  html += '<text class="order_item_detail">'+res[i].merMobile+'</text>';
		  html += '</div>';
		  html += '<div class="order_info_item">';
		  html += '<text class="order_item_title">申请时间：</text>';
		  html += '<text class="order_item_detail">'+createTime+'</text>';
		  html += '</div>';
		  html += '<div class="order_info_item">';
		  html += '<text class="order_item_title">下卡时间：</text>';
		  html += '<text class="order_item_detail">'+completeTime+'</text>';
		  html += '</div>';
		  html += '</div>';
		  html += '</div>';
		  html += '</div>';
	  }
	  $(".done_list").append(html)
	  html = "";
}