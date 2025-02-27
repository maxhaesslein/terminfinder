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
	}

	?>

	<input type="hidden" name="event" value="<?= $event ?>">

	<?php
	
	if( is_array($schedule) && count($schedule) ) {
		?>
		<table class="schedule-list">
			<thead>
				<tr>
					<th>
						Termin
					</th>
					<th>
						Auswahl
					</th>
					<th>
						Ja
					</th>
					<th>
						Nein
					</th>
					<th>
						Vielleicht
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
			<tr class="<?= implode(' ', $class ) ?>">
				<td>
					<strong title="<?= $id ?>"><?= $name ?></strong>
				</td>
				<td>
					<select name="<?= $id ?>">
						<option value="1">Ja</option>
						<option value="2">Vielleicht</option>
						<option value="0" selected>Nein</option>
					</select>
				</td>
				<td>
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
				<td>
					<?php
					if( $max_no === $no ) {
						echo '<strong>'.$no.'</strong>/'.$people_count;
					} else {
						echo $no.'/'.$people_count;
					}
					?>
				</td>
				<td>
					<?= $maybe ?>/<?= $people_count ?>
				</td>
				<?php
				foreach( $people as $person ) {
					$name = $person['name'] ?? false;
					if( ! $name ) continue;

					$p_events = $person['events'] ?? [];
					$option = $p_events[$id] ?? false;

					?>
					<td>
						<?= $option ?>
					</td>
					<?php
				}
				?>
			</tr>
			<?php
		}
		?>
		</table>

		<p><label>
			Name: <input type="text" name="name" required>
		</label></p>

		<p><button>Speichern</button></p>

		<?php
	}
	?>

</form>
