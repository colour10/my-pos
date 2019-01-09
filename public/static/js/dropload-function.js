$(function(){
	var authorization = storage.getItem('authorization');

	// 页数
    var page = 1;
    var page1 = 1;
    // 每页展示10个
    var size = 10;
    var info,info1;
    // dropload
    $('.auditbox').dropload({
    	
        scrollArea : window,
        domUp : {
            domClass   : 'dropload-up',
            domRefresh : '<div class="dropload-refresh"></div>',
            domUpdate  : '<div class="dropload-update"></div>',
            domLoad    : '<div class="dropload-load"><span class="loading"></span></div>'
        },
        domDown : {
            domClass   : 'dropload-down',
            domRefresh : '<div class="dropload-refresh"></div>',
            domLoad    : '<div class="dropload-load"><span class="loading"></span></div>',
            domNoData  : '<div class="dropload-noData"></div>'
        },
        loadDownFn : function(me){
            info = {
    			page: page++,
    			pagesize: size,
    			handelStatus: 0
        	}
            // 拼接HTML
            var result = '';
//            getOrderList(info,auditfunc);
//            me.resetload();
            
            $.ajax({
            	type: 'POST',
            	contentType : 'application/json',
            	headers:{
            		authorization: authorization
            	},
                url: baseurl+"/creditorderinfo/list",
                dataType: 'json',
                data: JSON.stringify(info),
                success: function(data){
                	if (data.code == "0031") {
    					window.location.href="../login.html?number="+Math.random();
    				}
    			   	if (data.code == "0000") {
    			   		if (data.data == "" || data.data == null) {
    			   			me.lock();
    			   		 	me.noData();
    			   		 	$(".auditbox .dropload-down").html("<div class='nodata'>暂无数据！</div>");
     			   			$(".auditbox .dropload-down").addClass("dropload-down-nodata");
						}else{
							if(page == 2 && data.data.list.length <= 0){
								var html = "<div class='data_null'><img src='http://cre-card.oss-cn-beijing.aliyuncs.com/images/nodata.png' /></div>";
								$(".auditbox").html(html);
								$(".auditbox .dropload-down").remove();
								
							}else{
								if((page-1) > data.data.pages){
		                			me.lock();
		                            me.noData();
		                            $(".auditbox .dropload-down").html("<div class='nodata'>没有更多数据了！</div>");
		        			   		$(".auditbox .dropload-down").addClass("dropload-down-nodata");
		                		}else{
		                			var arrLen = data.data.list.length;
		                            if(arrLen > 0){
		                            	setTimeout(function(){
		                            		auditfunc(data.data.list,arrLen);
		                                    me.resetload();
		                                },1000);
		                            }else{
		                                me.lock();
		                                me.noData();
		                                $(".auditbox .dropload-down").html("<div class='nodata'>没有更多数据了！</div>");
		            			   		$(".auditbox .dropload-down").addClass("dropload-down-nodata");
		                            } 
		                		}
							}
		                		
						}
    			   	}else{
    			   		$(".auditbox .dropload-down").html("<div class='nodata'>暂无数据！</div>");
    			   		$(".auditbox .dropload-down").addClass("dropload-down-nodata");
    			   	}
//                	console.log(data.data)
                	
                },
                error: function(xhr, type){
                    //alert('Ajax error!');
                    // 即使加载出错，也得重置
                    me.resetload();
                }
            });
        },
        threshold : 50
    });
    $('.donebox').dropload({
        scrollArea : window,
        domUp : {
            domClass   : 'dropload-up',
            domRefresh : '<div class="dropload-refresh"></div>',
            domUpdate  : '<div class="dropload-update"></div>',
            domLoad    : '<div class="dropload-load"><span class="loading"></span></div>'
        },
        domDown : {
            domClass   : 'dropload-down',
            domRefresh : '<div class="dropload-refresh"></div>',
            domLoad    : '<div class="dropload-load"><span class="loading"></span></div>',
            domNoData  : '<div class="dropload-noData"></div>'
        },
        loadDownFn : function(me){
            info1 = {
    			page: page1++,
    			pagesize: size,
    			handelStatus: 1
        	}
            // 拼接HTML
            var result = '';
//            getOrderList(info,auditfunc);
//            me.resetload();
            
            $.ajax({
            	type: 'POST',
            	contentType : 'application/json',
            	headers:{
            		authorization: authorization
            	},
                url: baseurl+"/creditorderinfo/list",
                dataType: 'json',
                data: JSON.stringify(info1),
                success: function(data){
//                	console.log(data.data)
                	if (data.code == "0031") {
    					window.location.href="../login.html?number="+Math.random();
    				}
    			   	if (data.code == "0000") {
    			   		if (data.data == "" || data.data == null) {
    			   			me.lock();
    			   		 	me.noData();
    			   		 	$(".donebox .dropload-down").html("<div class='nodata'>暂无数据！</div>");
     			   			$(".donebox .dropload-down").addClass("dropload-down-nodata");
						}else{
							if(page == 2 && data.data.list.length <= 0){
								var html = "<div class='data_null'><img src='http://cre-card.oss-cn-beijing.aliyuncs.com/images/nodata.png' /></div>";
								$(".donebox").html(html);
								$(".donebox .dropload-down").remove();
								//alert("none")
							}else{
		                		if((page1-1) > data.data.pages){
		                			me.lock();
		                            me.noData();
		                            $(".donebox .dropload-down").html("<div class='nodata'>没有更多数据了！</div>");
		        			   		$(".donebox .dropload-down").addClass("dropload-down-nodata");
		                		}else{
		                			var arrLen = data.data.list.length;
		                            if(arrLen > 0){
		                            	setTimeout(function(){
		                            		donefunc(data.data.list,arrLen);
		                                    me.resetload();
		                                },1000);
		                            }else{
		                                me.lock();
		                                me.noData();
		                                $(".donebox .dropload-down").html("<div class='nodata'>没有更多数据了！</div>");
		            			   		$(".donebox .dropload-down").addClass("dropload-down-nodata");
		                            } 
		                		}
		                		
		                	}
						}
    			   	}else{
    			   		$(".donebox .dropload-down").html("<div class='nodata'>暂无数据！</div>");
    			   		$(".donebox .dropload-down").addClass("dropload-down-nodata");
    			   	}
                },
                error: function(xhr, type){
                    //alert('Ajax error!');
                    // 即使加载出错，也得重置
                    me.resetload();
                }
            });
        },
        threshold : 50
    });
})