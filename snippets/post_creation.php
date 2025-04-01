<?php

if( ! defined('TERMINFINDER') ) exit;


$redirect_args['name'] = $name;


$title = $_POST['event_title'] ?? false;
if( ! $title ) {
	url_redirect( $redirect_args, ['error' => 'title'] );
}

$redirect_args['title'] = $title;

$id = sanitize_title($title);
if( ! $id ) {
	url_redirect( $redirect_args, ['error' => 'id'] );
}

$redirect_args['id'] = $id;

$description = $_POST['event_description'] ?? '';

$redirect_args['description'] = $description;

$priority_select_enabled = $_POST['priority-select-enabled'] ?? false;
if( $priority_select_enabled === '1' ) {
	$priority_select_enabled = true;
} else {
	$priority_select_enabled = false;
}

$redirect_args['priority_select_enabled'] = $priority_select_enabled;

$slot_names = $_POST['event_slot_name'] ?? [];
if( ! is_array($slot_names) || ! count($slot_names) ) {
	url_redirect($redirect_args, ['error' => 'event-names'] );
}

$redirect_args['slot_names'] = array_slice($slot_names, 1); // remove first element, because this is the new-event-template

$slot_selections = $_POST['event_slot_selection'] ?? [];
if( ! is_array($slot_selections) || ! count($slot_selections) ) {
	url_redirect($redirect_args, ['error' => 'event-selections'] );
}

$redirect_args['slot_selections'] = array_slice($slot_selections, 1); // remove first element, because this is the new-event-template

if( count($slot_names) != count($slot_selections) ) {
	url_redirect($redirect_args, ['error' => 'event-count']);
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
	url_redirect( $redirect_args, ['error' => 'save'] );
}


$redirect_args['user'] = get_hash($name);

url_redirect( $redirect_args, ['success' => true] );
