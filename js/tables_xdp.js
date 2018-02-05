$(document).ready(function() {
	
	var table = $("#table").val(); 	// Which table are we getting
	console.log("Generating dxDataGrid for table:",table);
	
	$.ajax({
		method: "POST",
		url: "ajax.php",
		data: {
			action: 'ajax_neighbors_'+table,
			__csrf_magic: csrfMagicToken,
		},
		dataType: "jsonp",
		success: function( response ) {
			// Build the dxDataGrid with the response
			var data = typeof(response.Response[0]) === 'undefined' ? [] : response.Response[0];
			var dataGrid = $("#xdp_neighbors_holder").dxDataGrid({
				dataSource: data,
				columnsAutoWidth: true,
				width: '99%',
				filterRow: {
				    visible: true,
				    applyFilter: "auto"
				},
				searchPanel: {
				    visible: true,
				    width: 240,
				    placeholder: "Search..."
				},
				headerFilter: {
				    visible: true
				},
				columns: [
					{
						dataField: "hostname",
						caption: "Hostname (A)",
						width: 150
					},
					{
						dataField: "interface_name",
						caption: "Interface (A)",
						width: 120
					},
					{
						dataField: "interface_alias",
						caption: "Description (A)",
					},
					{
						dataField: "interface_speed",
						caption: "Speed",
						width: 80,
					},
					{
						dataField: "neighbor_hostname",
						caption: "Hostname (B)",
						width: 150
					},
					{
						dataField: "neighbor_interface_name",
						caption: "Interface (B)",
						width: 120
					},
					{
						dataField: "neighbor_interface_alias",
						caption: "Description (B)",
					},
					{
						dataField: "neighbor_platform",
						caption: "Neighbor Platform",
						width: 150
					},
					{
						dataField: "last_seen",
						caption: "Last Seen",
						dataType: "datetime",
						width: 180,
						format: "dd/MM/yyyy HH:mm:ss"
					},
				],
				onToolbarPreparing: function(e) {
					var dataGrid = e.component;
			
					e.toolbarOptions.items.unshift(
					{
						location: "before",
						widget: 'dxSelectBox',
						options: {
							width: 250,
							placeholder: 'Select Type...',
							items: [
								{ name: 'CDP/LLDP', value: 'xdp', icon:  'ion-link'},
								{ name: 'IP Subnet', value: 'ipv4', icon: 'ion-code-working'},
								{ name: 'Interface Descriptions', value: 'ifalias', icon: 'ion-ios-color-wand-outline'},
							],
							displayExpr: "name",
							valueExpr: "value",
							itemTemplate: function(data) {
								return "<div class='custom-item'><span class='"+ data.icon +"' style='padding-right: 5px'></span>"+ data.name +"</div>";
							},
							onValueChanged: function(e){
								var value = e.value;
								window.location.replace("neighbor.php?action=neighbor_interface&neighbor_type="+value);	
							},
							onInitialized: function(e) {                 
								selectBox = e.component; 				// Save the component to access later
							}
						}
					});
				}
			}).dxDataGrid("instance");
			
		}
	});

	
});