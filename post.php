<?php

define( 'TERMINFINDER', 'post' );

include_once('include/helper.php');
include_once('include/config.php');
include_once('include/variables.php');
include_once('include/language.php');


if( ! $event ) {
	url_redirect();
}

$redirect = 'index.php?event='.$event;

if( $user_hash ) {
	$redirect .= '&user='.$user_hash;
}


$action = $_POST['action'] ?? false;

$name = $_POST['name'] ?? '';
$name = trim($name);
if( empty($name) ) {
	url_redirect($redirect.'&error=name');
}

$priority = $_POST['priority'] ?? '';
$priority = (int) trim($priority);


if( $action === 'new' ) {
	include( 'snippets/post_creation.php' );
} else {
	include( 'snippets/post_participation.php' );
}

exit;