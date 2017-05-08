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
	'ajax',
	'inline',
	'cron',
	'autocomplete',
	/* 'jquery', */
	'modal',
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

define('MAX_COINS_TREE', 16);			// maximum coins displayable in nav tree, after that they need to go into main body pane
define('TOP_COIN_PRICE', 10);			// # of top coins to randomly select from, when automatically generating featured coin for any category

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
if (!$_GET['universe'])	// if no top-level universe found
	$_GET['universe'] = 'US-Coins';	// default

// input and global variables, also needed in descBody and histBody functions
$AucArchiveURI = 'Auction-Archive';
$universeDesc = nameURLdecode($_GET['universe']);
$denominationID = intval($_GET['denomination']);
$denominationDesc = '';
$coin_seriesID = intval($_GET['coin_series']);
$coin_seriesDesc = '';
$catRow = array();
$denominationDesc = '';	// long denom
$coin_seriesDesc = '';	// coin series
$coinID = intval($_GET['coin']);
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

if (@$_GET['ajaxdesc'])	// if this is a call for a new AJAX description body
{
	descBody();
	exit;
} else if (@$_GET['ajaxhist'])	// if this is a call for a new AJAX history body
{
	histBody();
	exit;
}

$page_title = SITE_NAME . ' - ' . 'PCGS Coin Auction Archive';
$page_head = '';
$page_desc = '';
$navcrumb = array($AucArchiveURI.'/' => 'Auction Archive');

if ($universeDesc)	// if URL provides the universe (US Coins, World Coins, etc)
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
	$page_head = $page_desc.'Prices for '.$universeDesc.' from our Auction Archive';
	$page_desc = substr($page_desc, 0, -3);
} else	// if nothing defined, show top header title
	$page_head = $page_desc.'Auction Archive';

/*
<!-- breadcrumb nav -->
<div style="padding-top:4px; padding-bottom:12px; padding-left:10px; font-size:10px; font-family:verdana">
	<span class="blue"><a href="http://127.0.0.1/gc/" rel="nofollow">Home</a></span>
	<span class="breadcrumb">&nbsp;&gt; <span class="blue"><a href="Auction-Archive/" rel="nofollow">PCGS Coin Auction Archive</a></span></span>
	<span class="breadcrumb">&nbsp;&gt; <span class="blue"><a href="Auction-Archive/US-Coins/" rel="nofollow">US Coins</a></span></span>
	<span class="breadcrumb">&nbsp;&gt; <span class="blue"><a href="Auction-Archive/US-Coins/4/" rel="nofollow">Small Cents</a></span></span>
	<strong><span class="breadcrumb">&nbsp;&gt; <span class="blue"><a href="Auction-Archive/US-Coins/4/17/" rel="nofollow">1909-1957 Lincoln Cents</a></span></span></strong>
</div>
<!-- / breadcrumb nav -->
*/

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
$SAFE_headinclude = '
<script src="functions/javascript/jquery-3.2.0.min.js"></script>
<script src="functions/javascript/jstree/jstree.min.js"></script>
<link rel="stylesheet" href="functions/javascript/jstree/themes/default/style.min.css" />
<style>
.jstree li a ins{
	display:none !important;
} 
</style>
';

$pprint_array = array('page_title','headinclude');
$ilance->template->construct_header('main');
$ilance->template->parse_if_blocks('main');
$ilance->template->pprint('main', $pprint_array);
?>

<script src="functions/javascript/jquery-3.2.0.min.js"></script>
<script src="functions/javascript/jstree/jstree.min.js"></script>
<link rel="stylesheet" href="functions/javascript/jstree/themes/default/style.min.css" />
<style type="text/css">
.tree li a ins {
	display:none !important;
} 

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

<div style="font-size: 18px; font-weight: bold; padding-bottom: 4px;"><?php echo $page_head; ?></div>
<table cellpadding="0" cellspacing="0"><tr>
<td valign="top" style="border: 1px solid #CCC; padding: 4px;">
	<div id="navtree" style="width: 300px; padding-right: 10px;">
		<ul>
			<li class="jstree-open"><span style="font-size: 16px; font-weight: bold;"><?php echo $universeDesc; ?></span>
			<ul>
			<?php

			$sql = "SELECT * FROM ".DB_PREFIX. "catalog_toplevel den ORDER BY denomination_sort";
			// echo 'SQL = '.$sql; exit;
			$select = $ilance->db->query($sql);
			// echo 'Num rows = '.$ilance->db->num_rows($select); exit;
			while ($denNavRow = $ilance->db->fetch_array($select))
			{
				// echo '<PRE>'; print_r($denNavRow); echo '</PRE>'; exit;
				$den_id = $denNavRow['id'];			// get ID of this denom row
				$onThisDNnode = ($denominationID == $den_id);
				?>
				<li id="L1_<?php echo $den_id; ?>" class="<?php echo ($onThisDNnode?'jstree-open':'jstree-closed'); ?>">
					<span id="L1s_<?php echo $den_id; ?>" style="<?php echo ($onThisDNnode?'color: #600;font-weight: bold;':''); ?>">
					<span id="L1t_<?php echo $den_id; ?>"><?php echo ilance_htmlentities($denNavRow['denomination_long']); ?></span>
					(<?php echo $denNavRow['auction_count'];  ?>)
					<span id="L1a_<?php echo $den_id; ?>" style="<?php echo ($onThisDNnode && !$coin_seriesID?'':'display: none;');	?>">&#10148;</span>
					</span>
				<?php
				if ($onThisDNnode)	// if this is a denomination tree node that should be open (0=no denom open)
				{
					$sql = "SELECT * FROM ".DB_PREFIX. "catalog_second_level cs WHERE cs.coin_series_denomination_no=$denominationID ORDER BY cs.coin_series_sort";
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
								<span id="L2s_<?php echo $cs_id; ?>" style="<?php echo ($onThisCSnode?'color: #600;font-weight: bold;':''); ?>">
								<span id="L2t_<?php echo $cs_id; ?>"><?php echo ilance_htmlentities($csNavRow['coin_series_name']); ?></span>
								(<?php echo $csNavRow['auction_count']; // .','.$qty_coins.'/'.$coinID ?>)
								<span id="L2a_<?php echo $cs_id; ?>" style="<?php echo ($onThisCSnode && !$coinID?'':'display: none;');	?>">&#10148;</span>
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
										<li data-jstree='{"disabled":true}'>...</li>
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
											<span id="L3s_<?php echo $cn_id; ?>" style="<?php echo ($onThisCNnode?'color: #600;font-weight: bold;':''); ?>">
											<span id="L3t_<?php echo $cn_id; ?>"><?php echo ilance_htmlentities($coinDescL3); ?></span>
											(<?php echo '?';	// $cnNavRow['PCGS'].'-'.$cnNavRow['auction_count'].','.$qty_coins.'/'.$coinID ?>)
											<span id="L3a_<?php echo $cn_id; ?>" style="<?php echo ($onThisCNnode?'':'display: none;');	?>">&#10148;</span>
											</span>
										</li>
										<?php
									}
									if ($isCutCoins)
									{
										?>
										<li data-jstree='{"disabled":true}'>...</li>
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
<script type="text/javascript" language="Javascript">
	$(function () { $('#navtree').jstree(); });
	$("#navtree").jstree({
		"core" : {
			"themes" : {
				"variant" : "small"
			}
		}
	});	
	$('#navtree').jstree('hide_icons')
	/*
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
</script>
<td id="mainbody" valign="top" style="width: 100%; padding-left: 10px; border: 0px solid black;">
	<?php descBody(); ?>
	<div id="histside" style="display: none;"><?php histBody(); ?></div>
</td>
</tr>
<tr>
<td colspan="2" id="histbody" valign="top" style="width: 100%; padding-left: 10px; border: 0px solid black;">
	<div id="histdown"><?php histBody(); ?></div>
</td>
</tr>
</table>
<?php
// echo '<PRE>'; print_r($_GET); echo '</PRE>';
$pprint_array = array();
$ilance->template->construct_footer('main');
$ilance->template->parse_if_blocks('main');
$ilance->template->pprint('main', $pprint_array);
exit();

function descBody()	// display description body in right pane
{
	global $ilance;

	global $AucArchiveURI;
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
	$sql = "SELECT p.id, p.project_id, p.currentprice, p.project_title,
		concat( 'uploads/attachments/auctions/', (
			floor( a.project_id /100 ) ) *100, '/', a.project_id, '/', a.filehash, '.attach'
		) AS imgpath
	FROM ".DB_PREFIX."projects p LEFT JOIN ".DB_PREFIX."attachment a ON a.project_id = p.project_id
	$sqlJoin
	WHERE $sqlWhere date_end > DATE_ADD(NOW(),INTERVAL -6 MONTH) ORDER BY currentprice DESC LIMIT ".TOP_COIN_PRICE;
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
		$image = HTTP_SERVER.'/image/400/268/'.$row['project_id'].'-1.jpg';
		$image = 'http://www.greatcollections.com/image/400/268/'.$row['project_id'].'-1.jpg';
		// echo '$title='.$title.'<BR>$image='.$image.'<BR>';
		?>
		<div style="float: right; border-left: 5px; text-align: center;">
		<a href="Coin/<?php echo $row['project_id'].'/'.construct_seo_url_name($title); ?>/" title="<?php echo ilance_htmlentities($title); ?>">
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
	<button style="float: right; margin-top: -15px; margin-right: 2%; outline:0; color: white; background-color: #C00; font-size: 20px; font-weight: bold; border-radius: 10px; moz-border-radius: 10px; webkit-border-radius: 10px; Xheight: 20px; Xwidth: 20px; border:1px #00 solid; box-shadow: 3 3 2 2 #black;"
		onClick="histSwap();">
		Add this to<br />your Wantlist
	</button>
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
	<strong><ul class="aa_list">
		<li class="aa_list">Lincoln Cent sells for $26,000 at GreatCollections!</li>
		<li class="aa_list">Circulated pennies can be worth more than you think</li>
		<li class="aa_list">Cents in the News</li>
	</ul></strong>
	<?php
}
function histBody()	// display history body in bottom pane
{
	global $ilance;

	global $AucArchiveURI;
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
		}
		.hist_center {
			border-left: 1px solid #CCC;
		}
		.hist_right {
			border-right: 1px solid #CCC;
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
		}

		.hist_line {
			border: 0;
			height: 1px;
			background: #CCC;
		}

		.hist_foot td {
			border-bottom: 1px solid #CCC;
		}

	</style>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="hist">
		<tr>
			<td colspan="6">
				<table align="right" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 10px; margin-right: 10px; border: 1px solid #CCC; padding: 4px; background-color: #efe;">
					<tr>
						<td style="padding-right: 6px;"><b>Grader</b></td>
						<td><nobr>
							<input type="checkbox" checked /> PCGS
							<input type="checkbox" checked /> NGC
							<input type="checkbox" /> ANACS
							<input type="checkbox" /> CAC
							<input type="checkbox" /> Other
						</nobr></td>
					</tr>
					<tr>
						<td style="vertical-align: middle;"><b>Grade</b></td>
						<td style="vertical-align: middle; position: relative;">
							<hr style="border: 0; height: 10px; background: #CCF;">
							<button style="position: absolute; left: 200px; top: 4px; background-color: #66C; color: white;"><b>60</b></button>
							<button style="position: absolute; right: 10px; top: 4px; background-color: #66C; color: white;"><b>68</b></button>
						</td>
					</tr>
				</table>
				<h3>Items Sold</h3>
			</td>
			<td colspan="3"><h3>Upcoming Items</h3></td>
		</tr>
		<tr class="hist_head">
			<td class="hist_left">Grade (qty)</td>
			<td>Item</td>
			<td align="right"><nobr>Sold Price</nobr></td>
			<td>Bids</td>
			<td>Photo</td>
			<td>Date</td>

			<td class="hist_center">Grade</td>
			<td>Item</td>
			<td class="hist_right" align="right">Bid</td>
		</tr>
		<tr>
			<td colspan="6" class="hist_left"><hr class="hist_line"></td>
			<td colspan="3" class="hist_center hist_right"><hr class="hist_line"></td>
		</tr>
		<tr>
			<td class="hist_left hist_grade">68 <span style="font-weight: normal;">(1)</span></td>
			<td>1954 Lincoln Cent PCGS Proof-68 RD DCAM</td>
			<td align="right">$13,500</td>
			<td><nobr>2 bids</nobr></td>
			<td align="center">&#128247;</td>
			<td>Oct 2013</td>

			<td class="hist_center hist_grade">68 <span style="font-weight: normal;">(41)</span>
				<br />
				<img src="http://www.greatcollections.com/image/400/268/444450-1.jpg" width="80" />
			</td>
			<td>1997 Lincoln Cent PCGS MS-68 RD</td>
			<td class="hist_right" align="right">$500</td>
		</tr>
		<tr>
			<td class="hist_left hist_grade">66+ <span style="font-weight: normal;">(6)</span></td>
			<td>1909-S Lincoln Cent V.D.B. PCGS MS-66+ RD</td>
			<td align="right">$23,227</td>
			<td><nobr>55 bids</nobr></td>
			<td align="center">&#128247;</td>
			<td>Nov 2015</td>

			<td class="hist_center hist_grade">66+ <span style="font-weight: normal;">(6)</span></td>
			<td>1974-D Lincoln Cent PCGS MS-66+ RD</td>
			<td class="hist_right" align="right">$40</td>
		</tr>
		<?php for ($i=0; $i<3; $i++) { ?>
			<tr>
				<td class="hist_left Xhist_grade"></td>
				<td>1909-S Lincoln Cent V.D.B. PCGS MS-66+ RD</td>
				<td align="right">$15,500</td>
				<td><nobr>35 bids</nobr></td>
				<td align="center">&#128247;</td>
				<td>Feb 2016</td>

				<td class="hist_center Xhist_grade"></td>
				<td>1974-D Lincoln Cent PCGS MS-66+ RD</td>
				<td class="hist_right" align="right">$42</td>
			</tr>
		<?php } ?>
		<tr>
			<td class="hist_left hist_grade">66 <span style="font-weight: normal;">(16)</span></td>
			<td>1909-S Lincoln Cent V.D.B. PCGS MS-66+ RD</td>
			<td align="right">$23,227</td>
			<td><nobr>55 bids</nobr></td>
			<td align="center">&#128247;</td>
			<td>Nov 2015</td>

			<td class="hist_center hist_grade">66 <span style="font-weight: normal;">(16)</span></td>
			<td>1974-D Lincoln Cent PCGS MS-66+ RD</td>
			<td class="hist_right" align="right">$40</td>
		</tr>
		<?php for ($i=0; $i<5; $i++) { ?>
			<tr>
				<td class="hist_left Xhist_grade"></td>
				<td>1909-S Lincoln Cent V.D.B. PCGS MS-66+ RD</td>
				<td align="right">$15,500</td>
				<td><nobr>35 bids</nobr></td>
				<td align="center">&#128247;</td>
				<td>Feb 2016</td>

				<td class="hist_center Xhist_grade"></td>
				<td>1974-D Lincoln Cent PCGS MS-66+ RD</td>
				<td class="hist_right" align="right">$42</td>
			</tr>
		<?php } ?>
		<tr class="hist_foot">
			<td colspan="6" class="hist_left"></td>
			<td colspan="3" class="hist_center hist_right"></td>
		</tr>
	</table>
	<br />
	<?php
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
//	$name = str_replace('-','--',$name);			// convert any hyphens to double-hyphens
	return $name;
}
function nameURLdecode($name)						// de-convert name from URL format... no purpose
{
	$name = preg_replace('#-([^-])#',' $1',$name);	// convert any non-doubled hypens to spaces
//	$name = str_replace('--','-',$name);			// convert any doubled hypens to hyphens
	return $name;
}
function titleFromFields($row)						// assemble a title_engg, coin_series, desc from field list
{
	$title_engg = $row['coin_detail_year'].(!empty($row['coin_detail_mintmark'])?'-'.$row['coin_detail_mintmark']:'').' '.$row['coin_detail_coin_series'];
	$title_engg .= ' '.$row['coin_detail_suffix'].' '.$row['coin_detail_major_variety'].' '.$row['coin_detail_die_variety'].($row['coin_detail_proof']=='y'?' Proof':'');
	$coin_series = $row['coin_detail_coin_series'];
	$desc = $row['coin_detail_year'].' '.$row['coin_detail_coin_series'].' '.$row['coin_detail_suffix'].' '.$row['coin_detail_major_variety'].' '.$row['coin_detail_die_variety'];
	return array($title_engg, $coin_series, $desc);
}
?>