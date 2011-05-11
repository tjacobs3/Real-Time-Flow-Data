<?php
/**
 * Get Simulation Data
 * 
 * This file contains the functionality for serving the simulated data
 * via json objects. 
 */
 
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

include 'simulationParser.php';
include 'site_no.php';

// Initialize the time zone.  Required to get accurate millisecond data
date_default_timezone_set('America/Chicago'); 

$titles = array("00065" => "Gage Height", "00060" => "Discharge", "00045" => "Precipitation");
$simulatedFileLocation = isset($_GET["simLocation"]) ? $_GET["simLocation"] : "gate798.wsq";
$location = isset($_GET["location"]) ? $_GET["location"] : "U22";
$timePeriod = isset($_GET["period"]) ? $_GET["period"] : "7";
$chartType = isset($_GET["chartType"]) ? $_GET["chartType"] : "elevation";  // Can be "elevation" or "discharge"
$dataType = isset($_GET["dataType"]) ? $_GET["dataType"] : "both"; // Can be "real" or "simulated" or "both"
$columns;
$data;

//Parse given date based on Y-m-d H:i
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
 * Get Simulated Plot Data
 * Gets a series of simulated data from the file that the global $simulatedFilLocation points to
 * @param string $location Site location to get data for
 * @param string $type Data type to get the series for (ie. "00045" for precipitation)
 * @return array Array containing the x,y values for the series.  
 */
function get_simulated_plot_data($location, $type)
{
	$chartData = array();
	global $simulatedFileLocation;	
	global $timePeriod;
	parseFile($simulatedFileLocation, $timePeriod);
	$x = getSimulationData($location);
	if(!empty($x))
	{
		foreach ($x as $i => $values) 
		{ 
			foreach ($values as $key => $value) 
			{
				if($key == $type && ($i != NULL || $value != NULL))
				{
					//$chartData[$i] = $value; //plot_point($i, $value * $dataMultiplier, array());
					$pointData = array();
					$date = new DateTime($i);
					$pointData["x"] = $date->getTimestamp() * 1000;
					$pointData["y"] = (float) $value;
					$chartData[] = $pointData;
				}
			}
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



$chartData = array();
$chartData["series"] = array();

// Depending on the chart type, set the typeNum
$typeNum = "00060";
if ($chartType == "elevation") $typeNum = "00065";
if ($chartType == "discharge") $typeNum = "00060";
if ($chartType == "precipitation") $typeNum = "00045"; 
$typeSimName = ($chartType == "elevation") ? "elevation" : "flow";
if($chartType == "precipitation") $typeSimName = "precip";
if($dataType == "simulated" || $dataType == "both"){
	$pieces = explode("\\", $simulatedFileLocation);
	$chartData["series"]["".$pieces[1]] = get_simulated_plot_data($location, $typeSimName); 
}
 
// Return the simulated data
echo json_encode($chartData);
?>

