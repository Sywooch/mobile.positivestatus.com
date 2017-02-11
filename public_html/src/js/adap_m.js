$(document).ready(function() {
	$(".nav2 li a").each(function() {
		if ($(this).next().length > 0) {
			$(this).addClass("parent");
			$(".f16 a").removeClass("parent");
		};
	});
	adjustMenu();

	$(document).click(function(event) {
  		if($(event.target).closest('.nav2').length == 0)
 			$(".nav2 li").removeClass('hover');
	});




});


var adjustMenu = function() {
	$(".nav2").show();
		$(".nav2 li").off('mouseenter mouseleave');
		$(".nav2 li a.parent").off('click').on('click', function(e) {
			e.preventDefault();

			$(this).parent().siblings().removeClass('hover');
			$(this).closest(".nav2 > li").siblings().removeClass('hover');
		   	$(this).parent("li").toggleClass("hover").fadeIn('slow');
		});
}


