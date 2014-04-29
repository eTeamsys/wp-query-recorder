<?php
/*
Plugin Name: Query Recorder
Plugin URI: http://deliciousbrains.com
Description: Record SQL queries to a text file to run later
Author: Delicious Brains
Version: 0.1
Author URI: http://deliciousbrains.com
*/

// Copyright (c) 2013 Delicious Brains. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************


function dbrains_record_query( $sql ) {
	if ( !preg_match( '@^(INSERT|UPDATE|DELETE|DROP|CREATE)@i', $sql ) ) {
		return $sql;
	}

	$ignore_strings = array( 
		"_transient",
		"`option_name` = 'cron'",
		"post_type=deprecated_log",
		"SET `comment_count`",
		"`meta_key` = '_edit_lock'",
		"_yoast_wpseo_linkdex"
	);
	foreach ( $ignore_strings as $string ) {
		if ( false !== strpos( $sql, $string ) ) {
			return $sql;
		}
	}

	$upload_dir = wp_upload_dir();
	file_put_contents( $upload_dir['basedir'] . '/recorded-queries.txt', $sql . "\n", FILE_APPEND );

	return $sql;
}

// Set priority high to make sure it's the last filter to run
add_filter( 'query', 'dbrains_record_query', 9999 );
