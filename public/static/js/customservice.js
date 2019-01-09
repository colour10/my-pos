var authorization = storage.getItem('authorization');
$.ajax({
	   type : 'POST',  
	   contentType :'application/json',  
	   url:baseurl+"/appinfo/detail",
	   headers: {    
		   authorization : authorization
	   },
	   data : {},  
	   dataType : 'json',  
	   success : function(data) { 
		   if (data.code =="0000") {
			   var html='';
			   html+='<a href="tel:'+data.data.appTelephone+'">' 
			   html+='<span class="info_item_title">客服电话</span>' 
			   html+='<span class="info_item_detail">'+data.data.appTelephone+'</span>' 
			   html+='<span class="info_item_btn">立即拨打</span>	</a>' 
			   $(".info_item").html(html);
		   }
		   
	   }
})