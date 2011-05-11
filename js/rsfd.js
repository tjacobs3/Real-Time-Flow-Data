// Set up the site_names array
// This maps the site numbers to their actual names
var site_names = new Array();
site_names["U84"] = "U84";
site_names["D126"] = "D126";
site_names["U204"] = "Irving (ULTR, ADMT)";
site_names["U22"] = "Irving (ULTR, ADMT)";
site_names["D45"] = "Diversion";
site_names["D57"] = "D57";
site_names["D80"] = "Harger (ULTR, ADMT)";
site_names["U84"] = "U84";
site_names["D108"] = "D108";

// File to get the simulation files from.  Changing this folder is NOT supported
var simulationFolderName = "simulationfiles";

// Initialize the MVC
var rsfd = rsfd || {};
rsfd.ui = rsfd.ui || {};
rsfd.data = rsfd.data || {};
rsfd.chart = rsfd.chart || {};
rsfd.controller = rsfd.controller || {};

// UI - Show Loading
// Hides the main div and displays the loading div
rsfd.ui.showLoading = function () {
  $('#main-content>div').hide();
  $('#main-content #loading').show();
}

// UI - Show Select Prompt
// Hides the main div and shows the selection prompt
rsfd.ui.showSelectPrompt = function () {
  $('#main-content>div').hide();
  $('#main-content #select-prompt').show();
}

// UI - Get Location
// Gets the currently selected location
rsfd.ui.getLocation = function () {
  return $("#location option:selected").val()
}

// UI - Get Period
// Gets the currently selected period value
rsfd.ui.getPeriod = function () {
  return parseInt($("#period").val());
}

// UI - Get Data Type
// Outdated : returns "both"
rsfd.ui.getDataType = function () {
  return "both";
  return $("#data-type input[name='data-type']:checked").val();
}

// UI - Get All Parameters
// Returns an object holding the location, period and data_type
rsfd.ui.getAllParameter = function () {
  return {
    location: rsfd.ui.getLocation(),
    period: rsfd.ui.getPeriod(),
    data_type: rsfd.ui.getDataType()
  }
}

// UI - Get Parameters From URL
// Sets the location and period from GET variables in the url
rsfd.ui.getParametersFromURL = function () {
	var loc = rsfd.ui.getUrlVars()["location"];
	var period = rsfd.ui.getUrlVars()["period"];	
	if (loc) $("#location").val(loc);
	if (period)$("#period").val(period);
}

// UI - Get URL Vars
// Gets the GET variables from the URL
rsfd.ui.getUrlVars = function() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}

// UI - Add Simulated File Input
// Adds another field for FEQ files
rsfd.ui.addSimulatedFileInput = function () {
	if(!rsfd.ui.simCount) rsfd.ui.simCount = 2;
	$("#simulated_files_container").append("<label for=\"simulated_file_" + rsfd.ui.simCount + "\">FEQ File " + rsfd.ui.simCount + ": </label><input type=\"text\" name=\"simulated_file_" + rsfd.ui.simCount + "\" value=\"\" id=\"simulated_file_" + rsfd.ui.simCount + "\"readonly=\"readonly\"><br />");
	rsfd.ui.simCount++;
}

// UI - Get Simulated File Names
// Creates an array containing all the simulated file names that are selected
rsfd.ui.getSimulatedFileNames = function () {
	if(!rsfd.ui.simCount) rsfd.ui.simCount = 2;
	var fileNames = new Array();
	for(var i = 1; i < rsfd.ui.simCount; i++)
	{
		fileNames[i-1] = $("#simulated_file_" + i).val();
	}
	return fileNames;
}

// UI - Set File Names
// Sets the file name fields with the files found in the simulationFiles folder
rsfd.ui.setFileNames = function () {
	$.getJSON("get_simulationfile_names.php",
	function(data) {
		$.getJSON("get_simulation_alias.php",
		function(aliasData) {
			for(var i = 0; i < data.length; i++)
			{
				if(i > 0) rsfd.ui.addSimulatedFileInput();
				//Display alias name here
				$("#simulated_file_" + (i+1)).val(data[i]);
			}
		});
	});
}

// UI - Set Offset
// Sets the offset value by loading the values from get_site_offset.php
rsfd.ui.setOffset = function () {
	$.getJSON("get_site_offset.php",{location: rsfd.ui.getLocation()},
	function(data)
	{
		$('#elevation_shift_control').val(data);
	});
}

// UI - Reload Page
// Refreshes the page
rsfd.ui.reloadPage = function () {
	var url = window.location.href.split('?');
	window.location.href=url[0]+"?location=" + rsfd.ui.getLocation() + "&period=" + rsfd.ui.getPeriod();
}

// Data - Get Real Data
// Loads the observed data from get_plot_data.php
rsfd.data.getRealData = function (p, callbackFunc) {
  if (typeof callbackFunc !== 'function')
    return false;

  $.getJSON('get_plot_data.php', p, callbackFunc);
  
  return true;
};

// Data - Get Simulated Data
// Gets the simulation data from simfile using get_simulation_data.php
rsfd.data.getSimulatedData = function (p, simfile, callbackFunc) {
  if (typeof callbackFunc !== 'function')
    return false;
    
  p2 = {};
  for (var prop in p) {
    p2[prop] = p[prop];
  }
  if (typeof simfile === "string" && simfile !== '')
    p2.simLocation = simulationFolderName + "\\" + simfile;
    
  $.getJSON('get_simulation_data.php', p2, callbackFunc);
  
  return true;
}

// Data - Get Annotation
// Gets the annotation data from annotation.php
rsfd.data.getAnnotation = function (p, seriesName, callbackFunc) {
  if (typeof callbackFunc !== 'function')
    return false;

  p2 = {};
  for (var prop in p) {
    p2[prop] = p[prop];
  }
  if (typeof seriesName === "string" && seriesName !== '')
    p2.seriesName = seriesName;
    
  p2.method = 'annotation.get';
    
  $.getJSON('annotation.php', p2, callbackFunc);
}

// Data - Add Annotation
// Adds an annotation by using annotation.php
rsfd.data.addAnnotation = function (annotation, callbackFunc) {
  $.getJSON('annotation.php', 
    {
      method: 'annotation.add',
      location: annotation.location,
      chartType: annotation.chartType,
      seriesName: annotation.seriesName,
      timestamp: annotation.timestamp,
      content: annotation.content
    }, 
    callbackFunc);
}

// Data - Delete Annotation
// Deletes an annotation using annotation.php
rsfd.data.deleteAnnotation = function (annotation, callbackFunc) {
  $.getJSON('annotation.php', 
    {
      method: 'annotation.delete',
      location: annotation.location,
      chartType: annotation.chartType,
      seriesName: annotation.seriesName,
      timestamp: annotation.timestamp,
      content: annotation.content
    }, 
    callbackFunc);  
}

// Chart - constructor
// Creates a highchart chart in the given container holding no data
rsfd.Chart = function (container, title, location, yAxisName, chartType, id) {
  var that = this;
  if (typeof id !== 'string') {
    id = container + '_chart';
  }
  
  this.container = container;
  this.title = title;
  this.location = location;
  this.id = id;
  this.type = chartType;
  this.yAxisName = yAxisName;
  
  this.chart = new Highcharts.Chart({
    chart: {
      zoomType: 'x',
      animation: false,
      renderTo: container,
      events: {
        redraw: function () {
          that.refreshAllAnnotation();
        }
      }
    },
    title: {
      text: this.title
    },
    location: {
      text: this.location
    },
    plotOptions: {
      series: {
	animation: false,
	shadow: false,
	events: {
                legendItemClick: function(event) {
			 that.refreshAllAnnotation();
                }
            },	
        marker: {
          enabled: false,
          states: {
            hover: {
              enabled: true
            }
          }
        },
        point: {
          events: {
            click: function(that) {
              return function() {
    						var content = window.prompt("Enter annotation information here: ", "");
    						if (content != null && content != "") {
    						  that.userAddAnnotation(this.series.name, this.x, content);
    						}
              }
            } (this)
          }
        }
      }      
    },
    tooltip: {
      shadow:false,
      formatter: function () {
        return Highcharts.dateFormat("%b %d, %Y %l:%M%p <span style=\"visibility: hidden;\">-</span><br/>Value: ", this.x) + Math.round(this.y*100)/100;
      }
    },
    exporting: {
        enabled: true
    },
    
    xAxis: {
      type: 'datetime',
	  maxZoom: 1000 * 60 * 60 * 10, // 10 hours
      dateTimeLabelFormats: {
		day: '%m/%e/%y',
		hour: '%m/%e<br>%l:%M%p',
		minute: '%m/%e:%l:%M%p',
		second: '%l:%M:%S%p'
      }
    },
	  yAxis: [{
			title: {
			  text: this.yAxisName
		  }
         
      }],
    series: []
  });
  
  var con = $('#' + this.container);
  
  this.annotations = [];
  this.annotation_window = 
    $('<div></div>')
      .attr('id', id + '_annotation')
      .addClass('chart_annotation_list');
  this.annotation_list = $("<ul></ul>");
  this.annotation_window.append(this.annotation_list);
  con.append(this.annotation_window);
  
  this.series = {};
  this.prompt_count = 0;
  this.prompts = [];
  
  var prompt =
    $('<div></div>')
      .attr('id', id + '_prompt')
      .addClass('chart_prompt')
      .hide()
      .appendTo(con);
  var prompt_ul = 
    $('<ul></ul>')
      .appendTo(prompt)
  this.prompt_list = $(prompt_ul)
  this.prompt_window = $(prompt);
}

// Chart - Display Data
// Adds a series to the chart
rsfd.Chart.prototype.displayData = function (data) {
  $("#" + this.container).show();
  for (var type in data.series) {
    if (type in this.series) {
      this.series[type].remove();
    }
	var axisIndex = 0;
	//if(type === "Precipitation")  axisIndex = 1;
    if(data.series[type].length > 0)
	{
      this.series[type] = this.chart.addSeries({
        type: 'line',
        name: type,
        data: data.series[type],
		yAxis: axisIndex,
        pointStart: Date(0)
      });	
	}
  }
}

// Chart - Display Annotation
// Adds annotation to the chart
rsfd.Chart.prototype.displayAnnotation = function (data) {
  var i, anno;
  this.clearAllAnnotation();
  
  for (i in data) {
    anno = new rsfd.Annotation(this.annotations.length+1, data[i].location, this.type, 
                                data[i].seriesName, parseInt(data[i].timestamp), data[i].content);
    this.addAnnotation(anno);
  }
}

// Chart - User Add Annotation
// Runs when a user submits a new annotation
rsfd.Chart.prototype.userAddAnnotation = function (seriesName, timestamp, content) {
  var anno = new rsfd.Annotation(this.annotations.length+1, this.location, this.type, seriesName, timestamp, content);
    
  rsfd.data.addAnnotation(anno);
  this.addAnnotation(anno);
  
}

rsfd.Chart.prototype.showPrompt = function (content) {
  $("#" + this.container).show();
  if (typeof content === "undefined" || content === "")
    return;
  
  var p_id = ++this.prompt_count;
  this.prompts[p_id] = 
    $('<li></li>').text(content)
  this.prompt_list.append(this.prompts[p_id]);
  this.prompt_window.slideDown();
  return p_id;
}

rsfd.Chart.prototype.hidePrompt = function (id) {
  var that = this;
  if (typeof this.prompts[id] === 'undefined')
    return;
    
  this.prompts[id].slideUp(function() {
    $(this).remove();
    that.prompt_count--;

    if (that.prompt_count === 0) {
      if (that.chart.series.length === 0) {
        $("#" + that.container).hide();
      } else {
        that.prompt_window.hide();
      }
    }
  });
}

// Chart - Shift Values
// Shifts a series data values by amount
rsfd.Chart.prototype.shiftValues = function (seriesName, amount) { 
	for(sNames in this.series)
	{
		if(sNames === seriesName)
		{
			var data = this.series[sNames].data;
			for (var point in data) {
				data[point].update(data[point].y += amount, false, false);
			}
		}
	}
}

// Chart - Get Element By X
// Gets an element in the series by the x value
rsfd.Chart.prototype.getElementByX = function (seriesName, x) {
  var data = this.series[seriesName].data;
  var a = 0, b = data.length;
  var m;
  
  while(a < b) {
    m = parseInt((a+b)/2);
    if (data[m].x % x === 0) {
      return data[m];
    }
    
    if (data[m].x < x)
      a = m;
    else
      b = m;
  }  
}

// Chart - Add Annotation To Chart
// Draws an annotation to the chart
rsfd.Chart.prototype.addAnnotationToChart = function (annotation) {
  var posX, posY;
  var chart = this.chart;
  var data = this.series[annotation.seriesName].data;
  
  if (annotation.onChart !== undefined) {
    delete annotation.onChart;
  }
  
  if (annotation.timestamp < data[0].x || annotation.timestamp > data[data.length-1].x)
    return false;
    
  var element = this.getElementByX(annotation.seriesName, annotation.timestamp);
  var y = element.plotY;

  
  posX = chart.plotLeft + chart.xAxis[0].translate(annotation.timestamp) - 10;
  posY = chart.plotTop + y - 40;
  
  if (posX < chart.plotLeft || posX > chart.plotLeft + chart.plotWidth) {
    return false;
  }
  
  var group = chart.renderer.g('chart_annotation').attr({zIndex: 100}).add();
  
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
  
  chart.renderer.text(annotation.id, posX+6, posY+15).add(group);  
  
  annotation.onChart = group;
  
  return true;
}

// Chart - Add Annotation To List
rsfd.Chart.prototype.addAnnotationToList = function (annotation) {
  var that = this;
  if (annotation.onList !== undefined) {
    delete annotation.onList;
  }
  
  annotation.date = Date(annotation.timestamp);
  annotation.onList = $("#tmpl_annotation").tmpl(annotation).appendTo(this.annotation_list);
  
  annotation.onList.find(".annotation_delete").click(function () {
    that.removeAnnotation(annotation);
    annotation.del();
  });
}

// Chart - Add Annotation
rsfd.Chart.prototype.addAnnotation = function (annotation) {
  if (this.addAnnotationToChart(annotation)) 
    this.addAnnotationToList(annotation);
  this.annotations.push(annotation);    
}

// Chart - Add All Annotation
rsfd.Chart.prototype.addAllAnnotation = function () {
  var annotation;
  for (var i in this.annotations) {
    annotation = this.annotations[i];
    if (this.addAnnotationToChart(annotation)) 
      this.addAnnotationToList(annotation);
  }  
}

// Chart - Remove Annotation
rsfd.Chart.prototype.removeAnnotation = function (annotation) {
  annotation.remove();
}

// Chart - Remove All Annotation
rsfd.Chart.prototype.removeAllAnnotation = function () {
  for (var i in this.annotations) {
     this.removeAnnotation(this.annotations[i]);
  }
}

// Chart - Clear All Annotation
rsfd.Chart.prototype.clearAllAnnotation = function () {
  this.removeAllAnnotation();
  this.annotations.length = 0;
}

// Chart - Refresh All Annotation
// Removes all annotations and re-adds them
rsfd.Chart.prototype.refreshAllAnnotation = function (annotation) {
  this.removeAllAnnotation();
  this.addAllAnnotation();
}

// Annotation - constructor
rsfd.Annotation = function (id, location, chartType, seriesName, timestamp, content) {
  this.id = id;
  this.location = location;
  this.chartType = chartType;
  this.seriesName = seriesName;
  this.timestamp = timestamp;
  this.content = content;
};

rsfd.Annotation.prototype.remove = function () {
  if (this.onChart) {
    $(this.onChart.element).hide().remove();
  }
  if (this.onList) {
    $(this.onList).hide().remove();
  }
}

rsfd.Annotation.prototype.del = function () {
  this.remove();
  rsfd.data.deleteAnnotation(this);
}

// Controller - constructor
rsfd.Controller = function () {
  this.charts = {};
}

// Controller - register chart
// Adds a chart to the the controllers charts
rsfd.Controller.prototype.registerChart = function (type, chart) {
  this.charts[type] = chart;
}

// Controller - Shift Values
// Shifts the values of the the chart series by amount
rsfd.Controller.prototype.shiftValues = function (chartName, seriesName, amount) {
  if (typeof this.charts[chartName] !== "undefined")
  {
	  this.charts[chartName].shiftValues(seriesName, amount)
	  this.charts[chartName].chart.redraw();
  }
}

// Controller - Show Observed Data
// Loads the Observed data from the USGS servers and displays them on the chart
rsfd.Controller.prototype.showObservedData = function (chart, parameters) {
  parameters.chartType = chart.type;  
  var p_id = chart.showPrompt('Loading Observed Data');
  rsfd.data.getRealData(parameters, function (c, p_id, p, that) {
    return function (data) {
      c.displayData(data);
      that.showAnnotation(c, p, "Observed Data");
      c.hidePrompt(p_id);
      if(chart.type === 'elevation')
      {
        controller.shiftValues('elevation', 'Observed Data', parseFloat($('#elevation_shift_control').val()));
      }
    }
  } (chart, p_id, parameters, this));
}

// Controller - Show Simulated Data
// Loads the simulated data from the FEQ files and displays the series on the chart
rsfd.Controller.prototype.showSimulatedData = function (chart, parameters, simfile) {
  parameters.chartType = chart.type;  
  var p_id = chart.showPrompt('Loading Simulated Data: ' + simfile);
  rsfd.data.getSimulatedData(parameters, simfile, function (c, p_id, p, that) {
    return function (data) {
      c.displayData(data);
      for (var seriesName in data.series)
        that.showAnnotation(c, p, seriesName);      
      c.hidePrompt(p_id);
    }
  } (chart, p_id, parameters, this));
}

// Controller - show Annotation
// Loads annotations for the chart and displays them
rsfd.Controller.prototype.showAnnotation = function (chart, parameters, seriesName) {
  parameters.chartType = chart.type;  
  var p_id = chart.showPrompt('Loading Annotation for "' + seriesName + '"');
  rsfd.data.getAnnotation(parameters, seriesName, function (c, p_id, p) {
    return function (data) {
      c.displayAnnotation(data);
      c.hidePrompt(p_id);
    }
  } (chart, p_id, parameters));
}

// Controller - change location
// Sets the titles of the charts to reflect the new location
rsfd.Controller.prototype.changeLocation = function (loc)
{
  var chart_type, new_title, chart;
  for (chart_type in this.charts) {
    chart = this.charts[chart_type];
    new_title = site_names[loc] + {'elevation': ' Gage Height', 'discharge': ' Discharge'} [chart_type];
    chart.chart.setTitle({text: new_title});
  }
}

// Controller - Show Data
// Loads observed and simulated data onto the chart
rsfd.Controller.prototype.showData = function() {
  var p = rsfd.ui.getAllParameter();
  var chart, prompt_id;
  var simulatedNames = rsfd.ui.getSimulatedFileNames();
  for (var chart_type in this.charts) {
    chart = this.charts[chart_type];
    
    this.showObservedData(chart, p);
    
	  for (var i = 0; i < simulatedNames.length; i++)
	  {
		  this.showSimulatedData(chart, p, simulatedNames[i]);
	  }
  }
}

$(document).ready(function () {
  rsfd.ui.getParametersFromURL();
  //rsfd.ui.showSelectPrompt();
  var elevation_chart = new rsfd.Chart("elevation", site_names[rsfd.ui.getLocation()] + " Gage Height, " + new Date().getFullYear(), rsfd.ui.getLocation(), "Water-Surface Elevation, feet", "elevation");
  var discharge_chart = new rsfd.Chart("discharge", site_names[rsfd.ui.getLocation()] + " Discharge, " + new Date().getFullYear(), rsfd.ui.getLocation(), "Discharge in CFS", "discharge");
  var precipitation_chart = new rsfd.Chart("precipitation", site_names[rsfd.ui.getLocation()] + " Precipitation, " + new Date().getFullYear(), rsfd.ui.getLocation(), "Precipitation in Inches", "precipitation");
  controller = new rsfd.Controller();
  controller.registerChart("elevation", elevation_chart);
  controller.registerChart("discharge", discharge_chart);
  controller.registerChart("precipitation", precipitation_chart);
  rsfd.ui.setFileNames();
  rsfd.ui.setOffset();
  setTimeout("controller.showData()",500);

  $('#refresh-button').click(function () {
	rsfd.ui.reloadPage();
    //rsfd.ui.setOffset();
    //controller.showData();
    //controller.changeLocation(rsfd.ui.getLocation());
  });
  $("#elevation_shift_control_button").click(function () {
    controller.shiftValues('elevation', 'simulated', parseFloat($('#elevation_shift_control').val()));
  });
  $("#add_simulated_file_button").click(function () {
    rsfd.ui.addSimulatedFileInput();
  });  
  $("#add_sim_data_to_chart_button").click(function () {
    controller.showData();
  });   
})
