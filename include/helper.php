<?php

if( ! defined('TERMINFINDER') ) exit;


function url_redirect( $url = false ) {

	if( ! $url ) $url = 'index.php';

	header("Location: ".$url);

	exit;
}


function sanitize_title( $string, $replace = true ) {

	if( $replace ) {
    	$string = strtolower($string);
		$string = preg_replace('/[^a-z0-9\s]/', '', $string);
    	$string = str_replace(' ', '_', $string);
    }

	return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
