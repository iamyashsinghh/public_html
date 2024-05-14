@extends('seomanager.layouts.app')
@section('title', 'Dashboard | SEO Manager')
@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/charts/chart.css') }}">
@endsection
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card text-xs">
                            <div class="card-header border-0 text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">
                                    <i class="fas fa-th mr-1"></i>
                                    Venue Leads of {{ date('F') }} Month
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas class="chart" id="venue_chart_months"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card text-xs">
                            <div class="card-header border-0 text-light" style="background-color: var(--wb-renosand);">
                                <h3 class="card-title">
                                    <i class="fas fa-th mr-1"></i>
                                    Venue Leads of Year {{ date('Y', strtotime('-1 Year')) }} - {{ date('Y') }}
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs text-light" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas class="chart" id="venue_chart_years"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@section('footer-script')
    <script src="{{ asset('plugins/charts/chart.bundle.min.js') }}"></script>
    <script>
        const get_last_day_of_the_month = Number("{{ date('t') }}");
        const current_month_days_arr = [];
        for (let i = 1; i <= get_last_day_of_the_month; i++) {
            current_month_days_arr.push(`${i}-{{ date('M') }}`)
        }
        //VanuesChart
        new Chart("venue_chart_months", {
            type: "line",
            data: {
                labels: current_month_days_arr,
                datasets: [{
                        label: 'Total Leads',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#891010",
                        borderColor: "#891010",
                        data: ("{{ $venue_leads_for_this_month }}").split(",")
                    },
                    {
                        label: 'Call',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#a06b14",
                        borderColor: "#a06b14",
                        data: ("{{ $venue_call_leads_for_this_month }}").split(",")
                    },
                    {
                        label: 'Form ',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#aa559f",
                        borderColor: "#aa559f",
                        data: ("{{ $venue_form_leads_for_this_month }}").split(",")
                    },
                    {
                        label: 'Whatsapp',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#618200",
                        borderColor: "#618200",
                        data: ("{{ $venue_whatsapp_leads_for_this_month }}").split(",")
                    },
                    {
                        label: 'Ad Data',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#4497bb",
                        borderColor: "#4497bb",
                        data: ("{{ $venue_ads_leads_for_this_month }}").split(",")
                    },
                    {
                        label: 'Organic',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#cbe21d",
                        borderColor: "#cbe21d",
                        data: ("{{ $venue_organic_leads_for_this_month }}").split(",")
                    }
                ]
            },
            options: {
                legend: {
                    display: true
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 1
                        }
                    }],
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var label = data.datasets[tooltipItem.datasetIndex].label || '';
                            var value = tooltipItem.yLabel;
                            return label + ': ' + value;
                        }
                    }
                }
            }
        });

        new Chart("venue_chart_years", {
            type: "bar",
            data: {
                labels: ("{{ $yearly_calendar }}").split(','),
                datasets: [{
                        label: 'Total Leads',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#891010",
                        borderColor: "#891010",
                        data: ("{{ $venue_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Call',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#a06b14",
                        borderColor: "#a06b14",
                        data: ("{{ $venue_call_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Form ',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#aa559f",
                        borderColor: "#aa559f",
                        data: ("{{ $venue_form_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Whatsapp',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#618200",
                        borderColor: "#618200",
                        data: ("{{ $venue_whatsapp_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Ad Data',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#4497bb",
                        borderColor: "#4497bb",
                        data: ("{{ $venue_ads_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Organic',
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#cbe21d",
                        borderColor: "#cbe21d",
                        data: ("{{ $venue_organic_leads_for_this_year }}").split(",")
                    }
                ]
            },
            options: {
                legend: {
                    display: true
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 1
                        }
                    }],
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var label = data.datasets[tooltipItem.datasetIndex].label || '';
                            var value = tooltipItem.yLabel;
                            return label + ': ' + value;
                        }
                    }
                }
            }
        });
    </script>
@endsection

@endsection
