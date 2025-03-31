<?php

if( ! defined('TERMINFINDER') ) exit;


if( $title ) echo '<h2>'.$title.'</h2>';
if( $description ) echo '<p>'.$description.'</p>';

?>

<form id="lv-form" action="post.php" method="POST">

	<?php

	if( ! empty($_REQUEST['error']) ) {
		echo '<p style="color: red;"><strong>'.__('Error').'</strong> '.__('while saving').' ('.$_REQUEST['error'].')</p>';
	} elseif( isset($_REQUEST['success']) ) {
		echo '<p style="color: green;"><strong>'.__('Successfully saved').'</strong></p>';
		
		$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
	. "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?event=$event&user=".$_REQUEST['user'];

		echo '<p>'.__('Your link for re-editing your entry is').': <em><a href="'.$link.'">'.$link.'</a></em></p>';
	}

	?>

	<input type="hidden" name="event" value="<?= $event ?>">

	<?php
	
	if( is_array($schedule) && count($schedule) ) {

		$data_people_count = $people_count;
		if( ! $user_data ) $data_people_count++;

		?>
		<p id="sort-wrapper" hidden><label>
			<?= __('Sort by') ?>: <select id="sort-order" name="sort-order">
				<option value="chronological" selected><?= __('Sequentially') ?></option>
				<option value="vote-count"><?= __('Vote Count') ?></option>
			</select>
		</label></p>
		<table id="schedule-list" class="schedule-list" data-people-count="<?= $data_people_count ?>">
			<thead>
				<tr>
					<th class="event-title">
						<?= __('Session') ?>
					</th>
					<th class="yes">
						✅
					</th>
					<th class="maybe">
						❔
					</th>
					<th class="no">
						❌
					</th>
					<th class="selector">
						<?= __('Your selection') ?>
					</th>
					<?php
					foreach( $people as $person ) {
						$name = $person['name'] ?? false;
						if( ! $name ) continue;
						?>
						<th class="person">
							<?= $name ?>
						</th>
						<?php
					}
					?>
					<th class="person-toggle"></th>
				</tr>
			</thead>
		<?php
		$i = 0;
		foreach( $schedule as $id => $event ) {

			$name = $event['name'] ?? false;
			if( ! $name ) continue;

			$yes = $event['yes'] ?? 0;
			$no = $event['no'] ?? 0;
			$maybe = $event['maybe'] ?? 0;

			$class = [ 'event-line' ];

			if( count($winners) && in_array($id, $winners[1]) ) {
				$class[] = 'event-winner-1';
			} elseif( count($winners) > 1 && in_array($id, $winners[2]) ) {
				$class[] = 'event-winner-2';
			} elseif( count($winners) > 2 && in_array($id, $winners[3]) ) {
				$class[] = 'event-winner-3';
			}

			$data_yes = $yes;
			$data_no = $no;
			$data_maybe = $maybe;

			$selected_none = ' selected';
			$selected_yes = '';
			$selected_maybe = '';
			$selected_no = '';

			if( $user_data && ! empty($user_data['events']) ) {
				$selected_value = $user_data['events'][$id] ?? false;

				if( $selected_value === 0 ) {
					$selected_none = '';
					$selected_no = ' selected';
					$data_no--;
				} elseif( $selected_value === 1 ) {
					$selected_none = '';
					$selected_yes = ' selected';
					$data_yes--;
				} elseif( $selected_value === 2 ) {
					$selected_none = '';
					$selected_maybe = ' selected';
					$data_maybe--;
				}
			}

			?>
			<tr class="<?= implode(' ', $class ) ?>" data-yes="<?= $data_yes ?>" data-no="<?= $data_no ?>" data-maybe="<?= $data_maybe ?>" data-id="<?= $id ?>" data-sort="<?= $i ?>">
				<td class="event-title" title="<?= $id ?>">
					<?= $name ?>
				</td>
				<td class="yes">
					<?php
					if( $max_yes === $yes ) {
						echo '<strong>'.$yes.'</strong>';
					} else {
						echo $yes;
					}
					?>
				</td>
				<td class="maybe">
					<?= $maybe ?>
				</td>
				<td class="no">
					<?php
					if( $max_no === $no ) {
						echo '<strong>'.$no.'</strong>';
					} else {
						echo $no;
					}
					?>
				</td>
				<td class="selector">
					<select name="entry_<?= $id ?>" required>
						<option value=""<?= $selected_none ?>>--</option>
						<option value="1"<?= $selected_yes ?>><?= __('Yes') ?></option>
						<option value="2"<?= $selected_maybe ?>><?= __('If need be') ?></option>
						<option value="0"<?= $selected_no ?>><?= __('No') ?></option>
					</select>
				</td>
				<?php
				foreach( $people as $person ) {
					$name = $person['name'] ?? false;
					if( ! $name ) continue;

					$p_events = $person['events'] ?? [];
					$option = $p_events[$id] ?? false;

					if( $option === 0 ) {
						$option_string = '❌';
					} else if( $option === 1 ) {
						$option_string = '✅';
					} else if( $option === 2 ) {
						$option_string = "❔";
					} else {
						$option_string = '--';
					}

					?>
					<td class="person">
						<?= $option_string ?>
					</td>
					<?php
				}

				if( $i == 0 ) {
					?>
					<td class="person-toggle" rowspan="<?= count($schedule) ?>">
						&harr;
					</td>
					<?php
				}

				$i++;
				?>
			</tr>
			<?php
		}
		?>
		</table>

		<?php
		if( $user_data && ! empty($user_data['name']) ) {
			?>
			<p><?= __('Name') ?>: <strong><?= $user_data['name'] ?></strong></p>
			<input type="hidden" name="name" value="<?= $user_data['name'] ?>">
			<input type="hidden" name="user" value="<?= $_REQUEST['user'] ?>">
			<?php
		} else {
			?>
			<p><label>
				<?= __('Name') ?>: <input type="text" name="name" placeholder="<?= __('Name') ?>" value="" required>
			</label></p>
			<p><label>
				<?= __('Priority') ?>: <select name="priority"required>
					<option value="3"><?= __('Really want to attend') ?></option>
					<option value="2" selected><?= __('Prefer to attend') ?></option>
					<option value="1"><?= __('Optional') ?></option>
				</select>
			</label></p>
			<?php
		}
		?>
		<p><button id="submit-button"><?= __('Save') ?></button></p>

		<?php
	} else {

		// creation form
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
		<?php

	}
	?>

</form>
