<script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
<script src="../assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
<script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
<script src="../assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
<script src="https://www.amcharts.com/lib/3/plugins/dataloader/dataloader.min.js"></script>
<?php

?>
<script type="text/javascript">
    $(document).ready(function (e) {
        $('.today_top').trigger('click');
    });
    var chartTopCountries = AmCharts.makeChart("top_countries_serial", {
        "type": "serial",
        theme: "light",
        "dataLoader": {
          "url": "account_ajax.php?top=top-countires"
        },
        "valueAxes": [{
          "gridColor": "#888888",
          "gridAlpha": 0.2,
          "dashLength": 0
        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
          "balloonText": "[[category]]: <b>[[valueWithCurrnecy]]</b>",
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
    //
    var chartTopShipment = AmCharts.makeChart("top_shipment_serial", {
        "type": "serial",
        theme: "light",
        "dataLoader": {
          "url": "account_ajax.php?top=top-shipment"
        },
        "valueAxes": [{
          "gridColor": "#888888",
          "gridAlpha": 0.2,
          "dashLength": 0
        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
          "balloonText": "[[category]]: <b>[[valueWithCurrnecy]]</b>",
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

</script>