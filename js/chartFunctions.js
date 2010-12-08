var charts = new Array;

jQuery.fn.renderChart = function(attr) {
  i = charts.length;
  var chart = charts[i] = new Highcharts.Chart({
    chart: {
		  renderTo: $(this).attr('id'),
  		marginRight: 80,
  		zoomType: 'x',
  		defaultSeriesType: 'line',
  		events: {
        selection: function (event) {
          refreshAnnotations();
        }
      }
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
  			Highcharts.dateFormat('%m-%d-%Y %H:%M', this.x) +
  			'<br/> Value: '+ Highcharts.numberFormat(this.y, 2) +'';
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
  					click: function (location, title, i) {
  					  return function() {
  					    if (_mobileDevice) {
                  return;
                }
    					  console.log(this);
    						var reply = prompt("Enter annotation information here: ", "");
    						if (reply != null && reply != "") {
      						$.get("annotation.php", {
      						  location: location,
      						  chart_title: title,
      						  series_name: this.series.name,
      						  time: this.x,
                    annotation: reply
      						}, function (data) {
      						  console.log(data);
      						  refreshAnnotations();
      						});
      					}
  					  }
  					} (attr.location, attr.title.text)
  				}
  			}
  		}
  	},
  	yAxis: attr.yAxis,
  	title: attr.title,
    series: attr.series  
  });
  chart.rinfo = {
    title: attr.title.text,
    location: attr.location
  }
  this._chart = chart;
  return this;
}


/**
 * Grid theme for Highcharts JS
 * @author Torstein Hønsi
 */

Highcharts.theme = {
   colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
   chart: {
      backgroundColor: {
         linearGradient: [0, 0, 500, 500],
         stops: [
            [0, 'rgb(255, 255, 255)'],
            [1, 'rgb(240, 240, 255)']
         ]
      },
      borderWidth: 2,
      plotBackgroundColor: 'rgba(255, 255, 255, .9)',
      plotShadow: true,
      plotBorderWidth: 1
   },
   title: {
      style: { 
         color: '#000',
         font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
      }
   },
   subtitle: {
      style: { 
         color: '#666666',
         font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
      }
   },
   xAxis: {
      gridLineWidth: 1,
      lineColor: '#000',
      tickColor: '#000',
      labels: {
         style: {
            color: '#000',
            font: '11px Trebuchet MS, Verdana, sans-serif'
         }
      },
      title: {
         style: {
            color: '#333',
            fontWeight: 'bold',
            fontSize: '12px',
            fontFamily: 'Trebuchet MS, Verdana, sans-serif'

         }            
      }
   },
   yAxis: {
      minorTickInterval: 'auto',
      lineColor: '#000',
      lineWidth: 1,
      tickWidth: 1,
      tickColor: '#000',
      labels: {
         style: {
            color: '#000',
            font: '11px Trebuchet MS, Verdana, sans-serif'
         }
      },
      title: {
         style: {
            color: '#333',
            fontWeight: 'bold',
            fontSize: '12px',
            fontFamily: 'Trebuchet MS, Verdana, sans-serif'
         }            
      }
   },
   legend: {
      itemStyle: {         
         font: '9pt Trebuchet MS, Verdana, sans-serif',
         color: 'black'

      },
      itemHoverStyle: {
         color: '#039'
      },
      itemHiddenStyle: {
         color: 'gray'
      }
   },
   labels: {
      style: {
         color: '#99b'
      }
   }
};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);

function getDataElementByX(data, timestamp) {
  var x = timestamp;
  
  var a = 0, b = data.length;
  var m;
  
  while(a < b) {
    m = parseInt((a+b)/2);
    if (data[m].x % x == 0) {
      return data[m]
    }
    
    if (data[m].x < x)
      a = m;
    else
      b = m;
  }
}

function addAnnotationToGraph(chart, series_no, timestamp, number) {
  var posX, posY;
  var data = chart.series[series_no].data;
  var element = getDataElementByX(data, timestamp);
  var y = element.plotY;
  
  posX = chart.plotLeft + chart.xAxis[0].translate(timestamp) - 10;
  posY = chart.plotTop + y - 40;
  
  if (posX < chart.plotLeft || posX > chart.plotLeft + chart.plotWidth) {
    return;
  }
  
  var group = chart.renderer.g('annotation').attr({zIndex: 100}).add();
  
  //rect: function (x, y, width, height, round-corners, stroke-width)
  chart.renderer.rect(posX, posY, 20, 20, 2, 1)
                .attr({
                  fill: 'white',
                  stroke: 'grey',
                  strokeWidth: 1
                })
                .add(group);
  
  chart.renderer.path(['M', posX + 10, posY + 20, 'V', element.plotY + chart.plotTop])
                .attr({
                  stroke: 'grey',
                  strokeWidth: 1
                })
                .add(group);
  
  chart.renderer.text(number, posX+6, posY+15).add(group);
  
  chart.annotation_groups.push(group);
}

function annotationExpand(id, g) {
  annotationShrinkAll();
  $($('#' + id + ' dl')[g]).addClass('highlighted').removeClass('compact');
}

function annotationShrinkAll() {
  $('.annotation-list dl').removeClass('highlighted hover').addClass('compact');
}

function annotationHover(id, g) {
  annotationReleaseAllHover();
  $($('#' + id + ' dl')[g]).addClass('hover');
}

function annotationReleaseAllHover() {
  $('.annotation-list dl').removeClass('hover');
}

function displayAnnotations () {
  if (_mobileDevice)
    return;
  
  var c, s;
  var info, series;
  
  var annotation_container;
  
  for (c in charts) {
    info  = charts[c].rinfo;
    charts[c].annotation_groups = [];
    
    var annotation_container = $('<div></div>').attr('id', 'container_annotation' + (parseInt(c)+1)).addClass('container_annotation');
    
    $('#container_chart' + (parseInt(c)+1)).after(annotation_container);
    
    for (s in charts[c].series) {
      series = charts[c].series[s];
      id = info.location + '-' + info.title + '-' + series.name;
      id = id.replace(/ /g, "-");
            
      $.getJSON('get_annotations.php', {
          location    : info.location,
          chart_title : info.title,
          series_name : series.name
        }, function (chart, series_no, id, annotation_container) {
          return function (annotations) {
            var color = chart.series[series_no].color;
            var timestamp;
            var n = 0;
            var time;
            var a;
            
            console.log(annotations);
            
            var aldiv = 
              $('<div></div>')
                .attr('id', id)
                .addClass('annotation-list')
                .css('border-left', '3px solid ' + color)
                .appendTo(annotation_container);
            
            for (timestamp in annotations) {
              time = new Date(parseInt(timestamp));
              addAnnotationToGraph(chart, series_no, timestamp, ++n);
              
              a = $('<dl class="compact"><dt>' + n + '</dt><dd class="date">' + time.toLocaleString() + '</dd><dd class="annotation-text">' + annotations[timestamp] + '</dd></dl>');
              a.click(function (id, n) {
                return function () {
                  annotationExpand(id, n-1)
                }
              } (id, n));
              
              $('#' + id)
                .append(a);
            }
            
            var g;
            for (g in chart.annotation_groups) {
              $(chart.annotation_groups[g].element).click(
                function (id, g) {
                  return function () {
                    annotationExpand(id, g);
                  }
                } (id, g)
              ).hover(
                function (id, g) {
                  return function () {
                    annotationHover(id, g);
                  }
                } (id, g),
                function () {
                  annotationReleaseAllHover();
                }
              )
            }
          }
        } (charts[c], s, id, annotation_container)
      );
    }
  }
}

function destroyAnnotations (callback) {
  var c, s, a;
  var info, series;
  
  for (c in charts) {
    a = charts[c].annotation_groups;
    
    $(a).each(function (i, anno) {
      $(anno.element).remove()
      delete anno;
    });
  }
  
  $('.container_annotation').remove();
      
  if (typeof callback === 'function')
    callback();
}

function refreshAnnotations () {
  destroyAnnotations(displayAnnotations);
}