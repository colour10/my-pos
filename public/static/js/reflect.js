var storage = window.localStorage;
var authorization = storage.getItem('authorization');

//可提现金额
//可提现金额CreditCardInfo/amount
$.ajax({
	   type : 'POST',  
	   contentType : 'application/json', 
	   headers: {    
		   authorization : authorization
	   },
	   url:baseurl+"/CreditCardInfo/amount",
	   data : {},   
	   dataType : 'json',  
	   success: function(data){
		   $(".reflect_money_num").html(data.data.toFixed(2));
	   }
});


var info={
		page: 0,
		pagesize: 10
}
$.ajax({
   type : 'POST',  
   contentType : 'application/json', 
   headers: {    
	   authorization : authorization
   },
   url:baseurl+"/commision/commisionList",
   data : JSON.stringify(info),   
   dataType : 'json',  
   success: function(data){
	   console.log(data)
	    storage.setItem("reflect", data);
	   	if (data.code == "0031") {
			window.location.href="../login.html?number="+Math.random();
		}
	   	if (data.code == "0000") {
	   		if (data.data == "" || data.data == null) {
					var html="<div class='data_null'><img src='http://cre-card.oss-cn-beijing.aliyuncs.com/images/nodata.png' /></div>";
					$(".reflectList").append(html);
   			}else {
   				
   				var html = htmlString(data);
   				
   				
   				$(".reflectList").append(html);
   				
   				$(".reflect_item").click(function(){
   					var index = $(this).attr("data-index");
   					window.location.href = "reflectdetail.html?index="+index;
   				})
   			} 
	   	}
	   	storage.setItem('reflectDetail',JSON.stringify(data.data));
   }
});


function htmlString(data){
		var html = '';
		var len = data.data.length;
		
		for(var i = 0;i<len;i++){
			if(data.data[i].comStatus == 0){
				data.data[i].comStatus = "提现中";
			}else if(data.data[i].comStatus == 1){
				data.data[i].comStatus = "成功";
			}else{
				data.data[i].comStatus = "失败";
			}
			if(data.data[i].comSuccTime != null){
				data.data[i].comSuccTime = timestampToTime(data.data[i].comSuccTime);
			}else{
				data.data[i].comSuccTime = "-";
			}
			if(data.data[i].comCreateTime != null){
				data.data[i].comCreateTime = timestampToTime(data.data[i].comCreateTime);
			}else{
				data.data[i].comCreateTime = "-";
			}
			
			html += '<div class="reflectList_item reflect_item" data-index="'+i+'">';
				html += '<span class="item_money lineHeight">'+ data.data[i].comAmount.toFixed(2) +'</span>';
				html += '<span class="item_date lineHeight">'+ data.data[i].comCreateTime +'</span>';
				html += '<span class="item_state lineHeight">'+data.data[i].comStatus+'</span>';
				html += '<div class="item_right lineHeight">';
				html += '<img src="http://cre-card.oss-cn-beijing.aliyuncs.com/images/incentiveCard/reflect/you.png" />';
				html += '</div>';
				html += '</div>';
		}
		return html;
}



//提现
//判断是否已经绑卡及设置交易密码
function checkMessage(page){
	$.ajax({
		type : 'POST',  
	    contentType : 'application/json',  
	    url:baseurl+"/merchant/info/cardinfo",
	    data : {},  
	    headers: {    
			   authorization : authorization
		   },
	    dataType : 'json',  
	    success : function(data) {
	    	if (data.code == '0000') {
	        	if (data.data!=null) {
	        		//已经绑卡
	        		getPassword(page);
	        	}else{
	        		//未绑卡
	        		window.location.href = "/pages/mine/bindingbankcard.html?number="+Math.random();
	        	}
	    	}
	    }
	})
}

function getPassword(page){
	$.ajax({
		   type : 'POST',  
		   contentType : 'application/json',  
		   headers:{
			   authorization: authorization
		   },
		   url:baseurl+"/merchant/info/detail",
		   data : {},  
		   dataType : 'json',  
		   success : function(data) { 
			   if (data.code == "0000") {
				   var word = data.data.merPayPassword;
				   console.log(word);
				   var html="";
				   if(word == undefined ){
					    //未设置交易密码
	        			window.location.href = "/pages/mine/set_withdraw_pwd.html?number="+Math.random();
				   }else{
					    //已设置交易密码
	        			window.location.href = "/pages/incentivegold/" + page + ".html?number="+Math.random();
				   }
			   }
		   } 
		});
}