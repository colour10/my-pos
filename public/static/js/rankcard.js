$.ajax({
	type : 'get',  
    url: "/agent/wx/firstcard",
    dataType: 'json',
    timeout: 99999,
    processData: false,
    contentType: false,
    success : function(data) {

            // 打印
            var jsonObj = data.data;

            // 如果不是空，那么就写入卡号
        	if (data.length != '0') {
                var html = "";
                var length = jsonObj.length;
                for (var i=0; i<length; i++) {
                    html+='<div class="card_box">';
                    html+='<img src="'+jsonObj[i].bankImg+'" />';
                    html+='<span class="card_number">'+jsonObj[i].new_card_number+'</span>';
                    html+='</div>';
                    html+='<div class="changeCard_box">';
                    html+='<div class="changeCardBtn">';
                    html+='<div class="change_item" id ="changebank" >';
                    html+='<img src="/static/images/change.png" class="changeImg"></img>';
                    html+='<span class="change_text" onclick="window.location.href=\'/agent/wx/'+ jsonObj[i].id +'/wxeditcard\'">更换银行卡</span>';
                    html+='</div></div></div>';
                }

        		$("#card_exist").html(html);
        		// var cardAcctName = data.data.cardAcctName;
        		// var cardCredNo = data.data.cardCredNo;
        		// var cardId = data.data.cardId;
        		// $("#changebank").click(function(){
        		// 	var acctName = encodeURI(encodeURI(cardAcctName));
        		// 	window.location.href='changebankcard.html?cardAcctName='+acctName+'&cardCredNo='+cardCredNo+'&cardId='+cardId+'&number='+Math.random();
        		// })
    			} else { 
    				var html = "";
    				 html+='<img class="non_img" src="/static/images/wuyinhang.png"/>';
    				 html+='<div class="changeCard_box">';
    				 html+='<div class="changeCardBtn">';
    				 html+='<div class="change_item addCard">';
    				 html+='<img src="/static/images/add.png" class="changeImg"></img>';
    				 html+='<span class="change_text">添加银行卡</span>';
    				 html+='</div></div></div>';
    				$("#card_non_existe").html(html);
    				$(".addCard").click(function(){
    					window.location.href='/agent/wx/';
    				});
    			}

    },
	error : function(data) {  
    	
	}  
});


