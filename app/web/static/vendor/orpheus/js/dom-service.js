class DomService {
	
	filters = {
		'upper': value => {
			return value.toUpperCase();
		},
	}
	
	addJsLibrary(url, options = {}) {
		const deferred = $.Deferred();
		
		const script = document.createElement('script');
		
		script.addEventListener('load', () => {
			deferred.resolve();
		});
		script.addEventListener('error', () => {
			deferred.reject('Failed to load script');
		});
		
		// load the script file
		script.src = url;
		document.body.appendChild(script);
		
		return deferred.promise();
	}
	
	resolveCondition(condition, data) {
		const conditionParts = condition.split(' ');
		var invert = conditionParts[0] === 'not';
		var property = conditionParts.length > 1 ? conditionParts[1] : conditionParts[0];
		return invert ^ data[property];
	}
	
	renderTemplateString(string, data) {
		// Clean contents
		let template = string.replace(/>\s+</ig, '><');
		
		// Resolve values in attributes
		// Deprecated for TWIG compatibility
		template = template.replace(/\{\{ ([^\}]+) \}\}/ig, (all, variable) => {
			return this.resolveVariable(variable, data);
		});
		// Yes we want this one, simple brackets
		template = template.replace(/\{ ([^\}]+) \}/ig, (all, variable) => {
			return this.resolveVariable(variable, data);
		});
		
		return template;
	}
	
	resolveVariable(variable, data) {
		let tokens = variable.split('|');
		const property = tokens.shift().trim();
		let value = data[property];
		tokens.forEach(filter => {
			filter = filter.trim();
			const filterCallback = this.filters[filter];
			if( filterCallback ) {
				value = filterCallback(value);
			} else {
				console.warn('Unknown filter ' + filter);
			}
		});
		return value;
	}
	
	renderTemplateElement($template, data, prefix) {
		// Resolve conditional displays
		$template.find('[data-if]').each((index, $element) => {
			$element = $($element);
			if( !this.resolveCondition($element.data('if'), data) ) {
				$element.remove();
			}
		});
		// Fix image loading preventing
		$template.find('[data-src]').each((index, $element) => {
			$element = $($element);
			$element.attr('src', $element.data('src'));
			$element.removeAttr('data-src').data('src', null);
		});
		// Resolve values in content
		if( prefix ) {
			$template.fill(prefix, data);
		}
	}
	
	loadTemplate(key, target) {
		const deferred = $.Deferred();
		const $target = $(target);
		$target.load('/api/template/' + key, (responseText, textStatus, jqXHR) => {
			if( textStatus === 'success' ) {
				deferred.resolve($target);
			} else {
				deferred.reject($target);
			}
		});
		return deferred.promise();
	}
	
	global() {
		return $(window);
	}
	
	updateTemplateItem($item, data) {
		$item = $($item);
		if( !$item.data('rendering_template') ) {
			console.error('Not a template item', $item);
			return;
		}
		return this.renderTemplate($item.data('rendering_template'), data, $item.data('rendering_prefix'), $item.data('rendering_wrap'))
			.then(($newItem) => {
				$item.after($newItem);
				$item.remove();
				return $newItem;
			});
	}
	
	renderTemplate(template, data, prefix, wrap) {
		let isHtml = true;
		if( !template ) {
			throw new Error('Empty template');
		}
		if( isDomElement(template) ) {
			template = $(template);
		}
		if( isJquery(template) ) {
			if( template.is('template') ) {
				let content = template.html();
				if( !content ) {
					isHtml = false;
					content = template.prop('content').textContent.trim();
				}
				if( !content && template.data('key') ) {
					return this.loadTemplate(template.data('key'), template)
						.then(() => {
							return this.renderTemplate(template, data, prefix);
						});
				}
				if( !content ) {
					console.error('Template has no contents', template);
				}
				template = content;
			} else {
				template = template.html();
			}
		}
		let renderedTemplate = this.renderTemplateString(template, data);
		// Create jquery object preventing jquery to preload images
		renderedTemplate = renderedTemplate.replace('src=', 'data-src=');
		// If using text, we need to create a text node to use jQuery
		let $item = $(isHtml ? renderedTemplate : document.createTextNode(renderedTemplate));
		if( wrap ) {
			$item = $('<div class="template-contents"></div>').append($item);
		}
		$item.data('rendering_template', template);
		$item.data('rendering_prefix', prefix);
		$item.data('rendering_wrap', wrap);
		this.renderTemplateElement($item, data, prefix);
		return $.when($item);
	}
	
	showElement($element) {
		$element.show();
	}
	
	show(selector, object, hideSelector, prefix) {
		$(selector).each((index, element) => {
			let $element = $(element);
			if( $element.is('template') ) {
				this.renderTemplate($element, object, prefix)
					.then(($clone) => {
						$clone.addClass($element.prop('class'));
						$clone.data('controller', $element.data('controller'));
						$element.after($clone);
						this.showElement($clone);
					});
			} else {
				this.showElement($element);
			}
		});
		$(hideSelector).each((index, element) => {
			let $element = $(element);
			if( $element.data('rendering_template') ) {
				// Remove templates' clone
				$element.remove();
			} else {
				// Only hide others
				$element.hide();
			}
		});
	}
	
}

domService = new DomService();
