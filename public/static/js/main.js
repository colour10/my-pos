/*
window.addEventListener("scroll", function(e) {
    //alert("滚动了");
    //变量t就是滚动条滚动时，到顶部的距离
    var info = document.getElementsByClassName("info")[0];
    if(info){
    	info.className += ' infoFixed'
	    var t = document.documentElement.scrollTop || document.body.scrollTop;
	    // console.log(t)
	    var screenHeight = window.screen.height
	    // console.log(screenHeight)
	    if (t <= 0) {
	        info.className = 'info'
	    }
    }
    
});*/

function _touch() {
    var startx; //让startx在touch事件函数里是全局性变量。
    var endx;
    var el = document.getElementById('tjLists');
    if(el){
    	 var item1 = document.getElementsByClassName('tj_item')[0];
    	    function cons() { //独立封装这个事件可以保证执行顺序，从而能够访问得到应该访问的数据。
    	        console.log(starty, endy);
    	        
    	        if (startx > endx) { //判断左右移动程序
    	            // alert('left');
    	            item1.className += ' swiperLeft' 
    	        } else {
    	            // alert('right');
    	        }
    	    }
    	    el.addEventListener('touchstart', function(e) {

    	        var touch = e.changedTouches;
    	        startx = touch[0].clientX;
    	        starty = touch[0].clientY;
    	    });
    	    el.addEventListener('touchend', function(e) {
    	        var touch = e.changedTouches;
    	        endx = touch[0].clientX;
    	        endy = touch[0].clientY;
    	        cons(); //startx endx 的数据收集应该放在touchend事件后执行，而不是放在touchstart事件里执行，这样才能访问到touchstart和touchend这2个事件产生的startx和endx数据。另外startx和endx在_touch事件函数里是全局性的，所以在此函数中都可以访问得到，所以只需要注意事件执行的先后顺序即可。
    	    });
    }
   
}
_touch() 
