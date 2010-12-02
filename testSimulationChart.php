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
                                                defaultSeriesType: 'line',
                                                marginRight: 130,
                                                marginBottom: 25

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
						showFirstLabel: false,
                                                plotLines: {
                                                value: 0,
                                                width: 0.5,
                                                color: '#808080'
                                                }
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
								enabled: false,
								states: {
									hover: {
										enabled: false,
										radius: 2
									}
								}
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
                                        legend: {
                                                 layout: 'vertical',
                                                 align: 'right',
                                                 verticalAlign: 'top',
                                                 x: -10,
                                                 y: 50,
                                                 borderWidth: 5
                                              },
					series: [{
						type: 'spline',
						name: 'Location 1',
						data: [


<?php

	include 'simulationParser.php';
	parseFile();
	$x = getData('U22');
	//print_r($x);
	foreach ($x as $i => $values) 
	{
		$date =  date_parse_from_format("Y-m-d-H", $i);
		$month = ((int)$date[month]) - 1;
		echo "[Date.UTC(".$date[year].','.$month.','.$date[day].','.$date[hour].'), ';
		foreach ($values as $key => $value) 
		{
    			if($key == 'flow') echo $value.'],';
	    	}
	}

	echo ']';

?>

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

