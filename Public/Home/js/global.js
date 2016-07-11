$(document).ready(function(e) {
	$(".dhh-zj li").click(function(){
		var linum = $(".dhh-zj li").index(this);
		var lisrc = $(".dhh-zj li:eq("+linum+")").children("img").attr("src");
		$(".max-pic").children("img").attr("src",lisrc);
		$(".max-pic").show();
		var maxpicH = $(".max-pic").children("img").outerHeight(true);		
		$(".max-pic").css({"margin-top":-(maxpicH/2)});		
		$(".com-zindex").show();
		$("body").css({"overflow":"hidden"});

	});
	$(".com-zindex").click(function(){
		$(".max-pic").hide();
		$(this).hide();
		$("body").css({"overflow":"auto"});		
	});
	var headpicH = $(".head-pic").outerHeight(true);		
	$(window).scroll(function() {
	 	var scroH = $(this).scrollTop();

	    if(scroH>=(headpicH-40)){
			$(".header").css({"background-color":"rgba(187,187,187,1)"});
	    }
	    else if(scroH<headpicH){
			$(".header").css({"background-color":"rgba(187,187,187,0)"});	    	
	    }; 	
	}); 	
});

