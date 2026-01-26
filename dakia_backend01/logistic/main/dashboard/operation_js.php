<!--<script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>-->
<!--<script src="../assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
<script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>-->
<!--<script src="../assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>-->

<script type="text/javascript">
    $(document).ready(function (e) {
        serviceScannedReport("chart_1",[<?php echo $this->today; ?>]);
        serviceScannedReport("chart_2",[<?php echo $this->week; ?>]);
        serviceScannedReport("chart_3",[<?php echo $this->month; ?>]);
        
        userScannedReport("chart_4",[<?php echo $this->userToday; ?>]);
        userScannedReport("chart_5",[<?php echo $this->userWeek; ?>]);
        userScannedReport("chart_6",[<?php echo $this->userMonth; ?>]);
        
        upcommingManifest("upcomming_manifest",[<?php echo $this->graphUpcommingMenifestToday; ?>]);
        upcommingManifest("upcomming_manifest_weekly",[<?php echo $this->graphUpcommingMenifestWeek; ?>]);
        upcommingManifest("upcomming_manifest_monthly",[<?php echo $this->graphUpcommingMenifestMonth; ?>]);
        
        drawManifestScanChart('scanned_manifest',[<?php echo $this->graphDispatchedMenifestToday; ?>]);
        drawManifestScanChart("scanned_manifest_weekly",[<?php echo $this->graphDispatchedMenifestWeek; ?>]);
        drawManifestScanChart("scanned_manifest_monthly",[<?php echo $this->graphDispatchedMenifestMonth; ?>]);
    });
    function drawManifestScanChart(containerId, data){
        AmCharts.makeChart(containerId, {
            "type": "serial",
            "theme": "light",
            "fontFamily": 'Open Sans',
            "color": '#888888',
            "pathToImages": App.getGlobalPluginsPath() + "amcharts/amcharts/images/",
            "dataProvider": data,
            "balloon": {
                "cornerRadius": 6
            },
            "valueAxes": [{
                    "duration": "mm",
                    "durationUnits": {
                        "hh": "",
                        "mm": ""
                    },
                    "axisAlpha": 0
                }],
            "graphs": [{
                    "bullet": "square",
                    "bulletBorderAlpha": 1,
                    "bulletBorderThickness": 1,
                    "fillAlphas": 0.3,
                    "fillColorsField": "lineColor",
                    "legendValueText": "[[value]]",
                    "lineColorField": "lineColor",
                    "title": "duration",
                    "valueField": "duration"
                }],
//            "chartScrollbar": {},
            "chartCursor": {
                "categoryBalloonDateFormat": "YYYY MMM DD",
                "cursorAlpha": 0,
                "zoomable": false
            },
            "dataDateFormat": "YYYY-MM-DD",
            "categoryField": "date",
            "categoryAxis": {
                "dateFormats": [{
                        "period": "DD",
                        "format": "DD"
                    }, {
                        "period": "WW",
                        "format": "MMM DD"
                    }, {
                        "period": "MM",
                        "format": "MMM"
                    }, {
                        "period": "YYYY",
                        "format": "YYYY"
                    }],
                "parseDates": true,
                "autoGridCount": false,
                "axisColor": "#555555",
                "gridAlpha": 0,
                "gridCount": 50
            },
            "allLabels": [{
                    "text": "Dispatch Date",
                    "x": "!20",
                    "y": "!15",
//                    "width": "50%",
//                    "size": 0,
//                    "bold": true,
                    "align": "right"
                }, {
                    "text": "Manifest Count",
                    "rotation": 270,
                    "x": "5",
                    "y": "10",
//                    "width": "50%",
//                    "size": 15,
//                    "bold": true,
                    "align": "right"
                }]
        });
    }
    function upcommingManifest(containerId, data){
        AmCharts.makeChart(containerId, {
            "type": "serial",
            "theme": "light",
            "dataProvider": data,
            "valueAxes": [{
                    "gridColor": "#FFFFFF",
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
                    "valueField": "weight"
                }],
            "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
            },
            "categoryField": "count",
            "categoryAxis": {
                "gridPosition": "start",
                "gridAlpha": 0,
                "tickPosition": "start",
                "tickLength": 20
            },
            "allLabels": [{
                    "text": "Manifest Number",
                    "x": "!20",
                    "y": "!13",
//                    "width": "50%",
//                    "size": 0,
//                    "bold": true,
                    "align": "right"
                }, {
                    "text": "Weight",
                    "rotation": 270,
                    "x": "5",
                    "y": "10",
//                    "width": "50%",
//                    "size": 15,
//                    "bold": true,
                    "align": "right"
                }],
            "export": {
                "enabled": true
            }

        });
    }
    function userScannedReport(containerId, data){
        AmCharts.makeChart(containerId, {
            type: "serial",
            theme: "light",
            pathToImages: App.getGlobalPluginsPath() + "amcharts/amcharts/images/",
            autoMargins: !1,
            marginLeft: 80,
            marginRight: 8,
            marginTop: 10,
            marginBottom: 26,
            fontFamily: "Open Sans",
            color: "#888",
            dataProvider: data,
            valueAxes: [{
                    axisAlpha: 0,
                    position: "left"
                }],
            startDuration: 1,
            "legend": {
                "position": "bottom",
                "marginTop": 1,
                "autoMargins": true
            },
            graphs: [{
                    alphaField: "alpha",
                    balloonText: "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                    dashLengthField: "dashLengthColumn",
                    fillAlphas: 1,
                    title: "USER SCANNED PARCEL ",
                    type: "column",
                    valueField: "scannedTotal"
                }, {
                    balloonText: "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                    bullet: "round",
                    dashLengthField: "dashLengthLine",
                    lineThickness: 3,
                    bulletSize: 7,
                    bulletBorderAlpha: 1,
                    bulletColor: "#FFFFFF",
                    useLineColorForBulletBorder: !0,
                    bulletBorderThickness: 3,
                    fillAlphas: 0,
                    lineAlpha: 1,
                    title: "User",
                    valueField: "user"
                }],
            categoryField: "user",
            categoryAxis: {
                gridPosition: "start",
                axisAlpha: 0,
                tickLength: 0
            }
        });
    }
    function serviceScannedReport(containerId, data){
        AmCharts.makeChart(containerId, {
            type: "pie",
            theme: "light",
            fontFamily: "Open Sans",
            color: "#888",
            "labelRadius": -35,
            "labelText": "[[percents]]%",
            "depth3D": 30,
            dataProvider: data,
            valueField: "scanTotal",
            titleField: "service",
            "angle": "30",
            "radius": '200',
            "balloonText": "[[service]]: [[percents]]% ([[value]])\n[[caption]]",
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
    }
    
</script>