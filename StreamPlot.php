<?php
include 'realtimeParser.php';
include 'simulationParser.php';
	
$dataType = array("00065" => "Gage Height, Feet", "00060" => "Discharge, cubic feet per second", "00045" => "Precipitation, total, inches");
$titles = array("00065" => "Gage Height", "00060" => "Discharge", "00045" => "Precipitation");
$location = isset($_GET["location"]) ? $_GET["location"] : "Unspecified";
$showRealTime = isset($_GET["realTimeData"]) ? $_GET["realTimeData"] : false;
$showSimulated = isset($_GET["simulatedData"]) ? $_GET["simulatedData"] : false;
$showElevation = isset($_GET["elevation"]) ? $_GET["elevation"] : false;
$showDischarge = isset($_GET["discharge"]) ? $_GET["discharge"] : false;
$showPrecipitation = isset($_GET["precipitation"]) ? $_GET["precipitation"] : false;
$simulatedFileLocation = isset($_GET["simulatedLocation"]) ? $_GET["simulatedLocation"] : false;

function get_annotations ($location, $chart, $series) {
  $link = mysql_connect("mysql.imwillow.com", "uiuc492", "492492!!") or die(mysql_error());
  mysql_select_db("uiuc492") or die(mysql_error());
      
  $query = "SELECT * FROM `Annotation` WHERE location = '" . $location . "' AND chart_title ='" . $chart . "' AND series_name = '" . $series ."'";
  
  $result = mysql_query($query);
  $a = array();  
  while ($r = mysql_fetch_assoc($result)) {
     $a[$r['time']] = $r['annotation'];
  }
  
  mysql_close($link);
  return $a;
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

function plot_point ($time, $yVal, $annotations)
{
	$datetime = formatDate($time);
	$year = $datetime['year'];
	$month = $datetime['month'];
	$day = $datetime['day'];
	$hour = $datetime['hour'];
	$minute = $datetime['minute'];
	
	$timestamp = mktime($hour, $minute, 0, $month, $day, $year);
	echo "{";
	echo "x: Date.UTC(".$year.",". ($month - 1) .",".$day.",".$hour.",".$minute."), ";
	echo "y: ". $yVal;
	/**	if ( strtotime($time)*1000 == $annotations && $graphName == "Water Level")
    {
		echo ", marker: { symbol: 'url(/gfx/A.png)' }},";
    }**/
  if (array_key_exists($timestamp, $annotations)) {
    echo ", marker: {enabled: true, radius: 5}}, ";
  } else {
	  echo ", marker: { enabled: false }}, " ;
	}
}

function get_type($columnNum, $columns)
{
	global $dataType;
	$columnName = explode("_", $columns[$columnNum]);
	$columnName = $dataType[$columnName[1]];
	return $columnName;
}

function get_title($columnNum, $columns)
{
	global $titles;
	$columnName = explode("_", $columns[$columnNum]);
	$columnName = $titles[$columnName[1]];
	return $columnName;
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

function print_yAxis_code($labels, $opposite)
{ 
	echo 'yAxis: [';
	for($i = 0; $i < count($labels); $i++)
	{
		if($i == 0) echo "{";
		else echo ",{";
		?>
			title: {
				text: '<?php echo $labels[$i]; ?>'
			},
			startOnTick: true,
		<?php
		if($opposite[$i])
		{
			?>
				opposite: true,
				reversed: true,
				min: 0,
				max: 5,
			<?php
		}
		echo "}";
	}
	echo '],';
}

function print_title_code($title)
{
	echo 'title: {';
	echo '	text: \''. $title .'\',';
	echo '},';
}

function create_graph ($columns, $data, $simData, $columnNum, $location, $varName, $simulatedType, $dataMultiplier, $includePrecip)
{ ?>
	$('#container_<?php echo $varName; ?>').renderChart({
	<?php global $location; print_title_code($location . " " . get_title($columnNum, $columns)); ?>
	location: '<?php echo $location; ?>',
	<?php
		$labels[0] = get_type($columnNum, $columns); $opposite[0] = false;
		if($includePrecip) $labels[1] = 'Precipitation, total, .01 inches'; $opposite[1] = true;
		print_yAxis_code($labels, $opposite);
	?>
    
		series: [{
			type: 'spline',
			name: 'Observed Data', 
			yAxis: 0,
			data: [
			<?php
				$timeColumn = get_column_num("datetime", false, $columns);
				$annotations = get_annotations($location, get_title($columnNum, $columns), "Observed Data");
				
				foreach($data as $val)
				{
					if($val[$columnNum] != NULL)
					{
						plot_point($val[$timeColumn], $val[$columnNum] * $dataMultiplier, $annotations);
					}
				} ?>
				]
			}
			<?php
			if($includePrecip)
			{
				?>
				,{
				type: 'spline',
				name: 'Precipitation', 
				yAxis: 1,
				data: [
				<?php
					$precipColumn = get_column_num("00045", true, $columns);
					$timeColumn = get_column_num("datetime", false, $columns);
					foreach($data as $val)
					{
						if($val[$columnNum] != NULL)
						{
							plot_point($val[$timeColumn], $val[$precipColumn] * 10);
						}
					} ?>
					]
				}
			<?php } ?>
			<?php
			if($simData)
			{
				?>
				,{
				type: 'spline',
				name: 'Simulated Data',
				yAxis: 0,
				data: [
				<?php
				global $simulatedFileLocation, $location;	
				parseFile($simulatedFileLocation);
				$x = getSimulationData($location);
				foreach ($x as $i => $values) 
				{ 
					foreach ($values as $key => $value) 
					{
						if($key == $simulatedType && ($i != NULL || $value != NULL)) plot_point($i, $value * $dataMultiplier);
					}
				} 
			?> ]} 
			<?php } ?>
			],
	});
<?php 
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Realtime Data for <?php global $location; echo $location; ?></title>
		<!-- 1. Add these JavaScript inclusions in the head of your page -->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript" src="js/highcharts.js"></script>

		<!-- 1a) Optional: the exporting module -->
		<script type="text/javascript" src="js/modules/exporting.js"></script>
		<script type="text/javascript" src="js/chartFunctions.js"></script>
		<link rel="stylesheet" type="text/css" href="css/layout.css" />
		<script type="text/javascript">
			$(document).ready(function(){
<?php
				global $location, $showElevation, $showPrecipitation;
				$file = getFileAsArray("http://waterdata.usgs.gov/il/nwis/uv?cb_00065=on&cb_00060=on&cb_00045=on&format=rdb&period=7&site_no=05531300");
				$columns = getColumnNames($file);
				$data = getData($file);
				if($showElevation)
				{
					$columnNum = get_column_num("00065", true, $columns);
					create_graph($columns, $data, $showSimulated, $columnNum, $location, "chart1", "elevation", 1, $showPrecipitation);
				}
				if($showDischarge)
				{
					$columnNum = get_column_num("00060", true, $columns);
					create_graph($columns, $data, $showSimulated, $columnNum, $location, "chart2", "flow", 1, false);
				}
?>
			  displayAnnotations();
			});
		</script>
	</head>
	<body>
		<!-- 3. Add the container -->
		<div class="mainColumn">
		<h3 class="fancy">Plot for <?php global $location; echo $location; ?></h3>
		<?php 
			global $location, $showElevation;
			if($showElevation) echo '<div id="container_chart1" style="width: 820px; height: 400px; margin: 0px auto 20px auto"></div><br />';
			if($showDischarge) echo '<div id="container_chart2" style="width: 800px; height: 400px; margin: 0 auto"></div>';
		?>
        <div id="annotations">
          <div id="annotation-list">
          </div>
        </div>
		</div>
	</body>
</html>
