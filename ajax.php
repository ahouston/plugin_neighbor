<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2004-2017 The Cacti Group                                 |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 2          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
 | Cacti: The Complete RRDtool-based Graphing Solution                     |
 +-------------------------------------------------------------------------+
 | This code is designed, written, and maintained by the Cacti Group. See  |
 | about.php and/or the AUTHORS file for specific developer information.   |
 +-------------------------------------------------------------------------+
 | http://www.cacti.net/                                                   |
 +-------------------------------------------------------------------------+
*/

chdir('../../');

include_once('include/auth.php');
include_once('include/global.php');
include_once('plugins/neighbor/lib/neighbor_functions.php');

error_log("ACTION:".get_request_var('action'));
switch (get_request_var('action')) {
	case 'ajax_interface_map':
		error_log("Calling ajax_interface_map()...");
		header('Content-Type: application/json');
		ajax_interface_nodes();
		break;
	case 'ajax_map_save_options':
		ajax_map_save_options();
		break;
	case 'ajax_map_reset_options':
		ajax_map_reset_options();
		break;
	case 'ajax_map_list':
		ajax_map_list();
		break;
	default:
		header('Content-Type: application/json');
		break;
}


function ajax_map_list($format = 'jsonp',$ajax = true) {
	
	$format = $format ? $format  : (isset_request_var('format') ? get_request_var('format') : '');
	$query_callback = isset($_GET['callback']) ? $_GET['callback'] : "Callback";
	$results = db_fetch_assoc('SELECT * FROM plugin_neighbor__rules order by neighbor_type, name');
	$json = json_encode($results);
	$jsonp = sprintf("%s({\"Response\":[%s]})", $query_callback,json_encode($results,JSON_PRETTY_PRINT));
	
	if ($ajax) 	{
		header('Content-Type: application/json');
		print $format == 'jsonp' ? $jsonp : $json;
	}
	else {
		return($json);
	}
}

// Remove saved map options from DB
function ajax_map_reset_options($format = 'jsonp',$ajax = true) {
	
	$format = $format ? $format  : (isset_request_var('format') ? get_request_var('format') : '');
	$query_callback = isset($_GET['callback']) ? $_GET['callback'] : "Callback";
	
	$user_id = isset_request_var('user_id') ? get_request_var('user_id') : false;
	$rule_id = isset_request_var('rule_id') ? get_request_var('rule_id') : false;
	error_log(print_r($_REQUEST,true));
	$message = "";
	if ($user_id && $rule_id) { 
		db_execute_prepared("DELETE from plugin_neighbor__user_map where user_id=? AND rule_id=?", array($user_id,$rule_id));
		$message = sprintf("%d nodes reset.",db_affected_rows());
	}
	
	$results = array('message' => $message);
	$json  = json_encode($results);
	$jsonp = sprintf("%s({\"Response\":[%s]})", $query_callback,json_encode($results,JSON_PRETTY_PRINT));
	
	if ($ajax) 	{
		header('Content-Type: application/json');
		print $format == 'jsonp' ? $jsonp : $json;
	}
	else {
		return($json);
	}	
	
}


// Save the map positions to the DB
function ajax_map_save_options($format = 'jsonp',$ajax = true) {
	
	$format = $format ? $format  : (isset_request_var('format') ? get_request_var('format') : '');
	$query_callback = isset($_GET['callback']) ? $_GET['callback'] : "Callback";
	
	$user_id = isset_request_var('user_id') ? get_request_var('user_id') : false;
	$rule_id = isset_request_var('rule_id') ? get_request_var('rule_id') : false;
	
	$message = "";
	error_log("ajax_map_save_options() is saving for user: $user_id, rule: $rule_id");
	
	if ($user_id && $rule_id) { 
	
		error_log("Request".print_r($_REQUEST,true));
	
		$mapItems 	= isset_request_var('items') 	? get_request_var('items') 		: [];
		$seed 		= isset_request_var('seed')		? get_request_var('seed') 		: 0;
		$canvas_x 	= isset_request_var('canvas_x') ? get_request_var('canvas_x') 	: 1280;
		$canvas_y 	= isset_request_var('canvas_y') ? get_request_var('canvas_y') 	: 1080;
		$mapOptions = isset_request_var('options') 	? get_request_var('options') 	: [];

		$nodes = json_decode($mapItems,true);
		$projected = project_nodes($nodes, $canvas_x,$canvas_y, 0, false, true);		// We need to flip the Y axis around for some reason due to vis.js
		
		error_log("Nodes:".print_r($nodes,true));
		error_log("Projected:".print_r($projected,true));
		
		// First flush any entries for this rule
		db_execute_prepared("DELETE from plugin_neighbor__user_map where user_id=? AND rule_id=?", array($user_id,$rule_id));

		$items = json_decode($mapItems);
		$num_items = sizeof($items);
		
		foreach ($projected as $item) {
		//	error_log("Item:".print_r($item,true));
			db_execute_prepared("INSERT into plugin_neighbor__user_map values (?,?,?,?,?,?,?,?,?)", array(
				'',
				$user_id,
				$rule_id,
				$item['id'],
				$item['x'],
				$item['y'],
				$item['mass'],
				$item['label'],
				$seed
			));
		}
		$message = "$num_items nodes saved.";		
	}
	else {
		$message = 'Error - invalid user or map ID given.';		
	}
	
	$results = array('message' => $message);
	$json  = json_encode($results);
	$jsonp = sprintf("%s({\"Response\":[%s]})", $query_callback,json_encode($results,JSON_PRETTY_PRINT));
	
	if ($ajax) 	{
		header('Content-Type: application/json');
		print $format == 'jsonp' ? $jsonp : $json;
	}
	else {
		return($json);
	}	
	
}


