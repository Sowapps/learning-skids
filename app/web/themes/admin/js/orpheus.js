
(function ($) { // Preserve our jQuery
	
	$(function () {
		
		(function () {
			var hash = window.location.hash;
			hash && $('ul.nav a[href="' + hash + '"]').tab('show');
			$('.nav-tabs a').click(function (e) {
				$(this).tab('show');
				var scrollmem = $('body').scrollTop();
				window.location.hash = this.hash;
				$('html,body').scrollTop(scrollmem);
			});
		})();
		
		$('table.tablesorter').tablesorter();
		
		$('.modal').on('shown.bs.modal', function (e) {
			var target = $(e.relatedTarget);
			if( target.length && target.data("focus") ) {
				$(this).find(target.data("focus")).focus();
			} else {
				$(this).find(".modal-body :input:visible").first().focus();
			}
		});
		
		// http://ivaynberg.github.io/select2/
		$('select.select2').each(function () {
			var _ = $(this);
			var options = {};
			if( !_.hasClass("searchable") ) {
				options.minimumResultsForSearch = -1;
			}
			_.select2(options);
		});
		
		$('label:not([for])').click(function () {
			$(this).next().focus();
		});
	});
	
	function refresh() {
		$('.uploadlayer').each(function () {
			var _ = $(this);
			if( _.data('uploadlayer') ) {
				return;
			}
			_.data('uploadlayer', 1);
			var l = _.prev();
			var w = l.outerWidth();
			var h = l.outerHeight();
			var pos = l.position();
			_.css({
				"opacity": 0,
				"position": "absolute",
				"top": (pos.top + parseInt(l.css('margin-top'))) + "px",
				"left": (pos.left + parseInt(l.css('margin-left'))) + "px",
				"width": w + "px",
				"height": h + "px",
				"display": "block",
				"cursor": "pointer"
			});
			_.hover(function () {
				l.addClass("active");
			}, function () {
				l.removeClass("active");
			});
			if( _.data('submit') ) {
				_.change(function () {
					$(_.data('submit')).click();
				});
			}
		});
	}
})(jQuery);
