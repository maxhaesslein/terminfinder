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


function get_hash( $string ) {
	// NOTE: this is a simple and quick hashing function, and not cryptographically save.

	return hash('sha256', $string);
}


function hash_verify( $string, $hash ) {

	if ( get_hash($string) === $hash ) {
		return true;
	}

	return false;
}


function weightedPoints( $yes, $maybe, $no ) {
	// NOTE: this function also exists in assets/js/participation.js
	return $yes*6 + $maybe*3 - $no;
}
