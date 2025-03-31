<?php

if( ! defined('TERMINFINDER') ) exit;


if( $priority < 1 || $priority > 3 ) {
	url_redirect($redirect.'&error=priority');
}

// TODO: check if priority_select_enabled is set
// TODO: check if priority prerequisites are met

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

$people[] = [
	'name' => $name,
	'priority' => $priority,
	'events' => $new_events
];

$data['people'] = $people;


if( ! file_put_contents("data/".$event.".json", json_encode($data)) ) {
	url_redirect($redirect.'&error=save');
}

$redirect .= '&user='.get_hash( $name );

url_redirect($redirect.'&success');
