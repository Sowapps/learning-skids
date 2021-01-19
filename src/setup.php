<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */


function renderReadonlyInputHtml($label, $value) {
	$id = 'Input' . generateUniqueId();
	?>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="<?php echo $id; ?>">
			<?php echo $label; ?>
		</label>
		<div class="col-sm-9">
			<input type="text" readonly class="form-control-plaintext" id="<?php echo $id; ?>" value="<?php echo $value; ?>">
		</div>
	</div>
	<?php
}

function generateUniqueId() {
	static $id = 0;
	$id++;
	
	return $id;
}
