<?php
/**
 * Site numbers
 * 
 * Contains the mappings from site names to numbers
 * Add more entries to $site_no to support more sites
 */

	$site_no = array(
			"U22" => "05531175",
			"D57" => "05531300",
			"U84" => "05531410",
			"D108" => "05531500"
		);
	/**
	 * Converts a site name to a number
	 * @param $name The site whose number is required
	 * @return site number	
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
