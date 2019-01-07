$(document).ready(function() {
	
	/*============================================
	Sequence Slider
	==============================================*/
	var sequenceOptions = {
        autoPlay: true,
        autoPlayDelay: 4000,
        pauseOnHover: false,
        hidePreloaderDelay: 500,
        nextButton: true,
        prevButton: true,
        pauseButton: true,
        preloader: true,
        hidePreloaderUsingCSS: true,                   
        animateStartingFrameIn: true,    
        navigationSkipThreshold: 750,
        preventDelayWhenReversingAnimations: true
    };

    var sequence = $("#sequence").sequence(sequenceOptions).data("sequence");
	
	sequence.afterLoaded = function(){
		$('#sequence').delay(1000).animate({'opacity':1});
		
		setTimeout(function(){
			$("#sequence h2").fitText(0.9, { minFontSize: '24px', maxFontSize: '56px' });
			$("#sequence p").fitText(1.2, { minFontSize: '16px', maxFontSize: '28px' });
		},500);
	};
});	