$(document).ready(function() {
	$('.flipbook').turn({
		width: 800,
		height: 500,
		autoCenter: true,
		turnCorners: "bl,br",
		gradients: true,
		duration: 1000,
		elevation: 50
	});

	$('.page').on('click', function() {
		var currentPage = $(this).attr('id');
		var nextPage = parseInt(currentPage) + 1;
		$('.flipbook').turn('peel', 'br', nextPage);
		$('#page-flip-sound')[0].play();
	});

	$('.hard').on('click', function() {
		$('#page-flip-sound')[0].play();
	});

	$(document).keydown(function(e){
		if (e.keyCode == 37) {
			$('.flipbook').turn('previous');
			$('#page-flip-sound')[0].play();
		}
		else if (e.keyCode == 39) {
			$('.flipbook').turn('next');
			$('#page-flip-sound')[0].play();
		}
	});
});
