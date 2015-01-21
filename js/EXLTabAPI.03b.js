/********************************************************
 ** EXL Custom Tab API (for Primo)
 **
 **	contributions, corrections and suggestions welcome.
 **
 **	for documentation and/or to comment, the wiki is here:
 ** 	http://www.exlibrisgroup.org/display/Primo/EXL+Tab+API
 ** 
 ** or email: jacob.hanan@exlibrisgroup.com
 **
 ****************************************************/

function EXLTA_addHeadlessTab(tabType, content, evaluator){
        $('.EXLResultTabs').each(function(){
                if(!evaluator || (evaluator && evaluator(this))){
                        var htmlcontent = '';
                        if (typeof(content)=='function'){
                                log('trying function');
                                htmlcontent = content(this);
                        }else{
                                htmlcontent = content;
                        }
                        var customTabContainer = $('<div class="'+tabType+'-Container">'+htmlcontent+'</div>');
                        
						var result = $(this).parents('.EXLResult');						
						if (!EXLTA_isFullDisplay()){//Solves 'full display' bug where container isn't added to page.
							result = result.find('.EXLSummary');
						}
						result.append(customTabContainer);
                }

        });
}

function EXLTA_addOpenTab(tabName,tabType,url,tabHandler,firstTab,evaluator){
                EXLTA_addTab(tabName,tabType,url,tabHandler,firstTab);
                $('.'+tabType).click();
}
function EXLTA_addTab(tabName,tabType,url,tabHandler,firstTab,evaluator){
        EXLTA_addTabBySelector('.EXLResultTabs',tabName,tabType,url,tabHandler,firstTab,evaluator);
}

function EXLTA_addTabBySelector(selector,tabName,tabType,url,tabHandler,firstTab,evaluator){
        $(selector).each(function(){
                var customTab = $('<li class="EXLResultTab '+tabType+'"><a href="'+url+'">'+tabName+'</a></li>');
                var customTabContainer = $('<div class="EXLResultTabContainer '+tabType+'-Container"></div>');
                if(!evaluator || (evaluator && $(this))) { //evaluator(this))){ Removed this because this was throwing errors (evaluator is not a function)
                        if (firstTab==true){
                                                $(this).find('li').removeClass('EXLResultFirstTab');
                                                $(customTab).addClass('EXLResultFirstTab');
                                                $(this).prepend(customTab);
                        }else if (firstTab==undefined || firstTab==false){
                                                $(this).find('li').removeClass('EXLResultLastTab');
                                                $(customTab).addClass('EXLResultLastTab');
                                                $(this).append(customTab);
                        }else{
                                                $(this).find(firstTab).replaceWith(customTab);
						
						}

						if (EXLTA_isFullDisplay()) {
							$(this).parents('.EXLResult').append(customTabContainer);	                        
						} else {
							$(this).parents('.EXLResult').find('.EXLSummary').append(customTabContainer);	
						}

						$('#'+$(this).attr('id')+' .'+ tabType + ' a').click(function(e){
							tabHandler(e, this, tabType, url, $(this).parents('.EXLResultTab').hasClass('EXLResultSelectedTab'));
						});
					
                }
                $(this).parents('.EXLSummary').find('.'+tabType+'-Container').hide();

        });
}

function EXLTA_wrapResultsInNativeTab(element, content,url, headerContent){
        var popOut = '<div class="EXLTabHeaderContent">'+headerContent+'</div><div class="EXLTabHeaderButtons"><ul><li class="EXLTabHeaderButtonPopout"><span></span><a href="'+url+'" target="_blank"><img src="../images/icon_popout_tab.png" /></a></li><li></li><li class="EXLTabHeaderButtonCloseTabs"><a href="#" title="hide tabs"><img src="../images/icon_close_tabs.png" alt="hide tabs"></a></li></ul></div>';
        var header = '<div class="EXLTabHeader">'+ popOut +'</div>';
        var htmlcontent = '';
        if (typeof(content)=='function'){
                log('trying function');
                htmlcontent = content(element);
        }else{
                htmlcontent = content;
        }
        var body = '<div class="EXLTabContent">'+htmlcontent+'</div>';
        return header + body;
}
function EXLTA_closeTab(element){
        if(!EXLTA_isFullDisplay()){
                $(element).parents('.EXLResultTab').removeClass('EXLResultSelectedTab');
                $(element).parents('.EXLTabsRibbon').addClass('EXLTabsRibbonClosed');
                $(element).parents('.EXLResult').find('.EXLResultTabContainer').hide();
        }
}
function EXLTA_openTab(element,tabType, content, reentrant){
        $(element).parents('.EXLTabsRibbon').removeClass('EXLTabsRibbonClosed');
        $(element).parents('.EXLResultTab').siblings().removeClass('EXLResultSelectedTab').end().addClass('EXLResultSelectedTab');
        var container = $(element).parents('.EXLResult').find('.EXLResultTabContainer').hide().end().find('.'+tabType+'-Container').show();
        if (content && !(reentrant && $(container).attr('loaded'))){
                $(container).html(content);
                if(reentrant){
                        $(container).attr('loaded','true');
                }
        }
        return container;
}

function EXLTA_iframeTabHandler(e,element,tabType,url,isSelected){
                e.preventDefault();
                if (isSelected){
                        EXLTA_closeTab(element);
                }else{
                        EXLTA_openTab(element,tabType, EXLTA_wrapResultsInNativeTab(element,'<iframe src="'+url+'"></iframe>',url,''),true);
                }
}

function EXLTA_createWidgetTabHandler(content,reentrant){
        return function(e,element,tabType,url,isSelected){
                e.preventDefault();
                if (isSelected){
                        EXLTA_closeTab(element);
                }else{
                        EXLTA_openTab(element,tabType, EXLTA_wrapResultsInNativeTab(element,content,url,''),reentrant);
                }
        };
}

function EXLTA_addLoadEvent(func){
        addLoadEvent(func);
}

function EXLTA_isFullDisplay(){
	return $('.EXLFullView').size() > 0;
}
function EXLTA_searchTerms(){
        return $('#search_field').val();
}
function EXLTA_recordId(element){
        return $(element).parents('.EXLResult').find('.EXLResultRecordId').attr('id');
}

function EXLTA_getPNX(recordId){
        var r = $('#'+recordId).get(0);
        if (!r.pnx){
                //r.pnx = $.ajax({url: 'display.do',data:{fn: 'display', doc: recordId, showPnx: true},async: false,error:function(){log('pnx retrieval error')}}).responseXML;
                r.pnx = $.ajax({url: 'searchit/record/' + recordId + '.xml',
				dataType: "xml",
				async: false,
				error:function(){log('pnx retrieval error')}}).responseXML;
		//console.log(r.pnx);
        }
        return r.pnx;
}

function EXLTA_getSfxLink(element){
	try{
	var href = $(element).parents('.EXLResult').find('.EXLMoreTab a').attr('href');
	var modifiedHref = href.replace(/display\.do/,"expand.do").replace(/renderMode=poppedOut/,'renderMode=prefetchXml');
	var xml = $.ajax({url: modifiedHref ,global: false,async: false,error:function(){log('sfx retrieval error')}}).responseXML;
	var htmlText = $(xml).find('element').text();
	var url = htmlText.match(/href="([^"]*)"/)[1];
	return url.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>');
	}catch(errrrr){log(errrrr);}
	return undefined;	
}
function EXLTA_isbn(recordId){
        var pnx = EXLTA_getPNX(recordId);
        return $(pnx).find('isbn').eq(0).text();
}

function EXLTA_isbns(recordId){
        var pnx = EXLTA_getPNX(recordId);
		var isbns = new Array();
		$(pnx).find('isbn').each(function() {
			isbns.push($(this).text());
		});
		
		var isbn_string = isbns.join("isbn");
		
        return isbn_string;
}

function EXLTA_issn(recordId){ //contributed by Karsten Kryger Hansen
        var pnx = EXLTA_getPNX(recordId);
        return $(pnx).find('addata > issn').eq(0).text();
}

function EXLTA_year(recordId){
        var pnx = EXLTA_getPNX(recordId);
        return $(pnx).find('creationdate').eq(0).text();
}

function EXLTA_date(recordId){
        var pnx = EXLTA_getPNX(recordId);
        return $(pnx).find('addata > date').eq(0).text();
}

function EXLTA_volume(recordId){
	     var pnx = EXLTA_getPNX(recordId);
        return $(pnx).find('addata > volume').eq(0).text();
}

function EXLTA_issue(recordId){
        var pnx = EXLTA_getPNX(recordId);
        return $(pnx).find('addata > issue').eq(0).text();
}

function EXLTA_spage(recordId){
        var pnx = EXLTA_getPNX(recordId);
        return $(pnx).find('addata > spage').eq(0).text();
}

function EXLTA_epage(recordId){
        var pnx = EXLTA_getPNX(recordId);
        return $(pnx).find('addata > epage').eq(0).text();
}

function EXLTA_displaytype(recordId){
        var pnx = EXLTA_getPNX(recordId);
        return $(pnx).find('display > type').eq(0).text();
}

/* NOTE
   this value is buried in a marc style
   $Uhttp://myurl$Elink label
   the function that calls these will have to deal with them
   example: $$Uhttp://sfx.princeton.edu:9003/sfx%5Fpul?url%5Fver=Z39.88-2004&ctx%5Fver=Z3…t%5Fid=954921398115&svc%5Fval%5Ffmt=info:ofi/fmt:kev:mtx:sch%5Fsvc&$$DView Princeton's online holdings
*/
function EXLTA_getlinktosrc(recordId){
	var pnx = EXLTA_getPNX(recordId);
	return linktosrc = $(pnx).find('links > linktorsrc').eq(0).text();
}

function EXLTA_getLang() {
	var signoutText = $("#exlidSignOut").find("a").html();

        if (startsWith(signoutText,Array('Sign in','Sign out'))) {
		return 'en_US';
        } else if (startsWith(signoutText,Array('Log ind', 'Log ud'))) {
		return 'da_DK';
	}

}


// added by Kevin Reiss
function EXLTA_title(recordId){ 
    var pnx = EXLTA_getPNX(recordId);
    // should this title be split at "\"
    return $(pnx).find('display > title').eq(0).text();
}

//gets the "sort" title of the record 
// shorter than display title - better for searching
function EXLTA_sortTitle(recordId){
	var pnx = EXLTA_getPNX(recordId);
	return $(pnx).find('sort > title').eq(0).text();
}

// return an object containg all add data in key value form 
//TODO Handle repeated values return these as arrays appended to key
function EXLTA_getAddData(recordId) {
	var pnx = EXLTA_getPNX(recordId);
	var addData = new Object(); 
	var addDataElements = $(pnx).find('addata').children()
	addData.size = addDataElements.length;
	$(addDataElements).each(function(index) { //FIXME Handle multiple field values
		// does key exist?
		field_name = "var" + index;
		addData[$(this).get(0).tagName] = $(this).text();
	});
	
	return addData;
}

// return an object contain display data section 
// TODO handle repeated values 
function EXLTA_getDisplayData(recordId) {
	var pnx = EXLTA_getPNX(recordId);
	var displayData = new Object(); 
	var displayDataElements = $(pnx).find('display').children()
	displayData.size = displayDataElements.length;
	$(displayDataElements).each(function(index) {
		field_name = "var" + index;
		displayData[$(this).get(0).tagName] = $(this).text();
	});
	displayData.subjects = EXLTA_subjects(recordId);
	return displayData;
}

function EXLTA_subjects(recordId){
    var pnx = EXLTA_getPNX(recordId);
	var subjects = new Array();
	$(pnx).find('subject').each(function() {
		subjects.push($(this).text());
	});
	
    return subjects;
}

function EXLTA_availlibraries(recordId){
	var pnx = EXLTA_getPNX(recordId);
	var availlibraries = new Array();
	$(pnx).find('availlibrary').each(function() {
		availlibraries.push($.trim($(this).text())); 
	});
	
	return availlibraries;
}


function EXLTA_isLoggedIn() {
	var userName = $.trim($('span.EXLUserNameDisplay').text());
	if (userName != "Guest") {
		return true;
	} else {
		return false;
	}
}

function EXLTA_isDedupRecord(recordId) {
	if (RegExp('^dedupmrg').test(recordId)) { // this prefix may change for some sites 
		return true;
	} else {
		return false;
	}
}

function EXLTA_getResultNumberOnPage(pnxId) {
	var current_result = $('#'+pnxId);
	return $('a.EXLResultRecordId').index(current_result);
}

function startsWith(s,a) {
        for (x in a) {
                if (s.indexOf(a[x]) === 0) {
                        return true;
                }
        }
        return false;
}
