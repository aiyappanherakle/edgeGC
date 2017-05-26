<?php
/*==========================================================================*\
|| ######################################################################## ||
|| # ILance Marketplace Software 3.2.0 Build 1352
|| # -------------------------------------------------------------------- # ||
|| # Customer License # LuLTJTmo23V1ZvFIM-KH-jOYZjfUFODRG-mPkV-iVWhuOn-b=L
|| # -------------------------------------------------------------------- # ||
|| # Copyright ©2000–2010 ILance Inc. All Rights Reserved.                # ||
|| # This file may not be redistributed in whole or significant part.     # ||
|| # ----------------- ILANCE IS NOT FREE SOFTWARE ---------------------- # ||
|| # http://www.ilance.com | http://www.ilance.com/eula	| info@ilance.com # ||
|| # -------------------------------------------------------------------- # ||
|| ######################################################################## ||
\*==========================================================================*/
// #### load required phrase groups ############################################
$phrase['groups'] = array(
	'rfp',
	'search',
	'accounting',
	'buying',
	'selling',
	'subscription',
	'feedback'
);
// #### load required javascript ###############################################
$jsinclude = array(
	'functions',
	/* 'ajax', */
	'inline',
	'cron',
	/* 'autocomplete', */
	/* 'jquery', */
	/* 'modal', */
	'flashfix'
);
// #### define top header nav ##################################################
$topnavlink = array(
	'main_listings'
);
// #### setup script location ##################################################
define('LOCATION', 'main');

// #### require backend ########################################################
require_once('./functions/config.php');
require_once(DIR_CORE . 'functions_search.php');
require_once(DIR_CORE . 'functions_search_prefs.php');
$ilance->bid = construct_object('api.bid');
$ilance->bid_lowest_unique = construct_object('api.bid_lowest_unique');
$ilance->bid_proxy = construct_object('api.bid_proxy');

// #### require shipping backend #######################
require_once(DIR_CORE . 'functions_shipping.php');

date_default_timezone_set('America/Los_Angeles');

if (True)	// debugging mode for PHP and MySQL
{
	error_reporting(E_ALL);
	ini_set('display_errors', 'stdout');
	restore_error_handler();
	mysqli_report(MYSQLI_REPORT_STRICT);  // MYSQLI_REPORT_ALL also fails on warnings, like that an index isn't available to optimize a query
}
define('THIS_SCRIPT',HTTP_SERVER.'/auction_archive.php');
define('ISMARK',($_SERVER['SERVER_SOFTWARE'] == 'Microsoft-IIS/7.5' && $_SERVER['APPL_PHYSICAL_PATH'] == 'C:\\GC\\preProduction\\'));	// set if Mark devel server
define('UNGRADED','<span style="font-size: 18px;">Ungraded</span>');
// define('UNGRADED','<img src="'.HTTP_SERVER.'/images/Ungraded.png" width="23" height="44" />');
// define('UNGRADED','<div style="text-align: right; position: absolute; top: 35px; left: -25px; font-size: 14px; font-weight: bold; -ms-transform: rotate(270deg); -webkit-transform: rotate(270deg); transform: rotate(270deg);">Ungraded</div>&nbsp;&nbsp;');
define('MAX_COINS_TREE', 16);			// maximum coins displayable in nav tree, after that they need to go into main body pane
define('TOP_COIN_PRICE', 10);			// # of top coins to randomly select from, when automatically generating featured coin for any category
define('TOP_COIN_HIST', 6);				// # of top coins to list in the price history area
$gradeList = array(
	1=>'PO',
	2=>'FR',
	3=>'AG',
	4=>'G',
	6=>'G',
	8=>'VG',
	10=>'VG',
	12=>'F',
	15=>'F',
	20=>'VF',
	25=>'VF',
	30=>'VF',
	35=>'VF',
	40=>'XF',
	45=>'XF',
	50=>'AU',
	53=>'AU',
	55=>'AU',
	58=>'AU',
	60=>'MS/PR',
	61=>'MS/PR',
	62=>'MS/PR',
	63=>'MS/PR',
	64=>'MS/PR',
	65=>'MS/PR',
	66=>'MS/PR',
	67=>'MS/PR',
	68=>'MS/PR',
	69=>'MS/PR',
	70=>'MS/PR'
);
// echo count($gradeList); exit;

if (False)	// TODO: Detect old Auction Archive
{
	/* In index.php, replace 4 "CoinPrices":
	$new_list[] = array( 'url'=>'/^([Cc]+)oin([Pp]+)rices$/',
		'file'=>'denomination.php',
		'line'=>__LINE__,
		'parameters'=>array(   'cmd'=>'CoinPrices',
		) );
	$new_list[] = array( 'url'=>'/^([Cc]+)oin([Pp]+)rices\/$/',
		'file'=>'denomination.php',
		'line'=>__LINE__,
		'parameters'=>array(   'cmd'=>'CoinPrices',
		) );
	$new_list[] = array( 'url'=>'/^([Cc]+)oin([Pp]+)rice$/',
		'file'=>'denomination.php',
		'line'=>__LINE__,
		'parameters'=>array(   'cmd'=>'CoinPrices',
		) );
	$new_list[] = array( 'url'=>'/^([Cc]+)oin([Pp]+)rice\/$/',
		'file'=>'denomination.php',
		'line'=>__LINE__,
		'parameters'=>array(   'cmd'=>'CoinPrices',
		) );
	WITH THIS:
	$new_list[] = array( 'url'=>'/^CoinPrices?$/',
		'file'=>'denomination.php',
		'line'=>__LINE__,
		'parameters'=>array(   'cmd'=>'CoinPrices',
		) );
	$new_list[] = array( 'url'=>'#^CoinPrices?(/(US-Coin-Prices))?(/([0-9]+)/[^/]+)?(/([0-9]+)/[^/]+)?(/([0-9]+)/[^/]+)?/?$#i',
		'file'=>'auction_archive.php',
		'line'=>__LINE__,
		'parameters'=>array(
			'universe'=>'$2',
			'denomination'=>'$4',
			'coin_series'=>'$6',
			'coin'=>'$8',
			'old'=>'$8',
		));

	*/
	// Universe: http://www.greatcollections.com/CoinPrices/
	// Denom: http://www.greatcollections.com/CoinPrices/2/Half-CentsLarge-Cents
	// Coin Series: http://www.greatcollections.com/CoinPrices/SeriesCoin/6/Classic-Half-Cents
	
	// Do a silent redirect, or a 301 as below:
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: http://www.new-url.com');
}

// echo '<PRE>'; print_r($_GET); echo '</PRE>'; exit;
/*
	figure out where we are in the navigation
	Sample URI: Auction-Archive/US-Coins/4/Small-Cents/17/Lincoln-Cents
	$_GET['universe'] = 'US-Coins';
	$_GET['denomination'] = '4';	// Small-Cents
	$_GET['coin_series'] = '17';	// 1909-1957 Lincoln Cents
	$_GET['coin'] = '3379';			// 1957 Lincoln Cent
	
http://127.0.0.1/gc/Auction-Archive/US-Coins/4/Small-Cents/17/1909-1957-Lincoln-Cents/3379/1957-Lincoln-Cent
http://127.0.0.1/gc/Auction-Archive/US-Coins/4/Small-Cents/15/Flying-Eagle-Cents/2016/1857-Flying-Eagle-Cent/
http://127.0.0.1/gc/Auction-Archive/US-Coins/8/Half-Dimes/27/Capped-Bust-Half-Dimes/4276/1829-Capped-Bust-Half-Dime
*/
if (!@$_GET['universe'])	// if no top-level universe found
	$_GET['universe'] = 'US-Coin-Prices';	// default

// input and global variables, also needed in descBody and histBody functions
$AucArchiveURI = 'Auction-Archive';
$universeDesc = nameURLdecode($_GET['universe']);
$universeID = ($universeDesc=='US Coin Prices'?1:0);
$denominationID = intval(@$_GET['denomination']);
$denominationDesc = '';
$coin_seriesID = intval(@$_GET['coin_series']);
$coin_seriesDesc = '';
$catRow = array();
$denominationDesc = '';	// long denom
$coin_seriesDesc = '';	// coin series
$coinID = intval(@$_GET['coin']);
$coinDesc = '';
$coinRow = array();

if ($denominationID)
{
	$sqlField = "SELECT *, den.id AS den_id";
	$sqlTable = "FROM ".DB_PREFIX. "catalog_toplevel den";
	$sqlWhere = "WHERE den.id='$denominationID'";
	// ORDER BY den.denomination_sort"
	if ($coin_seriesID)
	{
		$sqlField .= ", cs.id AS cs_id";
		$sqlTable .= " LEFT JOIN ".DB_PREFIX. "catalog_second_level cs ON cs.coin_series_denomination_no=den.id";
		$sqlWhere .= " AND cs.id='$coin_seriesID'";
	}
	// echo 'SQL = '."$sqlField $sqlTable $sqlWhere"; exit;
	$select = $ilance->db->query("$sqlField $sqlTable $sqlWhere");
	// echo 'Num rows = '.$ilance->db->num_rows($select); exit;
	$catRow = $ilance->db->fetch_array($select);
	unset($select);		// this doesn't work: $ilance->db->free_result($select);
	// echo '<PRE>'; print_r($catRow); echo '</PRE>'; exit;
	$denominationDesc = @$catRow['denomination_long'];	// get long denom from database
	$coin_seriesDesc = @$catRow['coin_series_name'];	// get coin series from database
}
// echo 'coinID = '.$coinID; exit;
if ($coinID)
{
	$sql = "SELECT *, cc.id AS cc_id FROM ".DB_PREFIX. "catalog_coin cc WHERE cc.PCGS='$coinID'";
	// echo 'SQL = '."$sql"; exit;
	$select = $ilance->db->query($sql);
	// echo 'Num rows = '.$ilance->db->num_rows($select); exit;
	$coinRow = $ilance->db->fetch_array($select);
	unset($select);		// this doesn't work: $ilance->db->free_result($select);
	// echo '<PRE>'; print_r($coinRow); echo '</PRE>'; exit;
	list($coinDesc) = titleFromFields($coinRow);	// get coin title from database
	// echo '<PRE>'; print_r($coinDesc); echo '</PRE>'; exit;
}

if ($nodeID = @$_GET['ajaxnode'])			// if this is a call for a new AJAX jsTree navigation node
{
	outputTreeHTML($nodeID);				// generate HTML tree code for this node
	exit;
	echo '
		<!--<ul>-->
			<li id="'.$nodeID.'" class="jstree-open">New Node '.$nodeID.'
			<ul>
				<li id="" class="jstree-closed">Node 1</li>
				<li id="" class="jstree-closed">Node 2</li>
			</ul>
		<!--</ul>-->';
	exit;
}

$page_title = SITE_NAME . ' - ' . 'PCGS Coin Auction Archive';
$page_head = '';
$page_desc = '';
$navcrumb = array($AucArchiveURI.'/' => nameURLdecode($AucArchiveURI));

if ($universeID)	// if URL provides the universe (US Coins, World Coins, etc)
{
	$page_title .= ' > '.$universeDesc;
	$navcrumb[$AucArchiveURI.'/'.nameURLencode($universeDesc).'/'] = $universeDesc;
	if ($denominationDesc)	// if URL provides the denomination
	{
		$page_title .= ' > '.$denominationDesc;
		if (!$coin_seriesDesc)	// only need the denom if no coin series given in the URL
			$page_desc .= $denominationDesc.' - ';
		$navcrumb[$AucArchiveURI.'/'.nameURLencode($universeDesc).'/'.$denominationID.'/'.nameURLencode($denominationDesc).'/'] = $denominationDesc;
		// $navcrumb['Auction-Archive/'.$_GET['universe'].'/'.$_GET['denomination'].'/'] = $denominationDesc;
		if ($coin_seriesDesc)	// if URL provides the coin series
		{
			$page_title .= ' > '.$coin_seriesDesc;
			if (!$coinDesc)	// only need the coin series if no final coin given in the URL
				$page_desc .= $coin_seriesDesc.' - ';
			$navcrumb[$AucArchiveURI.'/'.nameURLencode($universeDesc).'/'.$denominationID.'/'.nameURLencode($denominationDesc).'/'.$coin_seriesID.'/'.nameURLencode($coin_seriesDesc).'/'] = $coin_seriesDesc;
			// $navcrumb['Auction-Archive/'.$_GET['universe'].'/'.$_GET['denomination'].'/'.$_GET['coin_series'].'/'] = $coin_seriesDesc;
			if ($coinDesc)	// if URL provides the universe (US Coins, World Coins, etc)
			{
				$page_title .= ' > '.$coinDesc;
				$page_desc .= $coinDesc.' - ';
				$navcrumb[$AucArchiveURI.'/'.nameURLencode($universeDesc).'/'.$denominationID.'/'.nameURLencode($denominationDesc).'/'.$coin_seriesID.'/'.nameURLencode($coin_seriesDesc).'/'.$coinID.'/'.nameURLencode($coinDesc).'/'] = $coinDesc;
			}
		}
	}
	$page_head = $page_desc.$universeDesc.' from our Auction Archive';
	$page_desc = substr($page_desc, 0, -3);
} else	// if nothing defined, show top header title
	$page_head = $page_desc.'Auction Archive';

if (@$_GET['ajaxdesc'])	// if this is a call for a new AJAX description body
{
	descBody();
	// now put some title information into hidden elements, so it can be extracted and placed where it is needed
	?>
	<span id="page_head_feed" style="display: none;"><?php echo $page_head; ?></span>
	<span id="page_title_feed" style="display: none;"><?php echo $page_title; ?></span>
	<?php
	exit;
}
if (@$_GET['ajaxhist'])	// if this is a call for a new AJAX history body
{
	histBody();
	exit;
}

$show['widescreen']=true;

$scriptpage = HTTP_SERVER . $ilpage['search'] . print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);

// $pprint_array = array('html','login_include','c'); 

$surfing_user_id=isset($_SESSION['ilancedata']['user']['userid']) and $_SESSION['ilancedata']['user']['userid']>0?$_SESSION['ilancedata']['user']['userid']:0;

/*
$select_featurednew= $ilance->db->query("select c.date_end,c.project_title,c.currentprice,c.project_id,c.buynow,c.description
										from " . DB_PREFIX . "projects c												
										where (c.status = 'expired' or c.status = 'closed')											
										AND c.visible = '1'										
										$pcgs_grading
										$grading_service
										group by c.project_id
										$orderby LIMIT " . (($ilance->GPC['page'] - 1) * $ilconfig['globalfilters_maxrowsdisplaysubscribers']) . "," . $ilconfig['globalfilters_maxrowsdisplaysubscribers']."
										");
				   
		
$select_featurednew12= $ilance->db->query("select c.date_end,c.project_title,c.currentprice,c.project_id,c.buynow,c.description
											from " . DB_PREFIX . "projects c							
											where (c.status = 'expired' or c.status = 'closed')
											AND c.visible = '1'
											$pcgs_grading
											$grading_service
											group by c.project_id
											$orderby ");
				   
$total_num1=$ilance->db->num_rows($select_featurednew12);

$pprint_array = array('grading_service1','grading_service2','listing','pcgs_no','prof','count','grade_range1','grade_range2','remote_addr','rid','login_include','headinclude','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
$ilance->template->fetch('main', 'pcgs_coin_auction_archive_search.html');
$ilance->template->parse_hash('main', array('ilpage' => $ilpage));	
$ilance->template->parse_loop('main', 'listpage');
$ilance->template->parse_if_blocks('main');
$ilance->template->pprint('main', $pprint_array);
*/
$headinclude .= '
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="functions/javascript/jquery-3.2.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="functions/javascript/jquery.cookie.js"></script>
<script src="functions/javascript/jstree/jstree.min.js"></script>
<link rel="stylesheet" href="functions/javascript/jstree/themes/default/style.min.css" />
<style type="text/css">
.tree li a ins {
	display:none !important;
} 
</style>
';

$pprint_array = array('page_title','headinclude');
$ilance->template->construct_header('main');
$ilance->template->parse_if_blocks('main');
$ilance->template->pprint('main', $pprint_array);
?>

<script type="text/javascript" language="Javascript">
	$(function () {
		// hybrid static and AJAX HTML tree definition: http://stackoverflow.com/questions/30968424/jstree-html-and-json
		var static_html = $('#navtree').html();
		$('#navtree').jstree({
			'core' : {
				"check_callback" : true,
				"data" : function (node, cb) {
					if (node.id === "#") {
						// alert(node.id);
						cb(static_html);
					} else {
						// enhance the AJAX call as needed (verb, data)
						// alert("<?php echo THIS_SCRIPT; ?>?ajaxnode=" + node.id);
						$.ajax({ url : "<?php echo THIS_SCRIPT; ?>?ajaxnode=" + node.id })
							.done(function (data) {
								// alert(data);
								cb(data);
							});
					}
				}
				/*
				'data' : {
					"url" : "<?php echo THIS_SCRIPT; ?>?ajaxnode=1",
					'data' : function (node) {
						// alert(node.id);
						return { 'id' : node.id };
					}
				}
				*/
			}
		});
		$('#navtree').on('select_node.jstree', function (e, data) {
			// alert(data.node.id);
			var level = nodeIDbreakout(data.node.id);	// break nodeID into a level and a database ID
			var dbID = level.dbID;
			if (level.level = 3 && (level.type == 'm1' || level.type == 'm2'))	// Is this a "...more..." nav node?  "L3m1_322"
			{
				// alert(data.node.parent);
				$('#navtree').jstree('select_node', data.node.parent);			// select parent node
				$('#navtree').jstree('deselect_node', data.node.id);			// de-select "more" node
				return;
			}
			level = level.level;

			clearAllNavHightlights();	// clear all arrows and "current" indications

			var IDs = [];				// init IDs
			var titles = [];			// init titles
			var urls = [];				// init urls
			var parentsID = data.node.parents;	// get array of parent IDs
			parentsID.unshift(data.node.id);	// force last level to beginning of this array
			for (var idx = 0; idx < parentsID.length && parentsID[idx] != '#'; idx++)	// loop through current (0) and all parents
			{
				var level = nodeIDbreakout(parentsID[idx]);	// get level, break its ID into a level and a database ID
				var dbID = level.dbID;
				level = level.level;
				if (level < 0)			// if descended below root, stop now
					break;
				IDs[level] = dbID;
				var name = $('#L'+level+'t_'+dbID)[0].innerText;
				titles[level] = name;
				urls[level] = (level>0? dbID + '/': '') + nameURLencode(name) + '/';
				// '<?php echo $AucArchiveURI; ?>/'
				// alert(urls[level]);
			}
			/*
			IDs[0] = 0;	// TODO!  US-Coins!!!!
			titles[0] = '<?php echo nameURLdecode($AucArchiveURI); ?>';
			urls[0] = '<?php echo $AucArchiveURI; ?>/';
			*/
			// Now we have info for all levels
<?php /*
<div style="padding-top:4px; padding-bottom:12px; padding-left:10px; font-size:10px; font-family:verdana">
	<span class="blue"><a href="http://127.0.0.1/gc/" rel="nofollow">Home</a></span>
	<span class="breadcrumb">&nbsp;&gt; <span class="blue"><a href="Auction-Archive/" rel="nofollow">PCGS Coin Auction Archive</a></span></span>
	<span class="breadcrumb">&nbsp;&gt; <span class="blue"><a href="Auction-Archive/US-Coins/" rel="nofollow">US Coins</a></span></span>
	<span class="breadcrumb">&nbsp;&gt; <span class="blue"><a href="Auction-Archive/US-Coins/4/" rel="nofollow">Small Cents</a></span></span>
	<strong><span class="breadcrumb">&nbsp;&gt; <span class="blue"><a href="Auction-Archive/US-Coins/4/17/" rel="nofollow">1909-1957 Lincoln Cents</a></span></span></strong>
</div>
*/ ?>
			var crumbDiv = $('.breadcrumb')[0].parentNode;	// get <div> that encapsulates breadcrumbs
			var patt = /^([\s\S]*?)<span class="breadcrumb">[\s\S]*$/;	// regexp pattern to claim everything after "home" breadcrumb trail
			var crumbDiv2 = patt.exec(crumbDiv.innerHTML);				// get just beginning of original crumb
			crumbDiv2 = crumbDiv2[1]+'<span class="breadcrumb">&nbsp;&gt; <span class="blue"><a href="<?php echo $AucArchiveURI; ?>/" rel="nofollow"><?php echo nameURLdecode($AucArchiveURI); ?></a></span></span>';
				// store original crumb, plus base Auction Archive
			var urlAccum = '<?php echo $AucArchiveURI; ?>/';	// init full URL to new page
			for (var idx = 0; idx < IDs.length; idx++)	// loop through all levels
			{
				// alert(nodes[idx]);
				urlAccum += urls[idx];				// accumulate full URL
				crumbDiv2 += 
				'<span class="breadcrumb">&nbsp;&gt; <span class="blue"'+(idx+1 == IDs.length?' style="font-weight: bold;"':'')+'><a href="'+urlAccum+'" rel="nofollow">'+htmlEscape(titles[idx])+'</a></span></span>';
					// add breadcumb node to breadcrumb code
				// L1a_32982
				if (idx > 0)						// for all levels hiiger than first
				{
					$('#L'+idx+'s_'+IDs[idx])[0].style.color = '#600';	// text is dark red
					$('#L'+idx+'s_'+IDs[idx])[0].style.fontWeight = 'bold';	// text is bold
					if (idx+1 == IDs.length)		// only put arrow on highest level
						$('#L'+idx+'a_'+IDs[idx])[0].style.display = '';	// show arrow nodes
				}
			}
			// alert(crumbDiv2);
			crumbDiv.innerHTML = crumbDiv2;
			<?php // http://stackoverflow.com/questions/28601879/html5-refresh-page-when-popstate-is-fired ?>
			window.historyInitiated = true;
			history.pushState(null, null, urlAccum);
			window.addEventListener("popstate", function(e) {
				if (window.historyInitiated) {
					window.location.reload();
				}
			});
			$.ajax({ url : urlAccum + "?ajaxdesc=1"}) 		// AJAX call for a new descripton body
				.done(function (data) {
					$('#descbody')[0].innerHTML = data;		// replace description body
					$('#page_head')[0].innerHTML = $('#page_head_feed')[0].innerText;	// updated page header title that was tucked into a hidden field of descbody
					document.title = $('#page_title_feed')[0].innerText;		// update page <title> from a hidden field of descbody
					// alert(data);
			});
			histFresh(urlAccum);							// AJAX call for a new history body
		});
	});
	function histFresh(urlAccum)							// AJAX call for a new history body
	{
		if (!urlAccum)										// if no URL given, use current/default URL
		{
			var patt = /^(.*)(\??.*)$/;	// regexp pattern to separate base on GET params of the URL
			urlAccum = patt.exec(window.location.href);
			urlAccum = urlAccum[0];							// ...without any "?" GET params
		}
		$.ajax({ url : urlAccum + "?ajaxhist=1"})
			.done(function (data) {
				$('#histbody')[0].innerHTML = data;
				updateNoItemsMsg1($('#closedcnt')[0].innerHTML, $('#opencnt')[0].innerHTML);
					// update messages if there are no items (pull values stored in hidden <div>s)
				// alert(data);
		});
	}
	<?php insertUpdateNoItemsMsg(1);	// insert the JS function, used in two places ?>
	$.jstree.defaults.core.themes.variant = 'small';<?php // this found method works with iLance header handling of jQuery/jsTree ?>

	/*
	<?php // this official method only works outside of iLance header handling of jQuery/jsTree ?>
	$("#navtree").jstree({
		"core" : {
			"themes" : {
				"variant" : "small"
			}
		}
	});	
	$('#navtree').jstree('hide_icons');


	$('#navtree')
	.on('changed.jstree', function (e, data) {
		alert('why?');
		var i, j, r = [];
		for (i = 0, j = data.selected.length; i < j; i++) {
			r.push(data.instance.get_node(data.selected[i]).text);
		}
		$('#event_result').html('Selected: ' + r.join(', '));
	})


	$('#navtree').jstree({ 'core' : {
		'data' : [
		   'Simple root node',
		   {
			 'text' : 'Root node 2',
			 'state' : {
			   'opened' : true,
			   'selected' : true
			 },
			 'children' : [
			   { 'text' : 'Child 1' },
			   'Child 2'
			 ]
		  }
		]
	} });
	*/
	function clearAllNavHightlights()
	{
		// L1a_32982
		var nodes = $('span:regex(id,^L[0-9]+a_[0-9]+$)');	// find all arrow nodes
		for (var i = 0; i < nodes.length; i++)
		{
			nodes[i].style.display = 'none';				// turn them off
		}
		// L1s_32982
		var nodes = $('span:regex(id,^L[0-9]+s_[0-9]+$)');	// find all nodes with nav titles, for their text styling
		for (var i = 0; i < nodes.length; i++)
		{
			nodes[i].style.color = '#000';
			nodes[i].style.fontWeight = '';
		}
	}
	function nodeIDbreakout(nodeID)		// break nodeID into a level and a database ID
	{
		var patt = /^L([0-9]+)([A-za-z]*[0-9]*)_([0-9]+)$/;	// regexp pattern to separate level and node ID numbers
		var aNode = patt.exec(nodeID);	// get level and node ID
		return {
			level: aNode[1],
			dbID: aNode[3],
			type: aNode[2]
		};
	}
	function nameURLencode(name)						// convert name to URL format
	{
		return name.trim().replace(/[ \/]/g,'-');			// convert any spaces to hyphens
	}
	<?php // https://j11y.io/javascript/regex-selector-for-jquery/ ?>
	jQuery.expr[':'].regex = function(elem, index, match) {
		var matchParams = match[3].split(','),
			validLabels = /^(data|css):/,
			attr = {
				method: matchParams[0].match(validLabels) ? 
					matchParams[0].split(':')[0] : 'attr',
			property: matchParams.shift().replace(validLabels,'')
		},
			regexFlags = 'ig',
			regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
		return regex.test(jQuery(elem)[attr.method](attr.property));
	}
	function htmlEscape(str)
	{
		return str
			.replace(/&/g, '&amp;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;');
	}
	<?php 	// AJAX spinner from - http://blog.oio.de/2010/11/08/how-to-create-a-loading-animation-spinner-using-jquery/
			// Generate spinner at http://ajaxload.info/ ?>
	$(function(){
		$(document).ajaxSend(function() {
			$("#spinner").show();
		}).ajaxStop(function() {
			$("#spinner").hide();
		}).ajaxError(function() {
			$("#spinner").hide();
		});
		/*
		$("#spinner").bind("ajaxSend", function() {
			$(this).show();
		}).bind("ajaxStop", function() {
			$(this).hide();
		}).bind("ajaxError", function() {
			$(this).hide();
		});
		*/
	});
</script>

<div id="spinner" class="spinner" style="display:none;">
    <img id="img-spinner" src="<?php echo HTTP_SERVER; ?>/images/ajax-loader.gif" alt="Loading"/>
</div>

<style type="text/css">
.spinner {
	position: fixed;
	top: 50%;
	left: 50%;
	margin-left: -50px; /* half width of the spinner gif */
	margin-top: -50px; /* half height of the spinner gif */
	text-align:center;
	z-index:1234;
	overflow: auto;
	width: 100px; /* width of the spinner gif */
	height: 102px; /*hight of the spinner gif +2px to fix IE8 issue */
}

.tree li a ins {
	display:none !important;
}<?php // this official method only works outside of iLance header handling of jQuery/jsTree ?>

i.jstree-icon.jstree-themeicon {
	display:none !important;
}<?php // this found method works with iLance header handling of jQuery/jsTree ?>

.jstree-node {
    Xfont-size: 13pt;
}
<?php // http://stackoverflow.com/questions/24746781/how-do-i-get-a-jstree-node-to-display-long-possibly-multiline-content ?>
.jstree-default a { 
    white-space:normal !important; height: auto; 
}
.jstree-anchor {
    height: auto !important;
}
.jstree-default li > ins { 
    vertical-align:top; 
}
.jstree-leaf {
    height: auto;
}
.jstree-leaf a{
    height: auto !important;
}
</style> 

<div id="page_head" style="font-size: 18px; font-weight: bold; padding-bottom: 4px;"><?php echo $page_head; ?></div>
<table cellpadding="0" cellspacing="0"><tr>
<td valign="top" style="padding: 4px;">
	<div id="navtree" style="width: 300px; border: 1px solid #CCC; padding-right: 0px;">
		<ul>
			<li id="L0_<?php echo $universeID; ?>" class="jstree-open"><span id="L0t_<?php echo $universeID; ?>" style="font-size: 16px; font-weight: bold;"><?php echo $universeDesc; ?></span>
			<ul>
			<?php
			outputTreeHTML('', $universeID, $denominationID, $coin_seriesID, $coinID); 	// generate initial HTML tree code
			?>
				<!--
				<li>Root node 1
				<ul>
					<li>Child node 1</li>
					<li><a href="#">Child node 2</a></li>
				</ul>
				</li>
				-->
			</ul></li>
		</ul>
	</div>
</td>
<td valign="top" style="width: 100%; padding-left: 10px; border: 0px solid black;">
	<div id="descbody"><?php descBody(); ?></div>
	<div><?php gradeSelector(); ?></div>
	<div id="histbody"><?php histBody(); ?></div>
</td>
</tr>
<!--
<tr>
<td colspan="2" id="histbody" valign="top" style="width: 100%; padding-left: 10px; border: 0px solid black;">
	<div id="histdown" style="display: none;"><?php // histBody(); ?></div>
</td>
</tr>
-->
</table>
<?php
// echo '<PRE>'; print_r($_GET); echo '</PRE>';
$pprint_array = array();
$ilance->template->construct_footer('main');
$ilance->template->parse_if_blocks('main');
$ilance->template->pprint('main', $pprint_array);
exit();

function outputTreeHTML($AJAXnode = '', $universeID = -1, $denominationID = -1, $coin_seriesID = -1, $coinID = -1)	// generate HTML tree code
{	// AJAXnode=0: generate whole initial tree, opened to current position. AJAXnode=Node Name: Re-generate given node, and define one level below
	global $ilance;

	// ob_start();	// start capturing PHP output, if we were using string return, rather than immediate output
	if ($AJAXnode)
	{
		list($level, $dbID) = nodeIDbreakout($AJAXnode);					// break nodeID into a level and a database ID
		switch ($level) {
		case 0:		// universe (Should never happen)
			die('outputTreeHTML() called with specific universe');
			/*
			$universeID = $dbID;
			$denominationID = 0;
			$coin_seriesID = 0;
			$coinID = 0;
			*/
			break;
		case 1:		// denom
			$universeID = 1;
			$denominationID = $dbID;
			$coin_seriesID = 0;
			$coinID = 0;
			break;
		case 2:		// series
			$universeID = 1;
			// We know the series number, but need to find the denomination ID above it
			$sql = "SELECT coin_series_denomination_no FROM ".DB_PREFIX. "catalog_second_level cs WHERE cs.coin_series_unique_no=$dbID";
			// echo 'SQL = '.$sql; exit;
			$select = $ilance->db->query($sql);
			// echo 'Num rows = '.$ilance->db->num_rows($select); exit;
			if ($row = $ilance->db->fetch_array($select))
				$denominationID = $row['coin_series_denomination_no'];
			else
				die('outputTreeHTML() could not determin denom ID from coin series ID');
			unset($select);
			$coin_seriesID = $dbID;
			$coinID = 0;
			break;
		case 3:		// specific PCGS # (Should never happen)
			die('outputTreeHTML() called with specific PCGS #');
			/*
			$universeID = 1;
			$denominationID = ;
			$coin_seriesID = $dbID;
			$coinID = 0;
			*/
			break;
		}
	} else {
		$level = -1;
		$dbID = -1;
	}

	if ($AJAXnode && $level >= 1)		// if we're getting one node, and it's at least a denomination node
		$SQLwhere = 'WHERE denomination_unique_no='.$denominationID;
	else
		$SQLwhere = '';

	$sql = "SELECT * FROM ".DB_PREFIX. "catalog_toplevel den $SQLwhere ORDER BY denomination_sort";
	// echo 'SQL = '.$sql; exit;
	$select = $ilance->db->query($sql);
	// echo 'Num rows = '.$ilance->db->num_rows($select); exit;
	while ($denNavRow = $ilance->db->fetch_array($select))
	{
		// echo '<PRE>'; print_r($denNavRow); echo '</PRE>'; exit;
		$den_id = $denNavRow['id'];			// get ID of this denom row
		$onThisDNnode = ($denominationID == $den_id);
		if ($AJAXnode && $level >= 2)		// if we're getting one node, and it's deeper than the denomination
			; // then we skip the actual denomination output
		else {
			?>
			<li id="L1_<?php echo $den_id; ?>" class="<?php echo ($onThisDNnode?'jstree-open':'jstree-closed'); ?>">
				<span id="L1s_<?php echo $den_id; ?>" style="<?php echo (!$AJAXnode && $onThisDNnode?'color: #600;font-weight: bold;':''); ?>">
				<span id="L1t_<?php echo $den_id; ?>"><?php echo ilance_htmlentities($denNavRow['denomination_long']); ?></span>
				(<?php echo $denNavRow['auction_count'];  ?>)
				<span id="L1a_<?php echo $den_id; ?>" style="<?php echo (!$AJAXnode && $onThisDNnode && !$coin_seriesID?'':'display: none;');	?>">&#10148;</span>
				</span>
			<?php
		}
		if ($onThisDNnode)	// if this is a denomination tree node that should be open (0=no denom open)
		{
			if ($AJAXnode && $level >= 2)		// if we're just getting a single node, and it's at least a coin series node
				$SQLwhere = 'cs.coin_series_unique_no='.$coin_seriesID.' AND ';
			else
				$SQLwhere = '';
			$sql = "SELECT * FROM ".DB_PREFIX. "catalog_second_level cs WHERE $SQLwhere cs.coin_series_denomination_no=$denominationID ORDER BY cs.coin_series_sort";
			// echo 'SQL = '.$sql; exit;
			$selectL2 = $ilance->db->query($sql);
			// echo 'Num rows = '.$ilance->db->num_rows($selectL2); exit;
			if ($ilance->db->num_rows($selectL2))	// if any rows
			{
				?>
				<ul>
				<?php
				while ($csNavRow = $ilance->db->fetch_array($selectL2))
				{
					$cs_id = $csNavRow['id'];		// get ID of this coin series row
					$sql = "SELECT COUNT(id) AS qty_coins FROM ".DB_PREFIX. "catalog_coin cc WHERE cc.coin_series_unique_no=$cs_id";
						// get # of coins in this coin series
					// echo 'SQL = '.$sql; exit;
					$selectL2q = $ilance->db->query($sql);
					// echo 'Num rows = '.$ilance->db->num_rows($selectL2q); exit;
					if ($res = $ilance->db->fetch_array($selectL2q))
						$qty_coins = $res['qty_coins'];		// get # of coins in this coin series
					else
						$qty_coins = 0;
					unset($selectL2q);
					$onThisCSnode = ($coin_seriesID == $cs_id);	// set if this is a coin series tree node that should be open (0=no coin series open)
					?>
					<li id="L2_<?php echo $cs_id; ?>" class="<?php echo ($onThisCSnode?'jstree-open':'jstree-closed'); ?>" style="">
						<span id="L2s_<?php echo $cs_id; ?>" style="<?php echo (!$AJAXnode && $onThisCSnode?'color: #600;font-weight: bold;':''); ?>">
						<span id="L2t_<?php echo $cs_id; ?>"><?php echo ilance_htmlentities($csNavRow['coin_series_name']); ?></span>
						(<?php echo $csNavRow['auction_count']; // .','.$qty_coins.'/'.$coinID ?>)
						<span id="L2a_<?php echo $cs_id; ?>" style="<?php echo (!$AJAXnode && $onThisCSnode && !$coinID?'':'display: none;');	?>">&#10148;</span>
						</span>
					<?php
					$coin_seriesDesc = @$catRow['coin_series_name'];	// get coin series from database
					if (True && $onThisCSnode)	// if this is a coin series tree node that should be open (0=no coin series open)
					{
						$isCutCoins = ($qty_coins > MAX_COINS_TREE);	// set if too many coins to display in nav tree
						$sql = "SELECT * FROM ".DB_PREFIX. "catalog_coin c1 WHERE c1.coin_series_unique_no=$cs_id";
						if ($isCutCoins)
						{
							// get the Orderno of the center primary catalog_coin record
							$sqlCut = "SELECT Orderno FROM ".DB_PREFIX. "catalog_coin cc WHERE cc.PCGS=$coinID";
							$selectCut = $ilance->db->query($sqlCut);
							if ($rowCut = $ilance->db->fetch_array($selectCut))
								$Orderno = $rowCut['Orderno'];
							else
								$Orderno = 0;
							unset($selectCut);

							$sql = "SELECT * FROM ($sql AND c1.Orderno <= $Orderno ORDER BY c1.Orderno DESC LIMIT ".intval(MAX_COINS_TREE/2).") AS c1b UNION
								(".str_replace('c1','c2',$sql)." AND c2.Orderno > $Orderno ORDER BY c2.Orderno ASC LIMIT ".intval(MAX_COINS_TREE/2).")";

							// http://stackoverflow.com/questions/30214003/select-records-around-a-specific-record-from-database
							// SELECT * FROM (SELECT * FROM x WHERE z >= n ORDER BY z LIMIT 10) a UNION (SELECT * FROM x WHERE z < n ORDER BY z DESC) LIMIT 10 
						}
						$sql .= " ORDER BY Orderno";
						if ($isCutCoins)
							$sql .= " LIMIT ".MAX_COINS_TREE;
						// echo 'SQL = '.$sql; exit;
						$selectL3 = $ilance->db->query($sql);
						// echo 'Num rows = '.$ilance->db->num_rows($selectL3); exit;
						if ($ilance->db->num_rows($selectL3))	// if any rows
						{
							?>
							<ul>
							<?php
							if ($isCutCoins)
							{
								?>
								<li id="L3m1_<?php echo $cs_id; ?>" style="font-style: italic;">...more...</li>
								<?php
							}
							while ($cnNavRow = $ilance->db->fetch_array($selectL3))
							{
								$cn_id = $cnNavRow['PCGS'];		// get ID of this coin series row
								list($coinDescL3) = titleFromFields($cnNavRow);	// get coin title from database
								// echo '<PRE>$coinDescL3='.$coinDescL3.'<BR>'; print_r($cnNavRow); echo '</PRE>'; exit;

								$onThisCNnode = ($coinID == $cn_id);	// set if this is a coin tree node that should be selected (0=no coin open)
								?>
								<li id="L3_<?php echo $cn_id; ?>" class="<?php echo '';	// ($onThisCNnode?'jstree-open':'jstree-closed') ?>" style="">
									<span id="L3s_<?php echo $cn_id; ?>" style="<?php echo (!$AJAXnode && $onThisCNnode?'color: #600;font-weight: bold;':''); ?>">
									<span id="L3t_<?php echo $cn_id; ?>"><?php echo ilance_htmlentities($coinDescL3); ?></span>
									(<?php echo '?';	// $cnNavRow['PCGS'].'-'.$cnNavRow['auction_count'].','.$qty_coins.'/'.$coinID ?>)
									<span id="L3a_<?php echo $cn_id; ?>" style="<?php echo (!$AJAXnode && $onThisCNnode?'':'display: none;');	?>">&#10148;</span>
									</span>
								</li>
								<?php
							}
							if ($isCutCoins)
							{
								?>
								<li id="L3m2_<?php echo $cs_id; ?>" style="font-style: italic;">...more...</li>
								<?php
							}
							?>
							</ul>
							<?php
						}
						unset($selectL3);
					}
					?>
					</li>
					<?php
				}
				?>
				</ul>
				<?php
			}
			unset($selectL2);
		}
		?>
		</li>
		<?php
	}
	unset($select);
	return;
	
	// wrap up if we were using string return, rather than immediate output
	$HTMLout = ob_get_contents();	// get HTML output into a string
	ob_end_clean();					// flush HTML output
	return $HTMLout;				// return accumulated output
}

function descBody()	// display description body in right pane
{
	global $ilance;

	global $AucArchiveURI;
	global $universeID;
	global $universeDesc;
	global $denominationID;
	global $denominationDesc;
	global $coin_seriesID;
	global $coin_seriesDesc;
	global $catRow;
	global $denominationDesc;
	global $coin_seriesDesc;
	global $coinID;
	global $coinDesc;
	global $coinRow;
	
	global $page_desc;

	?>
	
	<?php
	if ($coinID)				// if a specific PCGS number given
	{
		$sqlJoin = "";			// no join needed
		$sqlWhere = "p.cid=$coinID AND";	// limit search to specific PCGS number
	} else if ($coin_seriesID)	// if only a coin series number given, find a random PCGS number from the series
	{
		$sqlJoin = "";			// no join needed
		$sqlWhere = "p.coin_series_unique_no=$coin_seriesID AND";				// limit results to specific coin series number
	} else if ($denominationID)	// if only a denom given, find a random PCGS number from the series
	{
		$sqlJoin = "";			// no join needed
		$sqlWhere = "p.coin_series_denomination_no=$denominationID AND";		// limit results to specific denomination number
	} else						// if only the coin universe given, find a random PCGS number from the whole thing
	{
		$sqlJoin = "";			// no join needed
		$sqlWhere = "";			// do not limit results!
	}
	/*
	if ($coinID)				// if a specific PCGS number given
	{
		$sqlJoin = "";			// no join needed
		$sqlWhere = "p.cid=$coinID AND";	// limit search to specific PCGS number
	} else if ($coin_seriesID)	// if only a coin series number given, find a random PCGS number from the series
	{
		$sqlJoin = "LEFT JOIN ".DB_PREFIX."catalog_coin cc ON cc.PCGS=p.cid";	// need the catalog_coin table to find the series
		$sqlWhere = "cc.coin_series_unique_no=$coin_seriesID AND";				// limit results to specific coin series number
	} else if ($denominationID)	// if only a denom given, find a random PCGS number from the series
	{
		$sqlJoin = "LEFT JOIN ".DB_PREFIX."catalog_coin cc ON cc.PCGS=p.cid";	// need the catalog_coin table to find the series
		$sqlWhere = "cc.coin_series_denomination_no=$denominationID AND";		// limit results to specific denomination number
	} else						// if only the coin universe given, find a random PCGS number from the whole thing
	{
		$sqlJoin = "";			// no join needed
		$sqlWhere = "";			// do not limit results!
	}
	*/
	$sql = "SELECT p.id, p.project_id, p.currentprice, p.project_title,
		concat( 'uploads/attachments/auctions/', (
			floor( a.project_id /100 ) ) *100, '/', a.project_id, '/', a.filehash, '.attach'
		) AS imgpath
	FROM ".DB_PREFIX."projects p LEFT JOIN ".DB_PREFIX."attachment a ON a.project_id = p.project_id
	$sqlJoin
	WHERE $sqlWhere p.visible=1 AND p.status='expired' AND p.haswinner=1 AND date_end > DATE_ADD(NOW(),INTERVAL -6 MONTH) ORDER BY currentprice DESC LIMIT ".TOP_COIN_PRICE;
	// get title and image path for an item from the last 6 months that is in the top TOP_COIN_PRICE realized prices
	// echo 'SQL = '.$sql; exit;

	$select = $ilance->db->query($sql, MYSQLI_STORE_RESULT);	// execute query, make sure we're storing results so we can seek through them
	// echo 'Num rows = '.$ilance->db->num_rows($select); exit;
	mysqli_data_seek($select,rand(0,$ilance->db->num_rows($select)));	// seek to random place in recordset of up to TOP_COIN_PRICE rows

	$row = $ilance->db->fetch_array($select);
	unset($select);
	if ($row)		// if a coin record found
	{
		// echo '<PRE>'; print_r($row); echo '</PRE>'; exit;
		$title = $row['project_title'];	// get coin series from database
		if (ISMARK)	// if force remote sample image
			$image = 'http://www.greatcollections.com/image/400/268/'.$row['project_id'].'-1.jpg';
		else
			$image = HTTP_SERVER.'/image/400/268/'.$row['project_id'].'-1.jpg';
		// echo '$title='.$title.'<BR>$image='.$image.'<BR>';
		?>
		<div style="float: right; border-left: 5px; text-align: center;">
		<a href="<?php echo HTTP_SERVER.'Coin/'.$row['project_id'].'/'.construct_seo_url_name($title); ?>" title="<?php echo ilance_htmlentities($title); ?>">
		<img src="<?php echo $image; ?>" alt="<?php echo ilance_htmlentities($title); ?>" title="<?php echo ilance_htmlentities($title); ?>" />
		</br><?php echo ilance_htmlentities($title); ?>
		</br>Sold for <?php echo $ilance->currency->format($row['currentprice']); ?>
		</a>
		</div>
		<?php
	}

	if (isset($catRow['coin_series_description_long']))
	{
		echo ilance_htmlentities($catRow['coin_series_description_long']).'<br /><br />';
	}
	if (isset($catRow['coin_series_designer']))
	{
		echo 'The designed was made by '.ilance_htmlentities($catRow['coin_series_designer']).'<br /><br />';
	}

	// echo '<PRE style="white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;">$catRow=<BR>'; print_r(@$catRow); echo '</PRE>';
	// echo '<PRE style="white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;">$coinRow=<BR>'; print_r(@$coinRow); echo '</PRE>';
	?>
	<style type="text/css">
		.aa_list ul {
			margin: 1em 0;
			padding: 0 0 0 20px;
		}

		.aa_list li {
			list-style-type: disc;
		}
	</style>
	<h3>Additional Articles related to <?php echo ilance_htmlentities($page_desc); ?></h3>
	<button style="float: right; margin-top: -0px; margin-right: 2%; outline:0; color: white; background-color: #C00; Xfont-size: 14px; font-weight: bold; border-radius: 10px; moz-border-radius: 10px; webkit-border-radius: 10px; Xheight: 20px; Xwidth: 20px; border:1px #00 solid; box-shadow: 3 3 2 2 #black;">
		Add this to<br />your Wantlist
	</button>
	<strong><ul class="aa_list">
		<li class="aa_list">Lincoln Cent sells for $26,000 at GreatCollections!</li>
		<li class="aa_list">Circulated pennies can be worth more than you think</li>
		<li class="aa_list">Cents in the News</li>
	</ul></strong>
	<!--
	<br /><center><button style="text-align: center; margin-top: -0px; Xmargin-right: 2%; outline:0; color: white; background-color: #CCF; font-size: 14px; font-weight: bold; border-radius: 10px; moz-border-radius: 10px; webkit-border-radius: 10px; Xheight: 20px; Xwidth: 20px; border:1px #00 solid; box-shadow: 3 3 2 2 #black;"
		onClick="histSwap();">
		Swap History Positions
	</button></center>
	<script type="text/javascript" language="Javascript">
		function histSwap()
		{
			if (document.getElementById('histdown').style.display=='')
			{
				document.getElementById('histdown').style.display='none';
				document.getElementById('histside').style.display='';
			} else
			{
				document.getElementById('histdown').style.display='';
				document.getElementById('histside').style.display='none';
			}
		}
	</script>
	-->
	<?php
}
function gradeSelector()
{
	?>
	<style type="text/css">
		.ui-slider .ui-slider-handle {
			width:2em;
			left:-.6em;
			font-size: 16px;
			font-weight: bold;
			text-decoration:none;
			text-align:center;
		}
	</style>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #CCC; padding: 4px; background-color: #efe; margin-bottom: 10px;">
		<tr>
			<td colspan="2" style="text-align: center; font-size: 16px; font-weight: bold;"><b>Adjust Grades in Archive Results</b></td>
		</tr>
		<tr>
			<td style="padding-right: 6px;"><b>Grader</b></td>
			<td width="100%">
				<span style="white-space: nowrap;">
				<label><input id="gd_pcgs" type="checkbox" checked />PCGS</label>
				<label><input id="gd_ngc" type="checkbox" checked />NGC</label>
				<label><input id="gd_anacs" type="checkbox" checked />ANACS</label>
				<label><input id="gd_other" type="checkbox" checked />Other</label>
				</span>
				&nbsp;
				<span style="white-space: nowrap;">
				<label><input id="gd_cac" type="checkbox" checked />CAC</label>
				<label><input id="gd_noncac" type="checkbox" checked />non-CAC</label>
				&nbsp;
				<label><input id="gd_plus" type="checkbox" checked />Plus</label>
				<label><input id="gd_nonplus" type="checkbox" checked />non-Plus</label>
				</span>
			</td>
		</tr>
		<tr>
			<td style="vertical-align: middle;"><b>Grade</b></td>
			<td Xwidth="100%" style="vertical-align: middle; position: relative;">
				<div id="gradeslider" style="margin-bottom: 5px; margin: 10px; margin-right: 20px;"></div>
				<div><nobr><b><label><input id="gd_ungraded" type="checkbox" checked />Ungraded</label></b></nobr></div>
			</td>
		</tr>
	</table>
	<script type="text/javascript" language="Javascript">
		var gradeSliderMap = [<?php
			global $gradeList;
			foreach ($gradeList as $grade=>$gradeName)
				echo $grade.($grade<70?', ':'');
		?>];
		var allowChange = false;	// suppress change AJAX call during initialization
		$('#gradeslider').slider({
			range: true,
			min: 0,
			max: gradeSliderMap.length-1,
			values: [ 0, gradeSliderMap.length-1 ],
			slide: function(event, ui) {	// as slider is moved
				gradeSliderUpdate(ui);		// change handle text labels for every change, send ui for its new value
				allowChange = true;			// now that there's been real user interact, allow a change AJAX call
			},
			change: function(event, ui) {	// when slider change is actually done
				gradeStoreSet();			// store new grade values
				if (allowChange)			// only allow AJAX if full initialized
					histFresh();			// AJAX call for a new history body
			}
		});
		<?php /*
		http://stackoverflow.com/questions/21534628/slider-value-display-with-jquery-ui-2-handles
		http://stackoverflow.com/questions/5800714/jquery-slider-with-value-on-drag-handle-slider
		https://gist.github.com/thiyagaraj/8804699 (customer step/snap points)
		*/
		?>
		function gradeSliderUpdate(ui)	// update text buttons in slider
		{
			var value;
			if (ui && ui.handleIndex == 0)	// if we're getting a specific "slide" event call, and it's for the first slider
				value = ui.value;		// get direct, upcoming slider value
			else
				value = $("#gradeslider").slider("values", 0);

			$("#gradeslider").find(".ui-slider-handle:first").text(gradeSliderMap[value]);
			if (ui && ui.handleIndex == 1)	// if we're getting a specific "slide" event call, and it's for the second slider
				value = ui.value;		// get direct, upcoming slider value
			else
				value = $("#gradeslider").slider("values", 1);
			$("#gradeslider").find(".ui-slider-handle:last").text(gradeSliderMap[value]);
		}
		$(function () {
			gradeStoreGet();	// initial setting of values from a cookie

			// set change handler for all the grade checkboxes
			var nodes = $('input:regex(id,^gd_.*$)');	// find all grade storage elements
			for (var i = 0; i < nodes.length; i++)
			{
				$(nodes[i]).on('change', function() {		// set a "change" handler for each
					gradeStoreSet();
					histFresh();							// AJAX call for a new history body
				});			
			}
		});
		function gradeStoreSet()		// store to grade store
		{
			var serialized = '';
			var nodes = $('input:regex(id,^gd_.*$)');	// find all grade storage elements
			for (var i = 0; i < nodes.length; i++)
			{
				serialized += nodes[i].id+':'+nodes[i].checked+'\n';
				// nodes[i].style.display = 'none';				// turn them off
			}
			serialized += 'gd_min:'+gradeSliderMap[$("#gradeslider").slider("values", 0)]+'\n';		// store min and max slider values
			serialized += 'gd_max:'+gradeSliderMap[$("#gradeslider").slider("values", 1)]+'\n';
			// alert(serialized);
			$.cookie('grade', serialized, { expires: 365, path: '/' });
		}
		function gradeStoreGet()		// retrieve from grade store
		{
			var serialized = $.cookie('grade').trim();		// get cookie, lop off trailing newline
			if (typeof serialized === "undefined")	// return now if undefined, leaving base defaults
				return;
//			try {
				serialized = serialized.split('\n');	// split into array
				for (var i = 0; i < serialized.length; i++)	// for every parameter
				{
					var param = serialized[i].split(':');	// split into name/value parameter
					if (param[0] == 'gd_min')				// if special grade minimum
						$("#gradeslider").slider("values", 0, gradeSliderMap.indexOf(Number(param[1])) );
							// take grade and find it in gradeSliderMap, return index, store in slider
					else if (param[0] == 'gd_max')				// if special grade maximum
						$("#gradeslider").slider("values", 1, gradeSliderMap.indexOf(Number(param[1])) );
							// take grade and find it in gradeSliderMap, return index, store in slider
					else
						$('#'+param[0]).prop('checked', param[1]=='true');
								// set checkbox to value
				}
				gradeSliderUpdate();	// update handles with the newly set slider values
//			}	catch(err) {
//				// just leave things alone if there's an error
//			}
		}
	</script>
	<?php
}
function histBody()	// display history body in bottom pane
{
	global $ilance;

	global $AucArchiveURI;
	global $universeID;
	global $universeDesc;
	global $denominationID;
	global $denominationDesc;
	global $coin_seriesID;
	global $coin_seriesDesc;
	global $catRow;
	global $denominationDesc;
	global $coin_seriesDesc;
	global $coinID;
	global $coinDesc;
	global $coinRow;
	
	global $page_desc;
	global $gradeList;

	global $gradeParams;
	gradeStoreGet();		// retrieve from grade store into global $gradeParams
	$gd_min = intval($gradeParams['gd_min']);	// sanitize grade range
	$gd_max = intval($gradeParams['gd_max']);
	?>
	<style type="text/css">
		.hist table {
			border-collapse: collapse;
		}
		.hist tr {
			vertical-align: text-bottom;
		}
		.hist td {
			padding: 2px;
		}
		
		.hist_left {
			border-left: 1px solid #CCC;
			padding-left: 4px !important;
		}
		.hist_centerleft {
			padding-right: 4px !important;
		}
		.hist_center {
			border-left: 1px solid #CCC;
			padding-left: 4px !important;
		}
		.hist_right {
			border-right: 1px solid #CCC;
			padding-right: 4px !important;
		}

		.hist_head tr {
		}
		.hist_head td {
			padding-top: 6px;
			font-weight: bold;
			border-top: 1px solid #CCC;
		}
		
		.hist_grade {
			font-size: 20px;
			font-weight: bold;
			white-space: nowrap;
		}

		.hist_line {
			border: 0;
			height: 1px;
			background: #CCC;
		}

		.hist_foot td {
			border-bottom: 1px solid #CCC;
		}

	.cropthumb {
		/* display: block; */
		overflow: hidden;
		height: 120px;
		width: 80px;
	}

	.cropthumb img {
		margin-top: -50px;
		margin-left: -40px;
		display: inline-block; /* Otherwise it keeps some space around baseline */
		width: 200%;
		/* min-width: 100%;    Scale up to fill container width */
		/* min-height: 100%;   Scale up to fill container height */
		-ms-interpolation-mode: bicubic; /* Scaled images look a bit better in IE now */
	}
	</style>
	<?php // echo 'Grade cookie: '.$_COOKIE['grade']; ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="hist">
		<tr>
			<td colspan="5" style="vertical-align: bottom;">
				<div style="font-size: 16px; font-weight: bold;">Sold <?php echo ilance_htmlentities($page_desc); ?></div>
			</td>
			<td colspan="3" style="vertical-align: bottom;">
				<div style="font-size: 16px; font-weight: bold;">Upcoming <?php echo ilance_htmlentities($page_desc); ?></div>
			</td>
		</tr>
		<tr class="hist_head">
			<!--<td><nobr>Grade (qty)</nobr></td>-->
			<td class="hist_left">Grade / Item</td>
			<td align="right"><nobr>Sold For</nobr></td>
			<td align="center">Bids</td>
			<td>Photo</td>
			<td class="hist_centerleft" align="right">Date</td>

			<td class="hist_center">Grade / Item</td>
			<td align="right">Current Bid</td>
			<td class="hist_right" align="center">Photo</td>
		</tr>
		<tr>
			<td colspan="5" class="hist_left"><hr class="hist_line"></td>
			<td colspan="3" class="hist_center hist_right"><hr class="hist_line"></td>
		</tr>
		<?php
		if ($coinID)				// if a specific PCGS number given
		{
			$sqlJoin = "";			// no join needed
			$sqlWhere = "p.cid=$coinID AND";	// limit search to specific PCGS number
		} else if ($coin_seriesID)	// if only a coin series number given, find a random PCGS number from the series
		{
			$sqlJoin = "";			// no join needed
			$sqlWhere = "p.coin_series_unique_no=$coin_seriesID AND";				// limit results to specific coin series number
		} else if ($denominationID)	// if only a denom given, find a random PCGS number from the series
		{
			$sqlJoin = "";			// no join needed
			$sqlWhere = "p.coin_series_denomination_no=$denominationID AND";		// limit results to specific denomination number
		} else						// if only the coin universe given, find a random PCGS number from the whole thing
		{
			$sqlJoin = "";			// no join needed
			$sqlWhere = "";			// do not limit results!
		}

		// Create a SQL WHERE subclause to limit based on grading criteria. Will be used in several places
		if ($gradeParams['gd_ungraded']=='true')
			$sqlGrade = "(p.Grade BETWEEN $gd_min AND $gd_max OR p.Grade=0) AND ";	// user grade range in WHERE clause
		else
			$sqlGrade = "p.Grade BETWEEN $gd_min AND $gd_max AND ";	// user grade range in WHERE clause
		if ($gradeParams['gd_pcgs']=='false' || $gradeParams['gd_ngc']=='false' || $gradeParams['gd_anacs']=='false' || $gradeParams['gd_other']=='false')
		{	// if any limitations on grading organization, form an IN (list) SQL clause
			$sqlGrade .= 'p.Grading_Service IN (';
			if ($gradeParams['gd_pcgs']=='true')	// if user wants any grade, but it in the list
				$sqlGrade .= "'PCGS',";
			if ($gradeParams['gd_ngc']=='true')
				$sqlGrade .= "'NGC',";
			if ($gradeParams['gd_anacs']=='true')
				$sqlGrade .= "'ANACS',";
			if ($gradeParams['gd_other']=='true')
				$sqlGrade .= "'ICG','NCS',";
			$sqlGrade = substr($sqlGrade,0,-1);		// remove trailing comma
			$sqlGrade .= ') AND ';
		}
		if ($gradeParams['gd_cac'] != $gradeParams['gd_noncac'])	// if restrict by CAC (if both are the same, no real restriction)
			$sqlGrade .= 'p.Cac='.($gradeParams['gd_cac']=='true'?1:0).' AND ';

		$sqlJoin = "LEFT JOIN ".DB_PREFIX."coins c2 ON c2.coin_id=p.project_id";	// join to gain access to the "Plus" field

		if ($gradeParams['gd_plus'] != $gradeParams['gd_nonplus'])	// if restrict by plus grade (if both are the same, no real restriction)
		{
			$sqlGrade .= 'c2.Plus='.($gradeParams['gd_plus']=='true'?1:0).' AND ';	// include the plus field
		}
		// echo '$sqlGrade='.$sqlGrade.'<BR>';
		 
		$sql = "SELECT p.Grade, c2.Plus, COUNT(*) AS qty_total, SUM(p.status='open') AS qty_open FROM ".DB_PREFIX."projects p
		$sqlJoin
		WHERE $sqlGrade $sqlWhere p.buynow=0 AND p.visible=1 AND p.date_end > DATE_ADD(NOW(),INTERVAL -6 MONTH) GROUP BY p.Grade, c2.Plus ORDER BY p.Grade+(c2.Plus/2) DESC";
		// echo 'SQL = '.$sql; // exit;

		$gradeSelect = $ilance->db->query($sql);
		// echo 'Num rows = '.$ilance->db->num_rows($gradeSelect); exit;
		$soldCnt = 0;		// init counter for both past and upcoming counter
		$upcomingCnt = 0;
		while ($gradeRow = $ilance->db->fetch_array($gradeSelect))	// for each grade found for this matching coin
		{
			// echo '<PRE>'; print_r($gradeRow); echo '</PRE>'; exit;
			if (!$gradeRow['qty_total'])	// if no records in this grade, skip entirely
				continue;
			$Grade = $gradeRow['Grade'];	// get current grade
			$Plus = $gradeRow['Plus'];		// get current Plus designation
			?>
			<tr>
			<?php
			if ($gradeRow['qty_total'] > $gradeRow['qty_open'])	// if there are past records in this grade
			{
				?>
				<td colspan="5" class="hist_left hist_grade" style="padding-top: 8px;" style="position: relative;">
				<?php echo ($Grade?$Grade:UNGRADED).($Plus?'+':''); ?> <span style="font-size: 14px; font-weight: normal;">(<?php echo $gradeRow['qty_total']-$gradeRow['qty_open']; ?>)</span>
				</td>
				<?php
			} else {								// if nothing to be shown in this column
				?>
				<td colspan="5" class="hist_left"><?php
				if (!$soldCnt)	// in first empty head slot, save a place a disabled "There are none" message just in case we need it
				{
					?>
					<div id="noclosed"></div>
					<?php
				}
				?></td>
				<?php
			}
			if ($gradeRow['qty_open'])				// if there are future records in this grade
			{
				?>
				<td colspan="3" class="hist_center hist_right hist_grade" style="Xposition: relative;" Xrowspan="<?php echo count($upcoming)+1; ?>">
				<?php echo ($Grade?$Grade:UNGRADED).($Plus?'+':''); ?> <span style="font-size: 14px; font-weight: normal;">(<?php echo $gradeRow['qty_open']; ?>)</span>
				</td>
				<?php
			} else {								// if nothing to be shown in this column
				?>
				<td colspan="3" class="hist_center hist_right"><?php
				if (!$upcomingCnt)	// in first empty head slot, save a place a disabled "There are none" message just in case we need it
				{
					?>
					<div id="noopen"></div>
					<?php
				}
			?></td>
				<?php
			}
			?>
			</tr>
			<?php
			// get subquery usable for both history and upcoming
			$sql = "SELECT p.*, c2.Plus FROM ".DB_PREFIX."projects p
			$sqlJoin
			WHERE [HOLDING] $sqlGrade $sqlWhere p.Grade=$Grade AND c2.Plus=$Plus AND p.buynow=0 AND p.visible=1 AND p.date_end > DATE_ADD(NOW(),INTERVAL -6 MONTH) ORDER BY p.currentprice DESC LIMIT ".TOP_COIN_HIST;
			
			// make query UNIONing both history and upcoming subqueries, upcoming first so we can store it in an array before outputting anything
			$sql = "SELECT * FROM (".str_replace('[HOLDING]','p.status <> \'open\' AND p.haswinner=1 AND ',$sql).") AS po UNION (".str_replace('[HOLDING]','p.status = \'open\' AND ',$sql).") ORDER BY status <> 'open'";
			// echo 'SQL = '.$sql; exit;

			$histSelect = $ilance->db->query($sql);
			// echo 'Grade '.$Grade.' num rows = '.$ilance->db->num_rows($histSelect).'<br />'; // exit;
			$upcoming = array();	// init holding place for upcoming
			while (($histRow = $ilance->db->fetch_array($histSelect)) || !empty($upcoming))	// for each grade found for this matching coin, or buffered upcoming
			{
				// echo '<PRE>Field cnt='.count($histRow).':<br>'; print_r($histRow); echo '</PRE>'; // exit;
				// echo 'status='.$histRow['status'].'<br />';
				if ($histRow['status'] == 'open')		// while upcoming lots still coming in
				{
					array_push($upcoming, $histRow);	// push upcoming record into array, saving for later
				} else {
					// echo $histRow['project_title']."\t".$histRow['currentprice']."\t".$histRow['bids']."\t".date('M Y',strtotime($histRow['date_end'])).'<br />';
					?>
					<tr>
					<?php
					if (empty($histRow))					// if nothing to be shown in this column
					{
						?>
						<td class="hist_left Xhist_grade"></td>
						<td colspan="4"></td>
						<?php
					} else {								// if something to be shown in this column
						$soldCnt++;							// inc counter for past
						?>
						<td class="hist_left"><a href="<?php echo HTTP_SERVER.'Coin/'.$histRow['project_id'].'/'.construct_seo_url_name($histRow['project_title']); ?>"><?php echo ilance_htmlentities($histRow['project_title']); ?></a></td>
						<td align="right"><?php echo currency_format_whole($histRow['currentprice']+$histRow['buyer_fee']); ?></td>
						<td align="right">&nbsp;<nobr><?php echo $histRow['bids']; ?> bid<?php echo ($histRow['bids']!=1?'s':'<span style="visibility: hidden;">s</span>'); ?></nobr></td>
						<td align="center">&#128247;</td>
						<td class="hist_centerleft" align="right"><nobr><?php echo date('M Y',strtotime($histRow['date_end'])); ?></nobr></td>
						<?php
					}
					$histRow = array_shift($upcoming);		// try to get a saved upcoming lot
					if (empty($histRow))					// if nothing to be shown in this column
					{
						?>
						<td class="hist_center Xhist_grade"></td>
						<td></td>
						<td class="hist_right"></td>
						<?php
					} else {								// if something to be shown in this column
						$upcomingCnt++;						// inc counter for upcoming
						$title = $histRow['project_title'];
						?>
						<td class="hist_center"><a href="<?php echo HTTP_SERVER.'Coin/'.$histRow['project_id'].'/'.construct_seo_url_name($histRow['project_title']); ?>"><?php echo ilance_htmlentities($histRow['project_title']); ?></a></td>
						<td align="right"><?php echo currency_format_whole($histRow['currentprice']); ?></td>
						<td class="hist_right" align="center">&#128247;</td>
						<?php
					}
					?>
					</tr>
					<?php
					$lastGrade = $histRow['Grade'];
				}
			}
			unset($select);
		}
		unset($gradeSelect);
		?>
		<tr class="hist_foot">
			<td colspan="5" class="hist_left">
				<div id="noclosed2"></div>
			</td>
			<td colspan="3" class="hist_center hist_right">
				<div id="noopen2"></div>
			</td>
		</tr>
		<div id="closedcnt" style="display: none;"><?php echo $soldCnt; ?></div>
		<div id="opencnt" style="display: none;"><?php echo $upcomingCnt; ?></div>
		<script type="text/javascript" language="Javascript">
			$(function () {
				updateNoItemsMsg2(<?php echo $soldCnt;	// xfer PHP variables to JS ?>, <?php echo $upcomingCnt; ?>);
					// update messages if there are no items (this won't execute from within AJAX loaded HTML)
			});
			<?php insertUpdateNoItemsMsg(2);	// insert the JS function, used in two places ?>
		</script>
	</table>
	<br />
	<?php
}
$gradeParams = array();
function gradeStoreGet()		// retrieve from grade store
{
	global $gradeParams;

	$serialized = trim($_COOKIE['grade']);		// get cookie, lop off trailing newline
	if (!$serialized)							// return now if undefined, leaving base defaults
		return;
//	try {
		$serialized = preg_split("#\n#", $serialized);	// split into array
		foreach ($serialized as $param)					// for every parameter
		{
			$param = preg_split("#:#", $param);			// split into name/value parameter
			$gradeParams[$param[0]] = $param[1];		// assign to global array
		}
		// print_r($gradeParams);
//	}	catch(err) {
//		// just leave things alone if there's an error
//	}
}

function dispNavTree($universe, $level1 = '', $level2 = '')		// display a JavaScript navigation tree for this $universe,
{																// optionally opened to $level1 and maybe even $level2
	$sql = "SELECT * 
		FROM ".DB_PREFIX. "catalog_toplevel den LEFT JOIN
			".DB_PREFIX. "catalog_second_level cs ON cs.coin_series_denomination_no=den.id";
	$select_tree = $ilance->db->query($sql);
}
function nameURLencode($name)						// convert name to URL format
{
	$name = str_replace(' ','-',$name);				// convert any spaces to hyphens
	$name = str_replace('/','-',$name);				// convert any spaces to hyphens
//	$name = str_replace('-','--',$name);			// convert any hyphens to double-hyphens
	return $name;
}
function nameURLdecode($name)						// de-convert name from URL format
{
	$name = preg_replace('#-([^-])#',' $1',$name);	// convert any non-doubled hypens to spaces
//	$name = str_replace('--','-',$name);			// convert any doubled hypens to hyphens
	return $name;
}
function nodeIDbreakout($nodeID)					// break nodeID into a level and a database ID
{
	if (preg_match("/^L([0-9]+)_([0-9]+)$/", $nodeID, $res))
	{
		return array(intval($res[1]),intval($res[2]));		// return level and dbID, in their native integer form
	}
	return NULL;
}
function titleFromFields($row)						// assemble a title_engg, coin_series, desc from field list
{
	$title_engg = $row['coin_detail_year'].(!empty($row['coin_detail_mintmark'])?'-'.$row['coin_detail_mintmark']:'').' '.$row['coin_detail_coin_series'];
	$title_engg .= ' '.$row['coin_detail_suffix'].' '.$row['coin_detail_major_variety'].' '.$row['coin_detail_die_variety'].($row['coin_detail_proof']=='y'?' Proof':'');
	$coin_series = $row['coin_detail_coin_series'];
	$desc = $row['coin_detail_year'].' '.$row['coin_detail_coin_series'].' '.$row['coin_detail_suffix'].' '.$row['coin_detail_major_variety'].' '.$row['coin_detail_die_variety'];
	return array($title_engg, $coin_series, $desc);
}
function currency_format_whole($val)
{
	return '$'.number_format($val);
}
function insertUpdateNoItemsMsg($instance)
{
	?>
	function updateNoItemsMsg<?php echo $instance; ?>(soldCnt, upcomingCnt)	// update messages if there are no items (this won't execute from within AJAX loaded HTML)
	{
		var msg = '<div style="text-align: center; font-size: 14px; font-weight: bold;"><i>There are no matching {TYPE} items</i><br /><br /></div>';
		// var soldCnt = <?php // echo $soldCnt;	// xfer PHP variables to JS ?>;
		// var upcomingCnt = <?php // echo $upcomingCnt; ?>;
		if (soldCnt+upcomingCnt == 0)		// if no entries at all
		{									// put messages in the footer
			$('#noclosed2')[0].innerHTML = msg.replace('{TYPE}','sold');
			$('#noopen2')[0].innerHTML = msg.replace('{TYPE}','upcoming');
		} else if (soldCnt == 0)			// if just sold count is empty
		{									// put message in the spot reserved earlier
			$('#noclosed')[0].innerHTML = msg.replace('{TYPE}','sold');
		} else if (upcomingCnt == 0)		// if just upcoming count is empty
		{									// put message in the spot reserved earlier
			$('#noopen')[0].innerHTML = msg.replace('{TYPE}','upcoming');
		}
	}
	<?php
}
?>