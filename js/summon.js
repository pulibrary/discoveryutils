/**
 * set up intergrations / contextual behavior for summon
 */


jQuery.fn.exists = function(){return jQuery(this).length>0;} 

$(document).ready(function() {
        // select the article tab. if the the label changes this will need to be updated 
        // if the tab is no longer called Articles in Primo Back Office this function will break
        // code is looking to find a tab with the class ".EXLSearchTabLABELArticles" 
        var summonTabLabel = "Articles+";
        var pulSummonTab = $('.EXLSearchTabTitle').filter('.EXLSearchTabLABELArticles');
        var currentSearchValue = $("#search_field").val();
        //var summonBaseUrl = "http://princeton.summon.serialssolutions.com/";
        //var summonQueryString = "search?s.q=";
        var summonBaseUrl = "http://library.princeton.edu/searchit/search/summon";
        var summonQueryString = "?query=";
        var currentSummonUrl = "";
        var current_search_value;
        // test for all possible javascript null/empty values
        if (currentSearchValue != "" && currentSearchValue != undefined && currentSearchValue != null) {
          summonQueryString = summonQueryString+encodeURI(currentSearchValue);
        }
        currentSummonUrl = summonBaseUrl +summonQueryString;
        // create new tab with key search if available
        //removeAttr('onclick').
        if($('#exlidHomeContainer').exists()) { //if we are on the homepage 
          
          $(pulSummonTab).addClass('PULSummonLink').text(summonTabLabel);
          $(pulSummonTab).click(function(event) {
        	  var current_url = $(location).attr('href');
        	  
        	  if ((RegExp("search\\.do.*mode=Advanced*").test(current_url))) {
        		  var current_search_value = pulGetSummonAdvancedQueryValues();
        	  } else {
        		  var current_search_value = EXLTA_searchTerms();
        	  }
        	  if (current_search_value) {
        		  event.preventDefault();
        		  window.open(pulGetSummonLink(current_search_value));
        	  }
          });
        } else {
          
          $(pulSummonTab).addClass('PULSummonLink').attr('href', currentSummonUrl).attr('target', '_blank').text(summonTabLabel);
          $(pulSummonTab).click(function(event) {
        	  var current_url = $(location).attr('href');
        	  if ((RegExp("search\\.do.*mode=Advanced*").test(current_url))) {
        		  var current_search_value = pulGetSummonAdvancedQueryValues();
        	  } else {
        		  var current_search_value = EXLTA_searchTerms();
        	  }
        	  if (current_search_value) {
        		  event.preventDefault();
        		  window.open(pulGetSummonLink(current_search_value));
        	  }
          });
        }
        // now redo the form
        if($('.EXLSearchTabSelected .EXLSearchTabLABELArticles').exists()) {
          var summonForm = $('#searchForm.EXLSearchForm');
          var searchField = $('#search_field');
          $('#exlidSearchRibbon input[type="hidden').remove();
          
          $(summonForm).addClass('SummonForm').attr('target', '_none').attr('action', 'http://library.princeton.edu/searchit/search/summon').attr('method', 'get').attr('onsubmit', ''); 
          $(searchField).attr('name', "query");
          //$(searchField).attr('name', "s.q");
          if (currentSearchValue != "" && currentSearchValue != undefined && currentSearchValue != null) {
            $(pulSummonTab).addClass('PULSummonLink').attr('href', currentSummonUrl).attr('target', '_blank').text(summonTabLabel); 
          }
          $('#searchForm.EXLSearchForm input[type="hidden"]').remove();
          //$('#searchForm.EXLSearchForm input[type="radio"]').remove();
        }
        
});

// general interface cleanup routines 
$(document).ready(function() {
    var activeTab = $.trim($("#exlidSearchTabs li.EXLSearchTabSelected a.EXLSearchTabTitle").text());
    var hideAdvancedSearch = ["Course Reserves","All"];

    if (activeTab == "All" || activeTab == "Course Reserves" || activeTab=="Articles+") {
            $('.EXLSearchFieldRibbonAdvancedSearchLink').hide();
            $('.EXLSearchFieldRibbonBrowseSearchLink').hide();
    } else {
            $('.EXLSearchFieldRibbonAdvancedSearchLink').show();
            $('.EXLSearchFieldRibbonBrowseSearchLink').show();
    }

    var current_url = $(location).attr('href');
   
	if ((RegExp("search\\.do.*mode=Advanced*").test(current_url))) { //hide tabs on search form
		$('#exlidTab1').hide(); //Articles+
		$('#exlidTab2').hide(); //Course Reserves
		$('#exlidTab3').hide(); //All 
	}
    
	if (EXLTA_isLoggedIn() == false) {
		$('#exlidMyAccount').hide();
	} 
	$('#scopesListAdvanced .EXLAdvancedSearchFormRowInlineInput .EXLMainMenuITEMHelp').hide();  //hide "Find Databases" helper
	//jQuery.getJSON('/primo_library/libweb/ADDONS/get_user.jsp', function(data){console.log(data);});
	pulSetTabMessages();
});
