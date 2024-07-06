<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{asset('plugins/fontawesome/css/all.min.css')}}">
    <link rel="shortcut icon" href="{{asset('favicon.jpg')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/common.css')}}">
    <title>@yield('title') | {{env('APP_NAME')}}</title>
    @yield('header-css')
    @yield('header-script')
</head>
<style>
    body {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>
<body class="sidebar-mini layout-fixed">
    @include('includes.preloader')
    @include('vendor.layouts.navbar')
    @include('vendor.layouts.sidebar')

    <div class="wrapper">
        @section('main')
        @show
        @include('includes.footer')
    </div>

    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('adminlte/js/adminlte.js')}}"></script>
    <script src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('js/common.js')}}"></script>
    @php
    if(session()->has('status')){
    $type = session('status');
    $alert_type = $type['alert_type'];
    $msg = $type['message'];
    echo "<script>
        toastr['$alert_type'](`$msg`);
    </script>";
    }
    @endphp
    @yield('footer-script')
    <script>
        function initialize_datatable() {
            document.getElementById("clientTable").DataTable({
                pageLength: 10,
                language: {
                    "search": "_INPUT_", // Removes the 'Search' field label
                    "searchPlaceholder": "Type here to search..", // Placeholder for the search box
                    processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
                },
            });
        }

        function common_ajax(request_url, method, body = null) {
            return fetch(request_url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{csrf_token()}}",
                },
                body: body
            })
        }

        function handle_get_visit_forward_info(visit_id){
            fetch(`{{route('team.visit.getForwardInfo')}}/${visit_id}`).then(response => response.json()).then(data => {
            const visit_forward_info_table_body = document.getElementById('visit_forward_info_table_body');
            const modal = new bootstrap.Modal("#visitForwardedMemberInfo")
            visit_forward_info_table_body.innerHTML = "";
            if (data.success == true) {
                let i = 1;
                for (let item of data.visit_forwards) {
                    let tr = document.createElement('tr');
                    let tds = `<td>${i}</td>
                    <td>${item.name}</td>
                    <td>${item.venue_name}</td>`;

                    tr.innerHTML = tds;
                    visit_forward_info_table_body.appendChild(tr);
                    i++;
                }
                modal.show();
            } else {
                toastr[data.alert_type](data.message);
            }
        })
        }
    </script>
</body>

</html>
