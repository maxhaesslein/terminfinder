<?php

if( ! defined('TERMINFINDER') ) exit;

$version = '0.1';


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


// DEBUG
$data = [
	'title' => 'Shadowdark - Lost Citadel of the Scarlet Minotaur',
	'description' => 'Lorem Ipsum dolor sit amet',
	'schedule' => [
		'2702_14001800' => [
			'name' => '27.02., 14:00-18:00'
		],
		'2702_18002200' => [
			'name' => '27.02., 18:00-22:00'
		],
		'2802_18002200' => [
			'name' => '28.02., 18:00-22:00'
		]
	],
	'people' => [
		[
			'name' => 'Max',
			'events' => [
				'2702_14001800' => 1,
				'2702_18002200' => 1,
				'2802_18002200' => 1,
			]
		],
		[
			'name' => 'Tester',
			'events' => [
				'2702_14001800' => 2,
				'2702_18002200' => 2,
				'2802_18002200' => 0,
			]
		],
		[
			'name' => 'Tester 2',
			'events' => [
				'2702_14001800' => 0,
				'2702_18002200' => 1,
				'2802_18002200' => 2,
			]
		]
	]
];
// DEBUG end


$title = $data['title'] ?? false;
$description = $data['description'] ?? false;
$schedule = $data['schedule'] ?? [];
$people = $data['people'] ?? [];


foreach( $people as $person ) {
	$events = $person['events'] ?? [];

	foreach( $events as $id => $option ) {
		if( $option === 0 ) {
			if( ! isset($schedule[$id]['no']) ) $schedule[$id]['no'] = 0;
			$schedule[$id]['no']++;
		} elseif( $option === 1 ) {
			if( ! isset($schedule[$id]['yes']) ) $schedule[$id]['yes'] = 0;
			$schedule[$id]['yes']++;
		} elseif( $option === 2 ) {
			if( ! isset($schedule[$id]['maybe']) ) $schedule[$id]['maybe'] = 0;
			$schedule[$id]['maybe']++;
		}
	}
}


$max_yes = 0;
$max_no = 0;
$max_yes_maybe = 0;
foreach( $schedule as $id => $event ) {

	$yes = $event['yes'] ?? 0;
	$no = $event['no'] ?? 0;
	$maybe = $event['maybe'] ?? 0;

	if( $yes > $max_yes ) $max_yes = $yes;
	if( $no > $max_no ) $max_no = $no;

	if( $yes + $maybe > $max_yes_maybe ) $max_yes_maybe = $yes + $maybe;
}


$people_count = count($people);


$debug = false;
if( isset($_SERVER['LOCAL_DEV']) ) {
	$debug = true;
}

if( $debug ) $version .= '.'.time(); // cache buster for dev
