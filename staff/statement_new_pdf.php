<?php

require_once('./../functions/config.php');

if (!empty($_SESSION['ilancedata']['user']['userid']) AND $_SESSION['ilancedata']['user']['userid'] > 0 AND $_SESSION['ilancedata']['user']['isadmin'] == '1')
{

//error_reporting(E_ALL);

        //sprt order by catalog and coinid

		            unset($total_seller);
					 
					 unset($total_listing);
					 
					 unset($total_final);
					 
					 unset($total_net_consignor);

		if($ilance->GPC['start_date'] != '' AND $ilance->GPC['end_date'] != '')

		{

		$select = $ilance->db->query("SELECT distinct co.user_id 
		                              FROM ".DB_PREFIX."coins co,
									       ".DB_PREFIX."users u  		
                                     WHERE (date(co.End_Date) >= '".$ilance->GPC['start_date']."' AND date(co.End_Date) <= '".$ilance->GPC['end_date']."')
					                  AND co.user_id=u.user_id
					                  ORDER BY u.last_name ASC
								   ");

		} 

        //user consignment deatils

		$cou=$ilance->db->num_rows($select);

		if($ilance->db->num_rows($select) > 0)

         {
		 define('FPDF_FONTPATH','../font/');



require('pdftable_1.9/lib/pdftable.inc.php');

			

			$p = new PDFTable();

 		

			$p->AddPage();

			$p->setfont('times','',10);		

                while( $res_user = $ilance->db->fetch_array($select))

                {

	
	 					$name = fetch_user('username',$res_user['user_id']);
						
						$email=fetch_user('email',$res_user['user_id']);
						
						$first_name=fetch_user('first_name',$res_user['user_id']);
						
						$last_name=fetch_user('last_name',$res_user['user_id']);
						
						$address=fetch_user('address',$res_user['user_id']);
						
						$address2=fetch_user('address2',$res_user['user_id']);
						
						$city=fetch_user('city',$res_user['user_id']);
						
						$state=fetch_user('state',$res_user['user_id']);
						
						$zipcode=fetch_user('zip_code',$res_user['user_id']);						
						

	 					$new_header = '<table width="100%">
						<tr>

    <td size="24" family="helvetica" style="bold" nowrap><b>GreatCollections</b></td>
	<td>&nbsp;</td>
    <td size="13" family="helvetica" style="bold" nowrap><b>Consignor Statement</b></td>
    

  </tr>

  <tr>

    <td valign="top" size="10" family="helvetica" >Certified Coin Auctions & Direct Sales<br>
	
	                                               17500 Red Hill Avenue, Suite 160, Irvine, CA 92614-7290<br>

                                                    Tel: +1.800.44.COINS - Fax: +1.949.679.4178<br>

                                                     E-mail: info@greatcollections.com</td>

    <td >&nbsp;</td>

    <td >Date of Sale:&nbsp;'.$ilance->GPC['start_date'].'</td>
		
	</tr>					
						<tr >

						<td>&nbsp;Consignor Username : '.$name.' <br>E-mail : '.$email.'</td>

						</tr>
						<tr >

						<td>'.$first_name.' &nbsp; '.$last_name.'<br>'.$address.'<br>'.$address2.'<br>'.$city.' &nbsp; '.$state.' &nbsp; '.$zipcode.' </td>

						</tr>
						<tr>
						<td>&nbsp;
						</td>
						</tr>
						</table><table width="100%">

															<tr bgcolor="#CD9C9C"> 

																	  <td>ID</td>

																	<td width = "45%">Item Title</td>
                                                                     
																	
																	  <td>Listed</td>	

																	  <td>Bids</td>

																	  <td>Min Bid/<br>Buy Now</td>

																	  <td>Final<br>Price</td>

																	  <td>Listing<br> Fees</td>

																	  <td>Sellers<br> Fees</td>	
																	  							 

																	  <td>Net to Consignor</td>

																

																</tr>';

							$table1=$new_header;

				

				

				$user_coin_list = $ilance->db->query("SELECT * FROM ".DB_PREFIX."coins

					WHERE (date(End_Date) >= '".$ilance->GPC['start_date']."' AND date(End_Date) <= '".$ilance->GPC['end_date']."')

					AND user_id='".$res_user['user_id']."'

					GROUP BY coin_id

					ORDER BY user_id 

					");

				     unset($totins);

					 unset($totfvf);

					 unset($test4);

					 unset($test5);
					 unset($totfinal); 
					 
					

					 if($ilance->db->num_rows($user_coin_list)>0)

					 {

					 $i=0;

					

					 

					 $listcount=$ilance->db->num_rows($user_coin_list);

					 while($res=$ilance->db->fetch_array($user_coin_list))

					 {

					 

					 

					 $selectbid = $ilance->db->query("SELECT MIN(bidamount) AS bidamount, MAX(bidamount) AS final,count(*) AS count FROM ".DB_PREFIX."project_bids			

														WHERE project_id = '".$res['project_id']."'

														");

						$selectbin = $ilance->db->query("SELECT SUM(amount) AS binamount, SUM(qty) AS qty FROM ".DB_PREFIX."buynow_orders			

														WHERE project_id = '".$res['project_id']."'

														");

						$selectpjt = $ilance->db->query("SELECT insertionfee, fvf, featured, highlite, bold FROM ".DB_PREFIX."projects			

														WHERE project_id = '".$res['project_id']."'

														");

						$selectinvoice = $ilance->db->query("SELECT SUM(amount) AS newfvf FROM ".DB_PREFIX."invoices			

														WHERE projectid = '".$res['project_id']."'

														AND isfvf = '1'

														");
						$selectdisp = $ilance->db->query("SELECT * FROM ".DB_PREFIX."invoices
									
														WHERE projectid = '".$res['project_id']."'
														 
														");								
														
														// murugan changes on jun 24 								
						$enhancementfee = $ilance->db->query("SELECT SUM(amount) AS newenhance FROM ".DB_PREFIX."invoices			
						WHERE projectid = '".$res['project_id']."'
						AND isenhancementfee = '1'
						");

						$result = $ilance->db->fetch_array($selectbid, DB_ASSOC);

						$result1 = $ilance->db->fetch_array($selectbin, DB_ASSOC);

						$resultpjt = $ilance->db->fetch_array($selectpjt, DB_ASSOC);

						$resultinvoice = $ilance->db->fetch_array($selectinvoice, DB_ASSOC);
						
						$resultdisp = $ilance->db->fetch_array($selectdisp, DB_ASSOC);	
						
							//karthik on july22 for display *
									
						if($resultdisp['status']=='paid' AND $ilance->GPC['option']=='display')
						{
						 $disp='<font color="#FF0000">*</font>';
						}
						else
						{
						  $disp='';
						 } 
						
						// murugan june 24 
						$resenhancementfee = $ilance->db->fetch_array($enhancementfee, DB_ASSOC);

						 // miscellaneous Calculatation Murugan on jun 4 
						$misselect = $ilance->db->query("SELECT amount,invoicetype FROM ". DB_PREFIX ."invoices
				  						WHERE user_id ='".$res_user['user_id']."'
				  						AND projectid = '".$res['project_id']."'
				  						AND ismis = 1 ");
							
						if ($ilance->db->num_rows($misselect) > 0)
						{
							$resmis = $ilance->db->fetch_array($misselect, DB_ASSOC);
							//murugan july 7
							if($resmis['invoicetype'] == 'debit')
							{
								
								$misdebit[] = $resmis['amount'];
							}
							if($resmis['invoicetype'] == 'credit')
							{
								
								$miscredit[] = $resmis['amount'];
							}							
							$miscell[] = $resmis['amount'];
							//$misamt = $ilance->currency->format($resmis['amount'],$ilconfig['globalserverlocale_defaultcurrency']);
						}
						else
						{
							$miscell[] = 0;
							
							$miscredit[] = 0;
							$misdebit[] = 0;
							//$misamt = $ilance->currency->format(0,$ilconfig['globalserverlocale_defaultcurrency']);
						}		
						

						// Featured fee Amount

						if($resultpjt['featured'] !=0)

						{

							$featured = $ilconfig['productupsell_featuredfee'];

						}

						else

						{

						 	$featured = '0.00';

						}

						// highlite fee amount

						if($resultpjt['highlite'] !=0)

						{

							$highlite = $ilconfig['productupsell_highlightfee'];

						}

						else

						{

						 	$highlite = '0.00';

						}

						

						// bold fee amount

						if($resultpjt['bold'] !=0)

						{

							$bold = $ilconfig['productupsell_boldfee'];

						}

						else

						{

						 	$bold = '0.00';

						}

						// Total Amount (insertionfee , bold,highlight,featured)
						if($res['relist_count'] == 0)
						{
							$resultpjt['insertionfee'] = $resultpjt['insertionfee'];
						}
						else
						{
							$resultpjt['insertionfee'] = 0;
						}

						$listfeetotal = $resultpjt['insertionfee'] + $resenhancementfee['newenhance'];

						

						$totfvf[$i]= $resultinvoice['newfvf'];

						$totins[$i]= $resultpjt['insertionfee'] + $resenhancementfee['newenhance'];

						 

 

						$res['bids']= $result['count'];

						$bidtot[$i]= $result['count'];

						

						

						if($res['Minimum_bid'] != '')

						{						

							

							$test5[$i]= $res['Minimum_bid'];

							$res['bidamount'] = $ilance->currency->format($res['Minimum_bid'],$ilconfig['globalserverlocale_defaultcurrency']);

						}

						else

						{

						  	$res['bidamount'] = '0.00';

						}

					

						if($res['Buy_it_now'] != '')

						{						

							$test4[$i]= $res['Buy_it_now'];

							$res['binamount']  = $ilance->currency->format($res['Buy_it_now'],$ilconfig['globalserverlocale_defaultcurrency']);

							

						}

						else

						{

						  	$res['binamount']  = '0.00';

						}

						if($result['final'] != '')

						{

							$res['finalprice'] = $result['final'];

							$res['qty'] = '';

							$res['fvf'] = $ilance->currency->format($result['final'],$ilconfig['globalserverlocale_defaultcurrency']);

						}

						else

						{

							$res['finalprice'] = $result1['binamount'];

							if($result1['qty'] > 1)

							$res['qty'] = '<b>('.$result1['qty'].')</b>';

							else

							$res['qty'] = '';

							$res['fvf'] = $ilance->currency->format($result1['binamount'],$ilconfig['globalserverlocale_defaultcurrency']);

						}

						// Total Final price

						$totfinal[$i] = $res['finalprice'];
						
						//murugan july 7
				  $mis_totdebit = array_sum($misdebit);
				  $mis_totcredit = array_sum($miscredit);
				  	$miscellan = array_sum($miscell);
					// murugan july 21
					//$mis_total =  $mis_totcredit - $mis_totdebit;
					$mis_total = $mis_totdebit - $mis_totcredit;
					if($mis_total > 0)
					{
					$tot_mis = $ilance->currency->format($mis_total,$ilconfig['globalserverlocale_defaultcurrency']);
					}
					else
					{
					$tot_mis = 'US$'.number_format(abs($mis_total), 2, '.', '');
					}

						

						$res['seller_fee'] = $ilance->currency->format($resultinvoice['newfvf'],$ilconfig['globalserverlocale_defaultcurrency']);

						$res['listing_fee'] = $ilance->currency->format($listfeetotal,$ilconfig['globalserverlocale_defaultcurrency']);

						if($result['bidamount'] != '')

						{							

							 $res['net_consignor1'] = $result['final'] - ( $resultinvoice['newfvf'] + $listfeetotal) ;

							 if($res['net_consignor1'] > 0)

							 $res['net_consignor'] = $disp.$ilance->currency->format($res['net_consignor1'],$ilconfig['globalserverlocale_defaultcurrency']);

							 else

							 $res['net_consignor'] = '- US$'.abs($res['net_consignor1']). '';

							 

						}

						if($result1['binamount'] != '')

						{

							

							$res['net_consignor1'] = $result1['binamount'] - ( $resultinvoice['newfvf'] + $listfeetotal);

							if($res['net_consignor1'] > 0)

							$res['net_consignor'] =$disp.$ilance->currency->format($res['net_consignor1'],$ilconfig['globalserverlocale_defaultcurrency']);

							 else

							 $res['net_consignor'] = '- US$'.abs($res['net_consignor1']). '';

						}

						if($result['bidamount'] == '' AND $result1['binamount'] == '' )

						{

							$res['net_consignor1'] =  ($resultinvoice['newfvf'] + $listfeetotal);

							if($res['net_consignor1'] > 0)

							$res['net_consignor'] = '- US$'.$res['net_consignor1']. '.00';

							else

							$res['net_consignor'] = 'US$0.00';

						}

						$test[$i] = $res['net_consignor1'];

						//$res['net_consignor'] = $ilance->currency->format($res['net_consignor1'],$ilconfig['globalserverlocale_defaultcurrency']);

					if($res['Site_Id'] == '0')

					{

					  $res['Site_Id'] ='GC';

					  $res['Title'] = '<a href="'.$ilpage['merch'].'?id='.$res['coin_id'].'">'.$res['Title'].'</a>';

					  $res['stateid'] = '<a href="'.$ilpage['merch'].'?id='.$res['coin_id'].'">'.$res['coin_id'].'</a>';

					}

					else

					{

					$sitesel = $ilance->db->query("

					SELECT site_name FROM ".DB_PREFIX."affiliate_listing				

					WHERE id = '".$res['Site_Id']."'					

					");

						$siteres = $ilance->db->fetch_array($sitesel, DB_ASSOC);

					 $res['Site_Id'] =$siteres['site_name'];

					 $res['Title'] = $res['Title'];

					$res['stateid'] = $res['coin_id'];

					}

					

												

	$table1.='<tr>

					<td>'.$res['coin_id'].'</td> 

                    <td width = "45%">'.$res['Title'].' <br> Cert #:'.$res['Certification_No'].' <br> Alt. Inv #:'.$res['Alternate_inventory_No'].' </td>

		
					<td>'.$res['Site_Id'].'</td>

					<td>'.$res['bids'].'</td>

					<td>'.$res['bidamount'].'/<br>'.$res['binamount'].'</td>

					<td>'.$res['fvf'].'</td>

					<td>'.$res['listing_fee'].'</td>

					<td>'.$res['seller_fee'].'</td>				
					

                    <td>'.$res['net_consignor'].'</td>                        

  </tr>';

					

					

						$i++;

					 

					 }

					  

 $advanceselect = $ilance->db->query("SELECT sum(amount) as amount FROM " . DB_PREFIX . "user_advance WHERE statusnow = 'paid' AND user_id ='".$res_user['user_id']."' ");

				  $advanceres = $ilance->db->fetch_array($advanceselect);



				  $sum_inset = array_sum($totins);

				  $sum_finalvaluefe = array_sum($totfvf);

				  $sum_totfinalval = array_sum($totfinal);

				  $newnettotal = $sum_totfinalval - $sum_finalvaluefe - $sum_inset;				  

				  //$totnet_consignor = $ilance->currency->format(array_sum($test),$ilconfig['globalserverlocale_defaultcurrency']);

				  //$totnet_consignor = 'US$'.$newnettotal;
				 
				  $totnet_consignor = $ilance->currency->format($newnettotal,$ilconfig['globalserverlocale_defaultcurrency']);

				  //$totseller_fee = $ilance->currency->format(array_sum($test1),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totseller_fee = $ilance->currency->format(array_sum($totfvf),$ilconfig['globalserverlocale_defaultcurrency']);
				  
				   $total_seller[]=array_sum($totfvf);

				  $totlisting_fee = $ilance->currency->format(array_sum($totins),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totfvf = $ilance->currency->format(array_sum($totfinal),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totbinamount = $ilance->currency->format(array_sum($test4),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totbidamount = $ilance->currency->format(array_sum($test5),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totbids = array_sum($bidtot);				

				  $total_advance = $ilance->currency->format($advanceres['amount'],$ilconfig['globalserverlocale_defaultcurrency']);

				 
				 
				  $total_listing[]=array_sum($totins);
				  
				 
				  
				  $total_final[]=array_sum($totfinal);
				  
				  $total_net_consignor[]=$newnettotal;

				  // murugan FEB 23

				  			 //murugan july 7
				  $mis_totdebit = array_sum($misdebit);
				  $mis_totcredit = array_sum($miscredit);
				  	$miscellan = array_sum($miscell);
					// murugan july 21
					//$mis_total =  $mis_totcredit - $mis_totdebit;
					$mis_total = $mis_totdebit - $mis_totcredit;
					if($mis_total > 0)
					{
					$tot_mis = $ilance->currency->format($mis_total,$ilconfig['globalserverlocale_defaultcurrency']);
					}
					else
					{
					$tot_mis = 'US$'.number_format(abs($mis_total), 2, '.', '');
					}	

				  //$lastamountvalue = array_sum($test) - $advanceres['amount'];
				  // murugan changes on july 21

				  //$lastamountvalue = $newnettotal - $advanceres['amount'];
				   $lastamountvalue = $newnettotal - $advanceres['amount'] - $mis_totcredit + $mis_totdebit;

				  $lastamount = $ilance->currency->format($lastamountvalue,$ilconfig['globalserverlocale_defaultcurrency']);

				  //$lastamount = 'US$'.$lastamountvalue;

				  $statecount = '('.$listcount.' items) will settle on '.$settledate .' ('.$lastamount.')';

				 
				 
				 //default amount for Miscellaneous and Paid
				 
				 $default = 0;
				 
				 $def =   $ilance->currency->format($default,$ilconfig['globalserverlocale_defaultcurrency']);

					 

					 }

					//Buynow Items
			
				$buynow_list = $ilance->db->query("SELECT * FROM ".DB_PREFIX."buynow_orders

													WHERE (date(orderdate) >= '".$ilance->GPC['start_date']."' AND date(orderdate) <= '".$ilance->GPC['end_date']."')
								
													AND owner_id='".$res_user['user_id']."'
								
													ORDER BY owner_id
								
													");

				     

					 if($ilance->db->num_rows($buynow_list)>0)

					 {

					 $i=0;

				
					 $list_count=$ilance->db->num_rows($buynow_list);

					 while($res_buy=$ilance->db->fetch_array($buynow_list))

					 {

				

						$selectbin = $ilance->db->query("SELECT SUM(amount) AS binamount, SUM(qty) AS qty FROM ".DB_PREFIX."buynow_orders			

														WHERE project_id = '".$res_buy['project_id']."'

														");

						$selectpjt = $ilance->db->query("SELECT insertionfee, fvf, featured, highlite, bold FROM ".DB_PREFIX."projects			

														WHERE project_id = '".$res_buy['project_id']."'

														");

						$selectinvoice = $ilance->db->query("SELECT SUM(amount) AS newfvf FROM ".DB_PREFIX."invoices			

														WHERE projectid = '".$res_buy['project_id']."'

														AND isfvf = '1'

														");
						$selectdisp = $ilance->db->query("SELECT * FROM ".DB_PREFIX."invoices
									
														WHERE projectid = '".$res_buy['project_id']."'
														 
														");								
														
														// murugan changes on jun 24 								
						$enhancementfee = $ilance->db->query("SELECT SUM(amount) AS newenhance FROM ".DB_PREFIX."invoices			
						WHERE projectid = '".$res_buy['project_id']."'
						AND isenhancementfee = '1'
						");

						$result = $ilance->db->fetch_array($selectbid, DB_ASSOC);

						$result1 = $ilance->db->fetch_array($selectbin, DB_ASSOC);

						$resultpjt = $ilance->db->fetch_array($selectpjt, DB_ASSOC);

						$resultinvoice = $ilance->db->fetch_array($selectinvoice, DB_ASSOC);
						
						$resultdisp = $ilance->db->fetch_array($selectdisp, DB_ASSOC);	
						
							//karthik on july22 for display *
									
						if($resultdisp['status']=='paid' AND $ilance->GPC['option']=='display')
						{
						 $disp='<font color="#FF0000">*</font>';
						}
						else
						{
						  $disp='';
						 } 
						
						// murugan june 24 
						$resenhancementfee = $ilance->db->fetch_array($enhancementfee, DB_ASSOC);

						 // miscellaneous Calculatation Murugan on jun 4 
						$misselect = $ilance->db->query("SELECT amount,invoicetype FROM ". DB_PREFIX ."invoices
				  						WHERE user_id ='".$res_user['user_id']."'
				  						AND projectid = '".$res_buy['project_id']."'
				  						AND ismis = 1 ");
							
						if ($ilance->db->num_rows($misselect) > 0)
						{
							$resmis = $ilance->db->fetch_array($misselect, DB_ASSOC);
							//murugan july 7
							if($resmis['invoicetype'] == 'debit')
							{
								
								$misdebit[] = $resmis['amount'];
							}
							if($resmis['invoicetype'] == 'credit')
							{
								
								$miscredit[] = $resmis['amount'];
							}							
							$miscell[] = $resmis['amount'];
							//$misamt = $ilance->currency->format($resmis['amount'],$ilconfig['globalserverlocale_defaultcurrency']);
						}
						else
						{
							$miscell[] = 0;
							
							$miscredit[] = 0;
							$misdebit[] = 0;
							//$misamt = $ilance->currency->format(0,$ilconfig['globalserverlocale_defaultcurrency']);
						}		
						

						// Featured fee Amount

						if($resultpjt['featured'] !=0)

						{

							$featured = $ilconfig['productupsell_featuredfee'];

						}

						else

						{

						 	$featured = '0.00';

						}

						// highlite fee amount

						if($resultpjt['highlite'] !=0)

						{

							$highlite = $ilconfig['productupsell_highlightfee'];

						}

						else

						{

						 	$highlite = '0.00';

						}

						

						// bold fee amount

						if($resultpjt['bold'] !=0)

						{

							$bold = $ilconfig['productupsell_boldfee'];

						}

						else

						{

						 	$bold = '0.00';

						}

						// Total Amount (insertionfee , bold,highlight,featured)
						if($res['relist_count'] == 0)
						{
							$resultpjt['insertionfee'] = $resultpjt['insertionfee'];
						}
						else
						{
							$resultpjt['insertionfee'] = 0;
						}

						$listfeetotal = $resultpjt['insertionfee'] + $resenhancementfee['newenhance'];

						

						$totfvf[$i]= $resultinvoice['newfvf'];

						$totins[$i]= $resultpjt['insertionfee'] + $resenhancementfee['newenhance'];

						 

 

						$res['bids']='-';

						$bidtot[$i]= $result['count'];

						  	$res['bidamount'] = '0.00';

				
							$test4[$i]= $res_buy['amount'];

							$res['binamount']  = $ilance->currency->format($res_buy['amount'],$ilconfig['globalserverlocale_defaultcurrency']);


						if($result['final'] != '')

						{

							$res['finalprice'] = $result['final'];

							$res['qty'] = '';

							$res['fvf'] = $ilance->currency->format($result['final'],$ilconfig['globalserverlocale_defaultcurrency']);

						}

						else

						{

							$res['finalprice'] = $result1['binamount'];

							if($result1['qty'] > 1)

							$res['qty'] = '<b>('.$result1['qty'].')</b>';

							else

							$res['qty'] = '';

							$res['fvf'] = $ilance->currency->format($result1['binamount'],$ilconfig['globalserverlocale_defaultcurrency']);

						}

						// Total Final price

						$totfinal[$i] = $res['finalprice'];
						
						//murugan july 7
				  $mis_totdebit = array_sum($misdebit);
				  $mis_totcredit = array_sum($miscredit);
				  	$miscellan = array_sum($miscell);
					// murugan july 21
					//$mis_total =  $mis_totcredit - $mis_totdebit;
					$mis_total = $mis_totdebit - $mis_totcredit;
					if($mis_total > 0)
					{
					$tot_mis = $ilance->currency->format($mis_total,$ilconfig['globalserverlocale_defaultcurrency']);
					}
					else
					{
					$tot_mis = 'US$'.number_format(abs($mis_total), 2, '.', '');
					}

						

						$res['seller_fee'] = $ilance->currency->format($resultinvoice['newfvf'],$ilconfig['globalserverlocale_defaultcurrency']);

						$res['listing_fee'] = $ilance->currency->format($listfeetotal,$ilconfig['globalserverlocale_defaultcurrency']);

						if($result['bidamount'] != '')

						{							

							 $res['net_consignor1'] = $result['final'] - ( $resultinvoice['newfvf'] + $listfeetotal) ;

							 if($res['net_consignor1'] > 0)

							 $res['net_consignor'] = $disp.$ilance->currency->format($res['net_consignor1'],$ilconfig['globalserverlocale_defaultcurrency']);

							 else

							 $res['net_consignor'] = '- US$'.abs($res['net_consignor1']). '';

							 

						}

						if($result1['binamount'] != '')

						{

							

							$res['net_consignor1'] = $result1['binamount'] - ( $resultinvoice['newfvf'] + $listfeetotal);

							if($res['net_consignor1'] > 0)

							$res['net_consignor'] =$disp.$ilance->currency->format($res['net_consignor1'],$ilconfig['globalserverlocale_defaultcurrency']);

							 else

							 $res['net_consignor'] = '- US$'.abs($res['net_consignor1']). '';

						}

						if($result['bidamount'] == '' AND $result1['binamount'] == '' )

						{

							$res['net_consignor1'] =  ($resultinvoice['newfvf'] + $listfeetotal);

							if($res['net_consignor1'] > 0)

							$res['net_consignor'] = '- US$'.$res['net_consignor1']. '.00';

							else

							$res['net_consignor'] = 'US$0.00';

						}

						$test[$i] = $res['net_consignor1'];

						//$res['net_consignor'] = $ilance->currency->format($res['net_consignor1'],$ilconfig['globalserverlocale_defaultcurrency']);

					$sitesel = $ilance->db->query("

					SELECT site_name FROM ".DB_PREFIX."affiliate_listing				

					WHERE id = 'GC'					

					");

						$siteres = $ilance->db->fetch_array($sitesel, DB_ASSOC);

					 $res['Site_Id'] =$siteres['site_name'];

					 $res['Title'] = fetch_coin('Title',$res_buy['project_id']);

					$res['stateid'] = $res_buy['project_id'];
					
					
                      $res['Certification_No'] = fetch_coin('Certification_No',$res_buy['project_id']);
					  
					   $res['Alternate_inventory_No'] = fetch_coin('Alternate_inventory_No',$res_buy['project_id']);
							

	$table1.='<tr>

					<td>'.$res_buy['project_id'].'</td> 

                    <td width = "45%">'.$res['Title'].' <br> Cert #:'.$res['Certification_No'].' <br> Alt. Inv #:'.$res['Alternate_inventory_No'].' </td>

		
					<td>'.$res['Site_Id'].'</td>

					<td>'.$res['bids'].'</td>

					<td>'.$res['bidamount'].'/<br>'.$res['binamount'].'</td>

					<td>'.$res['fvf'].'</td>

					<td>'.$res['listing_fee'].'</td>

					<td>'.$res['seller_fee'].'</td>				
					

                    <td>'.$res['net_consignor'].'</td>                        

  </tr>';

					

					

						$i++;

					 

					 }

					  

 $advanceselect = $ilance->db->query("SELECT sum(amount) as amount FROM " . DB_PREFIX . "user_advance WHERE statusnow = 'paid' AND user_id ='".$res_user['user_id']."' ");

				  $advanceres = $ilance->db->fetch_array($advanceselect);



				  $sum_inset = array_sum($totins);

				  $sum_finalvaluefe = array_sum($totfvf);

				  $sum_totfinalval = array_sum($totfinal);

				  $newnettotal = $sum_totfinalval - $sum_finalvaluefe - $sum_inset;				  

				  //$totnet_consignor = $ilance->currency->format(array_sum($test),$ilconfig['globalserverlocale_defaultcurrency']);

				  //$totnet_consignor = 'US$'.$newnettotal;
				 
				  $totnet_consignor = $ilance->currency->format($newnettotal,$ilconfig['globalserverlocale_defaultcurrency']);

				  //$totseller_fee = $ilance->currency->format(array_sum($test1),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totseller_fee = $ilance->currency->format(array_sum($totfvf),$ilconfig['globalserverlocale_defaultcurrency']);
				  
				   $total_seller[]=array_sum($totfvf);

				  $totlisting_fee = $ilance->currency->format(array_sum($totins),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totfvf = $ilance->currency->format(array_sum($totfinal),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totbinamount = $ilance->currency->format(array_sum($test4),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totbidamount = $ilance->currency->format(array_sum($test5),$ilconfig['globalserverlocale_defaultcurrency']);

				  $totbids = array_sum($bidtot);				

				  $total_advance = $ilance->currency->format($advanceres['amount'],$ilconfig['globalserverlocale_defaultcurrency']);

				 
				 
				  $total_listing[]=array_sum($totins);
				  
				 
				  
				  $total_final[]=array_sum($totfinal);
				  
				  $total_net_consignor[]=$newnettotal;

				  // murugan FEB 23

				  			 //murugan july 7
				  $mis_totdebit = array_sum($misdebit);
				  $mis_totcredit = array_sum($miscredit);
				  	$miscellan = array_sum($miscell);
					// murugan july 21
					//$mis_total =  $mis_totcredit - $mis_totdebit;
					$mis_total = $mis_totdebit - $mis_totcredit;
					if($mis_total > 0)
					{
					$tot_mis = $ilance->currency->format($mis_total,$ilconfig['globalserverlocale_defaultcurrency']);
					}
					else
					{
					$tot_mis = 'US$'.number_format(abs($mis_total), 2, '.', '');
					}	

				  //$lastamountvalue = array_sum($test) - $advanceres['amount'];
				  // murugan changes on july 21

				  //$lastamountvalue = $newnettotal - $advanceres['amount'];
				   $lastamountvalue = $newnettotal - $advanceres['amount'] - $mis_totcredit + $mis_totdebit;

				  $lastamount = $ilance->currency->format($lastamountvalue,$ilconfig['globalserverlocale_defaultcurrency']);

				  //$lastamount = 'US$'.$lastamountvalue;

				  $statecount = '('.$listcount.' items) will settle on '.$settledate .' ('.$lastamount.')';

				 
				 
				 //default amount for Miscellaneous and Paid
				 
				 $default = 0;
				 
				 $def =   $ilance->currency->format($default,$ilconfig['globalserverlocale_defaultcurrency']);

				}	

				 

				 	$table1.='
					
					<tr><td>&nbsp;</td></tr>

				  <tr>

								<td>Gross Total</td>

								<td ></td>

								<td ></td>
                                
								 <td ></td>

								<td ></td>
							
								<td nowrap><b>'.$totfvf.'</b></td>

								<td nowrap><b>'.$totlisting_fee.'</b></td>

								<td nowrap><b>'.$totseller_fee.'</b></td>

								<td nowrap><b>'.$totnet_consignor.'</b></td>

							</tr>

							<tr >

							  <td>&nbsp;</td>



						  </tr>

							<tr>

								<td nowrap></td>

							    <td nowrap></td>
					
                                <td nowrap></td>

								<td nowrap></td>
								
								
								<td nowrap></td>

								<td nowrap>Advance </td>

								<td></b></td>

								<td></b></td>

								<td nowrap><b>'.$total_advance.'</b></td>

							</tr>
							
							<tr>

								<td nowrap></td>

							    <td nowrap></td>
					
                                <td nowrap></td>

								<td nowrap></td>
								
								
								<td nowrap></td>

								<td nowrap>Miscellaneous </td>

								<td></b></td>

								<td></b></td>

								<td nowrap><b>'.$tot_mis .'</b></td>

							</tr>
							
							<tr>

								<td nowrap></td>

							    <td nowrap></td>
					
                                <td nowrap></td>

								<td nowrap></td>
								
								
								<td nowrap></td>

								<td nowrap>Paid </td>

								<td></b></td>

								<td></b></td>

								<td nowrap><b>'.$def .'</b></td>

							</tr>

								<tr>

								<td nowrap></td>

							    <td nowrap></td>
					
                                <td nowrap></td>

								<td nowrap></td>
								
								
								<td nowrap></td>

								<td nowrap>Balance </td>

								<td></b></td>

								<td></b></td>


								<td nowrap><b>'.$lastamount.'</b></td>

							</tr>';
$table1.='</table>';
	
$table1.='<table width="100%"><tr><td>Thank you for consigning to GreatCollections.<br>We appreciate your business.</td></tr></table>';
	 

	

		$p->htmltable($table1);	

	$p->AddPage();

			$p->setfont('times','',10);		
			
			
			

	

 			  }

				  // Advance Calculateion

		}
		
		$table2.='<table width="100%">';
		
		 	$table2.='
					
					<tr><td>Total</td></tr>

				  <tr>
				  
				                     <td>Total FinalPrice</td>

									<td nowrap><b>'.$ilance->currency->format(array_sum($total_final),$ilconfig['globalserverlocale_defaultcurrency']).'</b></td>
									
					</tr>				

								
						<tr>	
						<td>Total Listing Fees</td>

								<td nowrap><b>'. $ilance->currency->format(array_sum($total_listing),$ilconfig['globalserverlocale_defaultcurrency']).'</b></td>
								
						</tr>
						<tr>		
						<td>Total Seller Fees</td>

								<td nowrap><b>'.$ilance->currency->format(array_sum($total_seller),$ilconfig['globalserverlocale_defaultcurrency']).'</b></td>
								
						</tr>
						
						<tr>		

                                <td>Total Net to Consignor</td>
								<td nowrap><b>'.$ilance->currency->format(array_sum($total_net_consignor),$ilconfig['globalserverlocale_defaultcurrency']).'</b></td>

							</tr>';

						
$table2.='</table>';
		
		$p->htmltable($table2);	

	//$p->AddPage();

			$p->setfont('times','',10);				


 $timeStamp = date("Y-m-d-H-i-s");

		/*$pdf->Output(''.$name.'_Consignment_Report_'.$orderby.'.pdf', 'D'); 		*/

$p->output('Statement_'.$timeStamp.'.pdf','D');

}

 else
	{
		  print_action_failed("Please Login to Continue",reports.'.php');
							
	      exit();
	 }		   	

?>

