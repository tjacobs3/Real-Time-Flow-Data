<?php
/**
 * Get Site Offset
 * 
 * This file contains the functionality for serving the observed data offsets
 * via a JSON object.  Change or add values to the $site_offsets array
 * to change the observed data offsets.
 */

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$location = isset($_GET["location"]) ? $_GET["location"] : "U22";

	$site_offsets = array(
			"U22" => 663.29,
			"D57" => 652.65,
			"U84" => 600,
			"D108" => 624.93
		);

	function site_name_to_offset()
	{
		global $site_offsets;
		global $location;
		if(array_key_exists($location, $site_offsets))
		{
			return $site_offsets[$location];
		}
	}

$location_offset = site_name_to_offset();
echo json_encode($location_offset);
?>	
