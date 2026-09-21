<?php
$this->title = '';
//$this->params['breadcrumbs'] = [['label' => $this->title]];
use app\models\doc_head;
use app\models\payments;
use app\models\User;
use yii\helpers\Url;

try {
    $cf = Yii::$app->user->identity->cd_cli;
} catch (Exception $e) {
    $usrid = '';
    //Yii::$app->user->identity->id;
    $ris = (new \yii\db\Query())
        ->select(['cd_cli', 'email', 'username', 'piva'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();

    $cf = '';
    // $ris['cd_cli'];
}
//yii::error($cf);
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>6 Cards with Bootstrap</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<style>
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-title {
        font-weight: bold;
    }

    .card-text {
        color: #555;
    }
</style>

<body>
    <div class="container my-5">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- First Row of Cards -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Esempio 1</h5>
                        <div id="chart01"></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Esempio 2</h5>
                        <div id="chart02"></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Esempio 3</h5>
                        <div id="chart03"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Second Row of Cards -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Esempio 4</h5>
                        <div id="chart04"></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Esempio 5</h5>
                        <div id="chart05"></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Esempio 6</h5>
                        <div id="chart06"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var options1 = {
            series: [{
                name: 'Marine Sprite',
                data: [44, 55, 41, 37, 22, 43, 21]
            }, {
                name: 'Striking Calf',
                data: [53, 32, 33, 52, 13, 43, 32]
            }, {
                name: 'Tank Picture',
                data: [12, 17, 11, 9, 15, 11, 20]
            }, {
                name: 'Bucket Slope',
                data: [9, 7, 5, 8, 6, 9, 4]
            }, {
                name: 'Reborn Kid',
                data: [25, 12, 19, 32, 25, 24, 10]
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                stackType: '100%'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                },
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            title: {
                text: '100% Stacked Bar'
            },
            xaxis: {
                categories: [2008, 2009, 2010, 2011, 2012, 2013, 2014],
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + "K"
                    }
                }
            },
            fill: {
                opacity: 1

            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart01"), options1);
        chart.render();
    </script>

    <script>
        var options2 = {
            series: [{
                data: [{
                        x: 'New Delhi',
                        y: 218
                    },
                    {
                        x: 'Kolkata',
                        y: 149
                    },
                    {
                        x: 'Mumbai',
                        y: 184
                    },
                    {
                        x: 'Ahmedabad',
                        y: 55
                    },
                    {
                        x: 'Bangaluru',
                        y: 84
                    },
                    {
                        x: 'Pune',
                        y: 31
                    },
                    {
                        x: 'Chennai',
                        y: 70
                    },
                    {
                        x: 'Jaipur',
                        y: 30
                    },
                    {
                        x: 'Surat',
                        y: 44
                    },
                    {
                        x: 'Hyderabad',
                        y: 68
                    },
                    {
                        x: 'Lucknow',
                        y: 28
                    },
                    {
                        x: 'Indore',
                        y: 19
                    },
                    {
                        x: 'Kanpur',
                        y: 29
                    }
                ]
            }],
            legend: {
                show: false
            },
            chart: {
                height: 350,
                type: 'treemap'
            },
            title: {
                text: 'Basic Treemap'
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart02"), options2);
        chart.render();
    </script>

    <SCRIPT>
        var options3 = {
            series: [{
                name: 'Net Profit',
                data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
            }, {
                name: 'Revenue',
                data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
            }, {
                name: 'Free Cash Flow',
                data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            },
            yaxis: {
                title: {
                    text: '$ (thousands)'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "$ " + val + " thousands"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart03"), options3);
        chart.render();
    </SCRIPT>
    <SCRIPT>
        var options4 = {
            series: [{
                name: 'TEAM A',
                type: 'column',
                data: [23, 11, 22, 27, 13, 22, 37, 21, 44, 22, 30]
            }, {
                name: 'TEAM B',
                type: 'area',
                data: [44, 55, 41, 67, 22, 43, 21, 41, 56, 27, 43]
            }, {
                name: 'TEAM C',
                type: 'line',
                data: [30, 25, 36, 30, 45, 35, 64, 52, 59, 36, 39]
            }],
            chart: {
                height: 350,
                type: 'line',
                stacked: false,
            },
            stroke: {
                width: [0, 2, 5],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%'
                }
            },

            fill: {
                opacity: [0.85, 0.25, 1],
                gradient: {
                    inverseColors: false,
                    shade: 'light',
                    type: "vertical",
                    opacityFrom: 0.85,
                    opacityTo: 0.55,
                    stops: [0, 100, 100, 100]
                }
            },
            labels: ['01/01/2003', '02/01/2003', '03/01/2003', '04/01/2003', '05/01/2003', '06/01/2003', '07/01/2003',
                '08/01/2003', '09/01/2003', '10/01/2003', '11/01/2003'
            ],
            markers: {
                size: 0
            },
            xaxis: {
                type: 'datetime'
            },
            yaxis: {
                title: {
                    text: 'Points',
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(y) {
                        if (typeof y !== "undefined") {
                            return y.toFixed(0) + " points";
                        }
                        return y;

                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart04"), options4);
        chart.render();


        var options5 = {
            series: [44, 55, 13, 43, 22],
            chart: {
                width: 380,
                type: 'pie',
            },
            labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
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

        var chart = new ApexCharts(document.querySelector("#chart05"), options5);
        chart.render();




        var options6 = {
            series: [76],
            chart: {
                type: 'radialBar',
                offsetY: -20,
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    track: {
                        background: "#e7e7e7",
                        strokeWidth: '97%',
                        margin: 5, // margin is in pixels
                        dropShadow: {
                            enabled: true,
                            top: 2,
                            left: 0,
                            color: '#444',
                            opacity: 1,
                            blur: 2
                        }
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            offsetY: -2,
                            fontSize: '22px'
                        }
                    }
                }
            },
            grid: {
                padding: {
                    top: -10
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    shadeIntensity: 0.4,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 53, 91]
                },
            },
            labels: ['Average Results'],
        };

        var chart = new ApexCharts(document.querySelector("#chart06"), options6);
        chart.render();
    </SCRIPT>


</body>

</html>