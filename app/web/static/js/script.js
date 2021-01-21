function formatNumber(val, unit, defVal) {
	return val ? number_format(val, 0, '', ' ') + (unit ? " " + unit : '') : defVal;
//	return val ? val+(unit ? " "+unit : '') : '';
}

function toHumanNumber(val, decimals) {
	return number_format(val * 1, decimals, LOCALE_DECIMAL_POINT, LOCALE_THOUSANDS_SEP);
}

function toComputerNumber(val) {
	return val.replace(LOCALE_DECIMAL_POINT, ".").replace(LOCALE_THOUSANDS_SEP, "") * 1;
}

(function ($) {
	
	$.fn.humanNumberVal = function (computerVal) {
		return $(this).val(toHumanNumber(computerVal, $(this).data("decimals")));
	};
	$.fn.computerNumberVal = function () {
		return $(this).val() ? toComputerNumber($(this).val()) : 0;
	};
	$(function () {
		
		$("label:not([for])").each(function () {
			if( $(this).find("input").length ) {
				return;
			}
			$(this).click(function () {
				var input = null;
				input = $(this).next();
				if( !input.is(":focusable") ) {
					input = $(input).next();
					if( !input.is(":focusable") ) {
						input = null;
					}
				}
				if( input ) {
					if( input.is(":hidden") || !input.is(":focusable") ) {
						input.trigger("activate");
					} else {
						input.trigger("focus");
					}
				}
			});
		});
	});

//Only if focused
	$.fn.setCaretPosition = function (caretPos) {
		var _ = this[0];
		if( _.createTextRange ) {
			var range = _.createTextRange();
			range.move('character', caretPos);
			range.select();
		} else if( _.selectionStart ) {
			_.setSelectionRange(caretPos, caretPos);
		}
	};
	
	/*
	$(function() {
	//	var cache = {};
		$("input.autocomplete").shown(function() {
			var _	= $(this);
			if( _.data("autocomplete") ) { return; }
			_.data("autocomplete", 1);
			_.data("autocomplete_cache", {});
			_.autocomplete({
				minLength:	2,
				source:		function(request, response) {
	//				var query = _.data("query") ? "&"+_.data("query") : "";
					// Targetting the autocomplete itself
					if( cache[what] && term in cache[what] ) {
						response(cache[what][term]);
						return;
					}
					if( !cache[what] ) {
						cache[what] = {};
					}
					lastXhr = $.getJSON("remote-search-what="+what+"&term="+term+".json", function( data, status, xhr ) {
						if( xhr === lastXhr && data.code == "OK" ) {
							response(data.other);
							return;
						}
						response([]);
					});
				}
			});
		});
	});
	
	function requestAutocomplete2(what, filter, response) {
	}
	*/
	
})(jQuery);
