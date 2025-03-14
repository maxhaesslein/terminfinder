<?php

if( ! defined('TERMINFINDER') ) exit;


if( ! $lang ) {
	$lang = 'en';
}

if( ! file_exists('include/language/'.$lang.'.php') ) {
	$lang = 'en';
}

$locale_code = 'en-US';
if( $lang == 'de' ) $locale_code = 'de-DE';


include_once('include/language/'.$lang.'.php');


function __($string) {

	global $texts;

	if( isset($texts[$string]) ) return $texts[$string];

	return $string;
}

function _e($string) {
	echo __($string);
}
