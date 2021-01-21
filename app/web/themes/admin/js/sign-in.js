$(function () {
	
	$('.sign-in-panel :button[data-toggle-panel]').click(function () {
		console.log($(this).data('togglePanel'));
		let $target = $($(this).data('togglePanel'));
		$('.sign-in-panel.show').removeClass('show');
		$target.addClass('show');
		
		// $('.sign-in-panel:visible').hide(function() {
		// 	console.log('Hidden');
		// 	$target.show();
		// });
		return false;
	});
	
});
