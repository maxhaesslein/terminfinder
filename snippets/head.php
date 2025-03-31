<?php

if( ! defined('TERMINFINDER') ) exit;

?><!DOCTYPE html>
<html lang="<?= $locale_code ?>">
<head>
	<meta charset="utf-8">

	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="viewport" content="viewport-fit=cover, user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">

	<title>Terminfinder</title>

	<meta name="version" content="<?= VERSION ?>">

	<link rel="stylesheet" href="assets/css/style.css?v=<?= VERSION ?>" type="text/css" media="all">

	<script type="text/javascript" src="assets/js/global.js?v=<?= VERSION ?>" defer></script>
	<?php
	if( is_array($schedule) && count($schedule) ) {
		// participation form
		?>
		<script type="text/javascript" src="assets/js/participation.js?v=<?= VERSION ?>" defer></script>
		<?php
	} else {
		// creation form
		?>
		<script type="text/javascript" src="assets/js/creation.js?v=<?= VERSION ?>" defer></script>
		<?php
	}
	?>

</head>