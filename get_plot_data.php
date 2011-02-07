<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

include 'realtimeParser.php';
include 'simulationParser.php';

date_default_timezone_set('America/Chicago'); 
$titles = array("00065" => "Gage Height", "00060" => "Discharge", "00045" => "Precipitation");
$file = getFileAsArray("http://waterdata.usgs.gov/il/nwis/uv?cb_00065=on&cb_00060=on&cb_00045=on&format=rdb&period=7&site_no=05531300");
//$file;
//$columns;
//$data;
$columns = getColumnNames($file);
$data = getData($file);
$simulatedFileLocation = "gate798.wsq";
//$simulatedFileLocation = isset($_GET["simLocation"]) ? $_GET["simLocation"] : "Unspecified";
$location = "U22";
//$location = isset($_GET["location"]) ? $_GET["location"] : "Unspecified";

function initData()
{
	global $file, $columns, $data, $location;
	$file = getFileAsArray("http://waterdata.usgs.gov/il/nwis/uv?cb_00065=on&cb_00060=on&cb_00045=on&format=rdb&period=7&site_no=".$location);
	$columns = getColumnNames($file);
	$data = getData($file);
}

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

function get_title()
{
	global $titles, $columns;
	$columnNum = get_column_num("00065", true, $columns);
	$columnName = explode("_", $columns[$columnNum]);
	$columnName = $titles[$columnName[1]];
	return $columnName;
}

function get_plot_data($location, $type)
{
	global $file, $columns, $data;

	$chartData = array();
	
	$columnNum = get_column_num("00065", true, $columns);
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

			//echo $year."-".($month)."-".$day." ".$hour.":".$minute."<br />";
			$date_str = $year."-".($month)."-".$day." ".$hour.":".$minute;
			$date = new DateTime($date_str);
			//$chartData[$date_str] = $val[$columnNum];
			$pointData = array();
			$pointData["x"] = $date->getTimestamp();
			$pointData["y"] = (float) $val[$columnNum];
			$chartData[] = $pointData;
			//$chartData["{x: ".$data_str.", y: ".$val[$columnNum]."}"
		}
	}	
	return $chartData;
}

function get_simulated_plot_data($location, $type)
{
	$chartData = array();
	global $simulatedFileLocation;	
	parseFile($simulatedFileLocation);
	$x = getSimulationData($location);
	foreach ($x as $i => $values) 
	{ 
		foreach ($values as $key => $value) 
		{
			if($key == $type && ($i != NULL || $value != NULL))
			{
				//$chartData[$i] = $value; //plot_point($i, $value * $dataMultiplier, array());
				$pointData = array();
				$date = new DateTime($i);
				$pointData["x"] = $date->getTimestamp();
				$pointData["y"] = (float) $value;
				$chartData[] = $pointData;
			}
		}
	} 
	return $chartData;
}

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

//initData();

$chartData = array();
$chartData["title"] = "U22" . " " . get_title();
$chartData["location"] = "U22";
$chartData["series"] = array();

$chartData["series"]["Observed Data"]  = get_plot_data(" ", " ");
$chartData["series"]["Simulated Data"]  = get_simulated_plot_data("U22", "flow"); 
 
echo json_encode($chartData);
?>