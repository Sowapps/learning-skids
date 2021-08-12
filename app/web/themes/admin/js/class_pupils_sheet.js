$(function () {
	
	let $tableWrapper = $('#TablePupilSkillListWrapper');
	let $loader;
	if( $tableWrapper.is('.loading') ) {
		let loadingSentences = shuffle([
			"Calcul du meilleur angle d'attaque pour la sarbacane...",
			"Lecture de l'encyclopédie sur l'anthropomorphisme...",
			"Mesure de la distance entre le lièvre et la tortue...",
			"Dictée sur Mein Kampf...",
			"Rattrapage pour les profs n'ayant pas préparé leur devoir à temps...",
			"Collage de la Tour Eiffel à taille réelle...",
			"Cours de Théologie sur la nécromancie...",
			"Époussetage des anciens profs pour le cours d'archéologie...",
			"Épreuve sur la défense contre les forces du mal...",
			"Cuisson d'un poux en pâte à sel...",
			"Médiation sur le sexisme exacerbé en classe...",
		]);
		let loadingIndex = 0;
		
		$loader = $('<div class="loader position-absolute d-flex justify-content-center align-items-center" style="left: 0; top: 0; width: 100%"></div>')
			.height(Math.min($tableWrapper.innerHeight(), '600') + 'px')
			.append(
				'<div class="d-flex flex-column">' +
				'<div class="text-center"><i class="fas fa-circle-notch fa-spin fa-4x"></i></div>' +
				'<div class="loading-sentence p-2 text-muted font-weight-bold">' + (loadingSentences[loadingIndex++]) + '</div>' +
				'</div>');
		$tableWrapper.append($loader);
		
		// setInterval is not working here because JS is single-threaded and DataTable is blocking
		let loadingInterval = setInterval(function () {
			$loader.find('.loading-sentence').text(loadingSentences[loadingIndex % loadingSentences.length]);
			loadingIndex++
			if( $loader.is(':hidden') ) {
				// Auto stop
				clearInterval(loadingInterval);
			}
		}, 2000);
	}
	
	let $table = $('#TablePupilSkillList');
	setTimeout(function () {
		const pupilAsColumn = !!$table.find('thead .item-pupil-person').length;
		const options = {
			scrollY: '600px',
			scrollX: true,
			scrollCollapse: true,
			paging: false,
			ordering: false,
			orderClasses: false,
			language: {
				"infoFiltered": "(sur _MAX_ au total)",
				"zeroRecords": "Aucun résultat",
				"loadingRecords": "Chargement...",
				"processing": "Calculs...",
				"thousands": " ",
				"paginate": {
					"first": "Début",
					"last": "Fin",
					"next": "Suivante",
					"previous": "Précédente"
				},
				"aria": {
					"sortAscending": ": Tri ascendant",
					"sortDescending": ": Tri descendant"
				}
			}
		};
		if( pupilAsColumn ) {
			// With pupil as column, we show skill category and group its skills
			// With skill as column, we don't show skill category, we don't group
			options.rowGroup = {
				dataSrc: 1
			};
			// With pupil as column, we show skill category
			// With skill as column, we don't show skill category, so no hidden column
			options.columnDefs = [
				{
					"targets": [1],
					"visible": false
				},
			];
			$.extend(options.language, {
				"lengthMenu": "Afficher _MENU_ compétences",
				"info": "_TOTAL_ compétences",
				"infoEmpty": "Aucune compétence",
				"search": "Rechercher une compétence :",
			});
		} else {
			$.extend(options.language, {
				"lengthMenu": "Afficher _MENU_ élèves",
				"info": "_TOTAL_ élèves",
				"infoEmpty": "Aucun élève",
				"search": "Rechercher un élève :",
			});
			options.ordering = true;
			options.order = [
				[1, 'asc']// Column 0 specify if skill is owned & name of pupil
			];
			// With pupil as column, we don't limit width
			// With skill as column, we limit the width of each column
			options.columnDefs = [
				{
					"targets": "_all",
					"width": "30rem"
				},
			];
		}
		console.log('DataTable', options);
		let table = $table
			.on('draw.dt', function () {
				// Remove loading content
				$loader && $loader.removeClass('d-flex').hide();
				$tableWrapper.find('table.invisible').removeClass('invisible');
			})
			.on('mouseenter', 'td', function () {
				// Follow user cursor through cells
				$table.find('.column-highlight').removeClass('column-highlight');
				$(table.rows().nodes()).removeClass('row-highlight');
				
				// Number of displayed rows
				let rowCount = table.rows({search: 'applied'}).count();
				if( rowCount < 2 ) {
					return;
				}
				let cell = table.cell(this).index();
				if( !cell ) {
					return;
				}
				// Highlight Column
				let colIdx = cell.column;
				let rowIdx = cell.row;
				if( colIdx < 1 ) {
					// Ignore first column
					return;
				}
				$(table.column(colIdx).nodes()).addClass('column-highlight');
				let $nodes;
				do {
					$nodes = $(table.column(--colIdx).nodes());
					$nodes.addClass('column-highlight');
					var first = $nodes.get(rowIdx);
				} while( first !== undefined && !$(first).is(':visible') );
				// Highlight Row
				$(this).parent().addClass('row-highlight');
			})
			.DataTable(options);
		// $table.find('.item-skill .skill-name').click(function () {
		// 	$('#TablePupilSkillList_filter input[type="search"]').val($(this).text()).keyup();
		// });
	});
	
	$table.data('manager', new PupilSkillManager($table, $('#FormPupilSkills'), $('#DialogPupilSkillEdit')));
});

function shuffle(array) {
	let j, x, i;
	for( i = array.length - 1; i > 0; i-- ) {
		j = Math.floor(Math.random() * (i + 1));
		x = array[i];
		array[i] = array[j];
		array[j] = x;
	}
	return array;
}

class PupilSkillManager {
	
	$table;
	$form;
	$skillEditDialog;
	pupilAsColumn;
	
	// pupilSkills;
	
	constructor($table, $form, $skillEditDialog) {
		this.$table = $table;
		this.$form = $form;
		this.$skillEditDialog = $skillEditDialog;
		this.pupilAsColumn = !!$table.find('thead .item-pupil-person').length;
		
		this.initialize();
	}
	
	initialize() {
		let manager = this;
		if( this.pupilAsColumn ) {
			const pupilPersons = this.$table.find('thead .item-pupil-person').map(function () {
				return $(this).data('pupilPerson');
			});
			this.$table.find('.item-pupil-skill').each(function () {
				let pupilPerson = pupilPersons[$(this).index() - 2];
				let pupilSkill = new PupilSkill(manager, this, pupilPerson, $(this).closest('.item-skill').data('skill') || {});
				$(this).data('pupilSkill', pupilSkill);
			});
		} else {
			const skills = this.$table.find('thead .item-skill').map(function () {
				return $(this).data('skill');
			});
			this.$table.find('.item-pupil-skill').each(function () {
				let skill = skills[$(this).index() - 1];
				let pupilSkill = new PupilSkill(manager, this, $(this).closest('.item-pupil-person').data('pupilPerson') || {}, skill);
				$(this).data('pupilSkill', pupilSkill);
			});
		}
	}
	
	async openSkillEditDialog(data) {
		const deferred = $.Deferred();
		const $dialog = this.$skillEditDialog;
		
		$dialog.modal('show')
			.resetForm()
			.fill('skill', data.skill)
			.fillByName(data.pupilSkill, 'pupilSkill[%s]');
		
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
				deferred.resolve(null);
				// deferred.reject();
			});
		
		return deferred.promise();
	}
	
}

class PupilSkill {
	
	static counter = 0;
	manager; // PupilSkillManager
	$element; // TR row in table for this skill
	skill; // Learning Sheet Skill
	pupilPerson; // Pupil Skill
	pupilSkill; // Pupil Skill
	status; // Pupil Skill Status, empty is no changes
	
	constructor(manager, $element, pupilPerson, skill) {
		this.manager = manager;
		this.$element = $($element);
		this.$inputAccept = $($element).find('.input-skill-accept');
		this.index = PupilSkill.counter;
		this.pupilPerson = pupilPerson;
		this.skill = skill;
		this.pupilSkill = this.$element.data('pupilSkill') || {};
		this.status = null;
		
		if( this.pupilSkill.id ) {
			// Accept is only for client-side to ensure object has data
			this.pupilSkill.accept = 1;
		} else {
			this.pupilSkill.accept = 0;
		}
		PupilSkill.counter++;
		
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
		this.$element.find('.input-skill-accept').change(async() => {
			let checked = this.$inputAccept.prop('checked');
			if( checked ) {
				if( this.skill.valuable ) {
					const data = await this.manager.openSkillEditDialog(this);
					if( data ) {
						this.accept(data.pupilSkill.value);
					} else {
						this.$inputAccept.prop('checked', false);
					}
				} else {
					this.accept(null);
				}
			} else {
				this.remove();
			}
		});
		this.$element.find('.action-value-edit').click(async() => {
			if( !this.skill.valuable ) {
				return;
			}
			const data = await this.manager.openSkillEditDialog(this);
			if( data ) {
				this.accept(data.pupilSkill.value);
			}
			return false;
		});
	}
	
	notifyChanges() {
		this.refresh();
	}
	
	getInputPrefix() {
		return 'pupilSkill[' + this.index + ']';
	}
	
	getInputClass() {
		return 'input-ps-' + this.pupilPerson.id + '-' + this.skill.id;
	}
	
	addDataToForm(key, value) {
		let $input = $('<input type="hidden">');
		$input.addClass(this.getInputClass());
		$input.attr('name', this.getInputPrefix() + '[' + key + ']');
		$input.val(value);
		this.getDomForm().append($input);
	}
	
	emptyForm() {
		this.getDomForm().find('.' + this.getInputClass()).remove();
	}
	
	getDomForm() {
		return this.manager.$form;
	}
	
	buildForm() {
		this.emptyForm();
		if( !this.status ) {
			return;
		}
		if( this.isExisting() ) {
			this.addDataToForm('id', this.pupilSkill.id);
		}
		this.addDataToForm('person_id', this.pupilPerson.id);
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
