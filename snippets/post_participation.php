<?php

if( ! defined('TERMINFINDER') ) exit;


$redirect_args['name'] = $name;


$new_events = [];
foreach( $_POST as $key => $value ) {

	if( ! str_starts_with($key, 'entry_') ) continue;

	$id = str_replace('entry_', '', $key );
	if( ! $id ) continue;

	$new_events[$id] = (int) $value;
}

if( ! count($new_events) ) {
	url_redirect( $redirect_args, ['error' => 'events'] );
}


$redirect_args['events'] = json_encode($new_events);


$priority = DEFAULT_PRIORITY;
if( $priority_select_enabled ) {

	$priority = $_POST['priority'] ?? '';
	$priority = (int) trim($priority);

	$redirect_args['priority'] = $priority;

	if( $priority === 1 ) { // optional
		// NOTE: allow all combinations

	} elseif( $priority === 2 ) { // prefer to attend
		
		// NOTE: we want to have 'yes' or 'maybe' for at least one third (rounded up) of the dates
		$yes_maybe_count = 0;
		foreach( $new_events as $new_event_option ) {
			if( $new_event_option === 1 || $new_event_option === 2 ) {
				$yes_maybe_count++;
			}
		}

		if( $yes_maybe_count < ceil(count($new_events)) * 1/3 ) {
			url_redirect( $redirect_args, ['error' => 'priority-too-low'] );
		}

	} elseif( $priority === 3 ) { // really want to attend

		// NOTE: we want to have 'yes' for at least one third (rounded up) of the dates
		$yes_count = 0;
		foreach( $new_events as $new_event_option ) {
			if( $new_event_option === 1 ) {
				$yes_count++;
			}
		}

		if( $yes_count < ceil(count($new_events)) * 1/3 ) {
			url_redirect( $redirect_args, ['error' => 'priority-too-low'] );
		}

	} else { // unknown value
		url_redirect( $redirect_args, ['error' => 'priority'] );
	}


}


$people[] = [
	'name' => $name,
	'priority' => $priority,
	'events' => $new_events
];

$data['people'] = $people;


if( ! file_put_contents("data/".$event.".json", json_encode($data)) ) {
	url_redirect( $redirect_args, ['error' => 'save'] );
}

$redirect_args['user'] = get_hash( $name );

url_redirect( $redirect_args, ['success' => true] );
