$(function () {
	
	let $table = $('#TablePupilSkillList');
	$table.data('manager', new PupilSkillManager($table, $('#DialogPupilSkillEdit')));
	
});

class PupilSkillManager {
	
	$table;
	$skillEditDialog;
	skills;
	
	constructor($table, $skillEditDialog) {
		this.$table = $table;
		this.$skillEditDialog = $skillEditDialog;
		this.$skillValueRowTemplate = $('#TemplateValueRow').detach();
		this.skills = [];
		
		this.initialize();
	}
	
	initialize() {
		let manager = this;
		this.$table.find('.item-skill').each(function () {
			let pupilSkill = new PupilSkill(manager, this);
			$(this).data('skill', pupilSkill);
			manager.skills.push(pupilSkill);
		});
	}
	
	async openSkillEditDialog(data) {
		const deferred = $.Deferred();
		const $dialog = this.$skillEditDialog;
		
		$dialog.modal('show')
			.resetForm()
			.fill('skill', data.skill)
			.fillByName(data.pupilSkill, 'pupilSkill[%s]');
		
		const hasValues = data.pupilSkill.id && data.pupilSkill.values && data.pupilSkill.values.length;
		console.log('data.pupilSkill', data.pupilSkill, hasValues);
		$dialog.find('.pupil-skill-history').showIf(hasValues);
		$dialog.find('.pupil-skill-history-body').empty();
		if( hasValues ) {
			for( const skillValue of data.pupilSkill.values ) {
				const $row = await domService.renderTemplate(this.$skillValueRowTemplate, skillValue);
				$dialog.find('.pupil-skill-history-body').append($row);
			}
		}
		
		$dialog.find('.action-accept')
			.off('click')
			.on('click', function () {
				let data = $dialog.getFormObject();
				$dialog.modal('hide');
				deferred.resolve(data);
			});
		
		$dialog.find('.action-cancel')
			.off('click')
			.on('click', function () {
				deferred.reject();
			});
		
		return deferred.promise();
	}
	
}

class PupilSkill {
	
	manager; // PupilSkillManager
	$row; // TR row in table for this skill - DEPRECATED
	$element; // TR row element in table for this skill
	skill; // Learning Sheet Skill
	pupilSkill; // Pupil Skill
	status; // Pupil Skill Status, empty is no changes
	
	constructor(manager, $row) {
		this.manager = manager;
		this.$element = $($row);
		this.$row = this.$element;
		this.index = this.$row.index();
		this.skill = this.$row.data('skill') || {};
		this.pupilSkill = this.$row.data('pupilSkill') || {};
		this.status = null;
		
		if( this.pupilSkill.id ) {
			// Accept is only for client-side to ensure object has data
			this.pupilSkill.accept = 1;
		} else {
			this.pupilSkill.accept = 0;
		}
		
		this.bindEvents();
		this.refresh();
	}
	
	isExisting() {
		return !!this.pupilSkill && !!this.pupilSkill.id;
	}
	
	isAccepted() {
		return (this.isExisting() && this.status !== PupilSkill.STATUS_REMOVE) || this.status === PupilSkill.STATUS_NEW;
	}
	
	accept(value) {
		this.pupilSkill.accept = 1;
		this.pupilSkill.value = this.skill.valuable ? value : null;
		this.status = this.isExisting() ? PupilSkill.STATUS_UPDATE : PupilSkill.STATUS_NEW;
		this.notifyChanges();
	}
	
	remove() {
		if( !this.isExisting() ) {
			this.status = null;
			this.pupilSkill = {accept: 0};
		} else {
			this.status = PupilSkill.STATUS_REMOVE;
			this.pupilSkill.value = null;
		}
		this.notifyChanges();
		return true;
	}
	
	bindEvents() {
		this.$row.find('.action-skill-accept').click(async() => {
			if( this.skill.valuable ) {
				const data = await this.manager.openSkillEditDialog(this)
				this.accept(data.pupilSkill.value);
			} else {
				this.accept(null);
			}
		});
		this.$row.find('.action-value-edit').click(async() => {
			if( !this.skill.valuable ) {
				return;
			}
			const data = await this.manager.openSkillEditDialog(this)
			this.accept(data.pupilSkill.value);
		});
		this.$row.find('.action-skill-reject').click(() => {
			this.remove();
		});
	}
	
	notifyChanges() {
		this.refresh();
	}
	
	getInputPrefix() {
		return 'pupilSkill[' + this.index + ']';
	}
	
	addDataToForm(key, value) {
		let $input = $('<input type="hidden">');
		$input.attr('name', this.getInputPrefix() + '[' + key + ']');
		$input.val(value);
		this.getDomForm().append($input);
	}
	
	getDomForm() {
		return this.$row.find('.form');
	}
	
	buildForm() {
		this.getDomForm().empty();
		if( !this.status ) {
			return;
		}
		if( this.isExisting() ) {
			this.addDataToForm('id', this.pupilSkill.id);
		}
		this.addDataToForm('skill_id', this.skill.id);
		this.addDataToForm('status', this.status);
		if( this.status !== PupilSkill.STATUS_REMOVE && this.pupilSkill.value !== null ) {
			this.addDataToForm('value', this.pupilSkill.value);
		}
	}
	
	refresh() {
		this.buildForm();
		
		let isAccepted = this.isAccepted();
		this.$element.find('.status-accepted').showIf(isAccepted);
		this.$element.find('.status-not-accepted').showIf(!isAccepted);
		
		if( !this.skill.valuable ) {
			this.$element.find('.action-value-edit').remove();
		}
		const valuated = this.skill.valuable && isAccepted;
		this.$element.find('.skill-valuable').showIf(valuated);
		this.$element.find('.skill-not-valuable').showIf(!valuated);
		
		let statusBg = null;
		if( isAccepted ) {
			statusBg = 'bg-success text-white';
		}
		
		if( this.skill.valuable ) {
			this.$element.find('.pupil-skill-value').text(this.pupilSkill.value || '##');
		}
		
		this.$element.find('.status-bg')
			.removeClass('bg-success text-white')
			.addClass(statusBg);
	}
}

PupilSkill.STATUS_NEW = 'new';
PupilSkill.STATUS_UPDATE = 'update';
PupilSkill.STATUS_REMOVE = 'remove';
