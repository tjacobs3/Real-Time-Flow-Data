var charts = new Array;

jQuery.fn.renderChart = function(attr) {
  i = charts.length;
  var chart = charts[i] = new Highcharts.Chart({
    chart: {
		renderTo: $(this).attr('id'),
		zoomType: 'x',
		defaultSeriesType: 'line'
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
						var reply = prompt("Enter annotation information here: ", "");
						document.getElementById("annotations").innerHTML =document.getElementById("annotations").innerHTML +
								"Date: " + this.x   + this.series.name + ": " + this.y + " Note: " + reply + "<br></br>";
					}
				}
			}
		}
	},
	yAxis: attr.yAxis,
	title: attr.title,
    series: attr.series
  });
  return this;
}