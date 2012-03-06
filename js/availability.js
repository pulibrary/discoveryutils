// grab availability for json requests service
var holdings_location = $('#holdings');
$.getJSON(holdings_location.attr('data-source'), function(data) { 
	var location_collection = {};
	$.each(data, function(key,val) {
		var location_details = _.find(loc_objects, function(location){ return location.voyagerLocationCode === key}); //query loc_objects for location details
		val.location_detailed_info = location_details; // add location details to script
		location_collection[key] = val;
	});
	//var location_codes = _.keys(location_collection);
	//var location_info = _.filter(loc_objects, function(loc, key) {
	//	return _.include(location_codes, loc.voyagerLocationCode);
	//});
	console.log(_.sortBy(_.keys(location_collection), function(location){ return location; }));
	console.log(location_collection);
	var holdings_template = _.template('\
		<div><%= _.size(location_collection) %> Matching Locations</div>\
		<ul>\
			<% _.chain(location_collection).keys(location_collection).sortBy(function(location){ return location; }).each(function(location) { %>\
				<% var loc = location_collection[location]; %>\
				<% var loc_details = loc.location_detailed_info; %>\
				<% var items = loc.items; %> \
				<li><%= loc_details.libraryDisplay %>|<%= loc_details.voyagerLocationCode %>\
					|<%= loc.totalItems %>|<%= loc.availableItems %>|<%= loc.requestableItems %> \
					<ul>\
						<% _.chain(items).keys(items).each(function(item) { %> \
							<% var item_details = items[item]; %> \
							<li><%= item_details.barcode %>|<%= item_details.requestable %></li> \
						<% }); %> \
					</ul>\
				</li> \
			<% }); %>\
		</ul>');
		var html = [];
		html += holdings_template({location_collection: location_collection});
		$(html).appendTo('#holdings');
});