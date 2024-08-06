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
    @include('manager.layouts.navbar')
    @include('manager.layouts.sidebar')

    <div class="wrapper">
        @section('main')
        @show
        @include('includes.footer')
    </div>
    <script>
        document.addEventListener('keydown', function(event) {
            if ((event.ctrlKey || event.metaKey) && (event.key === 'p' || event.key === 's')) {
                event.preventDefault();
                console.log(`Default action for Ctrl+${event.key.toUpperCase()} has been disabled.`);
            }
        });
    </script>
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('adminlte/js/adminlte.js')}}"></script>
    <script src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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
        function initialize_datatable(){
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

        function handle_get_forward_info(lead_id){
            fetch(`{{route('manager.lead.getForwardInfo')}}/${lead_id}`).then(response => response.json()).then(data => {
                const forward_info_table_body = document.getElementById('forward_info_table_body');
                const last_forwarded_info_paragraph = document.getElementById('last_forwarded_info_paragraph');
                const modal = new bootstrap.Modal("#leadForwardedMemberInfo")
                forward_info_table_body.innerHTML = "";
                last_forwarded_info_paragraph.innerHTML = "";
                if(data.success == true){
                    let i = 1;
                    for(let item of data.lead_forwards){
                        let tr = document.createElement('tr');
                        let tds = `<td>${i}</td>
                        <td>${item.name}</td>
                        <td>${item.venue_name}</td>
                        <td>
                            <span class="badge badge-${item.read_status == 0 ? 'danger' : 'success'}">${item.read_status == 0 ? 'Unread': 'Read'}</span>
                        </td>
                        <td>${moment(item.lead_datetime).format('DD-MMM-YYY hh:mm a')}</td>`;

                        tr.innerHTML = tds;
                        forward_info_table_body.appendChild(tr);
                        i++;
                    }
                    last_forwarded_info_paragraph.innerHTML = data.last_forwarded_info;
                    modal.show();
                }else{
                    toastr[data.$alert_type](`${data.message}`);
                }
            })
        }
    </script>
</body>

</html>
