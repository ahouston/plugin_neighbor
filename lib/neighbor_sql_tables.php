<?php

function neighbor_setup_table () {

	global $config, $database_default;
	include_once($config['library_path'] . '/database.php');
	add_fields_host();

	return;

	// CDP and LLDP Neighbors table
	// Table: plugin_neighbor__xdp
    db_execute("
        CREATE TABLE IF NOT EXISTS `plugin_neighbor__xdp` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `type` enum('cdp','lldp') NOT NULL,
            `host_id` int(11) NOT NULL,
            `host_ip` varchar(64) NOT NULL COMMENT 'Host IP address',
            `hostname` varchar(64) NOT NULL COMMENT 'Device Name from host',
            `snmp_id` int(11) NOT NULL,
            `interface_name` varchar(32) NOT NULL,
            `interface_alias` varchar(64) DEFAULT NULL,
            `interface_speed` int(11) DEFAULT NULL,
            `interface_status` varchar(16) DEFAULT NULL,
            `interface_ip` varchar(45) DEFAULT NULL,
            `interface_hwaddr` char(16) DEFAULT NULL,
            `neighbor_host_id` int(11) NOT NULL,
            `neighbor_hostname` varchar(64) NOT NULL,
            `neighbor_snmp_id` int(11) NOT NULL,
            `neighbor_interface_name` varchar(32) NOT NULL,
            `neighbor_interface_alias` varchar(64) DEFAULT NULL,
            `neighbor_interface_speed` int(11) DEFAULT NULL,
            `neighbor_interface_status` varchar(16) DEFAULT NULL,
            `neighbor_interface_ip` varchar(45) DEFAULT NULL,
            `neighbor_interface_hwaddr` char(16) DEFAULT NULL,
            `neighbor_platform` varchar(128) NOT NULL,
            `neighbor_software` varchar(128) NOT NULL,
            `neighbor_duplex` enum('Full','Half') NOT NULL,    
            `neighbor_last_changed` datetime NOT NULL,
			`last_seen` datetime NOT NULL,
            `neighbor_hash` char(32) NOT NULL,
            `record_hash` char(32) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `entry_hash` (`record_hash`),
            KEY `host_id` (`host_id`),
            KEY `type` (`type`),
            KEY `neighbor_host_id` (`neighbor_host_id`),
            KEY `snmp_id` (`snmp_id`),
            KEY `interface` (`interface_name`),
            KEY `neighbor_snmp_id` (`neighbor_snmp_id`),
            KEY `neighbor_interface` (`neighbor_interface_name`),
            KEY `neighbor_interface_2` (`neighbor_interface_name`),
            KEY `neighbor_hostname` (`neighbor_hostname`),
            KEY `neighbor_last_changed` (`neighbor_last_changed`),
			KEY `last_seen` (`last_seen`),
            KEY `neighbor_duplex` (`neighbor_duplex`),
            KEY `neighbor_hash` (`neighbor_hash`) USING BTREE
        ) AUTO_INCREMENT=45446 DEFAULT CHARSET=utf8mb4 
        ");

    // Table: plugin_neighbor__processes
    db_execute("CREATE TABLE IF NOT EXISTS `plugin_neighbor_processes` (
                `pid` int(10) unsigned NOT NULL,
                `taskid` int(10) unsigned NOT NULL,
                `started` timestamp NOT NULL default CURRENT_TIMESTAMP,
                PRIMARY KEY  (`pid`))
                ENGINE=MEMORY
                COMMENT='Running collector processes';");

    // Table: plugin_neighbor__ipv4_cache
    db_execute("CREATE TABLE IF NOT EXISTS `plugin_neighbor__ipv4_cache` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `host_id` int(11) NOT NULL,
                `hostname` varchar(64) NOT NULL,
                `snmp_id` int(11) NOT NULL,
                `ip_address` char(16) NOT NULL,
                `ip_netmask` char(16) NOT NULL,
                `vrf` varchar(64) NOT NULL,
                `last_seen` datetime NOT NULL COMMENT 'When did we last see this',
                PRIMARY KEY (`id`),
                UNIQUE KEY `host_id_2` (`host_id`,`ip_address`,`vrf`),
                KEY `snmp_id` (`snmp_id`),
                KEY `host_id` (`host_id`),
                KEY `ip_address` (`ip_address`),
                KEY `vrf` (`vrf`),
                KEY `last_seen` (`last_seen`)
              ) DEFAULT CHARSET=utf8mb4
    ");
    
    //Table: plugin_neighbor__ipv4
    db_execute("CREATE TABLE IF NOT EXISTS `plugin_neighbor__ipv4` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `vrf` varchar(64) NOT NULL,
                `host_id` int(11) NOT NULL,
                `hostname` varchar(64) NOT NULL COMMENT 'Device Name from host',
                `snmp_id` int(11) NOT NULL,
                `interface_name` varchar(32) NOT NULL,
                `interface_alias` varchar(64) DEFAULT NULL,
                `interface_ip` char(16) DEFAULT NULL,
                `interface_netmask` char(16) NOT NULL,
                `interface_hwaddr` char(16) DEFAULT NULL,
                `neighbor_host_id` int(11) NOT NULL,
                `neighbor_hostname` varchar(64) NOT NULL,
                `neighbor_snmp_id` int(11) NOT NULL,
                `neighbor_interface_name` varchar(32) NOT NULL,
                `neighbor_interface_alias` varchar(64) DEFAULT NULL,
                `neighbor_interface_ip` char(16) DEFAULT NULL,
                `neighbor_interface_netmask` char(16) NOT NULL,
                `neighbor_interface_hwaddr` char(16) DEFAULT NULL,
                `neighbor_hash` char(32) NOT NULL,
                `record_hash` char(32) NOT NULL,
                `last_seen` datetime NOT NULL,
               PRIMARY KEY (`id`),
               UNIQUE KEY `entry_hash` (`record_hash`),
               KEY `host_id` (`host_id`),
               KEY `neighbor_host_id` (`neighbor_host_id`),
               KEY `snmp_id` (`snmp_id`),
               KEY `interface` (`interface_name`),
               KEY `neighbor_snmp_id` (`neighbor_snmp_id`),
               KEY `neighbor_interface` (`neighbor_interface_name`),
               KEY `neighbor_interface_2` (`neighbor_interface_name`),
               KEY `neighbor_hostname` (`neighbor_hostname`),
               KEY `neighbor_hash` (`neighbor_hash`) USING BTREE,
               KEY `vrf` (`vrf`)
              ) DEFAULT CHARSET=utf8mb4
    ");

    	//Table: plugin_neighbor__graph_rules
    
	db_execute("CREATE TABLE `plugin_neighbor__graph_rules` (
		`id` mediumint(8) UNSIGNED NOT NULL,
		`name` varchar(255) NOT NULL DEFAULT '',
		`snmp_query_id` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`graph_type_id` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`enabled` char(2) DEFAULT ''
	      ) DEFAULT CHARSET=utf8mb4 COMMENT='Automation Graph Rules';
	");
    
	//Table: plugin_neighbor__graph_rules

	db_execute("CREATE TABLE `plugin_neighbor__graph_rule_items` (
		`id` mediumint(8) UNSIGNED NOT NULL,
		`rule_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
		`sequence` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`operation` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`field` varchar(255) NOT NULL DEFAULT '',
		`operator` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`pattern` varchar(255) NOT NULL DEFAULT ''
	      )  COMMENT='Automation Graph Rule Items';
	");
    
	// Table: plugin_neighbor__match_rule_items
	
	db_execute("CREATE TABLE `plugin_neighbor__match_rule_items` (
		`id` mediumint(8) UNSIGNED NOT NULL,
		`rule_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
		`rule_type` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`sequence` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`operation` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`field` varchar(255) NOT NULL DEFAULT '',
		`operator` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`pattern` varchar(255) NOT NULL DEFAULT ''
	      )  COMMENT='Automation Match Rule Items';
	");
	
	// Table: plugin_neighbor__rules
	
	db_execute("CREATE TABLE `plugin_neighbor__rules` (
		`id` mediumint(8) UNSIGNED NOT NULL,
		`name` varchar(255) NOT NULL DEFAULT '',
		`description` varchar(64) DEFAULT NULL,
		`enabled` char(2) DEFAULT ''
	      )  COMMENT='Automation Graph Rules';
	");
	
	// Table: plugin_neighbor__tree_rules
	
	db_execute("CREATE TABLE `plugin_neighbor__tree_rules` (
		`id` mediumint(8) UNSIGNED NOT NULL,
		`name` varchar(255) NOT NULL DEFAULT '',
		`tree_id` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`tree_item_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
		`leaf_type` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`host_grouping_type` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`enabled` char(2) DEFAULT ''
	      )  COMMENT='Automation Tree Rules';
	");
	
	// Table: plugin_neighbor__tree_rule_items
	
	db_execute("CREATE TABLE `plugin_neighbor__tree_rule_items` (
		`id` mediumint(8) UNSIGNED NOT NULL,
		`rule_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
		`sequence` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`field` varchar(255) NOT NULL DEFAULT '',
		`sort_type` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
		`propagate_changes` char(2) DEFAULT '',
		`search_pattern` varchar(255) NOT NULL DEFAULT '',
		`replace_pattern` varchar(255) NOT NULL DEFAULT ''
	      )  COMMENT='Automation Tree Rule Items';
	");
	
	$fields = array(
                        'neighbor_discover_enable',
                        'neighbor_discover_cdp',
                        'neighbor_discover_lldp',
                        'neighbor_discover_ip',
                        'neighbor_discover_switching',
                        'neighbor_discover_ifalias',
                        'neighbor_discover_ospf',
                        'neighbor_discover_bgp',
                        'neighbor_discover_isis',
       );
	$last = 'disabled';
	foreach ($fields as $field) {
		api_plugin_db_add_column ('neighbor', 'host', array('name' => $field, 'type' => 'char(3)', 'NULL' => false, 'default' => 'on', 'after' => $last));
		$last = $field;
	}

}

function add_fields_host() {

	$fields = array(
                        'neighbor_discover_enable',
                        'neighbor_discover_cdp',
                        'neighbor_discover_lldp',
                        'neighbor_discover_ip',
                        'neighbor_discover_switching',
                        'neighbor_discover_ifalias',
                        'neighbor_discover_ospf',
                        'neighbor_discover_bgp',
                        'neighbor_discover_isis',
       );
        $last = 'disabled';
        foreach ($fields as $field) {
                api_plugin_db_add_column ('neighbor', 'host', array('name' => $field, 'type' => 'char(3)', 'NULL' => false, 'default' => 'on', 'after' => $last));
                $last = $field;
        }
}





?>
