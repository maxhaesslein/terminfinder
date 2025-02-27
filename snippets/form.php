<?php

if( ! defined('TERMINFINDER') ) exit;


if( $title ) echo '<h2>'.$title.'</h2>';
if( $description ) echo '<p>'.$description.'</p>';

// TODO: if $people is empty, allow creation of new events

?>

<form id="lv-form" action="post.php" method="POST">

	<?php

	if( ! empty($_REQUEST['error']) ) {
		echo '<p style="color: red;"><strong>Fehler</strong> beim speichern :( -- '.$_REQUEST['error'].'</p>';
	} elseif( isset($_REQUEST['success']) ) {
		echo '<p style="color: green;"><strong>Erfolgreich gespeichert</strong></p>';
	}

	?>

	<input type="hidden" name="event" value="<?= $event ?>">

	<?php
	
	if( is_array($schedule) && count($schedule) ) {
		?>
		<table id="schedule-list" class="schedule-list" data-max-yes="<?= $max_yes ?>" data-max-no="<?= $max_no ?>" data-max-yes-maybe="<?= $max_yes_maybe ?>" data-people-count="<?= $people_count ?>">
			<thead>
				<tr>
					<th>
						Termin
					</th>
					<?php
					if( ! isset($_GET['success']) ) {
						?>
					<th>
						Auswahl
					</th>
						<?php
					}
					?>
					<th>
						Ja
					</th>
					<th>
						Vielleicht
					</th>
					<th>
						Nein
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

			?>
			<tr class="<?= implode(' ', $class ) ?>" data-yes="<?= $yes ?>" data-no="<?= $no ?>" data-maybe="<?= $maybe ?>">
				<td>
					<strong title="<?= $id ?>"><?= $name ?></strong>
				</td>
				<?php
				if( ! isset($_GET['success']) ) {
					?>
				<td>
					<select name="entry_<?= $id ?>" required>
						<option value="" selected>--</option>
						<option value="1">Ja</option>
						<option value="2">Vielleicht</option>
						<option value="0">Nein</option>
					</select>
				</td>
					<?php
				}
				?>
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
						$option_string = 'Vielleicht';
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
		if( ! isset($_GET['success']) ) {
			?>
			<p><label>
				Name: <input type="text" name="name" required>
			</label></p>
			<p><button>Speichern</button></p>
			<?php
		}

	}
	?>

</form>
