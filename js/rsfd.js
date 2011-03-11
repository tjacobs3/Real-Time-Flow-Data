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

rsfd.data.getAnnotation = function (p, seriesName, callbackFunc) {
  if (typeof callbackFunc !== 'function')
    return false;

  p2 = {};
  for (var prop in p) {
    p2[prop] = p[prop];
  }
  if (typeof seriesName === "string" && seriesName !== '')
    p2.seriesName = seriesName;
    
  $.getJSON('get_annotation.php', p2, callbackFunc);
}

rsfd.Chart = function (container, title, location, chartType, id) {
  if (typeof id !== 'string') {
    id = container + '_chart';
  }
  
  this.container = container;
  this.title = title;
  this.location = location;
  this.id = id;
  this.type = chartType;
  
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

rsfd.Chart.prototype.displayData = function (data) {
  for (var type in data.series) {
    if (type in this.series) {
      this.series[type].remove();
    }
    
    if (data.series[type].length > 0) {
      this.series[type] = this.chart.addSeries({
        type: 'spline',
        name: type,
        data: data.series[type],
        pointStart: Date(0)
      });
    }
  }
}

rsfd.Chart.prototype.displayAnnotation = function (data) {
  var i, anno;
  for (i in data) {
    anno = new rsfd.Annotation(this.annotations.length+1, data[i].location, data[i].seriesName,
                                parseInt(data[i].timestamp), data[i].content);
    this.addAnnotation(anno);
  }
}

rsfd.Chart.prototype.showPrompt = function (content) {
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
  if (typeof this.prompts[id] === 'undefined')
    return;
    
  this.prompts[id].slideUp(function() {
    $(this).remove();
    delete this;
    this.prompt_count--;

    if (this.prompt_count === 0)
      this.prompt_window.hide();
    
  });
}

rsfd.Chart.prototype.shiftValues = function (seriesName, amount) {
  if (typeof this.series[seriesName] === "undefined")
    return;
  
  var data = this.series[seriesName].data;
  
  for (var point in data) {
    data[point].update(data[point].y += amount, false, false);
  }
  
  this.redraw();
}

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

rsfd.Chart.prototype.addAnnotationToChart = function (annotation) {
  var posX, posY;
  var chart = this.chart;
  var data = this.series[annotation.seriesName].data;
  
  if (annotation.timestamp < data[0].x || annotation.timestamp > data[data.length-1].x)
    return;
    
  var element = this.getElementByX(annotation.seriesName, annotation.timestamp);
  var y = element.plotY;

  
  posX = chart.plotLeft + chart.xAxis[0].translate(annotation.timestamp) - 10;
  posY = chart.plotTop + y - 40;
  
  if (posX < chart.plotLeft || posX > chart.plotLeft + chart.plotWidth) {
    return;
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
  
  annotation.inChart = group;
}

rsfd.Chart.prototype.addAnnotationToList = function (annotation) {
  annotation.inList = $("<li></li>");
  $("<div></div>")
    .attr('class', 'annotation_id')
    .text(annotation.id)
    .appendTo(annotation.inList);
  $("<div></div>")
    .attr('class', 'annotation_timestamp')
    .text(Date(annotation.timestamp))
    .appendTo(annotation.inList);
  $("<div></div>")
    .attr('class', 'annotation_content')
    .text(annotation.content)
    .appendTo(annotation.inList);
  this.annotation_list.append(annotation.inList);
}

rsfd.Chart.prototype.addAnnotation = function (annotation) {
  this.annotations.push(annotation);
  this.addAnnotationToChart(annotation);
  this.addAnnotationToList(annotation);
}

rsfd.Chart.prototype.removeAnnotation = function (annotation) {
  annotation.remove();
}

rsfd.Chart.prototype.clearAnnotation = function (annotation) {
  for (var i in this.annotations) {
    this.removeAnnotation(this.annotations[i]);
  }
}

rsfd.Annotation = function (id, location, seriesName, timestamp, content) {
  this.id = id;
  this.location = location;
  this.seriesName = seriesName;
  this.timestamp = timestamp;
  this.content = content;
};

rsfd.Annotation.prototype.remove = function () {
  $(this.onChart).remove();
  $(this.onList).remove();
  delete this.onChart;
  delete this.onList;
}

rsfd.Controller = function () {
  this.charts = {};
}

rsfd.Controller.prototype.registerChart = function (type, chart) {
  this.charts[type] = chart;
}

rsfd.Controller.prototype.shiftValues = function (chartName, seriesName, amount) {
 return (typeof this.charts[chartName] !== "undefined") && this.charts[chartName].shiftValues();
}

rsfd.Controller.prototype.showObservedData = function (chart, parameters) {
  parameters.chartType = chart.type;  
  var p_id = chart.showPrompt('Loading Observed Data');
  rsfd.data.getRealData(parameters, function (c, p_id, p, that) {
    return function (data) {
      c.displayData(data);
      that.showAnnotation(c, p, "Observed Data");
      c.hidePrompt(p_id);
    }
  } (chart, p_id, parameters, this));
}

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

rsfd.Controller.prototype.showData = function() {
  var p = rsfd.ui.getAllParameter();
  var chart, prompt_id;
  
  for (var chart_type in this.charts) {
    chart = this.charts[chart_type];
        
    this.showObservedData(chart, p);    
    this.showSimulatedData(chart, p, '');
    this.showSimulatedData(chart, p, 'ld2g1.2011.wsq');
  }
}

$(document).ready(function () {
  //rsfd.ui.showSelectPrompt();
  var elevation_chart = new rsfd.Chart("elevation", rsfd.ui.getLocation() + " Gage Height", rsfd.ui.getLocation(), "elevation");
  var discharge_chart = new rsfd.Chart("discharge", rsfd.ui.getLocation() + " Discharge", rsfd.ui.getLocation(), "discharge");
  controller = new rsfd.Controller();
  controller.registerChart("elevation", elevation_chart);
  controller.registerChart("discharge", discharge_chart);
  controller.showData();
  $('#refresh-button').click(function () {
    controller.showData();
  });
  $("#elevation_shift_control_button").click(function () {
    controller.shiftValues('elevation', 'Observed Data', parseInt($('#elevation_shift_control').val()));
  });
})
