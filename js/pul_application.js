// test to add the bootstrap button effect to links 
function pulSetButtonStyles() 
{
	$('.EXLLinkedField').each(function(index){
		$(this).addClass('small');
		$(this).addClass('btn');
		var linked_field_text = $(this).text();
		var label_text = "Browse all items related to ";
		$(this).attr('title', label_text + linked_field_text);
	});
}

// grab the current search value
function pulArticleCheck() {
    var currentSearchValue = $('#search_field').val();
    //alert(currentSearchValue);
    return;
}

function pulBuildLocatorLink(loc_code, pnx_id) {
	var location_base = "http://library.princeton.edu/utils/map?";
	var locator_message = "Find this item on Campus";
	var locator_text = "Where to find it";
	var voyager_id = pnx_id.replace("PRN_VOYAGER", "");
	return "<span class='pulLocator'><a target='_blank' class='pulLocatorLink' href='" + location_base+"loc="+loc_code + "&id=" + voyager_id + "' title='" + locator_message + "'>" + locator_text + "</a></span>";
}

function pulBuildStackMapLink(loc_code, pnx_id) {
	var location_base = "http://library.princeton.edu/utils/map?";
	var locator_message = "Stackmap It";
	var locator_text = "Stackmap It";
	return "<span class='pulLocator'><a target='_blank' class='btn small' href='" + location_base+ "loc=" + loc_code + "&id=" + pnx_id + "' title='" + locator_message + "'>" + locator_text + "</a></span>";
}

function pulLocationSignIn() //FIXME do not believe this is used any longer
{
        //alert(tabURL);
        var currentSignInMessage = "Sign in for request options";
        var shortSignInMessage = "sign in";
        var recallSignInMessage = "Sign in to recall";
        var currentSignInURL = $("ul#exlidUserAreaRibbon li#exlidSignOut a").attr("href");

        var currentSignInMatch = "Sign-in in order to place requests";
        // do locations tab 
        // can to span.PULsignin
        $('div.EXLLocationTableActionsMenu ul li').each(function(index) {
                if($(this).text() == currentSignInMessage) {
                  $(this).replaceWith("<li><a href=\""+currentSignInURL+"\">"+currentSignInMessage+"</a></li>");
                }
        });
        // do request tab
        // long path html body.EXLCurrentLang_en_US div#contentEXL.EXLCustomLayoutContainer div#exlidResultsContainer.EXLCustomLayoutContainer div#resultsTileNoId.EXLCustomLayoutContainer div#resultsListNoId.EXLCustomLayoutTile table#exlidResultsTable.EXLResultsTable tbody tr#exlidResult0.EXLResult td.EXLSummary div#exlidResult0-TabContainer-requestTab.EXLResultTabContainer div#exlidResult0-TabContent.EXLTabContent div#PULRequestOptions ul.requestoptions li span.PULsignind
        $('span.PULsignin').each(function(index) {
                //if($(this).text().match(new RegExp(currentSignInMatch))) {
                  $(this).replaceWith("<span style='display: inline; padding:0px; border:0px;'><a href=\""+currentSignInURL+"\">"+recallSignInMessage+"</a>.</span");
                //}
        });
}

// Do the Mouseovers on the 
function pulRequestTabPopovers()
{
// make sure options display
//$('.EXLRequestTabContent div.popover-well').show();	
	$('.helpover').hide();
	$('.popable').each(function(index){
  	  var pulRequestType = $(this).attr('id').replace(/Select/,"");
	  $(this).mouseover(function() {
	  $('#'+pulRequestType).show();
	  $('#'+pulRequestType).click( function() {
	    $(this).hide();
	  });
	}).mouseleave(function() {
	  $('#'+pulRequestType).hide();
	}); // end mouse-in/mouse-out
     });          
}


function pulMoreLinks()
{
        $('.EXLDetailsLinks ul li.EXLFullDetailsGoogleBookItem a.EXLFullDetailsOutboundLink').filter(function(index) {
                next_sibling_text = $(this).next().children('span.EXLDetailsLinksTitle');
                alert(next_sibling_text.length);

        });
}

//function pulLoadAvailabilityData(pnxId) {
//	jQuery.getJSON('/primo_library/libweb/ADDONS/get_request_data.jsp?pnxId=' + pnxId, function(data){console.log(data);})
//}


//function pulLoadRequest(pnxId) {
//	return pnxId;
//}



function pulSetTabMessages() {
    var pulLocationMessage = "See current locations for this item @ PUL";
    var pulFullRecordMessage = "See details for this item";
    var pulRequestMessage = "See PUL request options for this item";
    var pulTagsMessage = "See tags PUL users have assigned this item"
    var pulOnlineMessage = "View this item online";
    $("ul.EXLResultTabs li.EXLLocationsTab  a").attr('title', pulLocationMessage);
    $("ul.EXLResultTabs li.EXLDetailsTab a").attr('title', pulFullRecordMessage);
    $("ul.EXLResultTabs li.EXLRequestTab a").attr('title', pulRequestMessage);
    $("ul.EXLResultTabs li.EXLReviewsTab a").attr('title', pulTagsMessage);
    $("ul.EXLResultTabs li.EXLViewOnlineTab a").attr('title', pulOnlineMessage);
}

function pulGetRecordDeepLink(pnxId) {
	var permalink_message = "This Item permalink";
	var deep_link_html = "<li class='EXLFullDetailsOtherItem'><span class='EXLDetailsLinksBullet'></span> \ " +
			"<span class='EXLDetailsLinksTitle'><a class='EXLFullDetailsInboundLink' \ " +
			"href='"+ pulGetRecordDeepLinkUrl(pnxId) +"'>"+ permalink_message +"</a></span></li>";
	
	return deep_link_html;
}

function pulGetRecordDeepLinkUrl(pnxId, http_port) {
	var http_port = typeof(http_port) != 'undefined' ? http_port : ':80';
	var deep_link_record_base = "primo_library/libweb/action/dlDisplay.do?institution=PRN&vid=PRINCETON&docId=";
	var current_host_name = window.location.hostname;
	return "http://" + current_host_name + http_port + "/" + deep_link_record_base + pnxId;
}

function pulGetBasicSearchDeepLinkUrl(pnxId, http_port) {
	
}


//Borrow Direct linker
//http://libserv51.princeton.edu/bd.link.isbn/link.to.bd.php?isbn={issn or isbn}
//need to post to server for this to work
//
//"<form action="http://libserv51.princeton.edu/bd.link.isbn/link.to.bd.php" method="post" target="_blank">
//<input type="hidden" name="isbn" value="' + rec_isxn +'"/>"; 
//</form>

function pulBuildBorrowDirectForm(pnxId, bd_class_value) {
	var bd_class_value = typeof(bd_class_value) != 'undefined' ? bd_class_value : 'btn small info';
	//var borrow_direct_base = "http://libserv51.princeton.edu/bd.link.isbn/link.to.bd.php";
	var bd_base_url = "http://libserv51.princeton.edu/bd.link/link.to.bd.php";
	var bd_tool_tip = "Copy unavailable, try Borrow Direct";
	var isbn = EXLTA_isbn(pnxId); // check for multiple isbns or issn? 
    var issn = EXLTA_issn(pnxId);
    var rec_isxn = "";
    //var bd_class_value = "btn small info";
    
    if(isbn) { // Check for valid/invalid ISBN/ISSN
    	var rec_isxn = isbn;
    	//console.log("isbn:"+isbn);
    } 
    if(issn) {
    	var rec_isxn = issn;
    	//console.log("issn:"+issn);
    }
    if (rec_isxn != "") {
    	var bd_search_index = "isbn";
    	var bd_search_value = encodeURI(rec_isxn);
    } else { // do a title search if no isxn available - deactivate until feature available 
    	var bd_search_index = "ti";
    	var title = EXLTA_sortTitle(pnxId); // get the sort title
    	var bd_search_value = encodeURI(title);
    }
    var bd_search_url = bd_base_url + '?' + bd_search_index + '=' + bd_search_value;
    var bd_class = 'class="' + bd_class_value + '"'; // how button should display
    var bd_form = '<div id="bdPnx' + pnxId + '"><a target="_blank"'+bd_class+' href="'+bd_search_url+'" title="'+bd_tool_tip+'">Check Borrow Direct</a></div>'; 
	return bd_form;
     
}

function pulGetSummonLink(query) {
    var summonBaseUrl = "http://library.princeton.edu/utils/search/summon";
    var summonQueryString = "?query=";
    
    return summonBaseUrl+summonQueryString+query;
}

function pulGetSummonAdvancedQueryValues() {
	var summon_query;
	$('input[type="text"]').each(function(index) {
		summon_query += $(this).val();
	});
	
	return summon_query;
}


function pulGetCoin(pnxId) {
	var addData = EXLTA_getAddData(pnxId);
	
	var query_string = "";
	var risType = addData.ristype;
	var rfr_format=addData.format; // check to make sure what are allowed types
	var coin_encoding = "ctx_enc=info%3Aofi%2Fenc%3AUTF-8";
	var coin_version = "ctx_ver=Z39.88-2004";
	var rft_id = "rft_id="+ encodeURIComponent(pulGetRecordDeepLinkUrl(pnxId));
	var rft_val_fmt = "rft_val_fmt=info:ofi/fmt:kev:mtx:"+rfr_format;
	var rfr_id="info:sid/utils.princeton.edu";
	for (var key in addData) {
		query_string += "&rft."+key+"="+encodeURIComponent(addData[key]);
	}
	console.log(query_string)
	return '<span class="Z3988" title="' + coin_version + '&' + coin_encoding + '&' + rft_val_fmt + '&' + rfr_id + '&' + rft_id + query_string +'">&nbsp;</span>';
}





/* on homepage don't show any tabs except for Catalog Plus */
function pulHideOnAdvancedSearchForm() {
	//check URL to see if Mode=Advanced Hide all forms 
	var current_url = $(location).attr('href');
	//select tabs to hide
}

function pulBuildLocatorMapOptions(pnx_id, current_result_number) {
        var ordered_locator_links = pulGetLocatorLinks(pnx_id);
        $('#exlidResult'+current_result_number+'-TabContent .EXLLocationList').each(function(index) {
                var location_message = $.trim($(this).find('.EXLLocationsTitle .EXLLocationsTitleContainer').text());
                var collection_finder = $(this).find('.EXLLocationInfo strong'); // this come back empty after sp 3.1.2
                var location_info = $(this).find('.EXLLocationInfo cite');
                //var online_resource = location_message.match(/(^Online|^ReCAP|^Forrestal Annex|^Fine Annex)/); //this should be sent out to a function 
                //if (!online_resource) {
                        $(collection_finder).replaceWith(ordered_locator_links[index] + "&nbsp;<span class='pulShelfLocation'>"  + collection_finder.text() + "</span>");
                //}
        });
        return;
}


function pulBuildStackMapOptions(pnx_id, current_result_number) {
    var ordered_locator_links = pulGetStackMapLinks(pnx_id);
    var loc_selector = '#exlidResult'+current_result_number+'-TabContent .EXLLocationList'
    if(EXLTA_isFullDisplay()) {
    	loc_selector = '.EXLLocationList';
    } 
    $(loc_selector).each(function(index) {
    	var location_message = $.trim($(this).find('.EXLLocationsTitle .EXLLocationsTitleContainer').text());
        var collection_finder = $(this).find('.EXLLocationInfo strong'); // this come back empty after sp 3.1.2
        var location_info = $(this).find('.EXLLocationInfo cite');
        var online_resource = location_message.match(/(^Online|^ReCAP|^Forrestal Annex|^Fine Annex)/); //this should be sent out to a function 
        if (!online_resource) {
        	$(location_info).append(ordered_locator_links[index]);
        }
    });
    return;
}

function pulBuildFullLocatorMapOptions(pnx_id, current_result_number) {
        var ordered_locator_links = pulGetLocatorLinks(pnx_id);
        $('.EXLLocationList').each(function(index) {
                var location_message = $.trim($(this).text());
                var collection_finder = $(this).find('.EXLLocationInfo strong'); // this come back empty after sp 3.1.2
                //var online_resource = location_message.match(/(^Online|^ReCAP|^Forrestal Annex|^Fine Annex)/);
                //if (!online_resource) {
                        $(collection_finder).replaceWith(ordered_locator_links[index] + "&nbsp;<span class='pulShelfLocation'>"  + collection_finder.text() + "</span>");
                //}
        });
        return;
}

function pulGetLocatorLinks(pnx_id) {
        var location_links = new Array();
       
        sorted_locations = pulGetLocationCodes(pnx_id);
        _.each(sorted_locations, function(location) {
        	var locator_link = pulBuildLocatorLink(location.loc, location.bib);
        	location_links.push(locator_link);
        });

        return location_links;
}

function pulGetStackMapLinks(pnx_id) {
    var location_links = new Array();
   
    sorted_locations = pulGetLocationCodes(pnx_id);
    _.each(sorted_locations, function(location) {
    	var locator_link = pulBuildStackMapLink(location.loc, location.bib);
    	location_links.push(locator_link);
    });

    return location_links;
}

function pulConvertPnxIdToVoyagerId(pnx_id) {
	return pnx_id.replace("PRN_VOYAGER", "");
}

//returns an array containing a list of objects holding location code mappings
function pulGetLocationCodes(pnx_id) {
	var location_mappings = new Array(); 
	var locations = EXLTA_availlibraries(pnx_id);
	if (EXLTA_isDedupRecord(pnx_id)) {
		for(var i=0,l=locations.length; i < l ; i++){
	            var location_matches = locations[i].split(/\$\$Y/);
		    var location_code_source_id = location_matches[1].split(/\$\$O/);
		    var location_mapping = { loc: pulStripLocEnding(location_code_source_id[0]), bib: pulConvertPnxIdToVoyagerId(location_code_source_id[1]) }; //FIXME use pnx_id instead of bib location_code_source_id[1]
		    location_mappings.push(location_mapping);
		}
	} else {
		for(var i=0,l=locations.length; i < l ; i++){
			var location_matches = locations[i].split(/\$\$Y/);
			var location_mapping = { loc: pulStripLocEnding(location_matches[1]), bib: pulConvertPnxIdToVoyagerId(pnx_id) }; //strip voyager prefix
			location_mappings.push(location_mapping);
		}
	}
	var sorted_location_mappings = _.sortBy(location_mappings, function(location) { return location.loc; }) //now these will be sorted 
	return sorted_location_mappings;	
}

function pulStripLocEnding(location_code) {
        var location_matches = location_code.split(/\$\$P/);        
	return location_matches[0];
}
/*
function pulGetDesupLocations(locations) {
	for(var i=0,l=locations.length; i < l ; i++){
		
	}
}
*/

// ADD functions to do linking-in-context for requests
//FIXME Should this be renamed? pulItemUnAvailableTest it actually actually returns true for unavailable PUL voyager item status codes 
function pulItemAvailableTest(status) {
	var item_unavailable_test = $.trim(status).match(/(^Charged|^vol.+Due|^Overdue|.+Renewed|^Renewed|^Recall Request|^Hold Request|^On Hold$|^In Transit On Hold$|^Missing|^Lost|^Claims Returned|^Withdrawn|^At Bindery|^Remote Storage Request)/);
	//console.log(status_message + "test is " + item_unavailable_test);
	if(item_unavailable_test) {
		return true;
	} else {
		return false;
	}
}


function pulItemInProcessOnOrderTest(status) {
	var item_on_order_in_process_test = $.trim(status).match(/(^In Process|^On Order)/);
	if(item_on_order_in_process_test) {
		return true;
	} else {
		return false;
	}
}

// function takes a javacsript object
// build key=value url params
function pulBuildRequestButton(request_item, location_label, location_details, top_level) {
	var location_label = typeof(location_label) != 'undefined' ? location_label : 'Check Options';
	var top_level = typeof(top_level) != 'undefined' ? top_level : false;
	var location_details = location_details;
	if (top_level) {
		var request_message = "Check Availability";
		var request_tooltip = "Check availability for this resource at "+location_label;
	} else {
		var request_message = "Check Options";
		var request_tooltip = "Check request options availabile for this copy";
	}	
	
	var request_base = "http://library.princeton.edu/requests/?";
	var aeon_request_base = "http://libweb5.princeton.edu/AeonBibRequest/Default.aspx?";
	// Handle Aeon Requests
	if(location_details.aeon == "Y") {
		request_base = aeon_request_base;
		request_tooltip = "Request to view in Reading Room";
		request_message = "Reading Room";
	}
	var request_target = "_blank";
	var request_button_class = "btn small info";
	var request_params = [];
	// deal with any params present
	$.each(request_item, function(key, value) {
		// hacks to pass items over to aeon request
		var param = encodeURIComponent(key)+"="+encodeURIComponent(value);
		if(location_details.aeon == "Y" && key == "bib") {
			param = encodeURIComponent("bibid")+"="+encodeURIComponent(value);
		}
		if(location_details.aeon == "Y" && key == "loc") {
			param = encodeURIComponent("location")+"="+encodeURIComponent(value);
		}
		if(location_details.aeon == "Y" && key == "barcode") {
			param = encodeURIComponent("itembarcode")+"="+encodeURIComponent(value);
		}
		if(location_details.aeon == "Y" && key == "call_no") {
			param = encodeURIComponent("call_no")+"="+encodeURIComponent(value);
		}
		// end aeon hacks
		request_params.push(param);
	});
	var query_string = request_params.join("&");
	var request_url = request_base+query_string;
	return '<a class="'+request_button_class+'" target="'+request_target+'" title="'+request_tooltip+'" href="'+request_url+'">'+request_message+'</a>';
}


// accepts a javascript location object 
// 
function pulAlwaysRequestableLocationMessage(location) {
	
}


//function takes a javascript location object 
function pulReadingRoomRequest(location) {
	
}

function pulExtractBarcode(barcode_display_string) {
	return barcode_display_string.replace("Barcode: ", "");
}

function pulItemOnShelfTest(status) {
	var item_available_test = $.trim(status).match(/^Not Charged/);
	if(item_available_test) {
		return true;
	} else {
		return false;
	}
}

// Build location Options on Standard TAb
function pulBuildLocationOptions(pnx_id, current_result_number) {
	var holdings = $('#exlidResult'+current_result_number+'-TabContent .EXLLocationList');
	var holdings_count = $(holdings).length; // try jquery 
	var locations = pulGetLocationCodes(pnx_id);
	var any_available_items = false; // assume no items are available by default
	// iterate through all items to see if Borrow Direct requesting is allowed 
	$('#exlidResult'+current_result_number+'-TabContent .EXLLocationListContainer .EXLSublocation .EXLLocationTable tr .EXLLocationTableColumn3').each(function(index) {
		var status_message = $(this).text(); //Get current status message, i.e. Charged/Not Charged
		if(pulItemOnShelfTest(status_message)) {
			any_available_items = true;
		}
		if(any_available_items) {
			return false;
		}
	});
	
	$(holdings).each(function(index) {
		var location_message = $.trim($(this).find('.EXLLocationsTitle .EXLLocationsTitleContainer').text());
		var online_resource = location_message.match(/^Online/); // test for online resource	
		var holding_status = $(this).find('em.EXLResultStatusAvailable,em.EXLResultStatusNotAvailable,em.EXLResultStatusMaybeAvailable');
		var collection_finder = $(this).find('.EXLLocationInfo strong').text();
		var collection_string = collection_finder.match(/\((\w+)\)/);
                if (collection_string) {
                        raw_code = collection_string[0].toLowerCase();
                        location_code = raw_code.substr(1, raw_code.length - 2);
                        console.log("Shelf loc is" + location_code );
                } else {
	    		var location_code = locations[index].loc;
		}
		status = $(this).find('em.EXLResultStatusAvailable,em.EXLResultStatusNotAvailable,em.EXLResultStatusMaybeAvailable');	
 		var holding_location = $.trim($(this).find('.EXLLocationsTitleContainer').text());
		var item_count = $(this).find('.EXLShowInfo').length;
		var bib_id = locations[index].bib;
		//console.log("item count is" +item_count);
		if(console) {
			console.log(loc_objects);
			console.log(location_code);
		}
		var location_details = _.find(loc_objects, function(loc, key) {
				return loc.voyagerLocationCode == location_code; 
			});
		if(console) {
			console.log(location_details);
		}
		var location_is_requestable = false; //flag to make sure that the location isn't requestable
		if ((location_details.requestable == "Y" && location_details.accessible == "N") || location_details.aeon == "Y" ) {
			var location_is_requestable = true;
		}
	
		var request_buttons = new Array();
		var has_barcode = false;

		var call_no = $(this).find('.EXLLocationInfo cite');
		location_call_number = call_no.text();
		if(item_count > 1) {
			$(this).find(".EXLHideInfo ul li:contains('Barcode')").each( function(index) { //hack to get at barcodes 
					has_barcode = true;
 					var barcode = pulExtractBarcode($.trim($(this).text()));
			        //console.log(bib_id+":"+location_code+":"+barcode);
 					var item_request_details = { bib: bib_id, loc: location_code, barcode: barcode };
			        request_buttons.push(pulBuildRequestButton(item_request_details,holding_location,location_details));	
			});
			if(!has_barcode) {
            	$(this).find(".EXLHideInfo ul li:contains('Location')").each( function(index) {
            		var item_request_details = {bib: bib_id, loc: location_code, call_no: location_call_number };
            		request_buttons.push(pulBuildRequestButton(item_request_details,holding_location,location_details));
            	});
			}
			pulProcessAvailability($(this),request_buttons,pnx_id, any_available_items, location_is_requestable,location_details);
			pulLocationTabCleanup($(this));
		} else if(item_count == 1) {
			$(this).find(".EXLHideInfo ul li:contains('Barcode')").each( function(index) {
				has_barcode = true;
                	var barcode = pulExtractBarcode($.trim($(this).text()));
                                //console.log(bib_id+":"+location_code+":"+barcode);
                	var item_request_details = { bib: bib_id, loc: location_code, barcode: barcode };
                	request_buttons.push(pulBuildRequestButton(item_request_details,holding_location,location_details));
            	});
			if(!has_barcode) {
				var item_request_details = {bib: bib_id, loc: location_code, call_no: location_call_number };
				request_buttons.push(pulBuildRequestButton(item_request_details,holding_location,location_details));
			}
			pulProcessAvailability($(this),request_buttons,pnx_id, any_available_items,location_is_requestable,location_details);
			pulLocationTabCleanup($(this));
		}
		else {
			if(!online_resource) { //IF not online we have no available item records show request at the holding level 
				var item_request_details = { bib: bib_id, loc: location_code, call_no: location_call_number};
				$(holding_status).replaceWith("<span>"+pulBuildRequestButton(item_request_details,holding_location,location_details,true)+"</span>"); //replace to deal with voyager bug and get rid of "may be available"
			}
			// else online holdings do nothing
		} 
	});	
    return;
	
}


function pulLocationTabCleanup(holding) {
	$(holding).find('.EXLSublocation .EXLLocationTable tr .EXLShowInfo a').each(function(index) {
        	var location_message = $.trim($(this).text());
        	if(location_message.match(/^RECAP/)) {
                	$(this).text('ReCAP');
        	}
        	if(location_message.match(/^Annex A/)) {
                	$(this).text('Annex A');
        	}
        	if(location_message.match(/^Annex B/)) {
                	$(this).text('Annex B, Fine Hall');
        	}
	});

	return;
}


function pulInsertHoldingsNote(holding,display_note) {
	//console.log(display_note);
	//console.log(holding);
	$(holding).find('.EXLResultStatusNotAvailable').each(function(index) {
		$(this).replaceWith(display_note);
	});
}

function pulProcessAvailability(holdings, locations, pnx_id, any_available_items, location_is_requestable, location_details) {
	var location_details = location_details;
	
	$(holdings).find('.EXLSublocation .EXLLocationTable tr .EXLLocationTableColumn3').each(function(index) {
        	var status_message = $(this).text(); //Get current status message, i.e. Charged/Not Charged
        	//console.log(location_is_requestable);
        	if(pulItemAvailableTest(status_message)) {
        		if (location_details.alwaysRequestable == "Y") {
        			// add message 
        			pulInsertHoldingsNote(holdings,"<em class='EXLResultStatusNotAvailable'>In Building, Ask at Desk</em>");
        		}
        		if(any_available_items) {
        			$(this).next().replaceWith("<td><div>"+locations[index]+"</div></td>");
        		} else {
                		var bd_form = pulBuildBorrowDirectForm(pnx_id); // will this work here
                		$(this).next().replaceWith("<td><div>"+bd_form+"</div></td>");
        		}
        	} else {
        		if(location_is_requestable == true || pulItemInProcessOnOrderTest(status_message)) {
        			//console.log('Request Me');
        			$(this).next().replaceWith("<td><div>"+locations[index]+"</div></td>");
        		} else {
        			//console.log('Come Get Me. ');
                	$(this).next().replaceWith("<td><div>Item On Shelf</div></td>");
        	
        		}
        	}
    });
}

function pulProcessFullAvailability(holdings, locations, pnx_id, any_available_items, location_is_requestable, location_details) {
		if (location_details.alwaysRequestable == "Y") {
			// add message 
			pulInsertHoldingsNote(holdings,"Items at this Location are always Available");
		}
        $(holdings).find('.EXLSublocation .EXLLocationTable tr .EXLLocationTableColumn3').each(function(index) {
                var status_message = $(this).text(); //Get current status message, i.e. Charged/Not Charged
                if(pulItemAvailableTest(status_message)) {
                	if (location_details.alwaysRequestable == "Y") {
            			// add message 
            			pulInsertHoldingsNote(holdings,"<em class='EXLResultStatusNotAvailable'>In Building, See Circulation Desk</em>");
            		}
                        if(any_available_items) {
                                $(this).nextAll().eq(2).replaceWith("<td><div>"+locations[index]+"</div></td>");
                        } else {
                                var bd_form = pulBuildBorrowDirectForm(pnx_id); // will this work here
                                $(this).nextAll().eq(2).replaceWith("<td><div>"+bd_form+"</div></td>");
                        }
                } else {
                	if(location_is_requestable == true || pulItemInProcessOnOrderTest(status_message)) {
            			//console.log('Request Me');
                        $(this).nextAll().eq(2).replaceWith("<td><div>"+locations[index]+"</div></td>");
                	} else {
                		//console.log('Come Get Me. ');
                		$(this).nextAll().eq(2).replaceWith("<td><div>Item On Shelf</div></td>");
                	}
                }
    });
}



// Build location options on full item view tab 
function pulBuildFullLocationOptions(pnx_id, current_result_number) {
	var holdings = $('.EXLLocationList');
	var holdings_count = holdings.length;
	var locations = pulGetLocationCodes(pnx_id);
  	var openurls = EXLTA_get_OpenURL(pnx_id);
	if (console) {
		console.log('openurls');
		console.log(openurls);
	}
	// hack for borrow direct checking 
	var any_available_items = false;
	$('.EXLLocationTableColumn3').each(function(index) {
    	var status_message = $(this).text(); //Get current status message, i.e. Charged/Not Charged
		if(pulItemOnShelfTest(status_message)) {
			any_available_items = true;
         	}
         	if(any_available_items) {
        		return false
         	}
        });
	$(holdings).each(function(index) {
                var location_message = $.trim($(this).find('.EXLLocationsTitle .EXLLocationsTitleContainer').text());
                var online_resource = location_message.match(/^Online/); // test for online resource    
                // must match EXLResultStatusNotAvailable EXLResultStatusAvailable and EXLResultStatusMaybeAvailable
                var holding_status = $(this).find('em.EXLResultStatusAvailable,em.EXLResultStatusNotAvailable,em.EXLResultStatusMaybeAvailable');
                var holding_location = $.trim($(this).find('.EXLLocationsTitleContainer').text());
                var collection_finder = $.trim($(this).find('.EXLLocationInfo strong').text());
		var collection_string = collection_finder.match(/\((\w+)\)/);
		if (collection_string) {
			raw_code = collection_string[0].toLowerCase();
			location_code = raw_code.substr(1, raw_code.length - 2);
                } else {
			var location_code = locations[index].loc;
		}

		var item_count = $(this).find('.EXLShowInfo').length;
                var bib_id = locations[index].bib;
        	var location_details = _.find(loc_objects, function(loc, key) {
        		return loc.voyagerLocationCode == location_code; 
        	});
        	var location_is_requestable = false; //flag to make sure that the location isn't requestable
        	if ((location_details.requestable == "Y" && location_details.accessible == "N") || location_details.aeon == "Y") {
        		var location_is_requestable = true;
        	}
                //console.log("holding: "+holding_status.text()+"location: "+holding_location+"items attached: " + item_count +"requestable"+location_is_requestable);
                var request_buttons = new Array();
                var has_barcode = false; // hack to test for barcode 
        	var call_no = $(this).find('.EXLLocationInfo cite');
        	location_call_number = call_no.text();
                if(item_count > 1) {
                        $(this).find(".EXLHideInfo ul li:contains('Barcode')").each( function(index) {
                        		has_barcode = true;
                                var barcode = pulExtractBarcode($.trim($(this).text()));
                                //console.log(bib_id+":"+location_code+":"+barcode);
                                var item_request_details = { bib: bib_id, loc: location_code, barcode: barcode };
                                request_buttons.push(pulBuildRequestButton(item_request_details, holding_location,location_details));
                        });
                        
                        if(!has_barcode) {
                        	$(this).find(".EXLHideInfo ul li:contains('Location')").each( function(index) {
                        		var item_request_details = {bib: bib_id, loc: location_code, call_no: location_call_number };
                        		request_buttons.push(pulBuildRequestButton(item_request_details,holding_location,location_details));
                        	});
            			}
                        
                        pulProcessFullAvailability($(this),request_buttons,pnx_id, any_available_items, location_is_requestable,location_details);
                        pulLocationTabCleanup($(this));
                } else if(item_count == 1) {
                        $(this).find(".EXLHideInfo ul li:contains('Barcode')").each( function(index) {
                        		has_barcode = true;
                                var barcode = pulExtractBarcode($.trim($(this).text()));
                                //console.log(bib_id+":"+location_code+":"+barcode);
                                var item_request_details = { bib: bib_id, loc: location_code, barcode: barcode };
                                request_buttons.push(pulBuildRequestButton(item_request_details, holding_location,location_details));
                        });
                        if(!has_barcode) {
            				var item_request_details = {bib: bib_id, loc: location_code, call_no: location_call_number };
            				request_buttons.push(pulBuildRequestButton(item_request_details,holding_location,location_details));
            			}
                        pulProcessFullAvailability($(this),request_buttons,pnx_id, any_available_items, location_is_requestable,location_details);
                        pulLocationTabCleanup($(this));
                }
                else {
                        if(!online_resource) {
                                if(console) {
                                  console.log('more than a signle holding');
				  console.log(openurls);
                                  if(openurls.length > 0) {
                                    console.log(openurls);
                                  }
                                }
                                
                                var item_request_details = { bib: bib_id, loc: location_code, call_no: location_call_number };
                                $(holding_status).replaceWith("<span>"+pulBuildRequestButton(item_request_details, holding_location, location_details,true)+"</span>");
                        }
                        // else online holdings do nothing
                }
        });
	
    	return;
}


//Add RIS Export to full details/results screens
function buildRISLink(pnx_id) {
	var ris_base = "http://library.princeton.edu/utils/record/";
	var ris_icon = "<span class='EXLButtonSendToIcon EXLButtonSendToIconRis'></span>";
	var ris_label = "Download as RIS";
	var ris_link = "<li class='EXLButtonSendToRis'><a href='"+ris_base+pnx_id+".ris' title='Export to RIS' target='blank'><span class='EXLButtonSendToLabel'>"+ris_label+"</span>"+ris_icon+"</a></li>";
	return ris_link;
}

function buildCatalogReportLink(pnx_id) {
	var base = "http://library.princeton.edu/requests/catalog_report.php?bib=";
	//var icon = "<span class='EXLButtonSendToIcon EXLButtonSendToIconCatalogReport'></span>";
	var label = "Cataloging problem report";
	var link = "<li class='EXLButtonSendToCatalogReport'><a href='"+base+pnx_id+"' title='"+label+"' target='blank'><span class='EXLButtonSendToLabel'>"+label+"</span></a></li>";
	return link;
}

/*new functions for resolve online links app */
function pulRewriteOnlineLinks() {
	
	$('.EXLViewOnlineLinksTitle a').each(function(index){
		var viewOnlineLink = $(this).attr('href');
		$(this).attr('href', pulConvertOnlineLink(viewOnlineLink));
	});
}

function pulConvertOnlineLink(raw_link, pnxid) {
	//var resolvePrefix = "http://library.princeton.edu/resolve/lookup";
	// exclude expand.do local links
	// exclude catalog/sfx based links
	//console.log(raw_link);
	var link = raw_link.replace("$$V", "");

	if (link.indexOf('sfx.princeton.edu') !== -1) {
		var raw_link_to_resource = EXLTA_getlinktosrc(pnxid);
		var link_to_resource = raw_link_to_resource.replace("$$V", "");
		var link_data = link_to_resource.replace("$$U", "");//match(/\$\$u(.+)\$\$D/g);
		var link_wsfx_cleanup = link_data.replace("$$DView Princeton's online holdings", "");
		//var link_wsfx_cleanup = link_url + "&rft.genre=article";
	} else {
		var link_wsfx_cleanup = link.replace("&svc.fulltext=yes", "");
	}
	
	if (link.indexOf("display") != -1){
		return link_wsfx_cleanup;
	} else {
		//"?recordid=" + pnxid + 
		return link_wsfx_cleanup;
		//return link_wsfx_cleanup;
	}
}

function pulBuildLocations() {
	//var locations = [];
	$.getJSON('/primo_library/libweb/ADDONS/locations.2012.07.02.json', function(data) {
		//console.log(data);
		var locations = []
		$.each(data, function(key,value) { 
			locations.push(value);
		});
	});
	//console.log(locations);
}

function pulBuildSpecialCollectionHoldings(pnx_id, current_result_number) {
        //console.log(pnx_id+":"+current_result_number)
        var holdings = $('#exlidResult'+current_result_number+'-TabContent .EXLLocationList, .EXLLocationList');
        holdings.append("<div class='archival-placeholder archival-loading-"+pnx_id+"'><img src='../images/icon_loading_circle.gif'/></div>");
        var holdings_count = $(holdings.length);
        //if(holdings_count == 1) {
                pulBuildArchivalHoldings(pnx_id,holdings);
        //}
}


function pulBuildArchivalHoldings(id,holding_listing) {
	$.ajax({
        url: "http://library.princeton.edu/utils/archives/"+id,
        //url: '/PrimoWebServices/xservice/getit?institution=PRN&docId=' + id,
        async: true,
        type: 'GET',
        dataType: 'html',
        success: function(data) {
            $('.archival-placeholder').hide();
            holding_listing.append(data);    
        },
        error: function(data) {
            $(".availability-results-spinner").hide();
            holding_listing.append("<div>Archival Request Information Unavailable</div>");
        },
        timeout: 60000 //one  minute

    });
}

function isPulArchives(pnx_id) {
    var EAD_string = new RegExp("EAD|Visuals|Theses");
    if (EAD_string.test(pnx_id)) {
        if(console) {
            console.log(pnx_id + "is special collections")
        }
        return true;
    } else {
        if(console) {
            console.log(pnx_id + "is not collections")
        }
        return false;
    }
}
