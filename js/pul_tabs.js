$(document).ready(function() {
	// for view online link rewrite
	$('.EXLViewOnlineTab a').each(function(index){
		var viewOnlineLink = $(this).attr('href');
		//console.log(viewOnlineLink);
    // This no longer works
    // Different paths depending on whether you are on a
    // results or full record page
    //
    // ON results page you 
    // 1. Need to know the hit number clicked on 
    // 2. Fetch ID from the following attribute 
    // 
    var summary_container = $(this).closet('.EXLSummary');
    var thumbnail_container = $(summary_container).siblings(".EXLThumbnail");
    var pnx_id = $(thumbnail_container).find('a.EXLResultRecordId').attr('.name');
		//var pnx_id = $(this).attr('data-record-id');
		
    // different for full record page 

    $(this).attr('href', pulConvertOnlineLink(viewOnlineLink));
	});
	
});


// Ajax loads for tabs 

// Comment Out; Append Titles 
//$(document).ajaxComplete(function(event, request, settings) {
//   if((RegExp("expand\\.do.*tabs=locationsTab").test(settings.url))) {
//        $('.EXLLocationsTitleContainer').not(':has(a)').append(
//            '<br /><a href="http://library.princeton.edu/">PUL Location Chart</a>');
//	    pulLocationSignIn(d);
//   }
//});
//

// Events for all tabs
// RIS Function
$(document).ajaxComplete(function(event, request, settings) {
	if ((RegExp("expand\\.do.*tabs=").test(settings.url))) {
		var match = settings.url.match(/&doc=((Visuals|EAD|Theses|PRN_VOYAGER|dedupmrg)\d{1,9})/);
		if (match) {
	    	var id = match[1];
			var current_result_number = EXLTA_getResultNumberOnPage(id);
			// add RIS option
			var ris_link = buildRISLink(id);
			var link = buildCatalogReportLink(id);
			if(!EXLTA_isFullDisplay()) {
				$('.EXLButtonSendToRis').hide();
				$('#exlidResult'+current_result_number+'-TabHeader #exlidTabHeaderButtons'+current_result_number+' .EXLTabHeaderButtonSendToList').append(ris_link);
				$('.EXLButtonSendToCatalogReport').hide();
				$('#exlidResult'+current_result_number+'-TabHeader #exlidTabHeaderButtons'+current_result_number+' .EXLTabHeaderButtonSendToList').append(link);
			} else {
				$('.EXLButtonSendToRis').hide();
				$('.EXLTabHeaderButtonSendToList').append(ris_link);
				$('.EXLButtonSendToCatalogReport').hide();
				$('.EXLTabHeaderButtonSendToList').append(link);
			}

		}
	}
});


// AJAX Events for Details Tab
// Permanent Linking for details tab
$(document).ajaxComplete(function(event, request, settings) {
	if ((RegExp("expand\\.do.*tabs=detailsTab").test(settings.url))) {
		var match = settings.url.match(/&doc=((EAD|Theses|Visuals|PRN_VOYAGER|dedupmrg)\d{1,9})/);
		
	    // the record id pattern will vary depending on your setup
	    if (match) {
	    	var id = match[1];
	    	//var current_result_number = $('a.EXLResultRecordId').index(current_result);
			var current_result_number = EXLTA_getResultNumberOnPage(id); // Try new function to get proper Index Number 
	    	//alert('current index ' + current_result_number + id);
	        $('#exlidResult'+current_result_number+'-TabContainer-detailsTab form .EXLDetailsTabContent .EXLDetailsLinks ul').append(pulGetRecordDeepLink(id));
		     
	    }
	    
		$('.EXLDetailsLinks .EXLDetailsLinksTitle a').each(function(index){
			var viewOnlineLink = $(this).attr('href');
			if($(this).text().indexOf("Link to Resource") != -1) {
				$(this).attr('href', pulConvertOnlineLink(viewOnlineLink));
			}
		});
	}
});

// Events for full display page only 
// permenant link for details full page display
$(document).ready(function() {
	var current_url = $(location).attr('href');
	if ((RegExp("(basket|display)\\.do.*").test(current_url))) {
		// grab current ID
		var match = current_url.match(/&doc=((Visuals|EAD|Theses|PRN_VOYAGER|dedupmrg)(\d{1,9}|\w+))/);
		var id = match[1];
		if(match) {
			$('form .EXLDetailsTabContent .EXLDetailsLinks ul').append(
	            '<li class="EXLFullDetailsOtherItem"><span class="EXLDetailsLinksBullet"></span><span class="EXLDetailsLinksTitle"><a class="EXLFullDetailsInboundLink" href="/primo_library/libweb/action/dlDisplay.do?institution=PRN&vid=PRINCETON&docId='+id+'">'+'This item Permalink'+'</a></span></li>');
			//var citation_data = EXLTA_getAddData(id);
			//console.log(citation_data);
			//var display_data = EXLTA_getDisplayData(id);
			//console.log(display_data);
			// Add the RIS Link
			var ris_link = buildRISLink(id);
			$('.EXLTabHeaderButtonSendToList').append(ris_link);
			var link = buildCatalogReportLink(id);
			$('.EXLTabHeaderButtonSendToList').append(link);
		}
		$('.EXLDetailsLinks .EXLDetailsLinksTitle a').each(function(index){
			var viewOnlineLink = $(this).attr('href');
			if($(this).text().indexOf("Link to Resource") != -1) {
				$(this).attr('href', pulConvertOnlineLink(viewOnlineLink));
			}
		});
	}
	//pulLocationSignIn()
});

// Ajax Events for locations tabs 

$(document).ready(function() {
        var current_url = $(location).attr('href');
        if ((RegExp("display\\.do.*tabs=locationsTab").test(current_url))) {
                // grab current ID
                var match = current_url.match(/&doc=((Visuals|EAD|Theses|PRN_VOYAGER|dedupmrg)(\d{1,9}|[\w\.]+))/);
                //var match = current_url.match(/&doc=((Visuals|EAD|PRN_VOYAGER|dedupmrg)(\d{1,9}|\w+))/);
                if(match) {
                        var pnx_id = match[1];
                        var current_result_number = EXLTA_getResultNumberOnPage(pnx_id);
                        //console.log(pnx_id+" : "+current_result_number);
                        if(isPulArchives(pnx_id)) {
                            pulBuildSpecialCollectionHoldings(pnx_id, current_result_number);
                        } else {
                            pulBuildFullLocationOptions(pnx_id, current_result_number);
                            pulBuildFullLocatorMapOptions(pnx_id, current_result_number);
                        }
                }
        }
	//pulLocationSignIn();
});


//Ajax Events for locations tabs 
$(document).ajaxComplete(function(event, request, settings) {
        if ((RegExp("expand\\.do.*tabs=locationsTab").test(settings.url))) {
            var match = settings.url.match(/&doc=((Visuals|EAD|Theses|PRN_VOYAGER|dedupmrg)(\d{1,9}|[\w\.]+))/);
            //var match = settings.url.match(/&doc=((EAD|PRN_VOYAGER|dedupmrg)(.+))/);
                // })->assert('rec_id', '(\w+|EADMC\d+\.?\w+)');
		//var not_display_borrow_direct_test = "/(Charged|Overdue)/";
                // check the PRIMO web service 
            if (match) {
                var pnx_id = match[1];
		if (console) {
                  console.log(pnx_id);
                }               
                var current_result_number = EXLTA_getResultNumberOnPage(pnx_id);
                // create a borrow direct form object that 
                
                if(isPulArchives(pnx_id)) {
                    pulBuildSpecialCollectionHoldings(pnx_id, current_result_number);
                }
                else if(!EXLTA_isFullDisplay()) {
                        pulBuildLocationOptions(pnx_id, current_result_number);
                        pulBuildLocatorMapOptions(pnx_id, current_result_number);
                } else {
                        pulBuildFullLocationOptions(pnx_id, current_result_number);
                        pulBuildFullLocatorMapOptions(pnx_id, current_result_number);
                }
            }
            //pulLocationSignIn();	
        }
	
});





// Ajax events ford requests tabs 
$(document).ajaxComplete(function(event, request, settings) {
	   if((RegExp("expand\\.do.*tabs=requestTab").test(settings.url))) {
		   var match = settings.url.match(/&doc=((EAD|Theses|PRN_VOYAGER|dedupmrg)\d{1,9})/);
		   if (match) {
			   var pnx_id = match[1];
			   var current_result_number = EXLTA_getResultNumberOnPage(pnx_id);
			   //$('#exlidResult'+current_result_number+'-TabContainer-requestTab .EXLRequestTabContent div:first').hide();
		if(EXLTA_isLoggedIn()) {
			//$('#exlidResult'+current_result_number+'-TabContainer-requestTab .EXLRequestTabContent div:first').hide();
			$('#exlidResult'+current_result_number+'-TabHeader .EXLTabHeaderContent').hide();
			
		}
		var voyager_id = pnx_id.replace("PRN_VOYAGER", "");
		$('.recall-message').addClass('btn info');
		$('.recall-message').text('Sign in to Recall');
                $('.item').attr('href', 'http://library.princeton.edu/requests/?bib='+voyager_id);
			   // Swap in a borrow direct form 
			   //$('#exlidResult'+current_result_number+'-TabContainer-requestTab .pul-bd-check').parent().append(pulBuildBorrowDirectForm(pnx_id)); //try 
		   //pulSetButtonStyles();
		   }
		   //pulRequestTabPopovers(); //popover effect
		   //pulLocationSignIn(); 
		   // insert borrow direct form
		   
	   }
});


// for view online links
$(document).ajaxComplete(function(event, request, settings) {
	if ((RegExp("expand\\.do.*tabs=viewOnlineTab").test(settings.url))) {
		pulRewriteOnlineLinks();
	}
});

