<?php

if( ! defined('TERMINFINDER') ) exit;

$preset_title = isset($_GET['title']) ? urldecode($_GET['title']) : '';
$preset_description = isset($_GET['description']) ? urldecode($_GET['description']) : '';
$preset_name = isset($_GET['name']) ? urldecode($_GET['name']) : '';
$preset_priority_select_enabled = isset($_GET['priority_select_enabled']) && $_GET['priority_select_enabled'] === "1" ? true : false;

$preset_slot_names = isset($_GET['slot_names']) ? urldecode($_GET['slot_names']) : '';
$preset_slot_selections = isset($_GET['slot_selections']) ? urldecode($_GET['slot_selections']) : '';

if( $preset_slot_names ) {
	$preset_slot_names = json_decode($preset_slot_names, true) ?? [];
}

if( $preset_slot_selections ) {
	$preset_slot_selections = json_decode($preset_slot_selections, true) ?? [];
}

?>

<input type="hidden" name="action" value="new">

<p><label>
	<?= __('Session Title') ?>: <input type="text" name="event_title" value="<?= $preset_title ?>" required>
</label></p>
<p><label>
	<?= __('Session Details') ?> (<?= __('optional') ?>): <input type="text" name="event_description" value="<?= $preset_description ?>">
</label></p>
<p><label>
	<?= __('Your Name') ?>: <input type="text" name="name" value="<?= $preset_name ?>" required>
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
		<?php
		if( is_array($preset_slot_names) && count($preset_slot_names) && is_array($preset_slot_selections) && count($preset_slot_selections) ) {

			for( $i = 0; $i < count($preset_slot_selections); $i++ ) {
				$preset_slot_name = $preset_slot_names[$i] ?? '';
				$preset_slot_selection = $preset_slot_selections[$i] ?? "1";

				?>
				<tr draggable="true">
					<td class="dragger"></td>
					<td>
						<label><input type="text" name="event_slot_name[]" placeholder="<?= __('Date, Time') ?>" value="<?= $preset_slot_name ?>" required></label>
					</td>
					<td>
						<select name="event_slot_selection[]">
							<option value="1"<?php if($preset_slot_selection == "1") echo ' selected'; ?>><?= __('Yes') ?></option>
							<option value="2"<?php if($preset_slot_selection == "2") echo ' selected'; ?>><?= __('If need be') ?></option>
						</select>
					</td>
					<td>
						<button title="<?= __('Remove Date') ?>">X</button>
					</td>
				</tr>
				<?php
			}

		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="4">
				<button id="add-event"><?= __('Add Date') ?></button>
			</th>
		</tr>
	</tfoot>
</table>

<p>
	<label>
		<input type="checkbox" name="priority-select-enabled" value="1"<?php if($preset_priority_select_enabled) echo ' checked'; ?>><?= __('Allow Priority Select') ?>
	</label>
	<br><small><?= __('If activated, the user can select between different priority options, like "I really want to attend" or "I don\'t need to attend".') ?></small>
</p>

<p><button><?= __('Create Session') ?></button></p>
