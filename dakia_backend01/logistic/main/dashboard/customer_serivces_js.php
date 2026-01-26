<script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
<script src="../assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
<script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
<script src="../assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
<!--<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/pie.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>-->
<script src="https://www.amcharts.com/lib/3/plugins/dataloader/dataloader.min.js"></script>
<?php

?>
<script type="text/javascript">
    $(document).ready(function (e) {
        $('.today_delivery').trigger('click');
        $('.today_worse').trigger('click');
        $('.today_top').trigger('click');
    });
    var chartTopCountries = AmCharts.makeChart("top_countries_serial", {
        "type": "serial",
        theme: "light",
        "dataLoader": {
          "url": "customer_service_ajax.php?top=top-countires"
        },
        "valueAxes": [{
          "gridColor": "#888888",
          "gridAlpha": 0.2,
          "dashLength": 0
        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
          "balloonText": "[[category]]: <b>[[value]]</b>",
          "fillAlphas": 0.8,
          "lineAlpha": 0.2,
          "type": "column",
          "valueField": "value"
        }],
        "categoryAxis": {
            "gridPosition": "start",
            "labelRotation": 45
        },
        "chartCursor": {
          "categoryBalloonEnabled": false,
          "cursorAlpha": 0,
          "zoomable": false
        },
        "categoryField": "country",
        "categoryAxis": {
          "gridPosition": "start",
          "gridAlpha": 0,
          "tickPosition": "start",
          "tickLength": 20,
          "labelRotation": 45
        }
    });

    
    
    
    
    var chartWorseGood = AmCharts.makeChart("", {
        "type": "serial",
        theme: "light",
        "dataLoader": {
          "url": "customer_service_ajax.php?serialchart=serial-worse&param=today"
        },
        "valueAxes": [{
          "gridColor": "#888888",
          "gridAlpha": 0.2,
          "dashLength": 0
        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
          "balloonText": "[[category]]: <b>[[value_label]]</b>",
          "fillAlphas": 0.8,
          "lineAlpha": 0.2,
          "type": "column",
          "valueField": "value"
        }],
        "categoryAxis": {
            "gridPosition": "start",
            "labelRotation": 45
        },
        "chartCursor": {
          "categoryBalloonEnabled": false,
          "cursorAlpha": 0,
          "zoomable": false
        },
        "categoryField": "service",
        "categoryAxis": {
          "gridPosition": "start",
          "gridAlpha": 0,
          "tickPosition": "start",
          "tickLength": 20,
          "labelRotation": 45
        }
    });

    function setTopWorseServiceDataSet(dataset_urls,contains) {
      AmCharts.loadFile(dataset_urls, {}, function(data) {
        chartWorseGood.dataProvider = AmCharts.parseJSON(data);
        chartWorseGood.write(contains);
        chartWorseGood.validateData();
      });
    }
    
    var chartTopGood = AmCharts.makeChart("", {
        "type": "serial",
        theme: "light",
        "dataLoader": {
          "url": "customer_service_ajax.php?serialchart=serial&param=today"
        },
        "valueAxes": [{
          "gridColor": "#888888",
          "gridAlpha": 0.2,
          "dashLength": 0
        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
          "balloonText": "[[category]]: <b>[[value_label]]</b>",
          "fillAlphas": 0.8,
          "lineAlpha": 0.2,
          "type": "column",
          "valueField": "value"
        }],
        "categoryAxis": {
            "gridPosition": "start",
            "labelRotation": 45
        },
        "chartCursor": {
          "categoryBalloonEnabled": false,
          "cursorAlpha": 0,
          "zoomable": false
        },
        "categoryField": "service",
        "categoryAxis": {
          "gridPosition": "start",
          "gridAlpha": 0,
          "tickPosition": "start",
          "tickLength": 20,
          "labelRotation": 45
        }
    });

    function setTopGoodServiceDataSet(dataset_urls,contains) {
      AmCharts.loadFile(dataset_urls, {}, function(data) {
        chartTopGood.dataProvider = AmCharts.parseJSON(data);
        chartTopGood.write(contains);
        chartTopGood.validateData();
      });
    }

    var chart = AmCharts.makeChart( '',{
            type: "pie",
            theme: "light",
            fontFamily: "Open Sans",
            color: "#888",
            "labelRadius": -35,
            "labelText": "[[percents]]%",
            "depth3D": 30,
            "dataProvider": {
              "url": "customer_service_ajax.php?piechart=pie&param=today",
              "format": "json"
            },
            valueField: "value",
            titleField: "caption",
            "angle": "30",
            "radius": '200',
            "balloonText": "[[caption]]: [[value]]",
            "legend": {
                "position": "bottom",
                "marginTop": 1,
                "autoMargins": true
            },
            exportConfig: {
                menuItems: [{
                        icon: App.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
                        format: "png"
                    }]
            }
        });
        
    function setDataSet(dataset_url,contain) {
        AmCharts.loadFile(dataset_url, {}, function(data) {
            chart.dataProvider = AmCharts.parseJSON(data);
            chart.write(contain);
            chart.validateData();
        });
    }
    //}
</script>