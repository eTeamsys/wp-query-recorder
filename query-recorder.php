<?php
/*
Plugin Name: Query Recorder
Plugin URI: http://deliciousbrains.com
Description: Record SQL queries to a text file to run later
Author: Delicious Brains
Version: 1.0
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

$GLOBALS['query_recorder_version'] = '1.0';

function query_recorder_init() {
	require_once 'class/query-recorder.php';

	global $query_recorder;
	$query_recorder = new Query_Recorder( __FILE__ );
}
add_action( 'init', 'query_recorder_init', 5 );
