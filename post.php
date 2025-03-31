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


$action = $_POST['action'] ?? false;

$name = $_POST['name'] ?? '';
$name = trim($name);
if( empty($name) ) {
	url_redirect($redirect.'&error=name');
}

$priority = $_POST['priority'] ?? '';
$priority = (int) trim($priority);


if( $action === 'new' ) {
	// creation

	$title = $_POST['event_title'] ?? false;
	if( ! $title ) {
		url_redirect($redirect.'&error=title');
	}

	$id = sanitize_title($title);
	if( ! $id ) {
		url_redirect($redirect.'&error=id');
	}

	$description = $_POST['event_description'] ?? '';

	$priority_select_enabled = $_POST['priority-select-enabled'] ?? false;
	if( $priority_select_enabled === '1' ) {
		$priority_select_enabled = true;
	} else {
		$priority_select_enabled = false;
	}

	$slot_names = $_POST['event_slot_name'] ?? [];
	$slot_selections = $_POST['event_slot_selection'] ?? [];

	if( ! is_array($slot_names) || ! count($slot_names) ) {
		url_redirect($redirect.'&error=event-names');
	}
	if( ! is_array($slot_selections) || ! count($slot_selections) ) {
		url_redirect($redirect.'&error=event-selections');
	}
	if( count($slot_names) != count($slot_selections) ) {
		url_redirect($redirect.'&error=event-count');
	}

	$schedule = [];
	$person_events = [];

	for( $i = 0; $i < count($slot_names); $i++ ) {

		$slot_name = $slot_names[$i];
		$slot_selection = $slot_selections[$i];

		if( ! $slot_name ) continue;

		$slot_id = 'ev-'.sanitize_title($slot_name);
		if( ! $slot_id ) continue;

		$schedule[$slot_id] = [
			'name' => $slot_name
		];

		$person_events[$slot_id] = (int) $slot_selection;
	}

	$data = [
		'title' => $title,
		'description' => $description,
		'schedule' => $schedule,
		'priority-select-enabled' => $priority_select_enabled,
		'people' => [
			[
				'name' => $name,
				'priority' => 3,
				'events' => $person_events
			]
		]
	];


	if( ! file_put_contents("data/".$event.".json", json_encode($data)) ) {
		url_redirect($redirect.'&error=save');
	}

	$redirect .= '&user='.get_hash($name);

	url_redirect($redirect.'&success');

} else {
	// submission

	if( $priority < 1 || $priority > 3 ) {
		url_redirect($redirect.'&error=priority');
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

}

exit;
