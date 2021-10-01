$(function () {
	
	let $table = $('#TablePupilSkillList');
	$table.data('manager', new PupilSkillManager($table, $('#DialogPupilSkillEdit')));
	
});

class PupilSkillManager {
	
	constructor($table, $skillEditDialog) {
		this.$table = $table;
		this.$skillEditDialog = $skillEditDialog;
		this.readOnly = !!$table.data('readonly');
		this.$skillValueRowTemplate = $('#TemplateValueRow').detach();
		this.skills = [];
		
		this.initialize();
		this.bindEvents();
	}
	
	bindEvents() {
		this.$skillEditDialog.on('hide.bs.modal', () => {
			this.$skillEditDialog.find('#CollapseValueHistory').collapse('hide');
		});
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
		const readOnly = data.readOnly;
		
		$dialog.modal('show')
			.resetForm()
			.fill('skill', data.skill)
			.fillByName(data.pupilSkill, 'pupilSkill[%s]')
			.toggleClass('state-readonly', readOnly);
		const $form = $dialog.getForm();
		$form.removeClass('was-validated');
		
		if( $dialog.find('.pupil-skill-history').length ) {
			const hasValues = data.pupilSkill.id && data.pupilSkill.values && data.pupilSkill.values.length;
			$dialog.find('.pupil-skill-history').toggle(!!hasValues);
			$dialog.find('.pupil-skill-history-body').empty();
			if( hasValues ) {
				for( const skillValue of data.pupilSkill.values ) {
					const $row = await domService.renderTemplate(this.$skillValueRowTemplate, skillValue);
					$dialog.find('.pupil-skill-history-body').append($row);
				}
			}
		}
		$dialog.find('.show-if-valuable').toggle(data.skill.valuable);
		$dialog.find('.show-if-valuable :input').prop('disabled', !data.skill.valuable);
		readOnly && $dialog.find('.modal-body').disableInputs() || $dialog.find('.modal-body').enableInputs();
		
		$dialog.find('.action-accept')
			.off('click')
			.on('click', () => {
				const valid = $form[0].checkValidity();
				$form.addClass('was-validated');
				if( valid ) {
					let data = $dialog.getFormObject();
					$dialog.modal('hide');
					deferred.resolve(data);
				}
			});
		
		$dialog.find('.action-cancel')
			.off('click')
			.on('click', () => {
				deferred.resolve(null);
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
		this.readOnly = manager.readOnly;
		this.skill = this.$row.data('skill') || {};
		this.skill.valuable = !!this.skill.valuable;// Format value
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
	
	accept(pupilSkill) {
		this.pupilSkill.accept = 1;
		this.pupilSkill.value = this.skill.valuable ? pupilSkill.value : null;
		this.pupilSkill.date = pupilSkill.date;
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
			await this.openEditDialog();
		});
		this.$row.find('.action-value-edit').click(async() => {
			this.openEditDialog();
			
			return false;
		});
		this.$row.find('.action-skill-reject').click(() => {
			if( this.readOnly ) {
				this.openEditDialog();
				
				return;
			}
			this.remove();
		});
	}
	
	async openEditDialog() {
		// Fix defaults
		this.pupilSkill.date ||= moment().format('DD/MM/YYYY');
		// Open dialog
		const data = await this.manager.openSkillEditDialog(this);
		if( data ) {
			this.accept(data.pupilSkill);
			return true;
		}
		// Cancel, do not mean reject
		return false;
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
		this.addDataToForm('date', this.pupilSkill.date);
		if( this.status !== PupilSkill.STATUS_REMOVE && this.pupilSkill.value !== null ) {
			this.addDataToForm('value', this.pupilSkill.value);
		}
	}
	
	refresh() {
		this.buildForm();
		
		let isAccepted = this.isAccepted();
		this.$element.find('.status-accepted').showIf(isAccepted);
		this.$element.find('.status-not-accepted').showIf(!isAccepted);
		
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
		this.$element.find('.pupil-skill-date').text(this.pupilSkill.date || '##');
		
		this.$element.find('.status-bg')
			.removeClass('bg-success text-white')
			.addClass(statusBg);
	}
}

PupilSkill.STATUS_NEW = 'new';
PupilSkill.STATUS_UPDATE = 'update';
PupilSkill.STATUS_REMOVE = 'remove';
