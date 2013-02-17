jQuery(function($) {
    var pb_tabs = $('.pb-tabs');
	$('.wp-tab-bar a', pb_tabs).click(function(event){
		event.preventDefault();
		// Limit effect to the container element.
		var context = $(this).closest('.wp-tab-bar').parent();
		$('.wp-tab-bar li', context).removeClass('wp-tab-active');
		$(this).parents('li').first().addClass('wp-tab-active');
		$('.wp-tab-panel', context).hide();
		$( $(this).attr('href'), context ).show();
	});
	// Make setting wp-tab-active optional.
	$('.wp-tab-bar', pb_tabs).each(function(){
		if ( $('.wp-tab-active', this).length ) {
			$('.wp-tab-active', this).click();
        } else {
            $('a', this).first().click();
        }
	});
});