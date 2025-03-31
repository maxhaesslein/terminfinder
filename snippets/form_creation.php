<?php

if( ! defined('TERMINFINDER') ) exit;

?>

<input type="hidden" name="action" value="new">

<p><label>
	<?= __('Session Title') ?>: <input type="text" name="event_title" required>
</label></p>
<p><label>
	<?= __('Session Details') ?> (<?= __('optional') ?>): <input type="text" name="event_description">
</label></p>
<p><label>
	<?= __('Name') ?>: <input type="text" name="name" required>
</label></p>

<strong><?= __('Dates') ?>:</strong>
<table id="new-table">
	<tbody>
		<tr id="new-event-template" draggable="true" hidden>
			<td class="dragger"></td>
			<td>
				<label><input type="text" name="event_slot_name[]" placeholder="<?= __('Date, Time') ?>"></label>
			</td>
			<td>
				<select name="event_slot_selection[]">
					<option value="1" selected><?= __('Yes') ?></option>
					<option value="2"><?= __('If need be') ?></option>
				</select>
			</td>
			<td>
				<button title="<?= __('Remove Date') ?>">X</button>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="4">
				<button id="add-event"><?= __('Add Date') ?></button>
			</th>
		</tr>
	</tfoot>
</table>

<p><button><?= __('Create Session') ?></button></p>
