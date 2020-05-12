var c3ChartDefaults = $().c3ChartDefaults();

  var donutData = {
    type : 'donut',
    columns: [
      ['Dogs', 2],
      ['Cats', 2],
      ['Fish', 3],
      ['Hamsters', 1]
    ],
    onclick: function (d, i) { console.log("onclick", d, i); },
    onmouseover: function (d, i) { console.log("onmouseover", d, i); },
    onmouseout: function (d, i) { console.log("onmouseout", d, i); }
  };

  // Small Donut Chart
  var donutChartSmallConfig = c3ChartDefaults.getDefaultRelationshipDonutConfig('8');
  donutChartSmallConfig.bindto = '#donut-chart-7';
  donutChartSmallConfig.tooltip = {show: true};
  donutChartSmallConfig.data = donutData;
  donutChartSmallConfig.legend = {
    show: true,
    position: 'right'
  };
  donutChartSmallConfig.size = {
    width: 250,
    height: 115
  };
  donutChartSmallConfig.tooltip = {
    contents: $().pfDonutTooltipContents
  };

  var donutChartSmall = c3.generate(donutChartSmallConfig);
  //
  var options2 = {
    series: [44, 55, 41, 17, 15],
    chart: {
    type: 'donut',
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }]
  };
  var chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
  chart2.render();
  //
  var options3 = {
    series: [44, 55, 41, 17, 15],
    chart: {
    type: 'donut',
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }]
  };
  var chart3 = new ApexCharts(document.querySelector("#chart3"), options3);
  chart3.render();
  //
  var options4 = {
    series: [44, 55, 41, 17, 15],
    chart: {
    type: 'donut',
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }]
  };
  var chart4 = new ApexCharts(document.querySelector("#chart4"), options4);
  chart4.render();
  //
  var options5 = {
    series: [44, 55, 41, 17, 15],
    chart: {
    type: 'donut',
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }]
  };
  var chart5 = new ApexCharts(document.querySelector("#chart5"), options5);
  chart5.render();
  //
  var options6 = {
    series: [44, 55, 41, 17, 15],
    chart: {
    type: 'donut',
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }]
  };
  var chart6 = new ApexCharts(document.querySelector("#chart6"), options6);
  chart6.render();