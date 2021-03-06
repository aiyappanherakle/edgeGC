/**
* Core Javascript functions for ILance.
*
* @package      iLance
* @version	$Revision: 1.0.0 $
* @author       ILance
*/

/**
* Browser Compatibility
*/
var DOMTYPE = '';
if (document.getElementById)
{
	DOMTYPE = 'std';
}
else if (document.layers)
{
	DOMTYPE = 'ns4';
}
else if (document.all)
{
	DOMTYPE = 'ie4';
}

if (typeof ILSESSION == 'undefined')
{
	ILSESSION = '';
}

/**
* Fetch Browser Agent
*/
var AGENT = navigator.userAgent.toLowerCase();
var checkopera = (AGENT.indexOf('opera') != -1);
var checksaf = ((AGENT.indexOf('safari') != -1) || (navigator.vendor == 'Apple Computer, Inc.'));
var checkwebtv = (AGENT.indexOf('webtv') != -1);
var checkie = ((AGENT.indexOf('msie') != -1) && (!checkopera) && (!checksaf) && (!checkwebtv));
var checkie4 = ((checkie) && (AGENT.indexOf('msie 4.') != -1));
var checkie7 = ((checkie) && (AGENT.indexOf('msie 7.') != -1));
var checkmoz = ((navigator.product == 'Gecko') && (!checksaf));
var checkkon = (AGENT.indexOf('konqueror') != -1);
var checkns = ((AGENT.indexOf('compatible') == -1) && (AGENT.indexOf('mozilla') != -1) && (!checkopera) && (!checkwebtv) && (!checksaf));
var checkns4 = ((checkns) && (parseInt(navigator.appVersion) == 4));
var checkmac = (AGENT.indexOf('mac') != -1);
var checkchrome = (AGENT.indexOf('chrome') != -1);

/**
* Define popup, delays and other objects
*/

/**
* Define slideshow rotation delay on item listing page
*/
var rotate_delay = 5000;
var current = 0;
var attw = null;
var drww = null;
var checkobj = null;
var ilobjects = new Array();
var category_popup_timer = null;

/**
* AJAX and Regular Expressions Compatibility
*/
var REGEXP_Compatible = (window.RegExp) ? true : false;
var AJAX_Compatible = false;

/**
* Core Functions
*/
function fetch_session_id()
{
	return (ILSESSION == '' ? '' : ILSESSION.substr(2, 32));
}

function fetch_js_object(idname)
{
	if (document.getElementById)
	{
		return document.getElementById(idname);
	}
	else if (document.all)
	{
		return document.all[idname];
	}
	else if (document.layers)
	{
		return document.layers[idname];
	}
	else
	{
		return null;
	}
}

function fetch_js_cookie(name) 
{
	v3cookiename = name + '=';
	v3cookiesize = document.cookie.length;
	v3cookiestart = 0;
	while (v3cookiestart < v3cookiesize) 
	{
		v3cookievalue = v3cookiestart + v3cookiename.length;
		if (document.cookie.substring(v3cookiestart, v3cookievalue) == v3cookiename) 
		{
			var v3cookievalue2 = document.cookie.indexOf (';', v3cookievalue);
			if (v3cookievalue2 == -1) 
			{
				v3cookievalue2 = v3cookiesize;
			}
			return unescape(document.cookie.substring(v3cookievalue, v3cookievalue2));
		}
		v3cookiestart = document.cookie.indexOf(' ', v3cookiestart) + 1;
		if (v3cookiestart == 0) 
		{
			break;
		}
	}
	return null;
}

function update_js_cookie(name, value, expires) 
{
	if (!expires) 
	{
		expires = new Date();
	}
	
	document.cookie = name + "=" + escape(value) + "; expires=" + expires.toGMTString() +  "; path=/";
}

function update_js_collapse_cookie(objid, setcookiedata, cookiename) 
{
	var cookiedata = fetch_js_cookie(cookiename);
	var cookietemp = new Array();
	
	if (cookiedata != null) 
	{
		cookiedata = cookiedata.split('|');
		for (i in cookiedata) 
		{
			if (cookiedata[i] != objid && cookiedata[i] != '') 
			{
				cookietemp[cookietemp.length] = cookiedata[i];
			}
		}
	}
	
	if (setcookiedata) 
	{
		cookietemp[cookietemp.length] = objid;
	}
	
	cookieexpire = new Date();
	cookieexpire.setTime(cookieexpire.getTime() + (500 * 86400 * 365));
	update_js_cookie(cookiename, cookietemp.join("|"), cookieexpire);
}

function toggle(objid) 
{
	if (!REGEXP_Compatible)
	{
		return false;
	}
	
	obj = fetch_js_object('collapseobj_' + objid);
	img = fetch_js_object('collapseimg_' + objid);
	
	if (!obj)
	{
		if (img)
		{
			img.style.display = 'none';
		}
		
		return false;
	}
	
	// #### our data is collapsed lets show it #############################
	if (obj.style.display == 'none') 
	{
		obj.style.display = '';
		
		// #### tell cookie to remove this obj from the list so php can show it
		update_js_collapse_cookie(objid, false, ILNAME + 'collapse');
		
		if (img) 
		{
			// #### flip the collapsed icon to expanded state
			img_re = new RegExp("_collapsed\\.gif$");
			img.src = img.src.replace(img_re, '.gif');
		}
	}
	
	// #### our data is expanded lets hide it ##############################
	else 
	{
		obj.style.display = 'none';
		
		// #### tell cookie to add this obj to the list so php can hide it
		update_js_collapse_cookie(objid, true, ILNAME + 'collapse');
		
		if (img) 
		{
			// #### flip the expanded icon collapsed state
			img_re = new RegExp("\\.gif$");
			img.src = img.src.replace(img_re, '_collapsed.gif');
		}
	}
	
	return false;
}

function agreesubmit(el) 
{
	checkobj = el
	
	if (document.all || document.getElementById) 
	{
		for (i = 0; i < checkobj.form.length; i++)
		{
			var tempobj = checkobj.form.elements[i]
			if (tempobj.type.toLowerCase() == 'submit')
				tempobj.disabled=!checkobj.checked
		}
	}
}

function defaultagree(el) 
{
	if (!document.all && !document.getElementById) 
	{
		if (window.checkobj && checkobj.checked)
		{
			return true
		}
		else 
		{
			alert(phrase['_please_read_accept_terms_to_submit_form'])
			return false
		}
	}
}

function confirm_js(message)
{
	grayscale = document.getElementsByTagName("html");
	grayscale[0].style.filter = "progid:DXImageTransform.Microsoft.BasicImage(grayscale=1)";
	if (confirm(message))
	{
		return true;
	}
	else
	{
		grayscale[0].style.filter = "";
		return false;
	}
}

function alert_js(message)
{
	grayscale = document.getElementsByTagName("html");
	grayscale[0].style.filter = "progid:DXImageTransform.Microsoft.BasicImage(grayscale=1)";
	if (alert(message))
	{
		return true;
	}
	else
	{
		grayscale[0].style.filter = "";
		return false;
	}	
}

function log_out() 
{
	grayscale = document.getElementsByTagName("html");
	grayscale[0].style.filter = "progid:DXImageTransform.Microsoft.BasicImage(grayscale=1)";
	if (confirm(phrase['_are_you_ready_to_log_out']))
	{
		return true;
	}
	else
	{
		grayscale[0].style.filter = "";
		return false;
	}
}

function showImage(imagename, imageurl, errors)
{
	document[imagename].src = imageurl;
	if (!haveerrors && errors)
	{
		haveerrors = errors;
		if (obj = fetch_js_object('inlineerror'))
		{
			obj.innerHTML = '<img name="inlineerror" src="images/default/icons/fieldempty.gif" width="21" height="13" border="0" alt="" />';
		}
	}
}

function noenter()
{
	return !(window.event && window.event.keyCode == 13); 
}

function createWindow(u, n, w, h, r)
{
	args = 'width=' + w + ',height=' + h + ',resizable=yes,scrollbars=yes,status=0';
	remote = window.open(u, n, args);
	if (remote != null) 
	{
		if (remote.opener == null)
		{
			remote.opener = self;
		}
	}
	if (r == 1)
	{
		return remote;
	}
}

function Attach(url) 
{
	if (!attw || attw.closed)
	{
		attw = createWindow(url, 'attachwin', 500, 400, 1);
	}
	attw.focus();
}

function toggle_tr(target)
{
	obj = fetch_js_object(target);
	obj.style.display = (obj.style.display == 'none') ? 'inline' : 'none';
}

function toggle_more(target, showmoreid, moretext, lesstext, showmoreicon)
{
	obj = fetch_js_object(target);
	
	if (obj.style.display == 'none') 
	{
		obj.style.display = '';
		update_js_collapse_cookie(target, true, ILNAME + 'collapse');
	}
	else 
	{
		obj.style.display = 'none';
		update_js_collapse_cookie(target, false, ILNAME + 'collapse');
	}
	
	obj2 = fetch_js_object(showmoreid);
	obj2.style.fontweight = 'bold';
	obj2.innerHTML = (obj.style.display == 'none')
		? moretext
		: lesstext;
	
	obj3 = fetch_js_object(showmoreicon);
	obj3.src = (obj.style.display == 'none')
		? IMAGEBASE + 'icons/arrowdown2.gif'
		: IMAGEBASE + 'icons/arrowup2.gif';
	
	return false;
}

function toggle_paid(target) 
{
	obj = fetch_js_object(target);
	if (obj.style.display == 'none') 
	{
		obj.style.display = 'inline';
	}
}

function toggle_free(target) 
{
	obj = fetch_js_object(target);
	if (obj.style.display == 'inline') 
	{
		obj.style.display = 'none';
	}
}

function toggle_bid_row(objid)
{
	//[*]customersalt[*] = fetch_js_object(objid);
	//[*]customersalt[*].style.display = ([*]customersalt[*].style.display == 'none') ? '' : 'none';
}

function toggle_hide(target)
{
	obj = fetch_js_object(target);
	if(obj!=null)
	{
	obj.style.display = (obj.style.display == 'none') ? 'none' : 'none';
	}
}

function toggle_show(target)
{
	obj = fetch_js_object(target);
	obj.style.display = (obj.style.display == 'none') ? '' : '';
}

function trim(field)
{
	value = field;
	while (value.charAt(value.length - 1) == " ") 
	{
		value = value.substring(0, value.length-1);
	} 
	while (value.substring(0, 1) == " ") 
	{
		value = value.substring(1, value.length);
	}
	
	return value;
}

function popUP(mypage, myname, w, h, scroll, titlebar) 
{
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable'
	win = window.open(mypage, myname, winprops)
	if (parseInt(navigator.appVersion) >= 4) 
	{
		win.window.focus();
	}
}

function urlswitch(styleobj, _type)
{
	var themeid = styleobj.options[styleobj.selectedIndex].value;
	if (themeid == '')
	{
		return;
	}
	var url = new String(window.location);
	var fragment = new String('');
	url = url.split('#');
	if (url[1])
	{
		fragment = '#' + url[1];
	}
	url = url[0];
	if (_type == 'dostyle')
	{
		if (url.indexOf('styleid=') != -1)
		{
			re = new RegExp("styleid=\\d+&?");
			url = url.replace(re, '');
		}
	}
	else
	{
	    if (url.indexOf('language=') != -1)
	    {
		    re = new RegExp("language=\\d+&?");
		    url = url.replace(re, '');
	    }
	}
	if (url.indexOf('?') == -1)
	{
		url += '?';
	}
	else
	{
		endchar = url.substr(url.length - 1);
		if (endchar != '&' && endchar != '?')
		{
			url += '&';
		}
	}
	if (_type == 'dostyle')
	{
		window.location = url + 'styleid=' + themeid + fragment;
	}
	else
	{
		window.location = url + 'language=' + themeid + fragment;
	}
}

function textcount(field, countfield, maxlimit) 
{
	if (field.value.length > maxlimit)
	{
		field.value = field.value.substring(0, maxlimit);
	}
	else
	{
		countfield.value = maxlimit - field.value.length;
	}
}

function populate_countries_pulldown(cboCountry, cboState, sDefaultCountry)
{
	var sDefault, sCountry;
	var selectOption = -1;
	
	cboCountry.options.length = 0;
	
	for (i = 0; i < sCountryString.split("|").length; i++)
	{
		sCountry = sCountryString.split("|")[i];
		if (sDefaultCountry == sCountry)
		{
			sDefault = true;
			if (navigator.appName == "Microsoft Internet Explorer")
			{
				//cboState.focus();
			}
		}
		else
		{
			sDefault = false;
		}
		if (sDefault)
		{
			var oo = new Option(sCountry, sCountry, sDefault, sDefault);
			oo.setAttribute("selected", "true");
			cboCountry.options[i] = oo;
			selectOption = i;
		}
		else
		{
			cboCountry.options[i] = new Option(sCountryString.split("|")[i]);
		}
	}
	if (selectOption > -1)
	{
		cboCountry.selectedIndex = selectOption;
	}
}

function populate_states_pulldown(cboCountry, cboState, sDefaultState)
{
	var sState, sDefault;
	var selectOption = -1;
	
	cboState.options.length = 0;
	
	for (i = 0; i < sStateArray[cboCountry.selectedIndex].split("|").length; i++)
	{
		sState = sStateArray[cboCountry.selectedIndex].split("|")[i];
		if (sDefaultState == sState)
		{
			sDefault = true;
			selectOption = i;
			if (navigator.appName == "Microsoft Internet Explorer")
			{
				//cboState.focus();
			}
		}
		else
		{
			sDefault = false;
		}
		if (sDefault)
		{
			var oo = new Option(sState, sState, sDefault, sDefault);
			oo.setAttribute("selected", "true");
			cboState.options[i] = oo;	
		}
		else
		{
			cboState.options[i] = new Option(sState,sState);
		}
	}
	if (selectOption > -1)
	{
		cboState.selectedIndex = selectOption;
	}
}

function populate_cities_pulldown(cboState, cboCity, sDefaultCity)
{
	var sState, sCity, sDefault;
	var selectOption = -1;
	
	cboCity.options.length = 0;
	
	for (i = 0; i < sCityArray[cboState[cboState.selectedIndex].text].split("|").length; i++)
	{
		sCity = sCityArray[cboState[cboState.selectedIndex].text].split("|")[i];
		if (sDefaultCity == sCity)
		{
			sDefault = true;
			selectOption = i;
			if (navigator.appName == "Microsoft Internet Explorer")
			{
				//cboCity.focus();
			}
		}
		else
		{
			sDefault = false;
		}
		if (sDefault)
		{
			var oo = new Option(sCity, sCity, sDefault, sDefault);
			oo.setAttribute("selected", "true");
			cboCity.options[i] = oo;	
		}
		else
		{
			cboCity.options[i] = new Option(sCity, sCity);
		}
	}
	if (selectOption > -1)
	{
		cboCity.selectedIndex = selectOption;
	}
}

function fetch_tags(parentobj, tag)
{
	if (parentobj == null)
	{
		return new Array();
	}
	else if (typeof parentobj.getElementsByTagName != 'undefined')
	{
		return parentobj.getElementsByTagName(tag);
	}
	else if (parentobj.all && parentobj.all.tags)
	{
		return parentobj.all.tags(tag);
	}
	else
	{
		return new Array();
	}
}

function construct_phrase()
{
	if (!arguments || arguments.length < 1 || !REGEXP_Compatible)
	{
		return false;
	}
	var args = arguments;
	var str = args[0];
	var re;
	for (var i = 1; i < args.length; i++)
	{
		re = new RegExp("%" + i + "\\$s", 'gi');
		str = str.replace(re, args[i]);
	}
	return str;
}

function construct_textarea_height(boxid, pixelvalue)
{
	var box = fetch_js_object(boxid);
	var boxheight = parseInt(box.style.height);
	var newheight = boxheight + pixelvalue;
	if (newheight > 0)
	{
		box.style.height = newheight + "px";
	}
	return false;
}

function mediashare_validate(field, errtext)
{
	if (field.value == '')
	{
		alert('You cannot continue without a ' + errtext);
		return false;
	}
	return true;
}

function unescape_cdata(str)
{
	var r1 = /<\=\!\=\[\=C\=D\=A\=T\=A\=\[/g;
	var r2 = /\]\=\]\=>/g;
	return str.replace(r1, '<![CDATA[').replace(r2, ']]>');
}

function urlencode(text)
{
	text = escape(text.toString()).replace(/\+/g, "%2B");
	var matches = text.match(/(%([0-9A-F]{2}))/gi);
	if (matches)
	{
		for (var matchid = 0; matchid < matches.length; matchid++)
		{
			var code = matches[matchid].substring(1,3);
			if (parseInt(code, 16) >= 128)
			{
				text = text.replace(matches[matchid], '%u00' + code);
			}
		}
	}
	text = text.replace('%25', '%u0025');
	return text;
}

function toggle_tab(tabid, obj, obj2, taboutputdiv)
{
	var linkList;
	var newtab;
	
	linkList = fetch_js_object(tabid).getElementsByTagName('li');
	for (i = 0; i < linkList.length; i++)
	{
		linkList[i].className = '';
	}
	
	fetch_js_object(obj).className = 'on';	
	newtab = fetch_js_object(obj2).innerHTML;
	fetch_js_object(taboutputdiv).innerHTML = newtab;
}

function toggle_big_tab(obj, phrase)
{
	var linkList;
	linkList = fetch_js_object('skilltabs').getElementsByTagName('li');
	for (i = 0; i < linkList.length; i++)
	{
		linkList[i].className = '';
	}
	
	fetch_js_object(obj).className = 'on';
	fetch_js_object('tabtext').innerHTML = phrase;
}

function fetch_watchlist_response()
{
	if (xmldata.handler.readyState == 4 && xmldata.handler.status == 200 && xmldata.handler.responseXML)
	{
		// format response
		response = fetch_tags(xmldata.handler.responseXML, 'status')[0];
		phpstatus = xmldata.fetch_data(response);
		
		//for Bug #5027
		if(phpstatus.indexOf("||")>0)
		{
			phpstatus = phpstatus.split('||');
			status_resp = phpstatus[0];
			prjcid = phpstatus[1];
			usrid = phpstatus[2];
			tracking_count = phpstatus[3]
			if (status_resp == 'addedwatchlist')
			{
				//window.location.reload();
			}
		
			if (status_resp == 'removed_watchlist') 
			{
				fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">You are watching this item</span>';
				window.location.reload();
			}
			
		}
		
		
		if (phpstatus == 'addeditem')
		{
			fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">Watch this item</span>';
			window.location.reload();
		}
		else if (phpstatus == 'alreadyaddeditem')
		{
			fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_this_item_is_already_added_to_your_watchlist'] + '</span>';	
		}
		else if (phpstatus == 'ask_w')
		{
			fetch_js_object('ask_open').innerHTML = '<span class="gray">Your question has been sent to GC</span>';	
			window.location.reload();
		}
		else if (phpstatus == 'ask_w1')
		{
			fetch_js_object('ask_open').innerHTML = '<span class="gray">Successfully sent your questions</span>';	
			window.location.reload();
		}
		//teller friend
		else if (phpstatus == 'fri')
		{
			
			
			fetch_js_object('fri_open').innerHTML = '<span class="gray">Successfully Sent</span>';
			window.location.reload();
		}
		else if (phpstatus == 'friend')
		{
			
			fetch_js_object('fri_open').innerHTML = '<span class="gray">Successfully Sent</span>';	
			window.location.reload();
		}
		///make kannan
		/*else if (phpstatus == 'make')
		{
			fetch_js_object('fri_open').innerHTML = '<span class="gray">' + phrase['_successfully_sent_your_question'] + '</span>';	
			window.location.reload();
		}
		else if (phpstatus == 'make_offer')
		{
			fetch_js_object('fri_open').innerHTML = '<span class="gray">' + phrase['_successfully_sent_your_question'] + '</span>';	
			window.location.reload();
		}*/
		
		
		
		else if (phpstatus == 'addedseller')
		{
			fetch_js_object('sellerwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_seller_is_in_favorites_js'] + '</span>&nbsp;&nbsp;&nbsp;<span class="smaller gray">[ <span class="blue"><a href="' + ILBASE + 'watchlist.php?tab=2">' + phrase['_view_watchlist_js'] + '</a></span> ]</span>';
		}
		else if (phpstatus == 'alreadyaddedseller')
		{
			fetch_js_object('sellerwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_this_seller_is_already_added_to_your_watchlist'] + '</span>';	
		}
		else if (phpstatus == 'error')
		{
			fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
			fetch_js_object('sellerwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_favorite_sellers'] + '</span>';
		}
		
		xmldata.handler.abort();
	}
}


function myedit(projectid, userid, comment)
{
	
	if (userid == '' || userid == 0 || projectid == '' || projectid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
		return false;
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	projectid = urlencode(projectid);
	xmldata.projectid = projectid;
	
	userid = urlencode(userid);
	xmldata.userid = userid;
	
	comment = urlencode(comment);
	xmldata.userid = comment;
	
	xmldata.onreadystatechange(fetch_watchlist_response);
	// send data to php
	
	xmldata.send('ajax.php', 'do=editwatchlist&projectid=' + projectid + '&userid=' + userid +'&comment='+ comment + '&s=' + ILSESSION + '&token=' + ILTOKEN);

}
function add_item_to_watchlist_comment(projectid, userid, comment)
{
	if (userid == '' || userid == 0 || projectid == '' || projectid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
		return false;
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	projectid = urlencode(projectid);
	xmldata.projectid = projectid;
	
	userid = urlencode(userid);
	xmldata.userid = userid;
	
	comment = urlencode(comment);
	xmldata.userid = comment;
	
	xmldata.onreadystatechange(fetch_watchlist_response);
	// send data to php
	
	xmldata.send('ajax.php', 'do=addwatchlist&projectid=' + projectid + '&userid=' + userid +'&comment='+ comment + '&s=' + ILSESSION + '&token=' + ILTOKEN);

}
//ask your question
function askqueadmin(projectid,askmail,askname,askcom,comment)
{
	
	//alert('do=askquestion&projectid=' + projectid + '&askname=' + askname +'&askcom=' + comment +'&comment='+ askcom + '&s=' + ILSESSION + '&token=' + ILTOKEN);
	
	if ( projectid == '' || projectid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
		return false;
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	projectid = urlencode(projectid);
	xmldata.projectid = projectid;
	
	
	
	xmldata.onreadystatechange(fetch_askqus_response);
	
	//alert('do=askquestion&projectid=' + projectid + '&askmail=' + askmail + '&askname=' + askname +'&askcom=' + comment +'&comment='+ askcom + '&s=' + ILSESSION + '&token=' + ILTOKEN);
	// send data to php
	xmldata.send('ajax.php', 'do=askquestion&projectid=' + projectid + '&askmail=' + askmail + '&askname=' + askname +'&askcom=' + comment +'&comment='+ askcom + '&s=' + ILSESSION + '&token=' + ILTOKEN);

}

//test
function askqueadmin_test(projectid,askmail,askname,askcom,comment)
{
	
	if ( projectid == '' || projectid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
		return false;
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	projectid = urlencode(projectid);
	xmldata.projectid = projectid;
	
	
	
	xmldata.onreadystatechange(fetch_askqus_response);
	
	//alert('do=askquestion&projectid=' + projectid + '&askmail=' + askmail + '&askname=' + askname +'&askcom=' + comment +'&comment='+ askcom + '&s=' + ILSESSION + '&token=' + ILTOKEN);
	// send data to php
	xmldata.send('ajax_askqus.php', 'do=askquestion&projectid=' + projectid + '&askmail=' + askmail + '&askname=' + askname +'&askcom=' + comment +'&comment='+ askcom + '&s=' + ILSESSION + '&token=' + ILTOKEN);

}

//test
function askqueadmin1_test(projectid,userid, comment)
{

	if ( projectid == '' || projectid == 0 || userid == '' || userid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
		return false;
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	projectid = urlencode(projectid);
	xmldata.projectid = projectid;
	
	
	xmldata.onreadystatechange(fetch_askqus_response);
	
	//alert('do=askquestion&projectid=' + projectid + '&userid=' + userid +'&comment='+ comment + '&s=' + ILSESSION + '&token=' + ILTOKEN);
	// send data to php
	xmldata.send('ajax_askqus.php', 'do=askquestion&projectid=' + projectid + '&userid=' + userid +'&comment='+ comment + '&s=' + ILSESSION + '&token=' + ILTOKEN);

}

//work by karthik
function askqueadmin1(projectid,userid, comment)
{
		
	if ( projectid == '' || projectid == 0 || userid == '' || userid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
		return false;
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	projectid = urlencode(projectid);
	xmldata.projectid = projectid;
	
	
	xmldata.onreadystatechange(fetch_askqus_response);
	
	//alert('do=askquestion&projectid=' + projectid + '&userid=' + userid +'&comment='+ comment + '&s=' + ILSESSION + '&token=' + ILTOKEN);
	// send data to php
	xmldata.send('ajax.php', 'do=askquestion&projectid=' + projectid + '&userid=' + userid +'&comment='+ comment + '&s=' + ILSESSION + '&token=' + ILTOKEN);

}
// teller friend
function teller_friend(pro,uid,msm,to,subj,frm)
{
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	pro = urlencode(pro);
	xmldata.projectid = pro;
	/*
	userid = urlencode(userid);
	xmldata.userid = userid;
	
	message = urlencode(message);
	xmldata.userid = message;
	
	to = urlencode(to);
	xmldata.userid = to;
	
	subject = urlencode(subject);
	xmldata.userid = subject;*/
	
	xmldata.onreadystatechange(fetch_watchlist_response);
	
	//alert('do=tellfriend&projectid=' + projectid + '&userid=' + userid +'&message='+ message +'&to='+ to +'&subject='+ subject +'&s=' + ILSESSION + '&token=' + ILTOKEN);
	// send data to phpfrm
	
	//alert('do=tellfriend&projectid=' + pro + '&userid=' + uid +'&message='+ msm +'&to='+ to +'&subject='+ subj +'&frm='+ frm +'&s=' + ILSESSION + '&token=' + ILTOKEN);
	xmldata.send('ajax.php', 'do=tellfriend&projectid=' + pro + '&userid=' + uid +'&message='+ msm +'&to='+ to +'&subject='+ subj +'&frm='+ frm +'&s=' + ILSESSION + '&token=' + ILTOKEN);

}

//work by karthik
function teller_friend1(projectid, ymail, message, to, subject)
{
	
	
	if ( projectid == '' || projectid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
		return false;
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	projectid = urlencode(projectid);
	xmldata.projectid = projectid;
	
	ymail = urlencode(ymail);
	xmldata.ymail = ymail;
	
	message = urlencode(message);
	xmldata.userid = message;
	
	to = urlencode(to);
	xmldata.userid = to;
	
	subject = urlencode(subject);
	xmldata.userid = subject;
	
	xmldata.onreadystatechange(fetch_watchlist_response);
	alert('sdfasdf');
	alert('do=tellfriend&projectid=' + projectid + '&ymail=' + ymail +'&message='+ message +'&to='+ to +'&subject='+ subject +'&s=' + ILSESSION + '&token=' + ILTOKEN);
	// send data to php
	xmldata.send('ajax.php', 'do=tellfriend&projectid=' + projectid + '&ymail=' + ymail +'&message='+ message +'&to='+ to +'&subject='+ subject +'&s=' + ILSESSION + '&token=' + ILTOKEN);

}

//make kannan
//make kannan
function make_admin(projectid, des, subj, mail)
{
	
	
	if (projectid == '' || projectid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
		return false;
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	projectid = urlencode(projectid);
	xmldata.projectid = projectid;	 
	
	des = urlencode(des);
	xmldata.userid = des;
	
	subj = urlencode(subj);
	xmldata.userid = subj;
	
	mail = urlencode(mail);
	xmldata.userid = mail;
	
	xmldata.onreadystatechange(fetch_watchlist_response);
	
		//alert('do=makeadmin&projectid=' + projectid +'&des='+ des +'&mail='+ mail +'&subj='+ subj +'&s=' + ILSESSION + '&token=' + ILTOKEN);
	// send data to php
	xmldata.send('ajax.php', 'do=makeadmin&projectid=' + projectid +'&des='+ des +'&mail='+ mail +'&subj='+ subj +'&s=' + ILSESSION + '&token=' + ILTOKEN);

}


function add_item_to_watchlist(projectid, userid)
{
	if (userid == '' || userid == 0 || projectid == '' || projectid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_items_to_watchlist'] + '</span>';
		return false;
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	projectid = urlencode(projectid);
	xmldata.projectid = projectid;
	
	userid = urlencode(userid);
	xmldata.userid = userid;
	
	xmldata.onreadystatechange(fetch_watchlist_response);

	// send data to php
	xmldata.send('ajax.php', 'do=addwatchlist&projectid=' + projectid + '&userid=' + userid + '&s=' + ILSESSION + '&token=' + ILTOKEN);
}

function add_seller_to_watchlist(sellerid, userid)
{
	if (userid == '' || userid == 0 || sellerid == '' || sellerid == 0)
	{
		fetch_js_object('itemwatchlistresponse').innerHTML = '<span class="gray">' + phrase['_please_signin_to_save_favorite_sellers'] + '</span>';
		return false;	
	}
	
	// set ajax handler
	xmldata = new AJAX_Handler(true);
	
	// url encode the vars
	sellerid = urlencode(sellerid);
	xmldata.sellerid = sellerid;
	
	userid = urlencode(userid);
	xmldata.userid = userid;
	
	xmldata.onreadystatechange(fetch_watchlist_response);

	// send data to php
	xmldata.send('ajax.php', 'do=addwatchlist&sellerid=' + sellerid + '&userid=' + userid + '&s=' + ILSESSION + '&token=' + ILTOKEN);
}

function print_states(fieldname, countryfieldname, divstateid)
{
	var ajaxRequest;
	
	//var ajaxDisplay = fetch_js_object(divstateid);
	//var ajaxDisplay = fetch_js_object(fieldname).disabled = true;
	fetch_js_object(fieldname).disabled = true;
	//ajaxDisplay.innerHTML = "<div style=\"height:25px; padding-top:6px\"><img src=\"" + IMAGEBASE + "working.gif\" border=\"0\" alt=\"\" id=\"working\" /></div>";
	try
	{
		ajaxRequest = new XMLHttpRequest();
	}
	catch (e)
	{
		try
		{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		}	
		catch (e)
		{
			try
			{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				return false;
			}
		}
	}
	
	ajaxRequest.onreadystatechange = function()
	{
		if (ajaxRequest.readyState == 4)
		{
			fetch_js_object(fieldname).disabled = false;
			var ajaxDisplay = fetch_js_object(divstateid);
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
			
		}
	}
	
	var countryname = fetch_js_object(countryfieldname).options[fetch_js_object(countryfieldname).selectedIndex].value;
	var querystring = "&countryname=" + countryname + "&fieldname=" + fieldname + "&s=" + ILSESSION + "&token=" + ILTOKEN;

	ajaxRequest.open('GET', ILBASE + 'ajax.php?do=showstates' + querystring, true);
	ajaxRequest.send(null);
}

function print_shipping_services(divcontainer, fieldname, domestic, international, shipperid)
{
	var ajaxRequest;
	fetch_js_object(divcontainer).disabled = true;
	try
	{
		ajaxRequest = new XMLHttpRequest();
	}
	catch (e)
	{
		try
		{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		}	
		catch (e)
		{
			try
			{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				return false;
			}
		}
	}
	ajaxRequest.onreadystatechange = function()
	{
		if (ajaxRequest.readyState == 4)
		{
			fetch_js_object(divcontainer).disabled = false;
			var ajaxDisplay = fetch_js_object(divcontainer);
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
			
		}
	}
	var querystring = "&fieldname=" + fieldname + "&domestic=" + domestic + "&international=" + international + "&shipperid=" + shipperid + "&s=" + ILSESSION + "&token=" + ILTOKEN;
	ajaxRequest.open('GET', ILBASE + 'ajax.php?do=showshippers' + querystring, true);
	ajaxRequest.send(null);
}

function get_html_translation_table(table, quote_style)
{
	// http://kevin.vanzonneveld.net
	// +   original by: Philip Peterson
	// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   bugfixed by: noname
	// %          note: It has been decided that we're not going to add global
	// %          note: dependencies to php.js. Meaning the constants are not
	// %          note: real constants, but strings instead. integers are also supported if someone
	// %          note: chooses to create the constants themselves.
	// %          note: Table from http://www.the-art-of-web.com/html/character-codes/
	// *     example 1: get_html_translation_table('HTML_SPECIALCHARS');
	// *     returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}
	
	var entities = {}, histogram = {}, decimal = 0, symbol = '';
	var constMappingTable = {}, constMappingQuoteStyle = {};
	var useTable = {}, useQuoteStyle = {};
	
	useTable      = (table ? table.toUpperCase() : 'HTML_SPECIALCHARS');
	useQuoteStyle = (quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT');
	
	// Translate arguments
	constMappingTable[0]      = 'HTML_SPECIALCHARS';
	constMappingTable[1]      = 'HTML_ENTITIES';
	constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
	constMappingQuoteStyle[2] = 'ENT_COMPAT';
	constMappingQuoteStyle[3] = 'ENT_QUOTES';
	
	// Map numbers to strings for compatibilty with PHP constants
	if (!isNaN(useTable)) {
	    useTable = constMappingTable[useTable];
	}
	if (!isNaN(useQuoteStyle)) {
	    useQuoteStyle = constMappingQuoteStyle[useQuoteStyle];
	}
	
	if (useQuoteStyle != 'ENT_NOQUOTES') {
	    entities['34'] = '&quot;';
	}
     
	if (useQuoteStyle == 'ENT_QUOTES') {
	    entities['39'] = '&#039;';
	}
     
	if (useTable == 'HTML_SPECIALCHARS') {
	    // ascii decimals for better compatibility
	    entities['38'] = '&amp;';
	    entities['60'] = '&lt;';
	    entities['62'] = '&gt;';
	} else if (useTable == 'HTML_ENTITIES') {
	    // ascii decimals for better compatibility
	  entities['38']  = '&amp;';
	  entities['60']  = '&lt;';
	  entities['62']  = '&gt;';
	  entities['160'] = '&nbsp;';
	  entities['161'] = '&iexcl;';
	  entities['162'] = '&cent;';
	  entities['163'] = '&pound;';
	  entities['164'] = '&curren;';
	  entities['165'] = '&yen;';
	  entities['166'] = '&brvbar;';
	  entities['167'] = '&sect;';
	  entities['168'] = '&uml;';
	  entities['169'] = '&copy;';
	  entities['170'] = '&ordf;';
	  entities['171'] = '&laquo;';
	  entities['172'] = '&not;';
	  entities['173'] = '&shy;';
	  entities['174'] = '&reg;';
	  entities['175'] = '&macr;';
	  entities['176'] = '&deg;';
	  entities['177'] = '&plusmn;';
	  entities['178'] = '&sup2;';
	  entities['179'] = '&sup3;';
	  entities['180'] = '&acute;';
	  entities['181'] = '&micro;';
	  entities['182'] = '&para;';
	  entities['183'] = '&middot;';
	  entities['184'] = '&cedil;';
	  entities['185'] = '&sup1;';
	  entities['186'] = '&ordm;';
	  entities['187'] = '&raquo;';
	  entities['188'] = '&frac14;';
	  entities['189'] = '&frac12;';
	  entities['190'] = '&frac34;';
	  entities['191'] = '&iquest;';
	  entities['192'] = '&Agrave;';
	  entities['193'] = '&Aacute;';
	  entities['194'] = '&Acirc;';
	  entities['195'] = '&Atilde;';
	  entities['196'] = '&Auml;';
	  entities['197'] = '&Aring;';
	  entities['198'] = '&AElig;';
	  entities['199'] = '&Ccedil;';
	  entities['200'] = '&Egrave;';
	  entities['201'] = '&Eacute;';
	  entities['202'] = '&Ecirc;';
	  entities['203'] = '&Euml;';
	  entities['204'] = '&Igrave;';
	  entities['205'] = '&Iacute;';
	  entities['206'] = '&Icirc;';
	  entities['207'] = '&Iuml;';
	  entities['208'] = '&ETH;';
	  entities['209'] = '&Ntilde;';
	  entities['210'] = '&Ograve;';
	  entities['211'] = '&Oacute;';
	  entities['212'] = '&Ocirc;';
	  entities['213'] = '&Otilde;';
	  entities['214'] = '&Ouml;';
	  entities['215'] = '&times;';
	  entities['216'] = '&Oslash;';
	  entities['217'] = '&Ugrave;';
	  entities['218'] = '&Uacute;';
	  entities['219'] = '&Ucirc;';
	  entities['220'] = '&Uuml;';
	  entities['221'] = '&Yacute;';
	  entities['222'] = '&THORN;';
	  entities['223'] = '&szlig;';
	  entities['224'] = '&agrave;';
	  entities['225'] = '&aacute;';
	  entities['226'] = '&acirc;';
	  entities['227'] = '&atilde;';
	  entities['228'] = '&auml;';
	  entities['229'] = '&aring;';
	  entities['230'] = '&aelig;';
	  entities['231'] = '&ccedil;';
	  entities['232'] = '&egrave;';
	  entities['233'] = '&eacute;';
	  entities['234'] = '&ecirc;';
	  entities['235'] = '&euml;';
	  entities['236'] = '&igrave;';
	  entities['237'] = '&iacute;';
	  entities['238'] = '&icirc;';
	  entities['239'] = '&iuml;';
	  entities['240'] = '&eth;';
	  entities['241'] = '&ntilde;';
	  entities['242'] = '&ograve;';
	  entities['243'] = '&oacute;';
	  entities['244'] = '&ocirc;';
	  entities['245'] = '&otilde;';
	  entities['246'] = '&ouml;';
	  entities['247'] = '&divide;';
	  entities['248'] = '&oslash;';
	  entities['249'] = '&ugrave;';
	  entities['250'] = '&uacute;';
	  entities['251'] = '&ucirc;';
	  entities['252'] = '&uuml;';
	  entities['253'] = '&yacute;';
	  entities['254'] = '&thorn;';
	  entities['255'] = '&yuml;';
	}
	else
	{
		return false;
	}
	
	// ascii decimals to real symbols
	for (decimal in entities)
	{
		symbol = String.fromCharCode(decimal)
		histogram[symbol] = entities[decimal];
	}
	
	return histogram;
}

function html_entity_decode(string, quote_style)
{
	// http://kevin.vanzonneveld.net
	// +   original by: john (http://www.jd-tech.net)
	// +      input by: ger
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   bugfixed by: Onno Marsman
	// +   improved by: marc andreu
	// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// -    depends on: get_html_translation_table
	// *     example 1: html_entity_decode('Kevin &amp; van Zonneveld');
	// *     returns 1: 'Kevin & van Zonneveld'
	// *     example 2: html_entity_decode('&amp;lt;');
	// *     returns 2: '&lt;'
     
	var histogram = {}, symbol = '', tmp_str = '', entity = '';
	tmp_str = string.toString();
	
	if (false === (histogram = get_html_translation_table('HTML_ENTITIES', quote_style)))
	{
		return false;
	}
     
	// &amp; must be the last character when decoding!
	delete(histogram['&']);
	histogram['&'] = '&amp;';
     
	for (symbol in histogram)
	{
		entity = histogram[symbol];
		tmp_str = tmp_str.split(entity).join(symbol);
	}
	
	return tmp_str;
}

function htmlspecialchars(string, quote_style)
{
	// http://kevin.vanzonneveld.net
	// +   original by: Mirek Slugen
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   bugfixed by: Nathan
	// +   bugfixed by: Arno
	// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// -    depends on: get_html_translation_table
	// *     example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
	// *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
     
	var histogram = {}, symbol = '', tmp_str = '', entity = '';
	tmp_str = string.toString();
	
	if (false === (histogram = get_html_translation_table('HTML_SPECIALCHARS', quote_style)))
	{
		return false;
	}
	
	for (symbol in histogram)
	{
		entity = histogram[symbol];
		tmp_str = tmp_str.split(symbol).join(entity);
	}
	
	return tmp_str;
}

function show_category_popup_link() 
{
	if (category_popup_timer != null) 
	{
		clearTimeout(category_popup_timer);
		category_popup_timer = null;
	}

	divObj = fetch_js_object('category_popup');
	divObj.style.display = '';
}

function show_category_popup() 
{
	div = fetch_js_object('category_popup');
	category_popup_timer = setTimeout("do_show_category_popup();", 750);
}

function hide_category_popup() 
{
	if (category_popup_timer != null) 
	{
		clearTimeout(category_popup_timer);
		category_popup_timer = null;
	}
	
	div = fetch_js_object('category_popup');
	category_popup_timer = setTimeout("do_hide_category_popup();", 550);
}

function do_hide_category_popup() 
{
	divObj = fetch_js_object('category_popup');
	divObj.style.display = 'none';
}

function do_show_category_popup() 
{
	if (category_popup_timer != null) 
	{
		clearTimeout(category_popup_timer);
		category_popup_timer = null;
	}

	divObj = fetch_js_object('category_popup');
	divObj.style.display = '';
}

function ie_prompt(str)
{
	var settings = 'dialogWidth: 350px; dialogHeight: 160px; center: yes; edge: raised; scroll: no; status: no;';
	var response = window.showModalDialog(IMAGEBASE + 'ie_prompt.html', str, settings);
	return response;
}

function ilance_prompt(str)
{
	try
	{
		if (window.showModalDialog)
		{
			return ie_prompt(str);
		}
		else
		{
			return prompt(str, '');
		}
	}
	
	catch (e)
	{ 
		return false; 
	} 	
}

function livefeecalculator(fee, type, objid)
{
	if (type == 'single')
	{
		if (fetch_js_object(objid).checked)
		{
			// adding new fee
			//alert('add: ' + fee);	
		}
		else
		{
			// removing old fee
			//alert('rem: ' + fee);
		}
	}
	else if (type == 'bulk')
	{
		if (fetch_js_object(objid).checked)
		{
			// adding new fee
			//alert('add: ' + fee);	
		}
		else
		{
			// removing old fee
			//alert('rem: ' + fee);
		}	
	}
}

function string_to_number(price)
{
	var thous = " '", decPoint, decComma, decMark, dec, re, matches, pre = '3';
	
	price = price.replace(/^\s+|\s+$/g, "");
	decPoint = price.lastIndexOf('.');
	decComma = price.lastIndexOf(',');
	
	if (decPoint > -1 && decComma > -1)
	{
		if (decPoint > decComma)
		{
			thous += ',';
		}
		else
		{
			thous += '.';
			decMark = ',';
		}
	}
	
	if ((price.indexOf(' ') > 0 || price.indexOf("'") > 0) && decComma)
	{
		decMark = ',';
	}
	
	if (price.substring(decPoint + 1).length === 3 && decComma < 1 && price.indexOf('.') < decPoint)
	{
		thous += '.';
	}
	
	if (price.substring(decComma + 1).length === 3 && decPoint < 1 && price.indexOf(',') < decComma)
	{
		thous += ',';
	}
	
	re = new RegExp("^(?:(\\d{1,3}(?:(?:(?:[" + thous + "]\\d{3})+)?)?|\\d+)?([,.]\\d{1,})?|\\d+)$");
	matches = re.exec(price);
	
	if (!matches)
	{
		return false;
	}
	
	if (!matches[1] && !matches[2])
	{
		matches[1] = matches[0];
	}
	
	dec = matches[2] && matches[2].length === 4 && !decMark && matches[1] !== '0' ? '' : '.';
	
	return (matches[1] || '').replace(/[,' .]/g, '') + '' + (matches[2] || '').replace(',', dec);
}

function disable_submit_button(theform)
{
	if (document.all || document.getElementById)
	{
		for (i = 0; i < theform.length; i++)
		{
			var tempobj = theform.elements[i];
			if (tempobj.type.toLowerCase() == 'submit' || tempobj.type.toLowerCase() == 'reset')
			{
				tempobj.disabled = true;
			}
		}
		
		setTimeout('alert("Your bulk upload is currently being processed.  Please allow some time to process and validate your input.  Thank you.")', 2000);
		
		return true;
	}
}

function rollovericon(img_name, img_src)
{
	document[img_name].src = img_src;
}

/*======================================================================*\
|| ####################################################################
|| # CVS: $RCSfile: functions.js,v $ - $Revision: 1.00 $
|| ####################################################################
\*======================================================================*/