var authorization = storage.getItem('authorization');
console.log("********"+authorization);
$.ajax({
	   type : 'POST',  
	   contentType : 'application/json',  
	   url:baseurl+"/CreditCardInfo/carddetail",
	   data : {},  
	   headers: {    
		   authorization : authorization
	   },
	   dataType : 'json',  
	   success : function(data) {  
		   if (data.code =="0000") {
			   console.log(data.data);
			   var html='';
			   for (var i = 0; i < data.data.length; i++) {
				   //console.log(data.data[i]);
				   var bankpinyin=getbankPinyin(data.data[i].bankId);
				   var url = data.data[i].creditCardJinduUrl;
					   html+='<div class="grogress_item col-sm-6 col-xs-6" data-href="'+url+'">'
					   html+='<img src="http://cre-card.oss-cn-beijing.aliyuncs.com/images/rank_center/'+bankpinyin+'.png" />'
					   html+='<text>'+data.data[i].merCardName+'</text></div>'
					   $(".bankGrogress").html(html);
			   }
			   $(".grogress_item").on("click",function(){
				    var href = $(this)[0].dataset.href
				   		location.href = href
			   })
		   }
		   
	   }
	   

})