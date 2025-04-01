<?php

define( 'TERMINFINDER', 'post' );

include_once('include/helper.php');
include_once('include/config.php');
include_once('include/variables.php');
include_once('include/language.php');


if( ! $event ) {
	url_redirect();
}

$redirect_args = ['event' => $event];

if( $user_hash ) {
	$redirect_args['user'] = $user_hash;
}


$action = $_POST['action'] ?? false;

$name = $_POST['name'] ?? '';
$name = trim($name);
if( empty($name) ) {
	url_redirect( $redirect_args, ['error' => 'name'] );
}

if( $action === 'new' ) {
	include( 'snippets/post_creation.php' );
} else {
	include( 'snippets/post_participation.php' );
}

exit;