<?php
 
class not_list_hotlist
{
	function not_listed_hotlistcoins()
	{
	    global $ilance,$ilconfig;
		$query1="SELECT * FROM " . DB_PREFIX . "cron_not_placebid  where Bid_listed='No' AND status='hot'";
 		$sql1 = $ilance->db->query($query1, 0, null, __FILE__, __LINE__);
		if($ilance->db->num_rows($sql1))
		{
 			while($line1 = $ilance->db->fetch_array($sql1))
			{
				$project_id=$line1['project_id'];
				if ($project_id > 0) 
				{
					$RE_SQL="SELECT p.project_id,p.hotlists FROM " . DB_PREFIX . "projects p
		left join " . DB_PREFIX . "users u on p.user_id = u.user_id
		WHERE p.project_id in (".$project_id.") and u.status='active' and p.status='open'";
					$re_result=$ilance->db->query($RE_SQL, 0, null, __FILE__, __LINE__);
					$re_place_count=$ilance->db->num_rows($re_result);
					if ($re_place_count > 0)
					{ 
		 				$ilance->db->query("UPDATE  " . DB_PREFIX . "projects
						SET hotlists='1'	
						WHERE  project_id = '".$project_id."'");	
						$ilance->db->query("UPDATE " .DB_PREFIX . "cron_not_placebid SET Bid_listed ='Yes' WHERE status='hot' and project_id = '".intval($project_id)."'");
					}
			 
				}
			}
		}
		 
    }
}

?>