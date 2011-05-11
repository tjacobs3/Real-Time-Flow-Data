<?php
/**
 * Get Plot Data
 * 
 * This file contains the functionality for serving the observed data
 * via json objects. 
 */

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

include 'realtimeParser.php';
include 'site_no.php';

// Initialize the time zone.  Required to get accurate millisecond data
date_default_timezone_set('America/Chicago'); 

//Initialize the parameters for what data to get.
$titles = array("00065" => "Gage Height", "00060" => "Discharge", "00045" => "Precipitation");
$simulatedFileLocation = isset($_GET["simLocation"]) ? $_GET["simLocation"] : "gate798.wsq";
$location = isset($_GET["location"]) ? $_GET["location"] : "U22";
$timePeriod = isset($_GET["period"]) ? $_GET["period"] : "7";
$chartType = isset($_GET["chartType"]) ? $_GET["chartType"] : "elevation";  // Can be "elevation" or "discharge" 
$dataType = isset($_GET["dataType"]) ? $_GET["dataType"] : "real"; // Can be "real" or "simulated" or "both"
$precip = isset($_GET["includePrecip"]) ? $_GET["includePrecip"] : "false";
$columns;
$data;

/**
 * Init Data
 * Downloads the observed data from USGS servers based on the parameters that 
 * were located in the url.  This function should always be called first.
 * It sets the global var $data which holds the parsed data
 */
function initData()
{
	global $columns, $data, $location, $timePeriod;
	$site_num = site_name_to_number($location);
	$file = getFileAsArray("http://waterdata.usgs.gov/il/nwis/uv?cb_00065=on&cb_00060=on&cb_00045=on&format=rdb&period=".$timePeriod."&site_no=".$site_num);
	$columns = getColumnNames($file);
	$data = getData($file);
}

/**
 * Format Date
 * Parses a string in the form Y-m-d H:i and returns an associative array containing
 * the date values
 * @param string $time date string to parse
 * @return array Array containing date values 
 */
function formatDate($time)
{
  $formattime = $time . ':00';
  $contents = explode(" ",$time);
  $date = explode("-", $contents[0]);
  $time = explode(":", $contents[1]);
  $dateTime['year'] = $date[0];
  $dateTime['month'] = $date[1];
  $dateTime['day'] = $date[2];
  $dateTime['hour'] = $time[0];
  $dateTime['minute'] = $time[1];
  return $dateTime;
}

/**
 * Get Plot Data
 * Gets a series of observed data from the previously initialized global var $data
 * @param string $typeNum Data type to get the series for (ie. "00045" for precipitation)
 * @return array Array containing the x,y values for the series.  
 */
function get_plot_data($typeNum)
{
	global $columns, $data;

	$chartData = array();
	$columnNum = get_column_num($typeNum, true, $columns);
	$timeColumn = get_column_num("datetime", false, $columns);
	$chartData = array();
	
	foreach($data as $val)
	{
		if($val[$columnNum] != NULL)
		{
			$datetime = formatDate($val[$timeColumn]);
			$year = $datetime['year'];
			$month = $datetime['month'];
			$day = $datetime['day'];
			$hour = $datetime['hour'];
			$minute = $datetime['minute'];

			$date_str = $year."-".($month)."-".$day." ".$hour.":".$minute;
			$date = new DateTime($date_str);
			$pointData = array();
			$pointData["x"] = $date->getTimestamp() * 1000;
			$pointData["y"] = (float) $val[$columnNum];
			$chartData[] = $pointData;
		}
	}	
	return $chartData;
}

/**
 * Get Column Num
 * Gets the column number of the data type.  Example: $data contains columns date, 00045, 00050, then asking for 00050 would return 2
 * @param string $title Title of the column (ie. "00045" for precipitation)
 * @param boolean $partial Accept partial title matches
 * @param array $columns Column names to check
 * @return integer Integer representing the column number.  
 */
function get_column_num($title, $partial, $columns)
{
	$columnNum = -1;
	for($i = 0; $i < count($columns); $i++)
	{
		if(!$partial && $columns[$i] == $title)
		{
			$columnNum = $i;
			break;
		}
		else if($partial && strpos($columns[$i], $title) !== false)
		{
			$columnNum = $i;
			break;
		}
	}
	return $columnNum;
}

// Initialize the data (download and parse observed data)
initData();

$chartData = array();
$chartData["series"] = array();

// Depending on the chart type, set the typeNum
$typeNum = "00060";
if($chartType == "elevation") $typeNum = "00065";
if($chartType == "discharge") $typeNum = "00060";
if($chartType == "precipitation") $typeNum = "00045";
if($dataType == "real" || $dataType == "both") 
{
  if($chartType == "precipitation") $chartData["series"]["Precipitation"] = get_plot_data($typeNum);
  else $chartData["series"]["Observed Data"] = get_plot_data($typeNum);   
}
 
echo json_encode($chartData);
?>