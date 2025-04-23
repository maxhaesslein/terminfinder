<?php

if( ! defined('TERMINFINDER') ) exit;


$options_default = [
	'language' => 'en', // can be 'en' or 'de'
	'default_priority' => 1, // 1 = lowest, 2 = middle, 3 = highest
];


if( file_exists('custom/config.php') ) {
	$options = include( 'custom/config.php' );
	$options = array_merge( $options_default, $options );
}
