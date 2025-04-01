<?php

if( ! defined('TERMINFINDER') ) exit;

$version = '1.1.0-dev.3';



$debug = false;
if( isset($_SERVER['LOCAL_DEV']) ) {
	$debug = true;
}

if( $debug ) $version .= '.'.time(); // cache buster for dev


define( 'DEFAULT_PRIORITY', 2 );
define( 'VERSION', $version );


$lang = $options['language'] ?? 'en';

$event = $_REQUEST['event'] ?? false;


// automatically create data folder, if it is missing
if( ! is_dir('data') ) {
	$oldumask = umask(0); // we need this for permissions of mkdir to be set correctly
	if( mkdir( 'data/', 0775, false ) === false ) {
		echo '<p><strong>Error: could not create <em>data/</em> subfolder.</strong> Please make sure that the folder is writeable.</p>';
		exit;
	}
	umask($oldumask); // we need this after changing permissions with mkdir
}


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
$priority_select_enabled = $data['priority-select-enabled'] ?? false;
$schedule = $data['schedule'] ?? [];
$people = $data['people'] ?? [];


for( $i = 0; $i < count($people); $i++ ) {

	if( ! isset($people[$i]['priority']) ) {
		$people[$i]['priority'] = DEFAULT_PRIORITY;
	}

	$person = $people[$i];

	$events = $person['events'] ?? [];

	foreach( $events as $id => $option ) {

		if( ! isset($schedule[$id]) ) continue;

		if( $option === 0 ) { // no

			if( ! isset($schedule[$id]['no']) ) $schedule[$id]['no'] = 0;
			$schedule[$id]['no']++;

			if( ! isset($schedule[$id]['no_value']) ) $schedule[$id]['no_value'] = 0;
			$schedule[$id]['no_value'] += $person['priority'];

		} elseif( $option === 1 ) { // yes

			if( ! isset($schedule[$id]['yes']) ) $schedule[$id]['yes'] = 0;
			$schedule[$id]['yes']++;

			if( ! isset($schedule[$id]['yes_value']) ) $schedule[$id]['yes_value'] = 0;
			$schedule[$id]['yes_value'] += $person['priority'];

		} elseif( $option === 2 ) { // maybe

			if( ! isset($schedule[$id]['maybe']) ) $schedule[$id]['maybe'] = 0;
			$schedule[$id]['maybe']++;

			if( ! isset($schedule[$id]['maybe_value']) ) $schedule[$id]['maybe_value'] = 0;
			$schedule[$id]['maybe_value'] += $person['priority'];

		}
	}
}


$best_matches = [];

$max_yes = 0;
$max_no = 0;
$max_yes_maybe = 0;
foreach( $schedule as $id => $ev ) {

	$yes = $ev['yes_value'] ?? 0;
	$no = $ev['no_value'] ?? 0;
	$maybe = $ev['maybe_value'] ?? 0;

	if( $yes > $max_yes ) $max_yes = $yes;
	if( $no > $max_no ) $max_no = $no;

	if( $yes + $maybe > $max_yes_maybe ) $max_yes_maybe = $yes + $maybe;

	$best_matches[] = [
		'points' => weightedPoints($yes, $maybe, $no),
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
	$user_hash = trim($_REQUEST['user']);

	for( $i = 0; $i < count($people); $i++ ) {

		$person = $people[$i];

		if( ! hash_verify( $person['name'], $user_hash ) ) continue;

		$user_data = $person;

		array_splice($people, $i, 1);
		break;

	}
}
