var rsfd = rsfd || {};
rsfd.ui = rsfd.ui || {};
rsfd.data = rsfd.data || {};
rsfd.chart = rsfd.chart || {};
rsfd.controller = rsfd.controller || {};

rsfd.ui.showLoading = function () {
  $('#main-content>div').hide();
  $('#main-content #loading').show();
}

rsfd.ui.showSelectPrompt = function () {
  $('#main-content>div').hide();
  $('#main-content #select-prompt').show();
}

rsfd.ui.getLocation = function () {
  return $("#location option:selected").val()
}

rsfd.ui.getPeriod = function () {
  return parseInt($("#period").val());
}

rsfd.ui.getDataType = function () {
  return "both";
  return $("#data-type input[name='data-type']:checked").val();
}

rsfd.ui.getAllParameter = function () {
  return {
    location: rsfd.ui.getLocation(),
    period: rsfd.ui.getPeriod(),
    data_type: rsfd.ui.getDataType()
  }
}

rsfd.ui.addSimulatedFileInput = function () {
	if(!rsfd.ui.simCount) rsfd.ui.simCount = 2;
	$("#simulated_files_container").append("<label for=\"simulated_file_" + rsfd.ui.simCount + "\">FEQ File " + rsfd.ui.simCount + ": </label><input type=\"text\" name=\"simulated_file_" + rsfd.ui.simCount + "\" value=\"\" id=\"simulated_file_" + rsfd.ui.simCount + "\"><br />");
	rsfd.ui.simCount++;
}

rsfd.ui.getSimulatedFileNames = function () {
	if(!rsfd.ui.simCount) rsfd.ui.simCount = 2;
	var fileNames = new Array();
	for(var i = 1; i < rsfd.ui.simCount; i++)
	{
		fileNames[i-1] = $("#simulated_file_" + i).val();
	}
	return fileNames;
}

rsfd.ui.setFileNames = function () {
	$.getJSON("get_simulationfile_names.php",
	function(data) {
		for(var i = 0; i < data.length; i++)
		{
			if(i > 0) rsfd.ui.addSimulatedFileInput();
			$("#simulated_file_" + (i+1)).val(data[i]);
		}
  });
}

rsfd.data.getRealData = function (p, callbackFunc) {
  if (typeof callbackFunc !== 'function')
    return false;
  
  $.getJSON('get_plot_data.php', p, callbackFunc);
  
  return true;
};

rsfd.data.getSimulatedData = function (p, simfile, callbackFunc) {
  if (typeof callbackFunc !== 'function')
    return false;
    
  p2 = {};
  
  for (var prop in p) {
    p2[prop] = p[prop];
  }
  
  if (typeof simfile === "string" && simfile !== '')
    p2.simLocation = simfile;
    
  $.getJSON('get_simulation_data.php', p2, callbackFunc);
  
  return true;
}

rsfd.chart.Chart = function (container, title, location, id) {
  if (typeof id !== 'string') {
    id = container + '_chart';
  }
  
  this.container = container;
  this.title = title;
  this.location = location;
  this.id = id;
  this.loading = 0;
  
  this.chart = new Highcharts.Chart({
    chart: {
      zoomType: 'x',
      renderTo: container
    },
    title: {
      text: this.title
    },
    location: {
      text: this.location
    },
    plotOptions: {
      series: {
        marker: {
          enabled: false,
          states: {
            hover: {
              enabled: true
            }
          }
        }
      }
    },
    tooltip: {
      formatter: function () {
        return Highcharts.dateFormat("%b %d, %Y", this.x) + ": " + this.y;
      }
    },
    xAxis: {
      type: 'datetime',
      dateTimeLabelFormats: {

      }
    },
    series: []
  });
  
  this.series = {};
  this.prompt_count = 0;
  this.prompts = [];
  
  var con = $('#' + this.container);
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

rsfd.chart.Chart.prototype.displayData = function (data) {
  for (var type in data.series) {
    if (type in this.series) {
      this.series[type].remove();
    }
    
    this.series[type] = this.chart.addSeries({
      type: 'spline',
      name: type,
      data: data.series[type],
      pointStart: Date(0)
    });
    
  }
}

rsfd.chart.Chart.prototype.showPrompt = function (content) {
  if (typeof content === "undefined" || content === "")
    return;
  
  var p_id = ++this.prompt_count;
  this.prompts[p_id] = 
    $('<li></li>').text(content)
  this.prompt_list.append(this.prompts[p_id]);
  this.prompt_window.slideDown();
  return p_id;
}

rsfd.chart.Chart.prototype.hidePrompt = function (id) {
  if (typeof this.prompts[id] === 'undefined')
    return;
    
  this.prompts[id].slideUp(function() {
    $(this).remove();
    delete this;
  });
  this.prompt_count--;

  if (this.prompt_count === 0)
    this.prompt_window.hide();
}

rsfd.chart.Chart.prototype.shiftValues = function (seriesName, amount) { 
  if(seriesName === "simulated")
  {
	for(sNames in this.series)
	{
		if(sNames === "Observed Data") continue;
		var data = this.series[sNames].data;
		for (var point in data) {
			data[point].update(data[point].y -= amount, false, false);
		}
		//this.redraw();
	}
  }
  else if (typeof this.series[seriesName] === "undefined")
    return;
  else {
	  var data = this.series[seriesName].data;
	  for (var point in data) {
		data[point].update(data[point].y += amount, false, false);
	  }
	  this.redraw();
  }
}

rsfd.controller.Controller = function () {
  this.charts = {};
}

rsfd.controller.Controller.prototype.registerChart = function (type, chart) {
  this.charts[type] = chart;
}

rsfd.controller.Controller.prototype.shiftValues = function (chartName, seriesName, amount) {
  if (typeof this.charts[chartName] !== "undefined")
  {
	this.charts[chartName].shiftValues(seriesName, amount)
	this.charts[chartName].chart.redraw();
  }
}

rsfd.controller.Controller.prototype.showObservedData = function (chart, parameters) {
  var p_id = chart.showPrompt('Loading Observed Data');
  rsfd.data.getRealData(parameters, function (c, p_id) {
    return function (data) {
      c.displayData(data);
      c.hidePrompt(p_id);
    }
  } (chart, p_id));
}

rsfd.controller.Controller.prototype.showSimulatedData = function (chart, parameters, simfile) {
  var p_id = chart.showPrompt('Loading Simulated Data: ' + simfile);
  rsfd.data.getSimulatedData(parameters, simfile, function (c, p_id) {
    return function (data) {
      c.displayData(data);
      c.hidePrompt(p_id);
    }
  } (chart, p_id));
}

rsfd.controller.Controller.prototype.showData = function() {
  var p = rsfd.ui.getAllParameter();
  var chart, prompt_id;
  var simulatedNames = rsfd.ui.getSimulatedFileNames();
  for (var chart_type in this.charts) {
    chart = this.charts[chart_type];
    p.chartType = chart_type;
    
    this.showObservedData(chart, p);
    
	for (var i = 0; i < simulatedNames.length; i++)
	{
		this.showSimulatedData(chart, p, simulatedNames[i]);
	}
  }
}

$(document).ready(function () {
  //rsfd.ui.showSelectPrompt();
  var elevation_chart = new rsfd.chart.Chart("elevation", rsfd.ui.getLocation() + " Gage Height", rsfd.ui.getLocation());
  var discharge_chart = new rsfd.chart.Chart("discharge", rsfd.ui.getLocation() + " Discharge", rsfd.ui.getLocation());
  controller = new rsfd.controller.Controller();
  controller.registerChart("elevation", elevation_chart);
  controller.registerChart("discharge", discharge_chart);
  rsfd.ui.setFileNames();
  controller.showData();
  $('#refresh-button').click(function () {
    controller.showData();
  });
  $("#elevation_shift_control_button").click(function () {
    controller.shiftValues('elevation', 'simulated', parseInt($('#elevation_shift_control').val()));
  });
  $("#add_simulated_file_button").click(function () {
    rsfd.ui.addSimulatedFileInput();
  });  
  $("#add_sim_data_to_chart_button").click(function () {
    controller.showData();
  });  
})
