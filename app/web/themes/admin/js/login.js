$(function () {
	
	$('.sign-in-panel :button[data-toggle-panel]').click(function () {
		console.log($(this).data('togglePanel'));
		let $target = $($(this).data('togglePanel'));
		$('.sign-in-panel.show').removeClass('show');
		$target.addClass('show');
		return false;
	});
	
});
