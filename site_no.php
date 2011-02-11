<?php
	$site_no = array(
			"U22" => "05531175",
			"D57" => "05531300",
			"U84" => "05531410",
			"D108" => "05531500"
		);
		
	function site_name_to_number($name)
	{
		global $site_no;
		if(array_key_exists($name, $site_no))
		{
			return $site_no[$name];
		}
		else return false;
	}
?>