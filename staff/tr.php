<?php
require_once('./../functions/config.php');
$r['285842']=11088;
$r['285858']=5689;
$r['286215']=82;
$r['286363']=8947;
$r['286384']=4233;
$r['287889']=4781;
$r['288311']=3255;
$r['288983']=6933;
$r['289132']=11899;
$r['289836']=10138;
$r['290022']=12054;
$r['290023']=6075;
$r['290049']=1034;
$r['290060']=9529;
$r['297265']=12174;
$r['297518']=6471;
$r['297547']=8388;
$r['297589']=12076;
$r['297821']=7267;
$r['298295']=10047;
$r['298310']=11169;
$r['298371']=5911;
$r['301898']=11108;
$r['301988']=11548;
$r['302003']=1321;
$r['302032']=4168;
$r['302472']=1321;

foreach($r as $inv=>$user)
{
$sql="SELECT *  FROM " . DB_PREFIX . "invoices WHERE  invoiceid>='".$inv."' and user_id='".$user."' limit 1";
$res = $ilance->db->query($sql, 0, null, __FILE__, __LINE__);
if($ilance->db->num_rows($res)>0)
{
	while($line=$ilance->db->fetch_array($res))
	{
		echo $inv.'='.$line['invoiceid'].'<br>';
	}
}
}
?>