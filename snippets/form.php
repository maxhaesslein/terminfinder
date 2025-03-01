<?php

if( ! defined('TERMINFINDER') ) exit;


if( $title ) echo '<h2>'.$title.'</h2>';
if( $description ) echo '<p>'.$description.'</p>';

?>

<form id="lv-form" action="post.php" method="POST">

	<?php

	if( ! empty($_REQUEST['error']) ) {
		echo '<p style="color: red;"><strong>Fehler</strong> beim speichern :( -- '.$_REQUEST['error'].'</p>';
	} elseif( isset($_REQUEST['success']) ) {
		echo '<p style="color: green;"><strong>Erfolgreich gespeichert</strong></p>';
		
		$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
	. "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?event=$event&user=".$_REQUEST['user'];

		echo '<p>Dein Link, um deinen Eintrag wieder zu bearbeiten, ist: <em><a href="'.$link.'">'.$link.'</a></</p>';
	}

	?>

	<input type="hidden" name="event" value="<?= $event ?>">

	<?php
	
	if( is_array($schedule) && count($schedule) ) {

		$data_people_count = $people_count;
		if( ! $user_data ) $data_people_count++;

		?>
		<table id="schedule-list" class="schedule-list" data-people-count="<?= $data_people_count ?>">
			<thead>
				<tr>
					<th>
						Termin
					</th>
					<th></th>
					<th>
						✅
					</th>
					<th>
						❔
					</th>
					<th>
						❌
					</th>
					<th></th>
					<th>
						Deine Auswahl
					</th>
					<th></th>
					<?php
					foreach( $people as $person ) {
						$name = $person['name'] ?? false;
						if( ! $name ) continue;
						?>
						<th>
							<?= $name ?>
						</th>
						<?php
					}
					?>
				</tr>
			</thead>
		<?php
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
			<tr class="<?= implode(' ', $class ) ?>" data-yes="<?= $data_yes ?>" data-no="<?= $data_no ?>" data-maybe="<?= $data_maybe ?>" data-id="<?= $id ?>">
				<td class="event-title" title="<?= $id ?>">
					<?= $name ?>
				</td>
				<td class="spacer"></td>
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
				<td class="spacer"></td>
				<td>
					<select name="entry_<?= $id ?>" required>
						<option value=""<?= $selected_none ?>>--</option>
						<option value="1"<?= $selected_yes ?>>Ja</option>
						<option value="2"<?= $selected_maybe ?>>Wenn's sein muss</option>
						<option value="0"<?= $selected_no ?>>Nein</option>
					</select>
				</td>
				<td class="spacer"></td>
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
					<td>
						<?= $option_string ?>
					</td>
					<?php
				}
				?>
			</tr>
			<?php
		}
		?>
		</table>

		<?php
		if( $user_data && ! empty($user_data['name']) ) {
			?>
			<p>Name: <strong><?= $user_data['name'] ?></strong></p>
			<input type="hidden" name="name" value="<?= $user_data['name'] ?>">
			<input type="hidden" name="user" value="<?= $_REQUEST['user'] ?>">
			<?php
		} else {
			?>
			<p><label>
				Name: <input type="text" name="name" value="" required>
			</label></p>
			<?php
		}
		?>
		<p><button>Speichern</button></p>

		<?php
	} else {

		// creation form
		?>

		<input type="hidden" name="action" value="new">

		<p><label>
			Termin-Titel: <input type="text" name="event_title" required>
		</label></p>
		<p><label>
			Termin-Beschreibung (optional): <input type="text" name="event_description">
		</label></p>
		<p><label>
			Dein Name: <input type="text" name="name" required>
		</label></p>

		<strong>Termine:</strong>
		<table id="new-table">
			<tbody>
				<tr id="new-event-template" draggable="true" hidden>
					<td class="dragger"></td>
					<td>
						<label><input type="text" name="event_slot_name[]" placeholder="Datum, Uhrzeit"></label>
					</td>
					<td>
						<select name="event_slot_selection[]">
							<option value="1" selected>Ja</option>
							<option value="2">Wenn's sein muss</option>
						</select>
					</td>
					<td>
						<button title="Termin entfernen">X</button>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="4">
						<button id="add-event">Termin hinzufügen</button>
					</th>
				</tr>
			</tfoot>
		</table>

		<p><button>Termin anlegen</button></p>
		<?php

	}
	?>

</form>
