<?php

if( ! defined('TERMINFINDER') ) exit;

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

	<?php // TODO ?>

</form>
