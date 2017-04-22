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
        'search'
);

// #### load required javascript ###############################################
$jsinclude = array(
	'jquery',
	'functions','modal','ajax'
);

// #### setup script location ##################################################
define('LOCATION', 'selling');

// #### require backend ########################################################
require_once('./functions/config.php');
require_once DIR_CORE . 'functions_search.php';

// #### require shipping backend ###############################################
require_once(DIR_CORE . 'functions_shipping.php');
 $show['widescreen'] = true;
// #### setup default breadcrumb ###############################################
$area_title = $phrase['_access_denied'];
$page_title = SITE_NAME . ' - ' . ucfirst($ilance->GPC['cmd']);
$navcrumb = array("$ilpage[selling]" => $ilcrumbs["$ilpage[selling]"]);

if (empty($_SESSION['ilancedata']['user']['userid']) OR $_SESSION['ilancedata']['user']['userid'] == 0)
{
	refresh(HTTPS_SERVER . $ilpage['login'] . '?redirect=' . urlencode('sell.php' . print_hidden_fields($string = true, $excluded = array(), $questionmarkfirst = true)));
	exit();
}

if (!empty($ilance->GPC['crypted']))
{
	$uncrypted = decrypt_url($ilance->GPC['crypted']);
}

($apihook = $ilance->api('selling_top')) ? eval($apihook) : false;
	
// #### CREATING OR UPDATING PRODUCT AUCTION ###########################
 if (isset($_SESSION['ilancedata']['user']['userid']) AND $_SESSION['ilancedata']['user']['userid'] > 0)
 {
///currently selling
 	$ilance->GPC['page'] = (!isset($ilance->GPC['page']) OR isset($ilance->GPC['page']) AND $ilance->GPC['page'] <= 0) ? 1 : intval($ilance->GPC['page']);		
	$sql_limit = 'LIMIT ' . (($ilance->GPC['page'] - 1) * $ilconfig['globalfilters_maxrowsdisplaysubscribers']) . ',' . $ilconfig['globalfilters_maxrowsdisplaysubscribers'];

if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'current')
	{
	$area_title = 'Current Selling';
	
	//bug1782 tamil for sort starts
	
	
		$scriptpage = HTTP_SERVER . $ilpage['search'] . print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
		$ilance->GPC['action']=isset($ilance->GPC['action'])?$ilance->GPC['action']:0;
		$ilance->GPC['q']=isset($ilance->GPC['q'])?$ilance->GPC['q']:'';
		$ilance->GPC['sort']=isset($ilance->GPC['sort'])?$ilance->GPC['sort']:'99';	
		switch($ilance->GPC['action'])
		{
		case 'itemid':
			if($ilance->GPC['sort']!='12' && $ilance->GPC['sort']!='')
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>';
				
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left<img alt="" src="images/default/expand_collapsed.gif"></a></td> ';
			  
			}
			 
			else
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=11&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left<img alt="" src="images/default/expand_collapsed.gif"></a></td> ';
			  
			 }  
			 
			break;
			
		case 'itemtitle':
	  

			if($ilance->GPC['sort']!='22' && $ilance->GPC['sort']!='')
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left<img alt="" src="images/default/expand_collapsed.gif"></a></td> ';
			  
			}
			
			else
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=21&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left<img alt="" src="images/default/expand_collapsed.gif"></a></td> ';
			  
			}  

			break;
			
		case 'bids':
	  

			if($ilance->GPC['sort']!='32' && $ilance->GPC['sort']!='')
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left<img alt="" src="images/default/expand_collapsed.gif"></a></td> ';
			  
			}
			
			else
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=31&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left<img alt="" src="images/default/expand_collapsed.gif"></a></td> ';
			  
			}  
			
			break;
				
		case 'minbid/buynow':
	  

			if($ilance->GPC['sort']!='42' && $ilance->GPC['sort']!='')
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left<img alt="" src="images/default/expand_collapsed.gif"></a></td> ';
			  
			}
			
			else
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=41&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left<img alt="" src="images/default/expand_collapsed.gif"></a></td> ';
			  
			}  
			
			break;
		
		case 'currentbids':
	  

			if($ilance->GPC['sort']!='52' && $ilance->GPC['sort']!='')
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by Itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by Itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by Bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by Minbid/Buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by Currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by Time" style="text-decoration:underline">Time left</a></td> ';
			  
			}
			
			else
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by Itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by Itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=51&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left</a></td> ';
			   
			}  
			
			break;
		
		case 'timeleft':
	  

			if($ilance->GPC['sort']!='52' && $ilance->GPC['sort']!='')
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left</a><img alt="" src="images/default/expand_collapsed.gif"></td> ';
			  
			}
			
			else
			{
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id<img alt="" src="images/default/expand_collapsed.gif"></a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow<img alt="" src="images/default/expand_collapsed.gif"></a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids<img alt="" src="images/default/expand_collapsed.gif"></a></td>'; 
				$listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=61&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left<img alt="" src="images/default/expand.gif"></a></td> ';
			   
			}  
			
			break;
		
		default:		
				$listing1 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=12&action=itemid" title="Sort by itemid" style="text-decoration:underline">Item id</a></td> 
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=22&action=itemtitle" title="Sort by itemtitle" style="text-decoration:underline">Item Title</a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=32&action=bids" title="Sort by bids" style="text-decoration:underline">Bids</a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=42&action=minbid/buynow" title="Sort by minbid/buynow" style="text-decoration:underline">Min Bid/Buynow</a></td>
				<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=52&action=currentbids" title="Sort by currentbids" style="text-decoration:underline">Current Bids</a></td>'; 
				 $listing2 =  '<td><a href="'.$ilpage['sell'] . '?cmd=current&sort=62&action=timeleft" title="Sort by itemid" style="text-decoration:underline">Time left</a></td> '; 
		
		}
		
if ($ilance->GPC['sort']=='11') 
{
$orderby1 ="ORDER BY project_id ASC";
}
else if ($ilance->GPC['sort']=='12') 
{
$orderby1 ="ORDER BY project_id DESC";
}
else if ($ilance->GPC['sort']=='21') 
{
$orderby1 ="ORDER BY project_title ASC";
}
else if ($ilance->GPC['sort']=='22') 
{
$orderby1 ="ORDER BY project_title DESC";
}
else if ($ilance->GPC['sort']=='31') 
{
$orderby1 ="ORDER BY bids ASC";
}
else if ($ilance->GPC['sort']=='32') 
{
$orderby1 ="ORDER BY bids DESC";
}
else if ($ilance->GPC['sort']=='41') 
{
$orderby1 ="ORDER BY price ASC";
}
else if ($ilance->GPC['sort']=='42') 
{
$orderby1 ="ORDER BY price DESC";
}
else if ($ilance->GPC['sort']=='51') 
{
$orderby1 ="ORDER BY currentprice ASC";
}
else if ($ilance->GPC['sort']=='52') 
{
$orderby1 ="ORDER BY currentprice DESC";
}
else if ($ilance->GPC['sort']=='61') 
{
$orderby1 ="ORDER BY date_end ASC";
}
else if ($ilance->GPC['sort']=='62') 
{
$orderby1 ="ORDER BY date_end DESC";
}
else if ($ilance->GPC['sort']=='99') 
{
$orderby1 ="ORDER BY date_end ASC";
}
	
	//bug1782 tamil for sort ends
	 $SQL="
                 SELECT p.*,GREATEST(p.buynow_price,p.startprice) as price,a.filehash,
 UNIX_TIMESTAMP(p.date_end) - UNIX_TIMESTAMP('" . DATETIME24H . "') AS mytime, UNIX_TIMESTAMP(p.date_starts) - UNIX_TIMESTAMP('" . DATETIME24H . "') AS starttime,count(w.watching_project_id) as watchlist_count
                FROM " . DB_PREFIX . "projects p
				left join " . DB_PREFIX . "attachment a on a.project_id=p.project_id and a.attachtype='itemphoto' and a.visible='1'
				left join " . DB_PREFIX . "watchlist w on p.project_id = w.watching_project_id
				WHERE  	p.user_id = '".$_SESSION['ilancedata']['user']['userid']."'   
				AND     p.visible ='1'
				AND  	p.project_state = 'product'
				AND    	p.status = 'open'
				group by p.project_id
				$orderby1
                ";
    $scriptpage = HTTP_SERVER .'Sell/Current'. print_hidden_fields(true, array('do','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
	$sql_gcsell1 = $ilance->db->query($SQL);
	$number = $ilance->db->num_rows($sql_gcsell1);	

	$counter = (intval($ilance->GPC['page']) - 1) * fetch_perpage();
	$prevnext = print_pagnation($number, $ilconfig['globalfilters_maxrowsdisplaysubscribers'], intval($ilance->GPC['page']), $counter, $scriptpage);	
	
	$sql_gcsell = $ilance->db->query($SQL.$sql_limit);           
				
			if ($ilance->db->num_rows($sql_gcsell) > 0)
			{
				while ($res_gcsell = $ilance->db->fetch_array($sql_gcsell))
				{
						if(strlen($res_gcsell['filehash'])>1)
						$uselistr = HTTPS_SERVER . 'image.php?cmd=thumb&subcmd=itemphoto&id=' . $res_gcsell['filehash'] .'&w=170&h=105';
						else
					    $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
					   
					
					if ($ilconfig['globalauctionsettings_seourls'])
						{
						$item_path=HTTP_SERVER.'Coin/'.$res_gcsell['project_id'].'/'.construct_seo_url_name($res_gcsell['project_title']).'';
						$htm ='<a href="'.$item_path.'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
						}	                    
						else
					    $htm ='<a href="merch.php?id='.$res_gcsell['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
						
						
					   if($res_gcsell['bids'] > 0)
					   {
					    $mess = '<font color="green">Item Has '.$res_gcsell['bids'].' Bids </font>';
					   }
					   
					   if($res_gcsell['bids'] == 0 AND $res_gcsell['filtered_auctiontype'] == 'regular' AND $res_gcsell['buynow'] == 0)
					   {
					    $mess = 'Listed for<br>Auction';
					   }
					   
					   if($res_gcsell['filtered_auctiontype'] == 'regular' AND $res_gcsell['buynow'] == 1)
					   {
					    $mess = '<font color="blue"> Listed for <br>Auction &<br>Buy Now</font>';
					   }
					   
					   if($res_gcsell['filtered_auctiontype'] == 'fixed' AND $res_gcsell['buynow_price'] > 0 AND $res_gcsell['buynow'] == 1)
					   {
					    $mess = '<font color="blue"> Listed for<br>Buy Now </font>';
					   }
					   
					 if ($res_gc_active['timeleft'] > DATETIME24H)
						{
							$dif = $res_gcsell['starttime'];
							$ndays = floor($dif / 86400);
							$dif -= $ndays * 86400;
							$nhours = floor($dif / 3600);
							$dif -= $nhours * 3600;
							$nminutes = floor($dif / 60);
							$dif -= $nminutes * 60;
							$nseconds = $dif;
							$sign = '+';
							if ($res_gcsell['starttime'] < 0)
							{
								//$row['starttime'] = - $row['starttime'];
								$sign = '-';
								//$row['currentbid'] = '-';
							}
							if ($sign != '-')
							{
								if ($ndays != '0')
								{
									$project_time_left = $ndays . $phrase['_d_shortform'] . ', ';	
									$project_time_left .= $nhours . $phrase['_h_shortform'] . '+';
								}
								else if ($nhours != '0')
								{
									$project_time_left = $nhours . $phrase['_h_shortform'] . ', ';
									$project_time_left .= $nminutes . $phrase['_m_shortform'] . '+';
								}
								else
								{
									$project_time_left = $nminutes . $phrase['_m_shortform'] . ', ';
									$project_time_left .= $nseconds . $phrase['_s_shortform'] . '+';	
								}
							}
							//$row['timetostart'] = $project_time_left;
							//$row['timeleft'] = $phrase['_starts'] . ': ' . $row['timetostart'];
						}
						else
						{
							$dif = $res_gcsell['mytime'];
							$ndays = floor($dif / 86400);
							$dif -= $ndays * 86400;
							$nhours = floor($dif / 3600);
							$dif -= $nhours * 3600;
							$nminutes = floor($dif / 60);
							$dif -= $nminutes * 60;
							$nseconds = $dif;
							$sign = '+';
							if ($res_gcsell['mytime'] < 0)
							{
								//$row['mytime'] = - $rows['mytime'];
								$sign = '-';
							}
							
							if ($sign == '-')
							{
								$project_time_left = $phrase['_ended'];
								//$row['currentbid'] = '-';
							}
							else
							{
								if ($ndays != '0')
								{
									$project_time_left = $ndays . $phrase['_d_shortform'] . ', ';	
									$project_time_left .= $nhours . $phrase['_h_shortform'] . '+';
								}
								else if ($nhours != '0')
								{
									$project_time_left = $nhours . $phrase['_h_shortform'] . ', ';
									$project_time_left .= $nminutes . $phrase['_m_shortform'] . '+';
								}
								else
								{
									$project_time_left = $nminutes . $phrase['_m_shortform'] . ', ';
									$project_time_left .= $nseconds . $phrase['_s_shortform'] . '+';	
								}
							}
                                                        
							$res_gc_sell['timelef'] = $project_time_left;
						}
					 
						$views_trackers  = $res_gcsell['views'].' / '.($res_gcsell['users_tracked']+$res_gcsell['watchlist_count']);
					   
					  // End here murugan
					   
					$res_gc_sell['item_path']=$item_path;
					$res_gc_sell['thumbnail'] = $htm;
					$res_gc_sell['item_id'] = $res_gcsell['project_id'];
					$res_gc_sell['item_title'] = $res_gcsell['project_title'];
					$res_gc_sell['minbid_min'] = $res_gcsell['startprice'];
					$res_gc_sell['minbid_buynow'] = $res_gcsell['buynow_price'];
					$res_gc_sell['bids'] = $res_gcsell['bids'];
					//$res_gc_sell['timelef'] = $res_gcsell['date_end'];
					$res_gc_sell['status'] = $res_gcsell['status'];
					$res_gc_sell['description'] = $mess;
					$res_gc_sell['current_bid'] = $res_gcsell['currentprice'];
					$res_gc_sell['views_trackers']=$views_trackers;
					$res_gcselling[] = $res_gc_sell;
					}
				}
				else
				{				
				$res_gcselling['mm'] = 'Nofound';
				}
				
				$pprint_array = array('prevnext','listing2','listing1','daylist','monthlist','yearlist','bidamount','project_title','buyingreminders','sellingreminders','scheduledcount','itemsworth','expertsrevenue','expertsearch','jobcount','expertcount','itemcount','feedbackactivity','messagesactivity','recentlyviewedflash','tagcloud','main_servicecats_img','main_productcats_img','main_servicecats','main_productcats','lanceads_folder','two_column_category_buyers','two_column_service_categories','two_column_product_categories','remote_addr','rid','default_exchange_rate','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
	$ilance->template->fetch('main', 'sell_current.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('res_gc_itempending','res_gc_itemsold','res_gcselling','res_gcsolding','res_gc_returning'));
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();
 }
				
				//##########################
				//// items sold
if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'sold')
	{
	$area_title = 'Sold Items';
	if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'soldsearch')
	{
		$scriptpage = HTTP_SERVER .'Sell/Sold'. print_hidden_fields(true, array('do','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
		$endDate = DATETODAY;
		if($ilance->GPC['searchsell'] == 1)
		{
			 $value = DATEYESTERDAY;
		}
		else if($ilance->GPC['searchsell'] == 2)
		{
			 $value = SEVENDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 3)
		{
		 $value = THIRTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 4)
		{
		 $value = SIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 5)
		{
			 $value = NINETYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 6)
		{
			 $value = ONEEIGHTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 7)
		{
			 $value = THREESIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 8)
		{
			 $value = SEVENTWENTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 9)
		{
			 $value = THOUSANDEIGHTYDAYSAGO;
		}
		else
		{
			$value = '2011-01-01';
		}

	   	$gcsold = " SELECT * FROM " . DB_PREFIX . "projects   
                WHERE  	user_id = '".$_SESSION['ilancedata']['user']['userid']."'   
				AND     visible ='1'
				AND  	project_state = 'product'
				AND    	status = 'expired'	
				AND    	(haswinner = '1')
				AND   (date(date_end) <= '".$endDate."' AND date(date_end) >= '".$value."')
				GROUP BY project_id
				ORDER BY id ASC  ";  

	   		$sql_gcsold1 = $ilance->db->query($gcsold);
			$number = $ilance->db->num_rows($sql_gcsold1);	

	
			$sql_gcsold = $ilance->db->query($gcsold.$sql_limit);
		}
		else
		{
			$scriptpage = HTTP_SERVER .'Sell/Sold?'. print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
			$gcsold = " SELECT * FROM " . DB_PREFIX . "projects   
                WHERE  	user_id = '".$_SESSION['ilancedata']['user']['userid']."'   
				AND     visible ='1'
				AND  	project_state = 'product'
				AND    	status = 'expired'	
				AND    	(haswinner = '1')	
				AND   (date(date_end) <= '".DATETODAY."' AND date(date_end) >= '".SEVENDAYSAGO."')			
				GROUP BY project_id
				ORDER BY id ASC ";

			$sql_gcsold1 = $ilance->db->query($gcsold);
			$number = $ilance->db->num_rows($sql_gcsold1);	

			$sql_gcsold = $ilance->db->query($gcsold.$sql_limit);
				 
		}
		if(isset($ilance->GPC['searchsell']))
								{
								  if($ilance->GPC['searchsell'] == 1)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1" selected="selected">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 2)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 3)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3" selected="selected">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 4)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" selected="selected">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 5)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" >Last 60 days</option>
								<option value="5" selected="selected">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 6)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6" selected="selected">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								else if($ilance->GPC['searchsell'] == 7)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7" selected="selected">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 8)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8" selected="selected">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 9)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9" selected="selected">Last 1080 days</option>
								</select>';
								  }
								  else
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0" selected="selected">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>								
								</select>';
								  }
								  
								}
								else
								{
								$drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								}

		$counter = (intval($ilance->GPC['page']) - 1) * fetch_perpage();
		$prevnext = print_pagnation($number, $ilconfig['globalfilters_maxrowsdisplaysubscribers'], intval($ilance->GPC['page']), $counter, $scriptpage);	
								
				if ($ilance->db->num_rows($sql_gcsold) > 0)
				{
				while ($res_gcsold = $ilance->db->fetch_array($sql_gcsold))
					{
					
					$sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gcsold['project_id']."'
                                               AND attachtype='itemphoto'
                                               
                       ");
                $fetch_new=$ilance->db->fetch_array($sql_atty);
                               
					   if($ilance->db->num_rows($sql_atty) == 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image.php?cmd=thumb&subcmd=itemphoto&id=' . $fetch_new['filehash'] .'&w=170&h=105';
							   
							   	//nov 28 for seo		   
							   
							   
				if ($ilconfig['globalauctionsettings_seourls'])
				{
						$item_path=HTTP_SERVER.'Coin/'.$res_gcsold['project_id'].'/'.construct_seo_url_name($res_gcsold['project_title']).'';
						$htm ='<a href="Coin/'.$res_gcsold['project_id'].'/'.construct_seo_url_name($res_gcsold['project_title']).'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
	            }
				 else
					    $htm ='<a href="merch.php?id='.$res_gcsold['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>'; 
							   
							   //$htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcsell['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
						$sql_inv = $ilance->db->query("SELECT * FROM " . DB_PREFIX . "invoices
                                               WHERE projectid = '".$res_gcsold['project_id']."'
                                               AND p2b_user_id ='".$_SESSION['ilancedata']['user']['userid']."'");
					  
					   if($ilance->db->num_rows($sql_inv) > 0)
					   {
					    	$fetch_inv=$ilance->db->fetch_array($sql_inv);
							// murugan changes on feb 08 
							/*$res_gc_sold['invoice'] = '<a href="invoicepayment.php?cmd=view&txn='.$fetch_inv['transactionid'].'">'.$fetch_inv['invoiceid'].'</a>';*/
							$explo = explode(' ',$fetch_inv['createdate']);
					    	// murugan changes on feb 08 
							//$res_gc_sold['invoice'] = '<a href="invoicepayment.php?cmd=view&txn='.$fetch_inv['transactionid'].'">'.$fetch_inv['invoiceid'].'</a>';
							$res_gc_sold['invoice'] = '<a href="consignor_statement.php?date='.$explo[0].'">'.$fetch_inv['invoiceid'].'</a>';
							
							if($fetch_inv['status'] == 'paid')
							{
								$invdate = '<br>'.date('m/d/Y',strtotime($fetch_inv['paiddate']));
								$res_gc_sold['status'] = 'Paid on'.$invdate;
							}
							else
							{
							  $invdate = '<br>'.date('m/d/Y',strtotime($fetch_inv['duedate']));
							  $res_gc_sold['status'] = 'To Be Paid on '.$invdate;
							}
					   }
					   else
					   {
					    	$res_gc_sold['invoice'] = 'Offline Payment';
							$res_gc_sold['status'] = 'Ended';
					   }
					   $selbid = $ilance->db->query("SELECT bidamount FROM " . DB_PREFIX . "project_bids
					   								WHERE project_id = '".$res_gcsold['project_id']."'
													AND bidstatus = 'awarded'");
						if($ilance->db->num_rows($selbid) > 0)
						{
							$resbid = $ilance->db->fetch_array($selbid);
							$res_gc_sold['current_bid'] = $resbid['bidamount'];
						}
						else
						{
							$res_gc_sold['current_bid'] = $res_gcsold['currentprice'];
						}
						
					$res_gc_sold['item_path']=$item_path;
					$res_gc_sold['thumbnail'] = $htm;
					$res_gc_sold['item_id'] = $res_gcsold['project_id'];
					//$res_gc_sold['item_title'] = '<a href="' . $ilpage['selling'] . '?cmd=product-management&amp;state=product&amp;id='.$res_gcsold['project_id'].'">'.$res_gcsold['project_title'] .'</a>';
					$res_gc_sold['item_title'] = $res_gcsold['project_title'];
					$res_gc_sold['minbid_buynow'] = $res_gcsold['startprice'];
					$res_gc_sold['bids'] = $res_gcsold['bids'];
					$res_gc_sold['date_end'] = date('m-d-Y',strtotime($res_gcsold['date_end']));
					//$res_gc_sold['status'] = 'Ended';					
					$res_gc_sold['description'] = 'Item Sold';
					//$res_gc_sold['current_bid'] = $resbid['bidamount'];
					$res_gc_itemsold[] = $res_gc_sold;
					}
				}
				else
				{				
				$res_gc_itemsold['mm'] = 'Nofound';
				}
				$pprint_array = array('prevnext','drop_value','daylist','monthlist','yearlist','bidamount','project_title','buyingreminders','sellingreminders','scheduledcount','itemsworth','expertsrevenue','expertsearch','jobcount','expertcount','itemcount','feedbackactivity','messagesactivity','recentlyviewedflash','tagcloud','main_servicecats_img','main_productcats_img','main_servicecats','main_productcats','lanceads_folder','two_column_category_buyers','two_column_service_categories','two_column_product_categories','remote_addr','rid','default_exchange_rate','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
	$ilance->template->fetch('main', 'sell_sold.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('res_gc_itempending','res_gc_itemsold','res_gcselling','res_gcsolding','res_gc_returning'));
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();
				
}
				//// items sold end
				//work by karthik
				//##########################
				//// buy now sold
if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'buynowsold')
	{
	$area_title = 'Buynow Item Sold';
	if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'soldsearch')
	{
		$scriptpage = HTTP_SERVER .'Sell/Buynowsold'. print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);      
		$endDate = DATETODAY;
		if($ilance->GPC['searchsell'] == 1)
		{
			 $value = DATEYESTERDAY;
		}
		else if($ilance->GPC['searchsell'] == 2)
		{
			 $value = SEVENDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 3)
		{
		 $value = THIRTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 4)
		{
		 $value = SIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 5)
		{
			 $value = NINETYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 6)
		{
			 $value = ONEEIGHTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 7)
		{
			 $value = THREESIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 8)
		{
			 $value = SEVENTWENTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 9)
		{
			 $value = THOUSANDEIGHTYDAYSAGO;
		}
		else
		{
			$value = '2011-01-01';
		}

	   		$buynw= " SELECT * FROM " . DB_PREFIX . "buynow_orders 
                WHERE  	owner_id = '".$_SESSION['ilancedata']['user']['userid']."'   
				AND   (date(orderdate) <= '".$endDate."' AND date(orderdate) >= '".$value."')  ";  
	   		
	   		$sql_buynw1 = $ilance->db->query($buynw);
			$number = $ilance->db->num_rows($sql_buynw1);	

	
			$sql_buynw = $ilance->db->query($buynw.$sql_limit);
		}
		else  
		{
			$scriptpage = HTTP_SERVER .'Sell/Buynowsold?'. print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
			$buynw= " SELECT * FROM " . DB_PREFIX . "buynow_orders 
                WHERE  	owner_id = '".$_SESSION['ilancedata']['user']['userid']."'   
				AND   (date(orderdate) <= '".DATETODAY."' AND date(orderdate) >= '".SEVENDAYSAGO."') "; 
			$sql_buynw1 = $ilance->db->query($buynw);
			$number = $ilance->db->num_rows($sql_buynw1);	
	
			$sql_buynw = $ilance->db->query($buynw.$sql_limit);
		
		}
		if(isset($ilance->GPC['searchsell']))
								{
								  if($ilance->GPC['searchsell'] == 1)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1" selected="selected">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 2)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 3)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3" selected="selected">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 4)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" selected="selected">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 5)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" >Last 60 days</option>
								<option value="5" selected="selected">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 6)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6" selected="selected">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 7)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7" selected="selected">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 8)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8" selected="selected">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 9)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9" selected="selected">Last 1080 days</option>
								</select>';
								  }
								  else
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0" selected="selected">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								 
								 }							
								  
								}
								else
								{
								$drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								}
	$counter = (intval($ilance->GPC['page']) - 1) * fetch_perpage();
	$prevnext = print_pagnation($number, $ilconfig['globalfilters_maxrowsdisplaysubscribers'], intval($ilance->GPC['page']), $counter, $scriptpage);		
				if ($ilance->db->num_rows($sql_buynw) > 0)
				{
				while ($res_gcsold = $ilance->db->fetch_array($sql_buynw))
					{
			
					
					$sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gcsold['project_id']."'
                                               AND attachtype='itemphoto'
                                               
                       ");
                $fetch_new=$ilance->db->fetch_array($sql_atty);
				
				 
					   if($ilance->db->num_rows($sql_atty) == 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image.php?cmd=thumb&subcmd=itemphoto&id=' . $fetch_new['filehash'] .'&w=170&h=105';
							   //nov 28 for seo		   
				if ($ilconfig['globalauctionsettings_seourls'])
				{
						
						$htm ='<a href="Coin/'.$res_gcsold['project_id'].'/'.construct_seo_url_name($res_gcsold['project_title']).'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
				}
	            else
					    $htm ='<a href="merch.php?id='.$res_gcsold['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
							   
							   //$htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcsell['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
					   
					  // fetch_auction('p',pid)
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
					  
						$sql_inv = $ilance->db->query("SELECT * FROM " . DB_PREFIX . "invoices
                                               WHERE projectid = '".$res_gcsold['project_id']."'
                                               AND p2b_user_id ='".$_SESSION['ilancedata']['user']['userid']."'											  
											   AND buynowid = '".$res_gcsold['orderid']."' ");
					  
					   
					   $selbid = $ilance->db->query("SELECT bidamount FROM " . DB_PREFIX . "project_bids
					   								WHERE project_id = '".$res_gcsold['project_id']."'
													AND bidstatus = 'awarded'");
						if($ilance->db->num_rows($selbid) > 0)
						{
							$resbid = $ilance->db->fetch_array($selbid);
							$res_gc_sold['current_bid'] = $resbid['bidamount'];
						}
						else
						{
							$res_gc_sold['current_bid'] = $res_gcsold['currentprice'];
						}
						
			$sql_gcsold = $ilance->db->query("
                 SELECT *
                FROM " . DB_PREFIX . "projects   
                WHERE  	user_id = '".$_SESSION['ilancedata']['user']['userid']."'   
				 AND project_id = '".$res_gcsold['project_id']."'
				 GROUP BY project_id			
				ORDER BY id ASC
                "); 
				$res_gcsold1=$ilance->db->fetch_array($sql_gcsold);
				//$explo = explode(' ',$res_gcsold1['date_end']);
				
						if($ilance->db->num_rows($sql_inv) > 0)
					   {
					   		$explo = explode(' ',$res_gcsold1['date_end']);
					    	$fetch_inv=$ilance->db->fetch_array($sql_inv);
							$item_path=HTTP_SERVER.'Coin/'.$res_gcsold1['project_id'].'/'.construct_seo_url_name($res_gcsold1['project_title']).'';
							// murugan changes on feb 08 
							//$res_gc_sold['invoice'] = '<a href="invoicepayment.php?cmd=view&txn='.$fetch_inv['transactionid'].'">'.$fetch_inv['invoiceid'].'</a>';
							$res_gc_sold['invoice'] = '<a href="consignor_statement.php?date='.$explo[0].'">'.$fetch_inv['invoiceid'].'</a>';
							if($fetch_inv['status'] == 'paid')
							{
								$invdate = date('m/d',strtotime($fetch_inv['paiddate']));
								$res_gc_sold['status'] = 'Paid on'.$invdate;
							}
							else
							{
							  $invdate = date('m/d',strtotime($fetch_inv['duedate']));
							  $res_gc_sold['status'] = 'To Be Paid on '.$invdate;
							}
					   }
					   else
					   {
					    	$res_gc_sold['invoice'] = 'Offline Payment';
							$res_gc_sold['status'] = 'Ended';
					   }
				//$res_gcsold1=$ilance->db->fetch_array($sql_gcsold); 
				if($res_gcsold1['status']=='closed'||$res_gcsold1['status']=='expired')
				{
				$res_gc_sold['status'] ='Ended';	
				}
				else
				{
				$res_gc_sold['status'] ='Open';	
				}
					$res_gc_sold['item_path'] = $item_path;
					$res_gc_sold['thumbnail'] = $htm;
					$res_gc_sold['item_id'] = $res_gcsold['project_id'];
					//$res_gc_sold['item_title'] = '<a href="' . $ilpage['selling'] . '?cmd=product-management&amp;state=product&amp;id='.$res_gcsold['project_id'].'">'.$res_gcsold['project_title'] .'</a>';
					$res_gc_sold['item_title'] = $res_gcsold1['project_title'];
					$res_gc_sold['minbid_buynow'] = $res_gcsold['amount'];
					$res_gc_sold['bids'] = $res_gcsold['bids'];
					$res_gc_sold['qty'] = $res_gcsold['qty'];
					$res_gc_sold['date_end'] = date('m-d-Y',strtotime($res_gcsold['date_end']));
					$res_gc_sold['orderdate'] = date('m-d-Y',strtotime($res_gcsold['orderdate']));		
					$res_gc_sold['description'] = 'Item Sold';
					//$res_gc_sold['current_bid'] = $resbid['bidamount'];
					$res_gc_itemsold[] = $res_gc_sold;
					}
				}
				
				else
				{				
				$res_gc_itemsold['mm'] = 'Nofound';
				}
				$pprint_array = array('prevnext','drop_value','daylist','monthlist','yearlist','bidamount','project_title','buyingreminders','sellingreminders','scheduledcount','itemsworth','expertsrevenue','expertsearch','jobcount','expertcount','itemcount','feedbackactivity','messagesactivity','recentlyviewedflash','tagcloud','main_servicecats_img','main_productcats_img','main_servicecats','main_productcats','lanceads_folder','two_column_category_buyers','two_column_service_categories','two_column_product_categories','remote_addr','rid','default_exchange_rate','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
	$ilance->template->fetch('main', 'sell_buynowsold.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('res_gc_itempending','res_gc_itemsold','res_gcselling','res_gcsolding','res_gc_returning'));
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();
				
}
				//// buy now sold end
				
//buy now unsold

if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'buynowunsold')
{
$area_title = 'Buynow Item Unsold';
	if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'unsoldsearch')
	{
		$scriptpage = HTTP_SERVER .'Sell/Buynowunsold'. print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false); 
		$endDate = DATETODAY;
		if($ilance->GPC['searchsell'] == 1)
		{
			 $value = DATEYESTERDAY;
		}
		else if($ilance->GPC['searchsell'] == 2)
		{
			 $value = SEVENDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 3)
		{
		 $value = THIRTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 4)
		{
		 $value = SIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 5)
		{
			 $value = NINETYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 6)
		{
			 $value = ONEEIGHTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 7)
		{
			 $value = THREESIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 8)
		{
			 $value = SEVENTWENTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 9)
		{
			 $value = THOUSANDEIGHTYDAYSAGO;
		}
		else
		{
			$value = '2011-01-01';
		}
	
	   	$buynww= " SELECT * FROM " . DB_PREFIX . "projects 
                WHERE  	user_id = '".$_SESSION['ilancedata']['user']['userid']."'
				AND buynow_qty != '0'
				AND (hasbuynowwinner = '1' OR hasbuynowwinner = '0')
				AND		filtered_auctiontype = 'fixed'
				AND status = 'expired'
				AND   (date(date_end) <= '".$endDate."' AND date(date_end) >= '".$value."')
				GROUP BY project_id  ";  
	   	$sql_buynww1 = $ilance->db->query($buynww);
		$number = $ilance->db->num_rows($sql_buynww1);	

		$sql_buynww = $ilance->db->query($buynww.$sql_limit);

		}
		else
		{
		/*$sql_buynww= $ilance->db->query("
                SELECT *
                FROM " . DB_PREFIX . "buynow_orders 
                WHERE  	owner_id = '".$_SESSION['ilancedata']['user']['userid']."'
				GROUP BY project_id   
                "); */
			$scriptpage = HTTP_SERVER .'Sell/Buynowunsold?'. print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
			$buynww= " SELECT * FROM " . DB_PREFIX . "projects 
                WHERE  	user_id = '".$_SESSION['ilancedata']['user']['userid']."'
				AND buynow_qty != '0'
				AND (hasbuynowwinner = '1' OR hasbuynowwinner = '0')
				AND		filtered_auctiontype = 'fixed'
				AND status = 'expired'
				AND   (date(date_end) <= '".DATETODAY."' AND date(date_end) >= '".SEVENDAYSAGO."')	
				GROUP BY project_id  "; 

			$sql_buynww1 = $ilance->db->query($buynww);
			$number = $ilance->db->num_rows($sql_buynww1);	

			$sql_buynww = $ilance->db->query($buynww.$sql_limit);
		
		}
		if(isset($ilance->GPC['searchsell']))
								{
								  if($ilance->GPC['searchsell'] == 1)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1" selected="selected">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 2)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 3)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3" selected="selected">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 4)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" selected="selected">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 5)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" >Last 60 days</option>
								<option value="5" selected="selected">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 6)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6" selected="selected">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 7)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7" selected="selected">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 8)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8" selected="selected">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 9)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9" selected="selected">Last 1080 days</option>
								</select>';
								  }
								  else
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0"  selected="selected">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  
								}
								else
								{
								$drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								}
	$counter = (intval($ilance->GPC['page']) - 1) * fetch_perpage();
	$prevnext = print_pagnation($number, $ilconfig['globalfilters_maxrowsdisplaysubscribers'], intval($ilance->GPC['page']), $counter, $scriptpage);	
			
				if ($ilance->db->num_rows($sql_buynww) > 0)
				{
				while ($res_gcsoldw = $ilance->db->fetch_array($sql_buynww))
					 {
					
					
					$sql_gcsold = $ilance->db->query("
                 SELECT *
                FROM " . DB_PREFIX . "projects   
                WHERE project_id = '".$res_gcsoldw['project_id']."' 	
				AND user_id = '".$_SESSION['ilancedata']['user']['userid']."'   
				AND     visible ='1'
				AND  	project_state = 'product'
				AND		filtered_auctiontype = 'fixed'
				AND    	status = 'expired'	
				AND  buynow_qty !='0'	
				GROUP BY project_id
				ORDER BY id ASC
                ");
				
				while ($res_gcsold = $ilance->db->fetch_array($sql_gcsold))
					 {
			
					
					$sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gcsold['project_id']."'
                                               AND attachtype='itemphoto'
                                               
                       ");
                $fetch_new=$ilance->db->fetch_array($sql_atty);
				
				 
					   if($ilance->db->num_rows($sql_atty) == 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image.php?cmd=thumb&subcmd=itemphoto&id=' . $fetch_new['filehash'] .'&w=170&h=105';
							      //nov 28 for seo
					 if ($ilconfig['globalauctionsettings_seourls'])
					 {
					   $item_path=HTTP_SERVER.'Coin/'.$res_gcsold['project_id'].'/'.construct_seo_url_name($res_gcsold['project_title']).'';
						$htm ='<a href="Coin/'.$res_gcsold['project_id'].'/'.construct_seo_url_name($res_gcsold['project_title']).'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					 }
	                     else
					    $htm ='<a href="merch.php?id='.$res_gcsold['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
							   
							  // $htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcsell['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
					   
					  // fetch_auction('p',pid)
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
					$res_gc_sold['item_path'] = $item_path;
					$res_gc_sold['thumbnail'] = $htm;
					$res_gc_sold['item_id'] = $res_gcsold['project_id'];
				
					$res_gc_sold['item_title'] = $res_gcsold['project_title'];
					$res_gc_sold['minbid_buynow'] = $res_gcsold['buynow_price'];
					//$res_gc_sold['bids'] = $res_gcsold['bids'];
					$res_gc_sold['qty'] = $res_gcsold['buynow_qty'];
					
					$res_gc_sold['date_end'] =  date('m-d-Y',strtotime($res_gcsold['date_end']));
									
					$res_gc_sold['status'] = 'Ended';
					//$res_gc_sold['current_bid'] = $resbid['bidamount'];
					$res_gc_itemsold[] = $res_gc_sold;
					}
					}
				}
				
				else
				{				
				$res_gc_itemsold['mm'] = 'Nofound';
				}
				$pprint_array = array('prevnext','drop_value','daylist','monthlist','yearlist','bidamount','project_title','buyingreminders','sellingreminders','scheduledcount','itemsworth','expertsrevenue','expertsearch','jobcount','expertcount','itemcount','feedbackactivity','messagesactivity','recentlyviewedflash','tagcloud','main_servicecats_img','main_productcats_img','main_servicecats','main_productcats','lanceads_folder','two_column_category_buyers','two_column_service_categories','two_column_product_categories','remote_addr','rid','default_exchange_rate','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
	$ilance->template->fetch('main', 'sell_buynowunsold.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('res_gc_itempending','res_gc_itemsold','res_gcselling','res_gcsolding','res_gc_returning'));
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();
				
}				
				///#####################################
				//item unsold
if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'unsold')
	{
	$area_title = 'Item Unsold';
    if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'unsoldsearch')
	{
		$scriptpage = HTTP_SERVER .'Sell/Unsold'. print_hidden_fields(true, array('do','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
		$endDate = DATETODAY;
		if($ilance->GPC['searchsell'] == 1)
		{
			 $value = DATEYESTERDAY;
		}
		else if($ilance->GPC['searchsell'] == 2)
		{
			 $value = SEVENDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 3)
		{
		 $value = THIRTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 4)
		{
		 $value = SIXTYDAYSAGO;
		} 
		else if($ilance->GPC['searchsell'] == 5)
		{
			 $value = NINETYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 6)
		{
			 $value = ONEEIGHTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 7)
		{
			 $value = THREESIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 8)
		{
			 $value = SEVENTWENTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 9)
		{
			 $value = THOUSANDEIGHTYDAYSAGO;
		}
		else
		{
			$value = '2011-01-01';
		}
		//	((c.site_id=1 and c.sold_qty=0) or c.site_id=0) And  	
				$gcunsold = " SELECT p.* FROM " . DB_PREFIX . "projects  p left join " . DB_PREFIX . "coins c  on c.coin_id=p.project_id
                WHERE  (c.site_id=0 or (c.site_id=1 and c.sold_qty=0)) and
				p.user_id = '".$_SESSION['ilancedata']['user']['userid']."'   
				AND     p.visible ='1'
				AND     p.haswinner = '0'
				AND 	p.hasbuynowwinner != '1'
				AND  	p.project_state = 'product'
				AND		p.filtered_auctiontype = 'regular'
				AND    	p.status = 'expired'	
				AND   (date(p.date_end) <= '".$endDate."' AND date(p.date_end) >= '".$value."')
				GROUP BY p.project_id	
				ORDER BY p.id ASC  ";
					
				$sql_gcunsold1 = $ilance->db->query($gcunsold);
				$number = $ilance->db->num_rows($sql_gcunsold1);	

				$sql_gcunsold = $ilance->db->query($gcunsold.$sql_limit);	
		}
		else
		{ 
			//((c.site_id=1 and c.sold_qty=0) or c.site_id=0) And  	
			$scriptpage = HTTP_SERVER .'Sell/Unsold?'. print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false); 
		        $gcunsold = " SELECT p.* FROM " . DB_PREFIX . "projects p   left join " . DB_PREFIX . "coins c  on c.coin_id=p.project_id
                WHERE  (c.site_id=0 or (c.site_id=1 and c.sold_qty=0)) and
				p.user_id = '".$_SESSION['ilancedata']['user']['userid']."'   
				AND     p.visible ='1'
				AND     p.haswinner = '0'
				AND 	p.hasbuynowwinner != '1'
				AND  	p.project_state = 'product'
				AND		p.filtered_auctiontype = 'regular'
				AND    	p.status = 'expired'	
				AND   (date(p.date_end) <= '".DATETODAY."' AND date(p.date_end) >= '".SEVENDAYSAGO."')	
				GROUP BY p.project_id		
				ORDER BY p.id ASC ";

                $sql_gcunsold1 = $ilance->db->query($gcunsold);
				$number = $ilance->db->num_rows($sql_gcunsold1);	

				$sql_gcunsold = $ilance->db->query($gcunsold.$sql_limit);
					
		}	
		
		if(isset($ilance->GPC['searchsell']))
								{
								  if($ilance->GPC['searchsell'] == 1)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1" selected="selected">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 2)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 3)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3" selected="selected">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 4)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" selected="selected">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 5)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" >Last 60 days</option>
								<option value="5" selected="selected">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 6)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6" selected="selected">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
							  
								  else if($ilance->GPC['searchsell'] == 7)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7" selected="selected">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 8)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8" selected="selected">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 9)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9" selected="selected">Last 1080 days</option>
								</select>';
								  }
								  else
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0" selected="selected">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  
								}
								else
								{
								$drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								}

		$counter = (intval($ilance->GPC['page']) - 1) * fetch_perpage();
		$prevnext = print_pagnation($number, $ilconfig['globalfilters_maxrowsdisplaysubscribers'], intval($ilance->GPC['page']), $counter, $scriptpage);								
								
				if ($ilance->db->num_rows($sql_gcunsold) > 0)
				{
				//status
				while ($res_gcunsold = $ilance->db->fetch_array($sql_gcunsold))
					{
					
					$sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gcunsold['project_id']."'
                                               AND attachtype='itemphoto'
                                               
                       ");
                $fetch_new=$ilance->db->fetch_array($sql_atty);
                               
					   if($ilance->db->num_rows($sql_atty) == 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image.php?cmd=thumb&subcmd=itemphoto&id=' . $fetch_new['filehash'] .'&w=170&h=105';
							   
							      					   //nov 28 for seo
					  if ($ilconfig['globalauctionsettings_seourls'])
					  {
						$item_path=HTTP_SERVER.'Coin/'.$res_gcunsold['project_id'].'/'.construct_seo_url_name($res_gcunsold['project_title']).'';
						$htm ='<a href="Coin/'.$res_gcunsold['project_id'].'/'.construct_seo_url_name($res_gcunsold['project_title']).'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					  }
	                     else
					    $htm ='<a href="merch.php?id='.$res_gcunsold['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
							   
							  // $htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcsell['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
					
					$res_gc_unsold['item_path'] = $item_path;
					$res_gc_unsold['thumbnail'] = $htm;
					$res_gc_unsold['item_id'] = $res_gcunsold['project_id'];
					$res_gc_unsold['item_title'] = $res_gcunsold['project_title'];
					$res_gc_unsold['minbid_min'] = $res_gcunsold['startprice'];
					$res_gc_unsold['minbid_buynow'] = $res_gcunsold['buynow_price'];
					$res_gc_unsold['bids'] = $res_gcunsold['bids'];
					$res_gc_unsold['description'] = 'Item Unsold';
					$res_gc_unsold['timelef'] = date('m-d-Y',strtotime($res_gcunsold['date_end']));
					$res_gc_unsold['status'] = 'Ended';
					$res_gcsolding[] = $res_gc_unsold;
					}
				}
				else
				{				
				$res_gcsolding['mm'] = 'Nofound';
				}
			$pprint_array = array('prevnext','drop_value','daylist','monthlist','yearlist','bidamount','project_title','buyingreminders','sellingreminders','scheduledcount','itemsworth','expertsrevenue','expertsearch','jobcount','expertcount','itemcount','feedbackactivity','messagesactivity','recentlyviewedflash','tagcloud','main_servicecats_img','main_productcats_img','main_servicecats','main_productcats','lanceads_folder','two_column_category_buyers','two_column_service_categories','two_column_product_categories','remote_addr','rid','default_exchange_rate','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
	$ilance->template->fetch('main', 'sell_unsold.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('res_gc_itempending','res_gc_itemsold','res_gcselling','res_gcsolding','res_gc_returning'));
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();	
	}
	//error_reporting(E_ALL);
				//item unsold end
				//#####################################
				//item pennding
if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'pending')
{
	$ilance->GPC['pages'] = (!isset($ilance->GPC['pages']) OR isset($ilance->GPC['pages']) AND $ilance->GPC['pages'] <= 0) ? 1 : intval($ilance->GPC['pages']);				
	$area_title = 'Item Pending';
	$scriptpage = HTTP_SERVER .'Sell/Pending'. print_hidden_fields(true, array('do','cmd','pages','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
	if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'pendingsearch')
	{
	    $endDate = DATETODAY;
		if($ilance->GPC['searchsell'] == 1)
		{
			 $value = DATEYESTERDAY;
		}
		else if($ilance->GPC['searchsell'] == 2)
		{
			 $value = SEVENDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 3)
		{
		 $value = THIRTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 4)
		{
		 $value = SIXTYDAYSAGO;
		} 
		else if($ilance->GPC['searchsell'] == 5)
		{
			 $value = NINETYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 6)
		{
			 $value = ONEEIGHTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 7)
		{
			 $value = THREESIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 8)
		{
			 $value = SEVENTWENTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 9)
		{
			 $value = THOUSANDEIGHTYDAYSAGO;
		}
		else
		{
			$value = '2011-01-01';
		}
		
			$gccoinpend = " SELECT p.* FROM " . DB_PREFIX . "projects   p left join " . DB_PREFIX . "coins c  on c.coin_id=p.project_id
                WHERE  	p.user_id = '".$_SESSION['ilancedata']['user']['userid']."' 				
				AND     p.visible ='1'
				AND     p.haswinner = '0'
				AND		(p.hasbuynowwinner ='0' OR (p.hasbuynowwinner ='1' AND p.buynow_qty != '0'))
				AND  	p.project_state = 'product'
				AND    	p.status = 'expired'				
				AND   (date(p.date_end) <= '".$endDate."' AND date(p.date_end) >= '".$value."')						
				group by p.id ";
			
			$sql_gccoinpend1 = $ilance->db->query($gccoinpend);
			$number = $ilance->db->num_rows($sql_gccoinpend1);	

			$sql_gccoinpend = $ilance->db->query($gccoinpend.$sql_limit);	
			
		}
		else
		{ 
		$gcpend = " SELECT * FROM " . DB_PREFIX . "projects pj,
				" . DB_PREFIX . "coins c ,
				" . DB_PREFIX . "catalog_coin cc, 
					" . DB_PREFIX . "catalog_second_level cs, 
					" . DB_PREFIX . "catalog_toplevel cd
                WHERE c.coin_id=pj.project_id
				AND     (c.site_id=0 or (c.site_id=1 and c.sold_qty=0)) 
				AND     pj.user_id = '".$_SESSION['ilancedata']['user']['userid']."'				
				AND     pj.visible ='1'
				AND     pj.haswinner = '0'
				AND		(pj.hasbuynowwinner ='0' OR (pj.hasbuynowwinner ='1' AND pj.buynow_qty != '0'))
				AND  	pj.project_state = 'product'
				AND     pj.status = 'expired'
				AND pj.cid=cc.PCGS 
				AND	cc.coin_series_unique_no=cs.coin_series_unique_no
				AND	cc.coin_series_denomination_no=cd.denomination_unique_no
				AND c.project_id != 0
				group by pj.project_id
				ORDER BY cd.denomination_sort,
				cs.coin_series_sort,
				cc.coin_detail_year ";

			$sql_gcpend1 = $ilance->db->query($gcpend);
			$number1 = $ilance->db->num_rows($sql_gcpend1);	

			$sql_limit = 'LIMIT ' . (($ilance->GPC['pages'] - 1) * $ilconfig['globalfilters_maxrowsdisplaysubscribers']) . ',' . $ilconfig['globalfilters_maxrowsdisplaysubscribers'];
			$sql_gcpend = $ilance->db->query($gcpend.$sql_limit);
		
		}	
		if(isset($ilance->GPC['searchsell']))
								{
								  if($ilance->GPC['searchsell'] == 1)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1" selected="selected">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 2)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 3)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3" selected="selected">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 4)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" selected="selected">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 5)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" >Last 60 days</option>
								<option value="5" selected="selected">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 6)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6" selected="selected">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }

								  else if($ilance->GPC['searchsell'] == 7)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  	 
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7" selected="selected">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 8)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  	 
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8" selected="selected">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 9)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>  	 
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9" selected="selected">Last 1080 days</option>
								</select>';
								  }
								  else
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0"  selected="selected">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  
								}
								else
								{
								$drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								}
		
		$counter1 = (intval($ilance->GPC['pages']) - 1) * fetch_perpage();
		$prevnext1 = print_pagnation($number1, $ilconfig['globalfilters_maxrowsdisplaysubscribers'], intval($ilance->GPC['pages']), $counter1, $scriptpage, 'pages');	
		
				if ($ilance->db->num_rows($sql_gcpend) > 0)
				{
				
				
				//status
				while ($res_gcpendding = $ilance->db->fetch_array($sql_gcpend))
					{
					$coin_detail_query=$ilance->db->query("select coin_id from ".DB_PREFIX."coins where coin_id='".$res_gcpendding['project_id']."'");
					if($ilance->db->num_rows($coin_detail_query)>0)
					{
					
					$sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gcpendding['project_id']."'
											   AND attachtype='itemphoto'
                                               
                                               
                       ");
                $fetch_new=$ilance->db->fetch_array($sql_atty);
                               
					   if($ilance->db->num_rows($sql_atty) >= 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image.php?cmd=thumb&subcmd=itemphoto&id=' . $fetch_new['filehash'] .'&w=170&h=105';
							   
							   			   //nov 28 for seo
					 if ($ilconfig['globalauctionsettings_seourls'])
					{
					$item_path=HTTP_SERVER.'Coin/'.$res_gcpendding['project_id'].'/'.construct_seo_url_name($res_gcpendding['project_title']).'';
						$htm ='<img  class="img_libox" src="'.$uselistr.'" style="padding: 10px; border-width:0px; cursor:pointer;">';
					}
	                     else
					    $htm ='<img  class="img_libox" src="'.$uselistr.'" style="padding: 10px; border-width:0px; cursor:pointer;">';
							 
							 //$htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcsell['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
					   
					   // murugan on mar 11
					   
					$res_gc_pend['minbid'] = $res_gcpendding['startprice'];
					$res_gc_pend['buynow'] = $res_gcpendding['buynow_price'];
					$res_gc_pend['item_path'] = $item_path;
					
					$res_gc_pend['thumbnail'] = $htm;
					$res_gc_pend['item_id'] = $res_gcpendding['project_id'];
					$res_gc_pend['item_title'] = $res_gcpendding['project_title'];					
					$res_gc_pend['bids'] = $res_gcpendding['bids'];
					$res_gc_pend['timelef'] = date('m-d-Y',strtotime($res_gcpendding['date_end']));
					$res_gc_pend['buynow_qty'] = $res_gcpendding['buynow_qty'];
					$resgcsold['status'] = $res_gcpendding['status'];
					$resgcsold['status'] = 'Pending';
					$res_gc_pend['status'] = $resgcsold['status'];
					
					$res_gc_pend['current_bid'] = $res_gcpendding['currentprice'];
					$res_gc_itempending[] = $res_gc_pend;
					
					
					}
					}
				
				
				
				}
				else
				{				
				$res_gc_itempending['mm'] = 'Nofound';
				}				
				//coin table			
					
				$gccoinpend = " SELECT * FROM  " . DB_PREFIX . "coins co,
					" . DB_PREFIX . "catalog_coin cc, 
					" . DB_PREFIX . "catalog_second_level cs, 
					" . DB_PREFIX . "catalog_toplevel cd 
				WHERE  	 co.user_id = '".$_SESSION['ilancedata']['user']['userid']."'
				AND		co.project_id='0'		 
				AND 	co.status = '0'							
				AND co.pcgs=cc.PCGS 
					AND	cc.coin_series_unique_no=cs.coin_series_unique_no
					AND	cc.coin_series_denomination_no=cd.denomination_unique_no
					GROUP BY co.coin_id
					ORDER BY  cc.Orderno ,(CASE WHEN (co.pcgs = '6000120' OR co.pcgs = '6000127' OR co.pcgs = '6000128' OR co.pcgs = '6000129') THEN co.title END) ASC,co.grade DESC
				";

			$sql_gccoinpend1 = $ilance->db->query($gccoinpend);
			$number = $ilance->db->num_rows($sql_gccoinpend1);	

			$sql_limit1 = 'LIMIT ' . (($ilance->GPC['page'] - 1) * $ilconfig['globalfilters_maxrowsdisplaysubscribers']) . ',' . $ilconfig['globalfilters_maxrowsdisplaysubscribers'];
			$sql_gccoinpend = $ilance->db->query($gccoinpend.$sql_limit1);
			$scriptpage1 = HTTP_SERVER .'Sell/Pending'. print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
			$counter = (intval($ilance->GPC['page']) - 1) * fetch_perpage();
			$prevnext = print_pagnation($number, $ilconfig['globalfilters_maxrowsdisplaysubscribers'], intval($ilance->GPC['page']), $counter, $scriptpage1);	
				
				 if ($ilance->db->num_rows($sql_gccoinpend) > 0)
				{				
				while ($res_gccoin_pendding = $ilance->db->fetch_array($sql_gccoinpend))
					{
					
					$sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gccoin_pendding['coin_id']."'
											    AND attachtype='itemphoto'
                                               
                                               
                       ");
                $fetch_new=$ilance->db->fetch_array($sql_atty);
                               
					   if($ilance->db->num_rows($sql_atty) >= 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image.php?cmd=thumb&subcmd=itemphoto&id=' . $fetch_new['filehash'] .'&w=170&h=105';
							   
							   			   //nov 28 for seo
					 if ($ilconfig['globalauctionsettings_seourls'])

						//$htm ='<a href="Coin/'.$res_gccoin_pendding['project_id'].'/'.construct_seo_url_name($res_gccoin_pendding['project_title']).'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
						$htm ='<img class="img_liboxpend" src="'.$uselistr.'" style="padding: 10px; border-width:0px; cursor:pointer;">';
	                     else
					    //$htm ='<a href="merch.php?id='.$res_gccoin_pendding['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
						$htm ='<img class="img_liboxpend" src="'.$uselistr.'" style="padding: 10px; border-width:0px; cursor:pointer;">';
							 
							 //$htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcsell['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
					//###coin_id, user_id, pcgs, Title, Description, Grading_Service, Grade, Quantity, Max_Quantity_Purchase, Certification_No, Condition_Attribute, Cac, Star, Plus, Coin_Series, Pedigee, Site_Id, Minimum_bid, Reserve_Price, Buy_it_now, End_Date, Alternate_inventory_No, Category, Other_information, consignid, coin_listed, in_notes, Service_Level, final_fee_percentage, final_fee_min, listing_fee, referal_id, notes, project_id, status, export, Sets, nocoin, pending###//
					$res_gc_coin_pend['thumbnail'] = $htm;
					$res_gc_coin_pend['item_id'] = $res_gccoin_pendding['coin_id'];
					$res_gc_coin_pend['item_title'] = $res_gccoin_pendding['Title'];
					$res_gc_coin_pend['minbid_buynow'] = $res_gccoin_pendding['Minimum_bid'];
					//$res_gc_coin_pend['bids'] = $res_gccoin_pendding['bids'];
					$res_gc_coin_pend['timelef'] = $res_gccoin_pendding['End_Date'];
					
					$resgcsold['status'] = $res_gccoin_pendding['status'];
					$resgcsold['status'] = 'Pending';
					$res_gc_coin_pend['status'] = $resgcsold['status'];
					
					$res_gc_coin_pend['current_bid'] = $res_gccoin_pendding['currentprice'];
					$res_gc_coin_pending[] = $res_gc_coin_pend;
					}
				}
				else
				{				
				$res_gc_coin_pending['mm'] = 'Nofound';
				}
				
			$pprint_array = array('prevnext1','prevnext','drop_value','daylist','monthlist','yearlist','bidamount','project_title','buyingreminders','sellingreminders','scheduledcount','itemsworth','expertsrevenue','expertsearch','jobcount','expertcount','itemcount','feedbackactivity','messagesactivity','recentlyviewedflash','tagcloud','main_servicecats_img','main_productcats_img','main_servicecats','main_productcats','lanceads_folder','two_column_category_buyers','two_column_service_categories','two_column_product_categories','remote_addr','rid','default_exchange_rate','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
	$ilance->template->fetch('main', 'sell_pending.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('res_gc_itempending','res_gc_itemsold','res_gcselling','res_gcsolding','res_gc_returning','res_gc_coin_pending'));
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();	
	}
					//item pennding end

// work for item pending download starts

if (isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'item_pending')
	{
	 
	  header("Location:item_pending_pdf.php");
	}
				
// work for item pending download end
					
					////returning starting
if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'returned')
{
	$area_title = 'Items Returned';
	
	if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'returnedsearch')
	{
		$scriptpage = HTTP_SERVER .'Sell/Returned'. print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);   
		$endDate = DATETODAY;
		if($ilance->GPC['searchsell'] == 1)
		{
			 $value = DATEYESTERDAY;
		}
		else if($ilance->GPC['searchsell'] == 2)
		{
			 $value = SEVENDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 3)
		{
		 $value = THIRTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 4)
		{
		 $value = SIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 5)
		{
			 $value = NINETYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 6)
		{
			 $value = ONEEIGHTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 7)
		{
			 $value = THREESIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 8)
		{
			 $value = SEVENTWENTYDAYSAGO;
		}
		else if($ilance->GPC['searchsell'] == 9)
		{
			 $value = THOUSANDEIGHTYDAYSAGO;
		}
		else
		{
			$value = '2011-01-01';
		}
	
	   	$gcsell = " SELECT ct.coin_id,ct.Title,ct.Minimum_bid,ct.Buy_it_now,cr.return_date
				 FROM  " . DB_PREFIX . "coins_retruned ct,
				 " . DB_PREFIX . "coin_return cr
				 WHERE cr.user_id = '".$_SESSION['ilancedata']['user']['userid']."'
				 AND cr.coin_id = ct.coin_id
				 AND   (date(cr.return_date) <= '".$endDate."' AND date(cr.return_date) >= '".$value."')
				 ORDER BY cr.return_date ASC ";
	   		$sql_gcsell1 = $ilance->db->query($gcsell);
			$number = $ilance->db->num_rows($sql_gcsell1);	

			$sql_gcsell = $ilance->db->query($gcsell.$sql_limit);
		}
		else  
		{
			$scriptpage = HTTP_SERVER .'Sell/Returned?'. print_hidden_fields(true, array('do','cmd','page','budget','searchid','list'), true, '', '', $htmlentities = true, $urldecode = false);
			$gcsell = " SELECT ct.coin_id,ct.Title,ct.Minimum_bid,ct.Buy_it_now,cr.return_date
				 FROM  " . DB_PREFIX . "coins_retruned ct,
				 " . DB_PREFIX . "coin_return cr
				 WHERE cr.user_id = '".$_SESSION['ilancedata']['user']['userid']."'
				 AND (date(cr.return_date) <= '".$endDate."' AND date(cr.return_date) >= '".SEVENDAYSAGO."') 
				 AND cr.coin_id = ct.coin_id
				 ORDER BY cr.return_date ASC ";

			$sql_gcsell1 = $ilance->db->query($gcsell);
			$number = $ilance->db->num_rows($sql_gcsell1);	

			$sql_gcsell = $ilance->db->query($gcsell.$sql_limit);
		}
	
	if(isset($ilance->GPC['searchsell']))
	{
								 if($ilance->GPC['searchsell'] == 1)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1" selected="selected">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 2)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 3)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3" selected="selected">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 4)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" selected="selected">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 5)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4" >Last 60 days</option>
								<option value="5" selected="selected">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 6)
								  {
								  $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6" selected="selected">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }

								  else if($ilance->GPC['searchsell'] == 7)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7" selected="selected">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 8)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8" selected="selected">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchsell'] == 9)
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9" selected="selected">Last 1080 days</option>
								</select>';
								  }
								  else
								  {
								  	 $drop_value='<select name="searchsell">
								<option value="0" selected="selected">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  
								}
								else
								{
								$drop_value='<select name="searchsell">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2" selected="selected">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								}
	$counter = (intval($ilance->GPC['page']) - 1) * fetch_perpage();
	$prevnext = print_pagnation($number, $ilconfig['globalfilters_maxrowsdisplaysubscribers'], intval($ilance->GPC['page']), $counter, $scriptpage);	
									
				if ($ilance->db->num_rows($sql_gcsell) > 0)
				{
				while ($res_gcsell = $ilance->db->fetch_array($sql_gcsell))
					{	
					$res_gc_sell['coin_id'] = $res_gcsell['coin_id'];
					$res_gc_sell['Title'] = $res_gcsell['Title'];
					if(!empty($res_gcsell['Minimum_bid']))
					$res_gc_sell['Minimum_bid'] = $res_gcsell['Minimum_bid'];
					else
					$res_gc_sell['Minimum_bid'] = '-';
					if(!empty($res_gcsell['Buy_it_now']))
					$res_gc_sell['Buy_it_now'] = $res_gcsell['Buy_it_now'];
					else
					$res_gc_sell['Buy_it_now'] = '-';
					$res_gc_sell['return_date'] = $res_gcsell['return_date'];													
					$res_gc_returning[] = $res_gc_sell;
					}
				}
				else
				{				
				$res_gc_returning['return'] = 'Nofound';
				}
	/*if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'returnedsearch')
	{		
				if ($ilance->db->num_rows($sql_gcreturn) > 0)
				{
				$res_gc_returning['return'] = 'Nofound';
				} 
			else
			{
			$res_gc_returning['return'] = 'Nofound';
			}
			
	
	}
	else{
	$res_gc_returning['return'] = 'Nofound';
	}*/
		
	$pprint_array = array('prevnext','drop_value','bidamount','project_title','buyingreminders','sellingreminders','scheduledcount','itemsworth','expertsrevenue','expertsearch','jobcount','expertcount','itemcount','feedbackactivity','messagesactivity','recentlyviewedflash','tagcloud','main_servicecats_img','main_productcats_img','main_servicecats','main_productcats','lanceads_folder','two_column_category_buyers','two_column_service_categories','two_column_product_categories','remote_addr','rid','default_exchange_rate','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
	
	$ilance->template->fetch('main', 'sell_returned.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('res_gc_itempending','res_gc_itemsold','res_gcselling','res_gcsolding','res_gc_returning'));
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();
 }
 }
	else
		{				////returning ending
				//##############################
	$pprint_array = array('bidamount','project_title','buyingreminders','sellingreminders','scheduledcount','itemsworth','expertsrevenue','expertsearch','jobcount','expertcount','itemcount','feedbackactivity','messagesactivity','recentlyviewedflash','tagcloud','main_servicecats_img','main_productcats_img','main_servicecats','main_productcats','lanceads_folder','two_column_category_buyers','two_column_service_categories','two_column_product_categories','remote_addr','rid','default_exchange_rate','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
	
	$ilance->template->fetch('main', 'sell_current.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('res_gc_itempending','res_gc_itemsold','res_gcselling','res_gcsolding','res_gc_returning'));
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();
 }

/*======================================================================*\
|| ####################################################################
|| # Downloaded: Wed, Jun 2nd, 2010
|| ####################################################################
\*======================================================================*/
?>