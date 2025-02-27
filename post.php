<?php

define( 'TERMINFINDER', 'post' );

include_once('include/helper.php');
include_once('include/variables.php');


if( ! $event ) {
	url_redirect();
}

$redirect = 'index.php?event='.$event;

url_redirect($redirect.'&not-implemented');

exit;
