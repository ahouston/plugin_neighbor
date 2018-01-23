<?php
/*
 ex: set tabstop=4 shiftwidth=4 autoindent:
 +-------------------------------------------------------------------------+
 | Copyright (C) 2006-2017 The Cacti Group                                 |
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
 | Cacti: The Complete RRDTool-based Graphing Solution                     |
 +-------------------------------------------------------------------------+
 | This code is designed, written, and maintained by the Cacti Group. See  |
 | about.php and/or the AUTHORS file for specific developer information.   |
 +-------------------------------------------------------------------------+
 | http://www.cacti.net/                                                   |
 +-------------------------------------------------------------------------+
*/

$guest_account = true;

chdir('../../');
include_once('./include/auth.php');
include_once($config['base_path'] . '/plugins/neighbor/lib/neighbor_functions.php');

/*
include_once($config['base_path'] . '/plugins/neighbor/neighbor_functions.php');
include_once($config['base_path'] . '/plugins/neighbor/setup.php');
include_once($config['base_path'] . '/plugins/neighbor/includes/database.php');
include($config['base_path'] . '/plugins/neighbor/includes/arrays.php');

neighbor_initialize_rusage();

plugin_neighbor_upgrade();

delete_old_thresholds();
*/

set_default_action('summary');

switch(get_request_var('action')) {
	case 'neighbor_map':
		general_header();
		neighbor_tabs();
		display_interface_map();
		bottom_footer();
	case 'summary':
		general_header();
		neighbor_tabs();
		display_interface_map(1);
		bottom_footer();
		break;
	case 'xdp':
		general_header();
		neighbor_tabs();
		xdpNeighbors();
		bottom_footer();
		break;
	case 'maps':
		general_header();
		neighbor_tabs();
		display_interface_map();
		bottom_footer();
		break;
	case 'ajax_hosts':
		get_allowed_ajax_hosts(true, false, 'h.id IN (SELECT host_id FROM plugin_neighbor__xdp)');
		break;
	case 'ajax_hosts_noany':
		get_allowed_ajax_hosts(true, false, 'h.id IN (SELECT host_id FROM plugin_neighbor__xdp)');
		break;
	case 'hoststat':
		general_header();
		neighbor_tabs();
		hosts();
		bottom_footer();
		break;
	default:
		general_header();
		neighbor_tabs();
		display_interface_map();
		bottom_footer();

		break;
}

// Clear the Nav Cache, so that it doesn't know we came from Thold
$_SESSION['sess_nav_level_cache'] = '';

////
// CDP & LLDP Neighbors 
////

function xdpNeighbors() {
	
	/* ================= input validation and session storage ================= */
    $filters = array(
		'rows' => array(
			'filter' => FILTER_VALIDATE_INT,
			'pageset' => true,
			'default' => '-1'
			),
		'page' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '1'
			),
		'filter' => array(
			'filter' => FILTER_CALLBACK,
			'pageset' => true,
			'default' => '',
			'options' => array('options' => 'sanitize_search_string')
			),
		'xdp_type' => array(
			'filter' => FILTER_CALLBACK,
			'pageset' => true,
			'default' => '',
			'options' => array('options' => 'sanitize_search_string')
			),
		'sort_column' => array(
			'filter' => FILTER_CALLBACK,
			'default' => 'hostname',
			'options' => array('options' => 'sanitize_search_string')
			),
		'sort_direction' => array(
			'filter' => FILTER_CALLBACK,
			'default' => 'ASC',
			'options' => array('options' => 'sanitize_search_string')
			),
		'cacti_only' => array(
			'filter' => FILTER_CALLBACK,
			'default' => 'on',
			'options' => array('options' => 'sanitize_search_string')
			),
		'host_id' => array(
			'filter' => FILTER_VALIDATE_INT,
			'pageset' => true,
			'default' => '-1'
			)
	);
	
	validate_store_request_vars($filters, 'sess_thold');
	/* ================= input validation ================= */

	/* if the number of rows is -1, set it to the default */
	if (get_request_var('rows') == -1) {
		$rows = read_config_option('num_rows_table');
	} else {
		$rows = get_request_var('rows');
	}
	
	
	$total_rows = 0;
	$page = get_request_var('page') ? get_request_var('page') : 1;
	
	$startRow = ($page-1) * $rows + 1;
	$endRow = (($page-1) * $rows) + $rows;
	$hostId 		= get_request_var('host_id');
	$xdpType 		= get_request_var('xdp_type');
	$sortColumn 	= get_request_var('sort_column');
	$sortDirection 	= get_request_var('sort_direction');
	$filterVal 		= get_request_var('filter');
	$cactiOnly 		= get_request_var('cacti_only');

	$xdpNeighbors = getXdpNeighbors($total_rows,$startRow, $rows,$xdpType,$hostId,$filterVal,$sortColumn,$sortDirection,$cactiOnly);
	
	html_start_box(__('CDP/LLDP Neighbors', 'neighbor'), '100%', '', '3', 'center', '');
	neighborFilter('xdp');
	html_end_box();
	
	//pre_print_r($xdpNeighbors);
	//print "Rows: $rows<br>";
	//print "Total Rows: $total_rows<br>";
	//print "Start: $startRow, End: $endRow<br>";
	//print "Page: $page<br>";
	//print "<pre>".print_r($xdpNeighborStats,1)."</pre>";
	
	$nav = html_nav_bar('neighbor.php?action=xdp', MAX_DISPLAY_PAGES, $page, $rows, $total_rows, 9, 'Neighbors', 'page', 'main');
	html_start_box('Neighbor Summary', '100%','' , '8', 'left', '');

	$display_text = array(
		'hostname'       		=> array('display' => __('Hostname', 'neighbor'),				'sort' => '',	'align' => 'left'),
		'type'       			=> array('display' => __('Type', 'neighbor'),					'sort' => '',	'align' => 'left'),
		'interface'     		=> array('display' => __('Interface', 'neighbor'),				'sort' => '',	'align' => 'left'),
		'alias'     			=> array('display' => __('Description', 'neighbor'),			'sort' => '',	'align' => 'left'),
		'neighbor_hostname'		=> array('display' => __('Neighbor Hostname', 'neighbor'),		'sort' => '',  	'align' => 'left'),
		'neighbor_interface'	=> array('display' => __('Neighbor Interface', 'neighbor'),		'sort' => '', 	'align' => 'left'),
		'neighbor_alias'		=> array('display' => __('Neighbor Description', 'neighbor'),	'sort' => '', 	'align' => 'left'),
		'neighbor_platform'		=> array('display' => __('Neighbor Platform', 'neighbor'),		'sort' => '', 	'align' => 'left'),
		'last_seen'				=> array('display' => __('Last Seen', 'neighbor'),				'sort' => '', 	'align' => 'left'));
	
	html_header_sort($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false, 'neighbor.php?action=xdp');
	if ($xdpNeighbors) {
		foreach ($xdpNeighbors as $id => $rec) {
			printf("<tr><td>%s</td><td> %s </td><td> %s </td><td> %s </td><td> %s </td><td> %s </td><td> %s </td><td> %s </td><td> %s </td>\n",
				snipToDots($rec['hostname'],20),
				snipToDots(strtoupper($rec['type']),30),
				snipToDots($rec['interface_name'],30),
				snipToDots($rec['interface_alias'],50),
				snipToDots($rec['neighbor_hostname'],20),
				snipToDots($rec['neighbor_interface_name'],30),
				snipToDots($rec['neighbor_interface_alias'],50),
				snipToDots($rec['neighbor_platform'],35),
				snipToDots($rec['last_seen'],20)
			);
			form_end_row();
		}
	}
	html_end_box();
	print $nav;
	
}

// Summary Action

function neighbor_summary() {
	
	$total_rows = 0;
	$rows = 1;
	
	$xdpNeighborStats = getXdpNeighborStats($total_rows);
	//print "<pre>".print_r($xdpNeighborStats,1)."</pre>";
	
	/* if the number of rows is -1, set it to the default */
	if (get_request_var('rows') == -1) {
		$rows = read_config_option('num_rows_table');
	} else {
		$rows = get_request_var('rows');
	}
	
	print 
	html_start_box('Neighbor Summary', '50%','' , '4', 'left', '');

	$display_text = array(
		'method'         => array('display' => __('Method', 'neighbor'),     	'sort' => '',	'align' => 'left'),
		'hosts'          => array('display' => __('Hosts', 'neighbor'),        	'sort' => '',  	'align' => 'center'),
		'interfaces'     => array('display' => __('Interfaces', 'neighbor'),    'sort' => '', 	'align' => 'center'),
		'last_polled'    => array('display' => __('Last Polled', 'neighbor'),   'sort' => '',  	'align' => 'center'));
	
	
	html_header_sort($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false, 'neighbor.php?action=summary');
	if ($xdpNeighborStats) {
		printf("<tr><td><a href='?action=xdp'>CPD/LLDP</a></td><td align='center'> %s </td><td align='center'> %s </td><td align='center'> %s </td>",$xdpNeighborStats['hosts'],$xdpNeighborStats['interfaces'],$xdpNeighborStats['last_polled']);
		form_end_row();
	}
	html_end_box();
	print $nav;
	
}




