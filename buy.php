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
        'buying',
        'selling',
        'rfp',
        'search',
        'feedback',
        'accounting',
        'javascript'
);

// #### load required javascript ###############################################
$jsinclude = array(
	'functions',
	'ajax',
	'inline',
	'cron',
	'autocomplete',
	'countries',
        'tabfx',
	'flashfix',
	'jquery'
);

// #### setup script location ##################################################
define('LOCATION', 'buying');
// #### require backend ########################################################
require_once('./functions/config.php');
require_once DIR_CORE . 'functions_search.php';
 $show['widescreen'] = true;
// #### setup default breadcrumb ###############################################
$area_title = $phrase['_access_denied'];
$page_title = SITE_NAME . ' - ' . $ilance->GPC['cmd'];
$navcrumb = array("$ilpage[buying]" => $ilcrumbs["$ilpage[buying]"]);

$uncrypted = (!empty($ilance->GPC['crypted'])) ? decrypt_url($ilance->GPC['crypted']) : array();

$ilance->GPC['cmd'] = isset($ilance->GPC['cmd']) ? $ilance->GPC['cmd'] : '';
$ilance->GPC['subcmd'] = isset($ilance->GPC['subcmd']) ? $ilance->GPC['subcmd'] : '';

if (empty($_SESSION['ilancedata']['user']['userid']) OR $_SESSION['ilancedata']['user']['userid'] == 0)
{
	refresh(HTTPS_SERVER . $ilpage['login'] . '?redirect=' . urlencode($ilpage['buying'] . print_hidden_fields($string = true, $excluded = array(), $questionmarkfirst = true)));
	exit();
}

$ilance->GPC['page'] = (!isset($ilance->GPC['page']) OR isset($ilance->GPC['page']) AND $ilance->GPC['page'] <= 0)
				? 1
				: intval($ilance->GPC['page']);
			
$sql_limit = 'LIMIT ' . (($ilance->GPC['page'] - 1) * $ilconfig['globalfilters_maxrowsdisplaysubscribers']) . ',' . $ilconfig['globalfilters_maxrowsdisplaysubscribers'];


// #### BUYING ACTIVITY MENU ###################################################
 
				///Active start
if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'active')
		{
		$area_title = 'Currently Buying';
		
		$ilance->bid = construct_object('api.bid');
		$ilance->bid_lowest_unique = construct_object('api.bid_lowest_unique');
		$ilance->bid_proxy = construct_object('api.bid_proxy');
		$ilance->bid_permissions = construct_object('api.bid_permissions');
		//july 4sort date_end
			$gc_buy = " SELECT *
	                FROM " . DB_PREFIX . "projects p,
						 " . DB_PREFIX . "project_bids b
	                WHERE b.user_id = '".$_SESSION['ilancedata']['user']['userid']."'
					AND   p.visible ='1'
					AND   p.project_id = b.project_id		
					AND	  b.bidstatus = 'placed'
					AND   p.status = 'open'
					GROUP BY p.project_id
					ORDER BY p.date_end ASC,b.bid_id DESC ";
			$sql_gcbuy1 = $ilance->db->query($gc_buy);
			$number = $ilance->db->num_rows($sql_gcbuy1);
					
			$sql_gcbuy = $ilance->db->query($gc_buy.$sql_limit);
				
			$scriptpage = HTTP_SERVER . 'Buy/Active?' . print_hidden_fields(true, array('subcmd', 'cmd', 'page', 'budget'), true, '', '', $htmlentities = true, $urldecode = false);
			$counter = (intval($ilance->GPC['page']) - 1) * fetch_perpage();
			$prevnext = print_pagnation($number, $ilconfig['globalfilters_maxrowsdisplaysubscribers'], intval($ilance->GPC['page']), $counter, $scriptpage);

				if ($ilance->db->num_rows($sql_gcbuy) > 0)
				{
				//status
				while ($res_gcact = $ilance->db->fetch_array($sql_gcbuy))
					{
					$sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gcact['project_id']."'
                                               AND attachtype='itemphoto'
                                               
                       ");
					   // murugan changes for bid amount display on Feb 22
					   
					   $selehigh = $ilance->db->query("SELECT MAX(bidamount) AS bidamount FROM  " . DB_PREFIX . "project_bids
					   								WHERE project_id = '".$res_gcact['project_id']."'");
						$reshigh = $ilance->db->fetch_array($selehigh);
						
						$pbit = $ilance->bid_proxy->fetch_user_proxy_bid($res_gcact['project_id'], $_SESSION['ilancedata']['user']['userid']);
						if ($pbit > 0)
						{
												// murugan test
								$highbidderidtest = $ilance->bid->fetch_highest_bidder($res_gcact['project_id']);
								if($highbidderidtest == $_SESSION['ilancedata']['user']['userid'])
								$proxybit = '<span class="green">'.$phrase['_your_maximum_bid'] . ': ' . $ilance->currency->format($pbit,$ilconfig['globalserverlocale_defaultcurrency']) . '</span>';
								else
								$proxybit = '<span class="red">'.$phrase['_your_maximum_bid'] . ': ' . $ilance->currency->format($pbit, $ilconfig['globalserverlocale_defaultcurrency']) . '</span>';
							
							
							
						}
						
						
                $fetch_new=$ilance->db->fetch_array($sql_atty);
                               
					   if($ilance->db->num_rows($sql_atty) == 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image/105/170/' . $fetch_new['filename'] ;
							   		   //nov 28 for merch.php seo
						if ($ilconfig['globalauctionsettings_seourls'])

						$htm ='<a href="Coin/'.$res_gcact['project_id'].'/'.construct_seo_url_name($res_gcact['project_title']).'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
	                    else
					    $htm ='<a href="merch.php?id='.$res_gcact['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
							   
							   //$htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcact['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
					
					$res_gc_active['thumbnail'] = $htm;
					
					$res_gc_active['item_id'] = $res_gcact['project_id']; 
							// nov 28 from seo
					 if ($ilconfig['globalauctionsettings_seourls'])	
						$res_gc_active['item_title'] ='<a href="Coin/'.$res_gcact['project_id'].'/'.construct_seo_url_name($res_gcact['project_title']).'">'.$res_gcact['project_title'].'</a>';
					else
					   	$res_gc_active['item_title']='<a href="merch.php?id='.$res_gcact['project_id'].'" >'.$res_gcact['project_title'].'</a>';
					
					
					//$res_gc_active['item_title'] = '<a href="'. $ilpage['merch'] .'?id='.$res_gcact['project_id'].'">'.$res_gcact['project_title'].'</a>';
					$res_gc_active['bids'] = $res_gcact['bids'];
					$res_gc_active['timelef'] = $res_gcact['date_end'];
					$res_gc_active['status'] = $res_gcact['status'];
					$res_gc_active['timeleft'] = $res_gcact['date_starts'];
					/*if($res_gcact['bidamount'] == $reshigh['bidamount'])
					$res_gc_active['current_bid'] = '<span style="color:#008000;">'.$reshigh['bidamount'].'</span>';
					else
					$res_gc_active['current_bid'] = '<span style="color:#FF0000;">'.$reshigh['bidamount'].'</span>';*/
					$res_gc_active['current_bid'] = $reshigh['bidamount'];
					//karthik
					$result = $ilance->db->query("
						SELECT *, UNIX_TIMESTAMP(date_end) - UNIX_TIMESTAMP('" . DATETIME24H . "') AS mytime, UNIX_TIMESTAMP(date_starts) - UNIX_TIMESTAMP('" . DATETIME24H . "') AS starttime
						FROM " . DB_PREFIX . "projects
						WHERE project_id = '" . $res_gcact['project_id'] . "'
						ORDER BY date_end ASC
					");
					if ($ilance->db->num_rows($result) > 0)
					{
						$rows = $ilance->db->fetch_array($result, DB_ASSOC);
					if ($res_gc_active['timeleft'] > DATETIME24H)
						{
							$dif = $rows['starttime'];
							$ndays = floor($dif / 86400);
							$dif -= $ndays * 86400;
							$nhours = floor($dif / 3600);
							$dif -= $nhours * 3600;
							$nminutes = floor($dif / 60);
							$dif -= $nminutes * 60;
							$nseconds = $dif;
							$sign = '+';
							if ($rows['starttime'] < 0)
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
							$dif = $rows['mytime'];
							$ndays = floor($dif / 86400);
							$dif -= $ndays * 86400;
							$nhours = floor($dif / 3600);
							$dif -= $nhours * 3600;
							$nminutes = floor($dif / 60);
							$dif -= $nminutes * 60;
							$nseconds = $dif;
							$sign = '+';
							if ($rows['mytime'] < 0)
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
                                                        
							$res_gc_active['timelef'] = $project_time_left;
						}
						}
						// end
					if($res_gcact['proposal'] !='')
					{
					$res_gc_active['proposal'] = substr($res_gcact['proposal'], 0, 30); 
					}
					else{
					$res_gc_active['proposal'] = '-';
					}
					$res_gc_active['proposal'] = $proxybit;
					$res_gcacting[] = $res_gc_active;
					}
				}
				else
				{				
				$res_gcacting['act'] = 'Nofound';
				}
///Active end
$pprint_array = array('prevnext','daylist','monthlist','yearlist','php_self2','sub','bidsub','servicetabs','producttabs','activebids','awardedbids','archivedbids','invitedbids','expiredbids','retractedbids','productescrow','buynowproductescrow','activerfps','draftrfps','archivedrfps','delistedrfps','pendingrfps','serviceescrow','highbidder','highbidderid','highest','php_self','searchquery','p_id','rfpescrow','rfpvisible','countdelisted','prevnext','prevnext2','input_style','redirect','referer','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
		
	$ilance->template->fetch('main', 'buy_active.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('service_buying_activity','res_gcacting','res_gcswining','res_gcsnotwining','res_gc_buynow'));	
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();
}
//I Won start
if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'won')
{	
$area_title = 'Item Won';
	if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'wonsearch')
	{ 
		$scriptpage = HTTP_SERVER . 'Buy/Won'. print_hidden_fields(true, array('page', 'budget'), true, '', '', $htmlentities = true, $urldecode = false);   	
		$endDate = DATETODAY;
		if($ilance->GPC['searchbuy'] == 1)
		{
			 $value = DATETODAY;
		}
		else if($ilance->GPC['searchbuy'] == 2)
		{
			 $value = SEVENDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 3)
		{
		 $value = THIRTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 4)
		{
			 $value = SIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 5)
		{
			 $value = NINETYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 6)
		{
			 $value = ONEEIGHTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 7)
		{
			 $value = THREESIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 8)
		{
			 $value = SEVENTWENTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 9)
		{
			 $value = THOUSANDEIGHTYDAYSAGO;
		}
		else
		{
			 $value = '2011-01-01';
		}
		
			$gcwon = " SELECT * FROM " . DB_PREFIX . "projects p,
				" . DB_PREFIX . "project_bids b 
                WHERE  	p.winner_user_id = '".$_SESSION['ilancedata']['user']['userid']."' 
				AND		b.bidstatus = 'awarded'
				AND     p.project_id = b.project_id
				AND    	p.haswinner = '1'
				AND   (date(p.date_end) <= '".$endDate."' AND date(p.date_end) >= '".$value."')
				ORDER BY p.date_end DESC "; 

			$sql_gcwon1 = $ilance->db->query($gcwon); 
			$number = $ilance->db->num_rows($sql_gcwon1);

			$sql_gcwon = $ilance->db->query($gcwon.$sql_limit); 
				//echo 'test';
					
		}
		else
		{ 
			$scriptpage = HTTP_SERVER . 'Buy/Won?'. print_hidden_fields(true, array('subcmd', 'cmd', 'page', 'budget'), true, '', '', $htmlentities = true, $urldecode = false);
		
		$gcwon = " SELECT * FROM " . DB_PREFIX . "projects p,
				" . DB_PREFIX . "project_bids b 
                WHERE  	p.winner_user_id = '".$_SESSION['ilancedata']['user']['userid']."' 
				AND		b.bidstatus = 'awarded'
				AND     p.project_id = b.project_id
				AND    	p.haswinner = '1'
				AND   (date(p.date_end) <= '".DATETODAY."' AND date(p.date_end) >= '".SEVENDAYSAGO."')
				ORDER BY p.date_end DESC "; 

			$sql_gcwon1 = $ilance->db->query($gcwon); 
			$number = $ilance->db->num_rows($sql_gcwon1);

			$sql_gcwon = $ilance->db->query($gcwon.$sql_limit);
		}

if(isset($ilance->GPC['searchbuy']))
								{
								  if($ilance->GPC['searchbuy'] == 1)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 2)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 3)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 4)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 5)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 6)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 7)
								  {
								  	 $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 8)
								  {
								  	 $drop_value='<select name="searchbuy">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8" selected="selected" >Last 720 days</option>
								<option value="9">Last 1080 days</option>
								</select>';
								  }
								  else if($ilance->GPC['searchbuy'] == 9)
								  {
								  	 $drop_value='<select name="searchbuy">
								<option value="0">-------All-------</option>
								<option value="1">Last 24 Hours</option>
								<option value="2">Last 7 days</option>
								<option value="3">Last 30 days</option>
								<option value="4">Last 60 days</option>
								<option value="5">Last 90 days</option>
								<option value="6">Last 180 days</option>
								<option value="7">Last 360 days</option>
								<option value="8">Last 720 days</option>
								<option value="9" selected="selected" >Last 1080 days</option>
								</select>';
								  }
								  else
								  {
								  	 $drop_value='<select name="searchbuy">
								<option value="0" selected="selected" >-------All-------</option>
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
								$drop_value='<select name="searchbuy">
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

				if ($ilance->db->num_rows($sql_gcwon) > 0)
				{
				while ($res_gcwon = $ilance->db->fetch_array($sql_gcwon))
					{
					
					//AND (invoicetype = 'p2b' OR invoicetype = 'buynow') may31
					$sql_inv = $ilance->db->query("SELECT * FROM " . DB_PREFIX . "invoices
                                               WHERE projectid = '".$res_gcwon['project_id']."'
                                               AND user_id ='".$_SESSION['ilancedata']['user']['userid']."'
											   
                                               
                       ");
					  
					  /* if($ilance->db->num_rows($sql_inv) > 0)
					   {
					    	
							$res_gc_won['invoice'] = $fetch_inv['invoiceid'];
					   }
					   else
					   {
					    	$res_gc_won['invoice'] = 'Offline Payment';
					   }*/ 
					   $fetch_inv=$ilance->db->fetch_array($sql_inv);
					   
					      if($fetch_inv['status'] == 'paid')
						   {
						     $res_gc_won['invoice'] = 'Paid-'.$fetch_inv['paiddate'];
						   }
						   else if($fetch_inv['status'] == 'unpaid')
						   {
						     $res_gc_won['invoice'] = '<a href="'. HTTPS_SERVER .'buyer_invoice.php'. '">Click to Pay</a>';
						   } 
						   else if($fetch_inv['status'] == 'scheduled')
						   {
						     $res_gc_won['invoice'] = 'Payment Pending';
						   }
						   else if($fetch_inv['status'] == 'complete')
						   {
						     $sql_com = $ilance->db->query("SELECT * FROM " . DB_PREFIX . "invoices
                                               WHERE combine_project LIKE '%".$fetch_inv['invoiceid']."%'
                                               AND user_id ='".$_SESSION['ilancedata']['user']['userid']."'    ");
							
							if($ilance->db->num_rows($sql_com)>0)
							{
									$fetch_com=$ilance->db->fetch_array($sql_com);
								
								  if($fetch_com['status'] == 'paid')
								   {
									 $res_gc_won['invoice'] = 'Paid-'.$fetch_com['paiddate'];
								   }
								   else if($fetch_com['status'] == 'unpaid')
								   {
									 $res_gc_won['invoice'] = '<a href="'. HTTPS_SERVER .'buyer_invoice.php'. '">Click to Pay</a>';
								   } 
								   else if($fetch_com['status'] == 'scheduled')
								   {
									 $res_gc_won['invoice'] = 'Payment Pending';
								   }
								   else
								   {
										$res_gc_won['invoice'] = $fetch_com['status'];
								   }
							 }
							 else
							 {
							 	$res_gc_won['invoice'] = 'Invoice Combined';
							 }
						   }
						   else
						   {
						   		$res_gc_won['invoice'] = $fetch_inv['status'];
						   }
						   
						   //may31 
					   $sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gcwon['project_id']."'
                                               AND attachtype='itemphoto'
                                               
                       ");
                $fetch_new=$ilance->db->fetch_array($sql_atty);
                               
					   if($ilance->db->num_rows($sql_atty) == 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image/105/170/' . $fetch_new['filename'] ;
							   //HTTPS_SERVER . $ilpage['image'] . '?cmd=thumb&subcmd=itemphoto&id=' . $fetch_new['filehash'] .'&w=170&h=105';
							   
							      //nov28 for seo
						 if ($ilconfig['globalauctionsettings_seourls'])

						$htm ='<a href="Coin/'.$res_gcwon['project_id'].'/'.construct_seo_url_name($res_gcwon['project_title']).'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
	                     else
					    $htm ='<a href="merch.php?id='.$res_gcwon['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
							   
							  // $htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcwon['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
					
					$res_gc_won['thumbnail'] = $htm; 
					$res_gc_won['item_id'] = $res_gcwon['project_id'];
								//nov 28 for seo
			 if ($ilconfig['globalauctionsettings_seourls'])	
					$res_gc_won['item_title'] ='<a href="Coin/'.$res_gcwon['project_id'].'/'.construct_seo_url_name($res_gcwon['project_title']).'">'.$res_gcwon['project_title'].'</a>';
			else
					$res_gc_won['item_title']='<a href="merch.php?id='.$res_gcwon['project_id'].'">'.$res_gcwon['project_title'].'</a>';
					
					//$res_gc_won['item_title'] = '<a href="'. $ilpage['merch'] .'?id='.$res_gcwon['project_id'].'">'.$res_gcwon['project_title'].'</a>';
					$res_gc_won['bids'] = $res_gcwon['bids'];
					if($res_gcwon['proposal'] !='')
					{
					$res_gc_won['proposal'] = substr($res_gcwon['proposal'], 0, 30); 
					}
					else{
					$res_gc_won['proposal'] = '<center>-</center>';
					}
					$res_gc_won['timelef'] = date('m-d-Y',strtotime($res_gcwon['date_end']));
					$res_gc_won['status'] = 'Ended';
					$res_gc_won['current_bid'] = $res_gcwon['bidamount'];
					$res_gcswining[] = $res_gc_won;
					}
				}
				else
				{				
				$res_gcswining['won'] = 'Nofound';
				}
// Iwon End
$pprint_array = array('drop_value','daylist','monthlist','yearlist','php_self2','sub','bidsub','servicetabs','producttabs','activebids','awardedbids','archivedbids','invitedbids','expiredbids','retractedbids','productescrow','buynowproductescrow','activerfps','draftrfps','archivedrfps','delistedrfps','pendingrfps','serviceescrow','highbidder','highbidderid','highest','php_self','searchquery','p_id','rfpescrow','rfpvisible','countdelisted','prevnext','prevnext2','input_style','redirect','referer','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
		
	$ilance->template->fetch('main', 'buy_won.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('service_buying_activity','res_gcacting','res_gcswining','res_gcsnotwining','res_gc_buynow'));	
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();
}
// I donot won start
if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'notwon')
{
$area_title = 'Item Not Win';
if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'notwonsearch')
{ 
	$scriptpage = HTTP_SERVER . 'Buy/Notwon'. print_hidden_fields(true, array('page', 'budget'), true, '', '', $htmlentities = true, $urldecode = false);	
		$endDate = DATETODAY;
		if($ilance->GPC['searchbuy'] == 1)
		{
			 $value = DATEYESTERDAY;
		}
		else if($ilance->GPC['searchbuy'] == 2)
		{
			 $value = SEVENDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 3)
		{
		 $value = THIRTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 4)
		{
			 $value = SIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 5)
		{
			 $value = NINETYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 6)
		{
			 $value = ONEEIGHTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 7)
		{
			 $value = THREESIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 8)
		{
			 $value = SEVENTWENTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 9)
		{
			 $value = THOUSANDEIGHTYDAYSAGO;
		}
		else
		{
			$value = '2011-01-01';
		}

			$gcnotwon = " SELECT * FROM " . DB_PREFIX . "projects p,
					" . DB_PREFIX . "project_bids b 
					WHERE  	b.user_id = '".$_SESSION['ilancedata']['user']['userid']."'
					AND     b.bidstatus ='outbid'
					AND 	p.winner_user_id != '".$_SESSION['ilancedata']['user']['userid']."'
					AND     p.project_id = b.project_id 					
					AND		(p.status = 'expired' OR p.status='finished')
					AND   (date(p.date_end) <= '".$endDate."' AND date(p.date_end) >= '".$value."')
					GROUP BY b.project_id "; 
			$sql_gcnotwon1 = $ilance->db->query($gcnotwon); 
			$number = $ilance->db->num_rows($sql_gcnotwon1);

			$sql_gcnotwon = $ilance->db->query($gcnotwon.$sql_limit);
		}
		else
		{
	 		$scriptpage = HTTP_SERVER . 'Buy/Notwon?'. print_hidden_fields(true, array('subcmd', 'cmd', 'page', 'budget'), true, '', '', $htmlentities = true, $urldecode = false);
			$gcnotwon = " SELECT * FROM " . DB_PREFIX . "projects p,
					" . DB_PREFIX . "project_bids b 
					WHERE  	b.user_id = '".$_SESSION['ilancedata']['user']['userid']."'
					AND     b.bidstatus ='outbid'
					AND 	p.winner_user_id != '".$_SESSION['ilancedata']['user']['userid']."'
					AND     p.project_id = b.project_id					
					AND		(p.status = 'expired' OR p.status='finished')		
					AND   (date(p.date_end) <= '".DATETODAY."' AND date(p.date_end) >= '".SEVENDAYSAGO."')			
					GROUP BY b.project_id ";

			$sql_gcnotwon1 = $ilance->db->query($gcnotwon); 
			$number = $ilance->db->num_rows($sql_gcnotwon1);

			$sql_gcnotwon = $ilance->db->query($gcnotwon.$sql_limit);
		} 
if(isset($ilance->GPC['searchbuy']))
								{
								  if($ilance->GPC['searchbuy'] == 1)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 2)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 3)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 4)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 5)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 6)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 7)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 8)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 9)
								  {
								  	 $drop_value='<select name="searchbuy">
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
								  	 $drop_value='<select name="searchbuy">
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
								$drop_value='<select name="searchbuy">
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

				if ($ilance->db->num_rows($sql_gcnotwon) > 0)
				{
				//status
				while ($res_gcnotwon = $ilance->db->fetch_array($sql_gcnotwon))
					{
					
					$sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gcnotwon['project_id']."'
                                               AND attachtype='itemphoto'
                                               
                       ");
					   
					   $sql_maxbid = $ilance->db->query("
                       SELECT MAX(bidamount) AS max_bid FROM
                       " . DB_PREFIX . "project_bids
                       WHERE user_id = '".$_SESSION['ilancedata']['user']['userid']."'
                             AND project_id = '".$res_gcnotwon['project_id']."'                            
                                               
                       ");
					   
					   $sql_high_bid=$ilance->db->query("
                       SELECT MAX(bidamount) AS max_bid FROM
                       " . DB_PREFIX . "project_bids
                       WHERE project_id = '".$res_gcnotwon['project_id']."'                            
                                               
                       ");
					   $res_high_bid= $ilance->db->fetch_array($sql_high_bid);
					   $res_maxbid= $ilance->db->fetch_array($sql_maxbid);
					   
					   $sqlproxy = $ilance->db->query("SELECT maxamount FROM " . DB_PREFIX . "proxybid
					  								 WHERE project_id = '".$res_gcnotwon['project_id']."'
													 AND user_id = '".$_SESSION['ilancedata']['user']['userid']."' ");
						$resproxy = $ilance->db->fetch_array($sqlproxy);
						if($res_maxbid['max_bid'] <= $resproxy['maxamount'])
						{
							$res_gc_notwon['max_bid'] = $resproxy['maxamount'];
						}
						else
						{
							$res_gc_notwon['max_bid'] = $res_maxbid['max_bid'];
						}
					   
                		$fetch_new=$ilance->db->fetch_array($sql_atty);
                               
					   if($ilance->db->num_rows($sql_atty) == 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image/105/170/' . $fetch_new['filename'] ;
							   //HTTPS_SERVER . $ilpage['image'] . '?cmd=thumb&subcmd=itemphoto&id=' . $fetch_new['filehash'] .'&w=170&h=105';
							   
							   //nov 28 for seo
				     if ($ilconfig['globalauctionsettings_seourls'])

						$htm ='<a href="Coin/'.$res_gcnotwon['project_id'].'/'.construct_seo_url_name($res_gcnotwon['project_title']).'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
	                     else
					    $htm ='<a href="merch.php?id='.$res_gcnotwon['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
							   //$htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcnotwon['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
					
					$res_gc_notwon['thumbnail'] = $htm;
					$res_gc_notwon['item_id'] = $res_gcnotwon['project_id'];
								
								//nov 28 for seo
					
					      if ($ilconfig['globalauctionsettings_seourls'])	
						    $res_gc_notwon['item_title'] ='<a href="Coin/'.$res_gcnotwon['project_id'].'/'.construct_seo_url_name($res_gcnotwon['project_title']).'">'.$res_gcnotwon['project_title'].'</a>';
						else
					   	  $res_gc_notwon['item_title']='<a href="merch.php?id='.$res_gcnotwon['project_id'].'">'.$res_gcnotwon['project_title'].'</a>';
					
					//$res_gc_notwon['item_title'] = '<a href="'. $ilpage['merch'] .'?id='.$res_gcnotwon['project_id'].'">'.$res_gcnotwon['project_title'].'</a>';
					$res_gc_notwon['bids'] = $res_gcnotwon['bids'];
					$res_gc_notwon['timelef'] = date('m-d-Y H:m:s',strtotime($res_gcnotwon['date_end']));
					$res_gc_notwon['status'] = 'Ended';
					$res_gc_notwon['current_bid'] = $res_high_bid['max_bid'];					
					$res_gc_notwon['proposal'] = substr($res_gcnotwon['proposal'], 0, 30); 
					$res_gcsnotwining[] = $res_gc_notwon;
					}
				}
				else
				{				
				$res_gcsnotwining['notwon'] = 'Nofound';
				}

$pprint_array = array('drop_value','daylist','monthlist','yearlist','php_self2','sub','bidsub','servicetabs','producttabs','activebids','awardedbids','archivedbids','invitedbids','expiredbids','retractedbids','productescrow','buynowproductescrow','activerfps','draftrfps','archivedrfps','delistedrfps','pendingrfps','serviceescrow','highbidder','highbidderid','highest','php_self','searchquery','p_id','rfpescrow','rfpvisible','countdelisted','prevnext','prevnext2','input_style','redirect','referer','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
		
	$ilance->template->fetch('main', 'buy_notwon.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('service_buying_activity','res_gcacting','res_gcswining','res_gcsnotwining','res_gc_buynow'));	
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();				
}
///I donot won end
if(isset($ilance->GPC['cmd']) AND $ilance->GPC['cmd'] == 'buynow')
{
$area_title = 'Items Bought';
//buy now start
if(isset($ilance->GPC['subcmd']) AND $ilance->GPC['subcmd'] == 'buynowsearch')
{ 
	$scriptpage = HTTP_SERVER . 'Buy/Buynow' . print_hidden_fields(true, array('page', 'budget'), true, '', '', $htmlentities = true, $urldecode = false);
		$endDate = DATETODAY;
		if($ilance->GPC['searchbuy'] == 1)
		{
			 $value = DATEYESTERDAY;
		}
		else if($ilance->GPC['searchbuy'] == 2)
		{
			 $value = SEVENDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 3)
		{
		 $value = THIRTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 4)
		{
			 $value = SIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 5)
		{
			 $value = NINETYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 6)
		{
			 $value = ONEEIGHTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 7)
		{
			 $value = THREESIXTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 8)
		{
			 $value = SEVENTWENTYDAYSAGO;
		}
		else if($ilance->GPC['searchbuy'] == 9)
		{
			 $value = THOUSANDEIGHTYDAYSAGO;
		}
		else
		{
			$value = '2011-01-01';
		}
		
		$gcbuy = " SELECT * FROM " . DB_PREFIX . "projects p,
				" . DB_PREFIX . "buynow_orders b 
                WHERE  	b.buyer_id = '".$_SESSION['ilancedata']['user']['userid']."'
				AND     p.project_id = b.project_id	
				AND   (date(b.orderdate) <= '".$endDate."' AND date(p.date_end) >= '".$value."')
				GROUP BY b.orderid			
				ORDER BY b.orderid DESC	
                ";

		$sql_gcbuy1 = $ilance->db->query($gcbuy);
		$number = $ilance->db->num_rows($sql_gcbuy1);

		$sql_gcbuy = $ilance->db->query($gcbuy.$sql_limit);
				 		
		}
		else
		{ 
			$scriptpage = HTTP_SERVER . 'Buy/Buynow?' . print_hidden_fields(true, array('subcmd', 'cmd', 'page', 'budget'), true, '', '', $htmlentities = true, $urldecode = false);
			$gcbuy = " SELECT * FROM " . DB_PREFIX . "projects p,
				" . DB_PREFIX . "buynow_orders b 
                WHERE  	b.buyer_id = '".$_SESSION['ilancedata']['user']['userid']."'
				AND     p.project_id = b.project_id	
				AND   (date(b.orderdate) <= '".DATETODAY."' AND date(p.date_end) >= '".SEVENDAYSAGO."')
				GROUP BY b.orderid			
				ORDER BY b.orderid DESC	
                ";

			$sql_gcbuy1 = $ilance->db->query($gcbuy);
			$number = $ilance->db->num_rows($sql_gcbuy1);
		
			$sql_gcbuy = $ilance->db->query($gcbuy.$sql_limit);
		}
		if(isset($ilance->GPC['searchbuy']))
								{
								  if($ilance->GPC['searchbuy'] == 1)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 2)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 3)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 4)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 5)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 6)
								  {
								  $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 7)
								  {
								  	 $drop_value='<select name="searchbuy">
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
								  
								   else if($ilance->GPC['searchbuy'] == 8)
								  {
								  	 $drop_value='<select name="searchbuy">
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
								  else if($ilance->GPC['searchbuy'] == 9)
								{
								$drop_value='<select name="searchbuy">
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
								$drop_value='<select name="searchbuy">
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
								$drop_value='<select name="searchbuy">
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
								
				if ($ilance->db->num_rows($sql_gcbuy) > 0)
				{
				//status
				while ($res_gcbuy = $ilance->db->fetch_array($sql_gcbuy))
					{
					 $sql_atty = $ilance->db->query("
                       SELECT * FROM
                       " . DB_PREFIX . "attachment
                       WHERE visible='1'
                                               AND project_id = '".$res_gcbuy['project_id']."'
                                               AND attachtype='itemphoto'
                                               
                       ");
               				 $fetch_new=$ilance->db->fetch_array($sql_atty);
                               
					   if($ilance->db->num_rows($sql_atty) == 1)
					   {
							   $uselistr = HTTPS_SERVER . 'image/105/170/' . $fetch_new['filename'] ;
							   //HTTPS_SERVER . $ilpage['image'] . '?cmd=thumb&subcmd=itemphoto&id=' . $fetch_new['filehash'] .'&w=170&h=105';
							   
							   				   //nov 28 for seo
							   
				if ($ilconfig['globalauctionsettings_seourls'])

						$htm ='<a href="Coin/'.$res_gcbuy['project_id'].'/'.construct_seo_url_name($res_gcbuy['project_title']).'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
	              else
					    $htm ='<a href="merch.php?id='.$res_gcbuy['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
							   
							   
							   //$htm ='<a href="'. $ilpage['merch'] .'?id='.$res_gcbuy['project_id'].'"><img  src="'.$uselistr.'" style="padding: 10px; border-width:0px;"></a>';
					   }
					   if($ilance->db->num_rows($sql_atty) == 0)
			   
					   {
						   $uselistr = $ilconfig['template_relativeimagepath'] . $ilconfig['template_imagesfolder'] . 'nophoto.gif';
							   
						   $htm ='<img src="' . $uselistr . '">';
					   }
					
					$res_gcbuynow['thumbnail'] = $htm;
					$res_gcbuynow['item_id'] = $res_gcbuy['project_id'];
									//nov 28 for seo
			 if ($ilconfig['globalauctionsettings_seourls'])	
				  $res_gcbuynow['item_title'] ='<a href="Coin/'.$res_gcbuy['project_id'].'/'.construct_seo_url_name($res_gcbuy['project_title']).'">'.$res_gcbuy['project_title'].'</a>';
			else
				 $res_gcbuynow['item_title']='<a href="merch.php?id='.$res_gcbuy['project_id'].'">'.$res_gcbuy['project_title'].'</a>';
					
					//$res_gcbuynow['item_title'] = '<a href="'. $ilpage['merch'] .'?id='.$res_gcbuy['project_id'].'">'.$res_gcbuy['project_title'].'</a>';
					$res_gcbuynow['bids'] = $res_gcbuy['qty'];
					$res_gcbuynow['timelef'] = date('m-d-Y',strtotime($res_gcbuy['orderdate']));
					$res_gcbuynow['status'] = 'Ended';
					if($res_gcbuy['invoiceid'] != '0')
					{
					$check_invoice_date=$ilance->db->query("
                       SELECT DATE(createdate) AS date_inv, transactionid  FROM
                       " . DB_PREFIX . "invoices
                       WHERE invoiceid= '".$res_gcbuy['invoiceid']."'                                          
                                               
                       ");
					   
					  $date_inv=$ilance->db->fetch_array($check_invoice_date); 
					  
					$res_gcbuynow['invoice'] = '<a href="invoicepayment.php?cmd=view&txn='.$date_inv['transactionid'].'">'.date('m-d-Y ',strtotime($date_inv['date_inv'])).'</a>';
					}
					else
					{
					  $res_gcbuynow['invoice'] = 'Offline Payment';
					}
					$res_gcbuynow['current_bid'] = $res_gcbuy['amount'];					
					$res_gc_buynow[] = $res_gcbuynow;
					}
					
				}
				else
				{				
				$res_gc_buynow['buynow'] = 'Nofound';
				}
//buy now end
///#########################
$pprint_array = array('drop_value','daylist','monthlist','yearlist','php_self2','sub','bidsub','servicetabs','producttabs','activebids','awardedbids','archivedbids','invitedbids','expiredbids','retractedbids','productescrow','buynowproductescrow','activerfps','draftrfps','archivedrfps','delistedrfps','pendingrfps','serviceescrow','highbidder','highbidderid','highest','php_self','searchquery','p_id','rfpescrow','rfpvisible','countdelisted','prevnext','prevnext2','input_style','redirect','referer','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
		
	$ilance->template->fetch('main', 'buy_now.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('service_buying_activity','res_gcacting','res_gcswining','res_gcsnotwining','res_gc_buynow'));	
	$ilance->template->parse_if_blocks('main');
	$ilance->template->pprint('main', $pprint_array);
	exit();
}
else
{
	$pprint_array = array('daylist','monthlist','yearlist','php_self2','sub','bidsub','servicetabs','producttabs','activebids','awardedbids','archivedbids','invitedbids','expiredbids','retractedbids','productescrow','buynowproductescrow','activerfps','draftrfps','archivedrfps','delistedrfps','pendingrfps','serviceescrow','highbidder','highbidderid','highest','php_self','searchquery','p_id','rfpescrow','rfpvisible','countdelisted','prevnext','prevnext2','input_style','redirect','referer','login_include','headinclude','onload','area_title','page_title','site_name','https_server','http_server','lanceads_header','lanceads_footer');
		
	$ilance->template->fetch('main', 'buy_active.html');
	$ilance->template->parse_hash('main', array('ilpage' => $ilpage));
	$ilance->template->parse_loop('main', array('service_buying_activity','res_gcacting','res_gcswining','res_gcsnotwining','res_gc_buynow')); 
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