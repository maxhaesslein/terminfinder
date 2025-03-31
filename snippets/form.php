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
		include('snippets/form_participation.php');
	} else {
		include('snippets/form_creation.php');
	}
	?>

</form>
