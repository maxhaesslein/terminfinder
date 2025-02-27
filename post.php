<?php

define( 'TERMINFINDER', 'post' );

include_once('include/helper.php');
include_once('include/variables.php');


if( ! $event ) {
	url_redirect();
}

$redirect = 'index.php?event='.$event;


$name = $_POST['name'] ?? '';
$name = trim($name);
if( empty(trim($name)) ) {
	url_redirect($redirect.'&error=name');
}

$new_events = [];
foreach( $_POST as $key => $value ) {

	if( ! str_starts_with($key, 'entry_') ) continue;

	$id = str_replace('entry_', '', $key );
	if( ! $id ) continue;

	$new_events[$id] = (int) $value;
}

if( ! count($new_events) ) {
	url_redirect($redirect.'&error=events');
}

if( ! is_array($data['people']) ) $data['people'] = [];
$data['people'][] = [
	'name' => $name,
	'events' => $new_events
];


if( ! file_put_contents("data/".$event.".json", json_encode($data)) ) {
	url_redirect($redirect.'&error=save');
}

url_redirect($redirect.'&success');

exit;
