// Called by neighbor.php

var mapList = [];
var mapToolbar;
var selectBox;
var rule_id;
var	user_id;

// Get the list of maps from AJAX
var ruleDropdown = function() {
	
	$.ajax({
			method: "GET",
			url: "ajax.php?action=ajax_map_list&format=jsonp",
			dataType: "jsonp",
			// Work with the response
			success: function( response ) {
				console.log(response);
				obj = typeof(response.Response[0]) === 'undefined' ? [] : response.Response[0];
				var mapList = obj;
				selectBox.option('items',mapList);
			}
	});
}


$(document).ready(function() {

	rule_id = $("#rule_id").val();
	user_id = $("#user_id").val();

	var tabs = [
		{     
		    id: 0,
		    text: "Maps", 
		    icon: "globe", 
		    content: "neighbor_map"
		},
		{ 
		    id: 1,
		    text: "Interface", 
		    icon: "fa fa-link", 
		    content: "neighbor_interface" 
		},
		{ 
		    id: 2,
		    text: "Routing", 
		    icon: "fa fa-cloud", 
		    content: "neighbor_routing" 
		},
		{ 
		    id: 3,
		    text: "Summary", 
		    icon: "fa fa-list", 
		    content: "neighbor_summary" 
		},			
	];
	
	// Main dxTabs row
	
	$("#neighbor_tabs").dxTabs({
	    items: tabs,
	    width: "99%",
	    onItemClick: function(e) {
		var redirectUrl = 'neighbor.php?action=' + e.itemData.content;
		window.location.href = redirectUrl;
	    }
	});
	
	
	// Map Toolbar
	
	if ($("#neighbor_map_toolbar").length) { 
	
		//	mapList = $.map(obj, function(value, index) { return [value]; });
		console.log("mapList:",mapList);
		console.log("Type of Response:",typeof(mapList));
				
		mapToolbar = $("#neighbor_map_toolbar").dxToolbar({
			width: "99%",
			onInitialized: function() {
				ruleDropdown(user_id,rule_id);
			},
			dataSource: [
				{
					location: 'before',
					widget: 'dxButton',
					options: {
						text: 'Select Map',
						hoverStateEnabled: false,
					}
				},
				{
					location: 'before',
					widget: 'dxSelectBox',
					options: {
						items: [],
						displayExpr: "name",
						valueExpr: "id",
						itemTemplate: function(data) {
							var icon = data.neighbor_type == 'interface' ? 'fa fa-link' : 'fa fa-cloud';
							return "<div class='custom-item'><span class='"+icon+"' style='padding-right: 5px'></span>"+ data.name +"</div>";
						},
						onInitialized: function(e) {                 
							selectBox = e.component; 				// Save the component to access later
						}
					}
				},
				{
					location: 'before',
					widget: 'dxTextBox',
					options: {
						placeholder: "Filter the hosts",
						onChange: function(e) { console.log("E is:",e); filterHosts(e);}
					}
					
				},
				{
					locateInMenu: 'always',
					text: 'Save',
					onClick: function() {
						storeCoords();
					}
				},
				{
					locateInMenu: 'always',
					text: 'Reset',
					onClick: function() {
						 var result = DevExpress.ui.dialog.confirm("Are you sure?", "Reset map to default");
							result.done(function (dialogResult) {
							if (dialogResult) {
								resetMap();
							}
							else {
								DevExpress.ui.notify("Reset cancelled","warning",3000);
							}
						});
						
					}
				},
				{
					locateInMenu: 'always',
					text: 'Seed',
					onClick: function() {
						var seed = network.getSeed();
						DevExpress.ui.notify("Seed is: " + seed);
					}
				}
			]
				
		}).dxToolbar("instance");

	
	}
	
	
});
