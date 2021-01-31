// A PART OF THIS CODE IS WAITING FOR TRANSFER TO OFFICIAL ORPHEUS.JS
(function ($) {
	$(function () {
		
		if( typeof moment !== "undefined" ) {
			moment.locale($('html').attr("lang"));
		}
		
		// Mask input
		if( $.fn.mask ) {
			$.mask.definitions['s'] = '[0-6]';
			$("input[data-mask]").each(function () {
				var options = {};
				if( $(this).data("mask-autoclear") !== undefined ) {
					options.autoclear = $(this).data("mask-autoclear");
				}
				$(this).mask($(this).data("mask") + "", options);
			});
		}
		
		$("[data-form-group]").each(function () {
			$(this).find(":input").attr('data-parsley-group', $(this).data('form-group'));
		});
		
		$('.input-group.datepicker').each(function () {
			let $datepicker = $(this);
			let options = {
				allowInputToggle: true,
				stepping: 5,
				locale: getLocale(),
				icons: {
					time: 'far fa-clock',
					date: 'far fa-calendar-alt',
					up: 'fas fa-chevron-up',
					down: 'fas fa-chevron-down',
					previous: 'fas fa-chevron-left',
					next: 'fas fa-chevron-right',
					today: 'fas fa-calendar-check-o',
					clear: 'fas fa-trash',
					close: 'fas fa-times'
				}
			};
			if( !$datepicker.hasClass('with-time') ) {
				options.format = 'L';
			}
			if( $datepicker.data('defaultDate') ) {
				options.defaultDate = $datepicker.data('defaultDate');
			}
			$datepicker.datetimepicker(options);
		});
		
		// Bootstrap custom file
		$('.custom-file').each(function () {
			const $label = $(this).find('.custom-file-label');
			const $input = $(this).find('.custom-file-input');
			$input.change(function () {
				let name = basename($input.val());
				if( !$label.data('text') ) {
					$label.data('text', $label.text());
				}
				$label.text(name);
				if( !$input.val() ) {
					$input.val(name);
				}
			});
		});
		
		$(document).on('click', '[data-toggle-class]', function () {
			let $actionner = $(this);
			let $target = $($actionner.data('toggleTarget'));
			if( $target.length > 1 ) {
				$target = $target.has($actionner);
			}
			$target.toggleClass($actionner.data('toggleClass'));
		});
		
		$('[data-form-change],[data-form-change-not]').each(function () {
			let $subject = $(this);
			// If any change in the form
			$subject.closest('form').find(':input').one('change', function () {
				$subject.removeClass($subject.data('formChangeNot')).addClass($subject.data('formChange'));
			});
		});
		
		$('.modal-focus').each(function () {
			let $focused = $(this);
			let $dialog = $focused.closest('.modal')
			$dialog.on('shown.bs.modal', function () {
				$focused.focus();
			})
		});
		
		$('[data-enter]').each(function () {
			let $element = $(this);
			$element.pressEnter(function () {
				let action = $element.data('enter');
				if( action === 'click' ) {
					let $target = $($element.data('target'));
					$target.click();
				}
			});
		});
		
	});
})(jQuery);

function getLocale() {
	return $('html').attr('lang');
}

function removeSystemNotification(channel) {
	var notification = $('#SystemNotification-' + channel);
	if( notification.is(":hidden") ) {
		return;
	}
	var notificationContainer = $('#SystemNotificationContainer');
	if( notificationContainer.find(".system_notification:visible").length > 1 ) {
		notification.slideUp(200);
	} else {
		notificationContainer.slideUp(300, function () {
			notification.hide();
		});
	}
}

var systemNotificationExpires = {};

function addSystemNotification(channel, text, type, expire) {
	if( !type ) {
		type = 'info';
	}
	var notificationContainer = $('#SystemNotificationContainer');
	if( !notificationContainer.length ) {
// 		notificationContainer = $('<div id="SystemNotificationContainer"></div>').hide();
// 		$("body").prepend(notificationContainer);
		$("body").prepend($('<div id="SystemNotificationWrapper"><div id="SystemNotificationContainer"></div></div>'));
		notificationContainer = $('#SystemNotificationContainer').hide();
	}
	var notification = $('#SystemNotification-' + channel);
	if( !notification.length ) {
		notificationNotification = $('<div id="SystemNotification-' + channel + '" class="system_notification alert" role="alert"></div>');
		notificationContainer.append(notificationNotification.hide());
	} else {
		notificationNotification.removeClass('alert-' + notificationNotification.data("type"));
	}
	notificationNotification.html(text).addClass('alert-' + type).data("type", type);
	if( notificationContainer.is(":hidden") ) {
		notificationNotification.show();
		notificationContainer.slideDown(300);
// 		notificationContainer.show("drop", {direction: "up"}, 10000);
	} else {
		notificationNotification.slideDown(200);
	}
	if( expire > 0 ) {
		if( systemNotificationExpires[channel] ) {
			// Existing timeout in progress
			clearTimeout(systemNotificationExpires[channel]);
		}
		systemNotificationExpires[channel] = setTimeout(function () {
			removeSystemNotification(channel);
		}, expire);
	}
}
