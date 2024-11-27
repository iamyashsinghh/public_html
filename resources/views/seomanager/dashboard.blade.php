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
                                    Venue Leads || Average: <span id="avarageLeadId">{{ round($average_leads_for_month, 2)}}</span>
                                </h3>
                                <div class="card-tools">
                                    <select id="month-selector" class="form-control form-control-sm"
                                        style="width: auto; display: inline; font-weight: 600;">
                                        <option value="Current Month" style="font-weight: 600;">
                                            {{ date('F Y') }}
                                        </option>
                                        @foreach (range(1, 12) as $i)
                                            <option value="{{ now()->subMonthsNoOverflow($i)->format('F Y') }}"
                                                {{ $i === 0 ? 'selected' : '' }} style="font-weight: 600;">
                                                {{ now()->subMonthsNoOverflow($i)->format('F Y') }}
                                            </option>
                                        @endforeach
                                    </select>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        tension: 0, // Use `tension` instead of `lineTension` in Chart.js v3+
                        backgroundColor: "#891010",
                        borderColor: "#891010",
                        data: "{{ $venue_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'Call',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#a06b14",
                        borderColor: "#a06b14",
                        data: "{{ $venue_call_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'Form',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#aa559f",
                        borderColor: "#aa559f",
                        data: "{{ $venue_form_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'WhatsApp',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#618200",
                        borderColor: "#618200",
                        data: "{{ $venue_whatsapp_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'Ad Data',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#4497bb",
                        borderColor: "#4497bb",
                        data: "{{ $venue_ads_leads_for_this_month }}".split(","),
                    },
                    {
                        label: 'Organic',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#cbe21d",
                        borderColor: "#cbe21d",
                        data: "{{ $venue_organic_leads_for_this_month }}".split(","),
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                    },
                    tooltip: {
                        mode: "index",
                        intersect: false,
                        callbacks: {
                            label: function(tooltipItem) {
                                const label = tooltipItem.dataset.label || "";
                                const value = tooltipItem.raw;
                                return `${label}: ${value}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        type: "category",
                        title: {
                            display: false,
                            text: "Days",
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: false,
                            text: "Leads Count",
                        },
                        ticks: {
                            min: 1, // Minimum value on the y-axis
                        },
                    },
                },
            },
        });

        new Chart("venue_chart_years", {
            type: "bar",
            data: {
                labels: ("{{ $yearly_calendar }}").split(','),
                datasets: [{
                        label: 'Total Leads',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#891010",
                        borderColor: "#891010",
                        data: ("{{ $venue_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Call',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#a06b14",
                        borderColor: "#a06b14",
                        data: ("{{ $venue_call_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Form ',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#aa559f",
                        borderColor: "#aa559f",
                        data: ("{{ $venue_form_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Whatsapp',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#618200",
                        borderColor: "#618200",
                        data: ("{{ $venue_whatsapp_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Ad Data',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#4497bb",
                        borderColor: "#4497bb",
                        data: ("{{ $venue_ads_leads_for_this_year }}").split(",")
                    },
                    {
                        label: 'Organic',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#cbe21d",
                        borderColor: "#cbe21d",
                        data: ("{{ $venue_organic_leads_for_this_year }}").split(",")
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                    },
                    tooltip: {
                        mode: "index",
                        intersect: false,
                        callbacks: {
                            label: function(tooltipItem) {
                                const label = tooltipItem.dataset.label || "";
                                const value = tooltipItem.raw;
                                return `${label}: ${value}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        type: "category",
                        title: {
                            display: false,
                            text: "Years",
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: false,
                            text: "Leads Count",
                        },
                        ticks: {
                            min: 1, // Minimum value on the y-axis
                        },
                    },
                },
            },
        });

        document.getElementById('month-selector').addEventListener('change', function() {
            const selectedMonth = this.value.split(' ')[0];
            const selectedYear = this.value.split(' ')[1];
            if (selectedMonth == 'Current' && selectedYear == 'Month') {
                location.reload();
                return;
            }
            fetch(`{{ route('seomanager.dashboard.data') }}?month=${selectedMonth}&year=${selectedYear}`)
                .then(response => response.json())
                .then(data => {
                    const chart = Chart.getChart("venue_chart_months");
                    chart.data.datasets[0].data = data.venue_leads.split(",");
                    chart.data.datasets[1].data = data.call_leads.split(",");
                    chart.data.datasets[2].data = data.form_leads.split(",");
                    chart.data.datasets[3].data = data.whatsapp_leads.split(",");
                    chart.data.datasets[4].data = data.ads_leads.split(",");
                    chart.data.datasets[5].data = data.organic_leads.split(",");

                    const venueLeads = data.venue_leads.split(",").map(Number);
                    const count = venueLeads.length;
                    const sum = venueLeads.reduce((acc, val) => acc + val, 0);
                    const average = parseFloat((sum / count).toFixed(2));
                    document.getElementById('avarageLeadId').innerText = `${average}`;
                    chart.update();
                })
                .catch(err => console.error('Error fetching chart data:', err));
        });
    </script>
@endsection

@endsection
