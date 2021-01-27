$(function () {
	
	let $learningSheet = $('#LearningSheet');
	$learningSheet.data('learningSheet', new LearningSheet($learningSheet));
	
	$learningSheet.on('click', '.toggle-list', function () {
		let $rowCategory = $(this).closest('.item-category');
		if( $rowCategory.is('.list-hide') ) {
			$rowCategory.removeClass('list-hide').addClass('list-show');
		} else {
			$rowCategory.removeClass('list-show').addClass('list-hide');
		}
	});
	
	$learningSheet.on('click', '[data-toggle-class]', function () {
		let $actionner = $(this);
		let $target = $($actionner.data('toggleTarget'));
		if( $target.length > 1 ) {
			$target = $target.has($actionner);
		}
		$target.toggleClass($actionner.data('toggleClass'));
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
				console.log('Click', $target);
				$target.click();
			}
		});
	});
	
});

class EditableEntity {
	
	data;
	
	load(newData) {
		this.setData(newData, false);
	}
	
	hasData() {
		return !!this.data;
	}
	
	getState() {
		return this.isNew ? 'new' : (this.isRemoved ? 'remove' : 'update');
	}
	
	getInputPrefix() {
		throw new Error('Abstract method, it should be implemented by sub-class');
	}
	
	getDomForm() {
		throw new Error('Abstract method, it should be implemented by sub-class');
	}
	
	refresh() {
		throw new Error('Abstract method, it should be implemented by sub-class');
	}
	
	buildForm() {
		throw new Error('Abstract method, it should be implemented by sub-class');
	}
	
	addDataToForm(key, value) {
		let $input = $('<input type="hidden">');
		$input.attr('name', this.getInputPrefix() + '[' + key + ']');
		$input.val(value);
		this.getDomForm().append($input);
	}
	
}

class LearningSheet extends EditableEntity {
	
	constructor($learningSheet) {
		super();
		
		this.$learningSheet = $learningSheet;
		this.categories = [];
		this.data = null;
		this.$categoryEditDialog = $(this.$learningSheet.data('categoryEditDialog'));
		this.$sheetEditDialog = $(this.$learningSheet.data('sheetEditDialog'));
		
		this.allowSheetUpdate = true;
		this.allowCategoryCreate = false;
		this.allowCategoryUpdate = false;
		this.allowCategoryDelete = false;
		this.allowSkillCreate = false;
		this.allowSkillUpdate = false;
		this.allowSkillDelete = false;
		this.hasChanges = false;
		
		this.isEdited = false;
		
		let $categoryTemplate = this.$learningSheet.find('.item-category.template');
		
		// Extract Child skill template
		let $skillTemplate = $categoryTemplate.find('.item-skill.template');
		$skillTemplate.removeClass('template');
		this.skillTemplateHtml = $skillTemplate[0].outerHTML;
		$skillTemplate.remove();
		
		// Extract category template
		$categoryTemplate.removeClass('template');
		this.categoryTemplateHtml = $categoryTemplate[0].outerHTML;
		$categoryTemplate.remove();
		this.categoryIndex = 0;
		this.skillIndex = 0;
	}
	
	notifyAboutChanges() {
		this.hasChanges = true;
		$('.notify-learning-sheet-save').show();
	}
	
	addEmptyCategory() {
		this.lastCategory = this.addCategory(null);
	}
	
	setData(newData, merge) {
		if( merge === undefined ) {
			// If there are data auto means we have to merge it
			merge = this.hasData();
		}
		let wasHavingData = this.hasData();
		if( newData ) {
			let categories = newData.categories || [];
			delete newData.categories;
			if( merge ) {
				$.extend(this.data, newData)
			} else {
				this.data = newData;
			}
			if( !this.data.label || newData.name ) {
				this.data.label = this.data.name;
			}
			if( !this.data.level_label || newData.level ) {
				this.data.level_label = t('level_' + this.data.level);
			}
			categories.forEach(category => this.addCategory(category));
		}
		// Is existing & was having data (and now new data)
		this.isEdited = wasHavingData;
		if( this.isEdited ) {
			this.notifyAboutChanges();
		}
		if( !wasHavingData ) {
			this.addEmptyCategory();
		}
		this.refresh();
	}
	
	getInputPrefix() {
		return 'sheet';
	}
	
	getDomForm() {
		return this.getSheetCategory().getDomSheetColumn().find('.form');
	}
	
	buildForm() {
		if( !this.hasData() ) {
			return;
		}
		this.getDomForm().empty();
		if( !this.isEdited ) {
			return;
		}
		this.addDataToForm('name', this.data.name);
		this.addDataToForm('level', this.data.level);
	}
	
	refresh() {
		if( this.categories.length === 0 ) {
			return;
		}
		// First category row is displaying learning sheet
		this.getSheetCategory().refresh();
	}
	
	getSheetCategory() {
		return this.categories[0];
	}
	
	addCategory(data) {
		let $category = $(this.categoryTemplateHtml.replaceAll('{{ index }}', this.categoryIndex));
		let category = new LearningCategory(this.categoryIndex, $category, this, this.categories.length === 0);
		$category.data('category', category);
		
		this.categories.push(category);
		this.$learningSheet.append($category);
		category.load(data);
		this.categoryIndex++;
		return category;
	}
	
}

class LearningCategory extends EditableEntity {
	
	constructor(index, $category, learningSheet, showSheetColumn) {
		super();
		
		this.index = index;
		this.$category = $category;
		this.learningSheet = learningSheet;
		this.showSheetColumn = showSheetColumn;
		this.isNew = true;// Is implying new creation
		this.isEdited = false;// Was edited by user (new or existing one)
		this.isRemoved = false;// Was removed (existing one)
		this.showSheetColumn = showSheetColumn;
		this.skills = [];
		this.data = null;
		this.bindEvents();
	}
	
	getDomSheetColumn() {
		return this.$category.find('.column-sheet');
	}
	
	getDomCategoryColumn() {
		return this.$category.find('.column-category');
	}
	
	getDomSkillColumn() {
		return this.$category.find('.column-skill');
	}
	
	getDomSkillList() {
		return this.$category.find('.column-skill .list-skill');
	}
	
	getDomForm() {
		return this.$category.find('.column-category .form');
	}
	
	openEditSheetForm() {
		if( !this.learningSheet.allowSheetUpdate ) {
			throw "Sheet update not allowed";
		}
		let $dialog = this.learningSheet.$sheetEditDialog;
		$dialog
			.resetForm()
			.fill('sheet', this.learningSheet.data)
			.modal('show');
		
		$dialog
			.find('.action-save')
			.off('click')
			.on('click', () => {
				let data = $dialog.getFormObject();
				console.log('data', data);
				$dialog.modal('hide');
				this.learningSheet.setData(data.learningSheet);
			});
	}
	
	openCreateForm() {
		if( !this.learningSheet.allowCategoryCreate ) {
			throw "Category update not allowed";
		}
		let $dialog = this.learningSheet.$categoryEditDialog;
		$dialog.modal('show');
		
		let $dialogTitle = $dialog.find('.modal-title');
		$dialogTitle
			.text($dialogTitle.data('textNew'))
			.resetForm();
		$dialog
			.find('.action-save')
			.off('click')
			.on('click', () => {
				let data = $dialog.getFormObject();
				console.log('Validate new category', data);
				$dialog.modal('hide');
				this.setData(data.category);
				this.learningSheet.addEmptyCategory();
			});
	}
	
	bindEvents() {
		let category = this;
		this.$category.find('.action-sheet-update').click(function () {
			category.openEditSheetForm();
		});
		this.$category.find('.action-category-add').click(function () {
			category.openCreateForm();
		});
	}
	
	setData(newData, merge) {
		if( merge === undefined ) {
			// If there are data auto means we have to merge it
			merge = this.hasData();
		}
		let wasHavingData = this.hasData();
		if( newData ) {
			let skillsData = newData.skills || [];
			delete newData.skills;
			if( merge ) {
				$.extend(this.data, newData)
			} else {
				this.data = newData;
			}
			if( !this.data.label || newData.name ) {
				this.data.label = this.data.name;
			}
			skillsData.forEach(skill => this.addSkill(skill));
		}
		this.isNew = !this.hasData() || !this.data.id;
		// Is new && now having data
		// Is existing & was having data (and now new data)
		this.isEdited = (this.isNew && this.hasData()) || (!this.isNew && wasHavingData);
		this.isRemoved = false;
		this.refresh();
	}
	
	addSkill(data) {
		let $skill = $(this.learningSheet.skillTemplateHtml.replaceAll('{{ index }}', this.learningSheet.skillIndex));
		let skill = new LearningSkill($skill, data, this);
		$skill.data('skill', skill);
		
		this.skills.push(skill);
		this.getDomSkillList().append($skill);
		this.learningSheet.skillIndex++;
	}
	
	getInputPrefix() {
		return 'category[' + this.index + ']';
	}
	
	buildForm() {
		this.getDomForm().empty();
		if( !this.isNew && (this.isEdited || this.isRemoved) ) {
			this.addDataToForm('id', this.data.id);
		}
		if( this.isEdited || this.isRemoved ) {
			this.addDataToForm('state', this.getState());
		}
		if( this.isEdited ) {
			this.addDataToForm('name', this.data.name);
		}
	}
	
	refresh() {
		// Learning sheet column
		if( this.showSheetColumn ) {
			this.getDomSheetColumn().fill('sheet', this.learningSheet.data);
			this.learningSheet.buildForm();
		} else {
			this.getDomSheetColumn().remove();
			this.$category.find('[data-class-no-sheet]').each(function () {
				$(this).addClass($(this).data('classNoSheet'));
			});
		}
		// Category Column
		let $categoryColumn = this.getDomCategoryColumn();
		this.getDomForm().empty();
		if( this.hasData() ) {
			// Has data
			$categoryColumn.fill('category', this.data);
			$categoryColumn.find('.category-details').show();
			$categoryColumn.find('.action-category-add').hide();
			this.buildForm();
		} else {
			// No data, empty category to add new one
			$categoryColumn.find('.category-details').hide();
			$categoryColumn.find('.action-category-add').show();
			$categoryColumn.showIf(this.learningSheet.allowCategoryCreate);
		}
		// Skills column
		let $skillColumn = this.getDomSkillColumn();
		if( this.hasData() ) {
			$skillColumn.show();
			this.getDomSkillList().showIf(this.skills.length);
		} else {
			$skillColumn.hide();
			this.getDomSkillColumn().hide();
		}
		$skillColumn.find('.action-skill-add').showIf(this.learningSheet.allowSkillCreate);
	}
	
}

class LearningSkill extends EditableEntity {
	
	constructor($skill, data, category) {
		super();
		
		this.$skill = $skill;
		this.category = category;
		this.learningSheet = category.learningSheet;
		this.data = null;
		this.load(data);
	}
	
	setData(newData, merge) {
		if( merge === undefined ) {
			// If there are data auto means we have to merge it
			merge = this.hasData();
		}
		// let wasHavingData = this.hasData();
		if( newData ) {
			let skillsData = newData.skills || [];
			delete newData.skills;
			if( merge ) {
				$.extend(this.data, newData)
			} else {
				this.data = newData;
			}
			if( !this.data.label || newData.name ) {
				this.data.label = this.data.name;
			}
			skillsData.forEach(skill => this.addSkill(skill));
		}
		this.refresh();
	}
	
	refresh() {
		this.$skill.fill('skill', this.data);
	}
	
}
