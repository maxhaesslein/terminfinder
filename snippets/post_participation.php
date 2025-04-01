<?php

if( ! defined('TERMINFINDER') ) exit;


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


$priority = DEFAULT_PRIORITY;
if( $priority_select_enabled ) {

	$priority = $_POST['priority'] ?? '';
	$priority = (int) trim($priority);


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
			url_redirect($redirect.'&error=priority-to-low');
		}

	} elseif( $priority === 3 ) { // really want to attend

		// NOTE: we want to have 'yes' for at least half (rounded up) of the dates
		$yes_count = 0;
		foreach( $new_events as $new_event_option ) {
			if( $new_event_option === 1 ) {
				$yes_count++;
			}
		}

		if( $yes_count < ceil(count($new_events)) / 2 ) {
			url_redirect($redirect.'&error=priority-to-low');
		}

	} else { // unknown value
		url_redirect($redirect.'&error=priority');
	}


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
