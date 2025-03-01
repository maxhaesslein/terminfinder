<?php

if( ! defined('TERMINFINDER') ) exit;

$version = '0.3';


$event = $_REQUEST['event'] ?? false;


$data = [];

if( $event ) {
	$event = sanitize_title($event);

	if( file_exists('data/'.$event.'.json') ) {
		$data_blob = file_get_contents('data/'.$event.'.json');

		if( $data_blob ) {
			$data = json_decode(trim($data_blob), true) ?? [];
		}

	}

}


$title = $data['title'] ?? false;
$description = $data['description'] ?? false;
$schedule = $data['schedule'] ?? [];
$people = $data['people'] ?? [];


foreach( $people as $person ) {
	$events = $person['events'] ?? [];

	foreach( $events as $id => $option ) {
		if( $option === 0 ) { // no
			if( ! isset($schedule[$id]['no']) ) $schedule[$id]['no'] = 0;
			$schedule[$id]['no']++;
		} elseif( $option === 1 ) { // yes
			if( ! isset($schedule[$id]['yes']) ) $schedule[$id]['yes'] = 0;
			$schedule[$id]['yes']++;
		} elseif( $option === 2 ) { // maybe
			if( ! isset($schedule[$id]['maybe']) ) $schedule[$id]['maybe'] = 0;
			$schedule[$id]['maybe']++;
		}
	}
}


$best_matches = [];

$max_yes = 0;
$max_no = 0;
$max_yes_maybe = 0;
foreach( $schedule as $id => $ev ) {

	$yes = $ev['yes'] ?? 0;
	$no = $ev['no'] ?? 0;
	$maybe = $ev['maybe'] ?? 0;

	if( $yes > $max_yes ) $max_yes = $yes;
	if( $no > $max_no ) $max_no = $no;

	if( $yes + $maybe > $max_yes_maybe ) $max_yes_maybe = $yes + $maybe;

	$best_matches[] = [
		'points' => $yes*3 + $maybe*2,
		'id' => $id
	];

}


$winners = [];

if( count($best_matches) ) {
	usort( $best_matches, function($a, $b){
		return $b['points'] - $a['points'];
	});

	$c_points = $best_matches[0]['points'];
	$place = 1;
	foreach( $best_matches as $best_match ) {
		if( $best_match['points'] < $c_points) {
			$place++;
			$c_points = $best_match['points'];
		}
		if( $place > 3 ) break;

		if( ! isset($winners[$place]) ) $winners[$place] = [];

		$winners[$place][] = $best_match['id'];

	}
}


$people_count = count($people);


$user_hash = false;
$user_data = false;
if( ! empty($_REQUEST['user']) ) {
	$user_hash = base64_decode(str_replace(['-', '_'], ['+', '/'], $_REQUEST['user']));

	for( $i = 0; $i < count($people); $i++ ) {

		$person = $people[$i];

		if( ! password_verify($person['name'], $user_hash) ) continue;

		$user_data = $person;

		array_splice($people, $i, 1);
		break;

	}
}



$debug = false;
if( isset($_SERVER['LOCAL_DEV']) ) {
	$debug = true;
}

if( $debug ) $version .= '.'.time(); // cache buster for dev
