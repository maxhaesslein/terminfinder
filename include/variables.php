<?php

if( ! defined('TERMINFINDER') ) exit;

$version = '0.1';


$event = $_REQUEST['event'] ?? false;


$schedule = [];

if( $event ) {
	$event = sanitize_title($event);

	if( file_exists('data/'.$event.'.json') ) {
		$data = file_get_contents('data/'.$event.'.json');

		if( $data ) {
			$schedule = json_decode(trim($data), true) ?? [];
		}

	}

}


$debug = false;
if( isset($_SERVER['LOCAL_DEV']) ) {
	$debug = true;
}

if( $debug ) $version .= '.'.time(); // cache buster for dev
