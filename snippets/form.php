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

		echo '<p>Deine Link, um deinen Eintrag wieder zu bearbeiten, ist: <a href="'.$link.'">'.$link.'</a></p>';
	}

	?>

	<input type="hidden" name="event" value="<?= $event ?>">

	<?php
	
	if( is_array($schedule) && count($schedule) ) {

		$people_count_js = $people_count;
		if( $user_data ) $people_count_js--;

		?>
		<table id="schedule-list" class="schedule-list" data-max-yes="<?= $max_yes ?>" data-max-no="<?= $max_no ?>" data-max-yes-maybe="<?= $max_yes_maybe ?>" data-people-count="<?= $people_count_js ?>">
			<thead>
				<tr>
					<th>
						Termin
					</th>
					<th>
						Ja
					</th>
					<th>
						Vielleicht
					</th>
					<th>
						Nein
					</th>
					<th>
						Deine Auswahl
					</th>
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

			if( $yes + $maybe === $max_yes_maybe ) $class[] = 'event-winner';

			$selected_none = ' selected';
			$selected_yes = '';
			$selected_maybe = '';
			$selected_no = '';

			if( $user_data && ! empty($user_data['events']) ) {
				$selected_value = $user_data['events'][$id] ?? false;

				if( $selected_value === 0 ) {
					$selected_none = '';
					$selected_no = ' selected';
				} elseif( $selected_value === 1 ) {
					$selected_none = '';
					$selected_yes = ' selected';
				} elseif( $selected_value === 2 ) {
					$selected_none = '';
					$selected_maybe = ' selected';
				}
			}

			?>
			<tr class="<?= implode(' ', $class ) ?>" data-yes="<?= $yes ?>" data-no="<?= $no ?>" data-maybe="<?= $maybe ?>">
				<td>
					<strong title="<?= $id ?>"><?= $name ?></strong>
				</td>
				<td class="yes">
					<?php
					if( $max_yes === $yes ) {
						if( $yes === $people_count ) {
							echo '<strong>'.$yes.'/'.$people_count.'</strong>';
						} else {
							echo '<strong>'.$yes.'</strong>/'.$people_count;
						}
					} else {
						echo $yes.'/'.$people_count;
					}
					?>
				</td>
				<td class="maybe">
					<?= $maybe ?>/<?= $people_count ?>
				</td>
				<td class="no">
					<?php
					if( $max_no === $no ) {
						echo '<strong>'.$no.'</strong>/'.$people_count;
					} else {
						echo $no.'/'.$people_count;
					}
					?>
				</td>
				<td>
					<select name="entry_<?= $id ?>" required>
						<option value=""<?= $selected_none ?>>--</option>
						<option value="1"<?= $selected_yes ?>>Ja</option>
						<option value="2"<?= $selected_maybe ?>>Wenn's sein muss</option>
						<option value="0"<?= $selected_no ?>>Nein</option>
					</select>
				</td>
				<?php
				foreach( $people as $person ) {
					$name = $person['name'] ?? false;
					if( ! $name ) continue;

					$p_events = $person['events'] ?? [];
					$option = $p_events[$id] ?? false;

					if( $option === 0 ) {
						$option_string = 'Nein';
					} else if( $option === 1 ) {
						$option_string = 'Ja';
					} else if( $option === 2 ) {
						$option_string = "Wenn's sein muss";
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
