<?php

if( ! defined('TERMINFINDER') ) exit;

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

				$class = ['person'];
				$priority_string = '';

				if( $priority_select_enabled ) {
					$class[] = 'priority-'.$person['priority'];

					if( $person['priority'] === 1 ) {
						$priority_string = 'Prio 3';
					} elseif( $person['priority'] === 2 ) {
						$priority_string = 'Prio 2';
					} elseif( $person['priority'] === 3 ) {
						$priority_string = 'Prio 1';
					}

					$priority_string = '<br><small>('.$priority_string.')</small>';
				}

				?>
				<th class="<?= implode(' ', $class) ?>">
					<?= $name ?>
					<?= $priority_string ?>
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

	$data_yes_value = $event['yes_value'] ?? 0;
	$data_maybe_value = $event['maybe_value'] ?? 0;
	$data_no_value = $event['no_value'] ?? 0;

	if( $user_data && ! empty($user_data['events']) ) {
		$selected_value = $user_data['events'][$id] ?? false;

		if( $selected_value === 0 ) {
			$selected_none = '';
			$selected_no = ' selected';
			$data_no--;
			$data_no_value -= $user_data['priority'];
		} elseif( $selected_value === 1 ) {
			$selected_none = '';
			$selected_yes = ' selected';
			$data_yes--;
			$data_yes_value -= $user_data['priority'];
		} elseif( $selected_value === 2 ) {
			$selected_none = '';
			$selected_maybe = ' selected';
			$data_maybe--;
			$data_maybe_value -= $user_data['priority'];
		}
	}

	?>
	<tr class="<?= implode(' ', $class ) ?>" data-yes="<?= $data_yes ?>" data-no="<?= $data_no ?>" data-maybe="<?= $data_maybe ?>" data-yes_value="<?= $data_yes_value ?>" data-no_value="<?= $data_no_value ?>" data-maybe_value="<?= $data_maybe_value ?>" data-id="<?= $id ?>" data-sort="<?= $i ?>">
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

			$class = ['person'];
			if( $priority_select_enabled ) {
				$class[] = 'priority-'.$person['priority'];
			}

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
			<td class="<?= implode(' ', $class) ?>">
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
	<?php
}
?>

<?php
if( $priority_select_enabled ) {

	$selected = $user_data['priority'] ?? DEFAULT_PRIORITY;

	?>
	<p id="priority-select-wrapper">
		<label>
			<?= __('Priority') ?>: <select id="priority-select" name="priority" required>
				<option value="3"<?php if( $selected === 3 ) echo ' selected'; ?>><?= __('Really want to attend') ?></option>
				<option value="2"<?php if( $selected === 2 ) echo ' selected'; ?>><?= __('Prefer to attend') ?></option>
				<option value="1"<?php if( $selected === 1 ) echo ' selected'; ?>><?= __('Optional') ?></option>
			</select>
		</label>
		<span id="priority-select-description-3" class="priority-select-description">(<?= __('set yes for at least half of the dates, or lower the priority') ?>)</span>
		<span id="priority-select-description-2" class="priority-select-description">(<?= __('set yes or maybe for at least half of the dates, or lower the priority') ?>)</span>
		<span id="priority-select-description-1" class="priority-select-description">(<?= __('no restrictions') ?>)</span>
	</p>
	<?php
}
?>

<p><button id="submit-button"><?= __('Save') ?></button></p>
