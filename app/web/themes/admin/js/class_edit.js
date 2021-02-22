$(function () {
	
	// Add Pupil
	let newPupilList = new FormList('.list-new-class-pupil', '.item-new-class-pupil');
	newPupilList.addItem();
	$(newPupilList.$list).on('keyup', newPupilList.itemSelector + ':last-child :input', function () {
		console.log('Key up');
		let $item = $(this).closest(newPupilList.itemSelector);
		if( $item.find(':input.person_firstname').val() && $item.find(':input.person_lastname').val() ) {
			newPupilList.addItem();
		}
	});
	$(newPupilList.$list).on('click', newPupilList.itemSelector + ':not(:last-child) .action-item-remove', function () {
		newPupilList.removeItem(this);
	});
	
	// Edit Pupil
	let $dialogClassPupilEdit = $("#DialogClassPupilEdit").modal({show: false});
	$('.item-pupil .action-edit-pupil').click(function () {
		$row = $(this).closest('.item-pupil');
		$dialogClassPupilEdit.find('form').resetForm();
		$dialogClassPupilEdit.fill('pupil', $row.data('item'));
		$dialogClassPupilEdit.fillByName($row.data('item'), 'person[%s]');
		$dialogClassPupilEdit.modal('show');
	});
	
});

class FormList {
	
	constructor($list, itemSelector) {
		this.$list = $($list);
		this.itemSelector = itemSelector;
		let $template = $($list).find(this.itemSelector).filter('.template');
		this.itemTemplate = $template[0].outerHTML;
		$template.remove();
		this.itemIndex = 0;
	}
	
	addItem() {
		let $item = $(this.itemTemplate.replaceAll('{{ index }}', this.itemIndex));
		$item.removeClass('template');
		this.$list.append($item);
		this.itemIndex++;
	}
	
	removeItem($item) {
		$item = $($item);
		if( !$item.is(this.itemSelector) ) {
			$item = $item.closest(this.itemSelector);
			if( !$item.length ) {
				throw 'Unable to find item to remove';
			}
		}
		$item.remove();
	}
}
