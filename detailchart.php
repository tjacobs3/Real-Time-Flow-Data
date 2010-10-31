<?php
function plot_point ($time, $yVal, $graphName)
{
    $annotations = 1286952000000; 
	$format = 'Y-m-d H:i';
	$datetime = date_parse_from_format($format, $time);
	$year = $datetime[year];
	$month = $datetime[month];
	$day = $datetime[day];
	$hour = $datetime[hour];
	$minute = $datetime[minute];
	echo "{";
	echo "x: Date.UTC(".$year.",". $month.",".$day.",".$hour.",".$minute."), ";
	echo "y: ". $yVal;
	if ( strtotime($time)*1000 == $annotations && $graphName == "Water Level")
    {
		echo ", marker: { symbol: 'url(/gfx/A.png)' }},";
    }
	else echo ", marker: { enabled: false }}, ". "/** " . (strtotime($time)*1000) . "**/" ;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Parser Example</title>
		<!-- 1. Add these JavaScript inclusions in the head of your page -->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript" src="/js/highcharts.js"></script>

		<!-- 1a) Optional: the exporting module -->
		<script type="text/javascript" src="/js/modules/exporting.js"></script>


		<!-- 2. Add the JavaScript to initialize the chart on document ready -->
				<script type="text/javascript">

                                var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						zoomType: 'x',
                        defaultSeriesType: 'line'

					},
				        title: {
						text: 'Stream Gauge Height'
					},
				        subtitle: {
						text: 'Click and drag in the plot area to zoom in'
					},
					xAxis: {
						type: 'datetime',
						maxZoom: 1 * 24 * 3600000, // fourteen days
						title: {
							text: null
						}
					},
					yAxis: {
						title: {
							text: 'Water Level (feet)'
						},
						min: 0.6,
						startOnTick: true,
						showFirstLabel: false
					},
					tooltip: {
						formatter: function() {
							return ''+
								Highcharts.dateFormat('%A %B %e %Y', this.x) + ':'+
								' Water Level = '+ Highcharts.numberFormat(this.y, 2) +' feet';
						}
					},
					legend: {
						enabled: true
					},
					plotOptions: {
						spline: {
                                                cursor: 'pointer',
                                                marker: {
								enabled: true,
							},
                                                linewidth: 1,
                                                point: {
                                                   events: {
                                                      click: function() {
                                                        var reply = prompt("Enter annotation information here: ", "")
                                                        document.getElementById("annotations").innerHTML =document.getElementById("annotations").innerHTML +
                                                                                        "Date: " + this.x   + this.series.name + ": " + this.y + " Note: " + reply + "<br></br>";
                                                        

                                                      }
                                                   }
                                                }
                                             }

					},

					series: [{
						type: 'spline',
						name: 'Water Level',
						data: [


						<?php
						$filename = "testData.txt";
						$fd = fopen ($filename, "r");
						$contents = fread ($fd,filesize ($filename));
						fclose ($fd);
						$delimiter = "	";
						$splitcontents = explode($delimiter, $contents);
						$counter = 0;
						$arraycount = 0;
						$format = 'Y-m-d H:i';
						$time;
						$yVal;
						foreach($splitcontents as $val)
						{

							if($counter == 2)
							{
								$time = $val;
							}
							if($counter == 4)
							{
								$counter = -1;
								$yVal = $val;
								plot_point($time, $yVal, "Water Level");
							}
							$counter = $counter + 1;
						}
						?>
						]
					}, {
						type: 'spline',
						name: 'Water Level for Amazon River',
						data: [


						<?php
						$filename = "dummyData.txt";
						$fd = fopen ($filename, "r");
						$contents = fread ($fd,filesize ($filename));
						fclose ($fd);
						$delimiter = "	";
						$splitcontents = explode($delimiter, $contents);
						$counter = 0;
						$arraycount = 0;
						$format = 'Y-m-d H:i';
						$time;
						$yVal;
						foreach($splitcontents as $val)
						{

							if($counter == 2)
							{
								$time = $val;
							}
							if($counter == 4)
							{
								$counter = -1;
								$yVal = $val;
								plot_point($time, $yVal, 'Water Level for Amazon River');
							}
							$counter = $counter + 1;
						}
						?>
						]
					}]
				});


			});

		</script>
	</head>
	<body>

		<!-- 3. Add the container -->
		<div id="container" style="width: 800px; height: 400px; margin: 0 auto"></div>
                <div id="annotations" style="width: 400px; height: 400px; margin: 0 auto"></div>
	</body>
</html>
