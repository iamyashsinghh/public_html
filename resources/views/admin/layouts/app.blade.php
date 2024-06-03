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

<body class="sidebar-mini layout-fixed">
    @include('includes.preloader')
    @include('admin.layouts.navbar')
    @include('admin.layouts.sidebar')

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
        // global function: this is used for client side datatable non server processing.
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

        // global function: for http request
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
            fetch(`{{route('admin.lead.getForwardInfo')}}/${lead_id}`).then(response => response.json()).then(data => {
                const forward_info_table_body = document.getElementById('forward_info_table_body');
                const last_forwarded_info_paragraph = document.getElementById('last_forwarded_info_paragraph');
                const modal = new bootstrap.Modal("#leadForwardedMemberInfo")
                forward_info_table_body.innerHTML = "";
                last_forwarded_info_paragraph.innerHTML = "";
                if(data.success == true){
                    let i = 1;
                    for(let item of data.lead_forwards){
                       let updated_at = moment(item.updated_at).format("DD-MMM-YYYY hh:mm a")
                        let tr = document.createElement('tr');
                        let tds = `<td>${i}</td>
                        <td>${updated_at}</td>
                        <td>${item.from_name}</td>
                        <td>${item.to_name}</td>
                        <td>${item.venue_name}</td>
                        <td><span class="badge badge-${item.read_status == 0 ? 'danger' : 'success'}">${item.read_status == 0 ? 'Unread': 'Read'}</span></td>`;
                        tr.innerHTML = tds;
                        forward_info_table_body.appendChild(tr);
                        i++;
                    }
                    last_forwarded_info_paragraph.innerHTML = data.last_forwarded_info;
                    modal.show();
                }else{
                    toastr[data.$alert_type](data.message);
                }
                console.log(data.lead_forwards)
            })
        }

        function handle_get_nvlead_forwarded_info(lead_id){
            fetch(`{{route('admin.nvlead.getForwardInfo')}}/${lead_id}`).then(response => response.json()).then(data => {
                const forward_info_table_body = document.getElementById('forward_info_table_body');
                const last_forwarded_info_paragraph = document.getElementById('last_forwarded_info_paragraph');
                const modal = new bootstrap.Modal("#nvLeadForwardedInfoModal")
                forward_info_table_body.innerHTML = "";
                last_forwarded_info_paragraph.innerHTML = "";
                if(data.success == true){
                    let i = 1;
                    for(let item of data.lead_forwards){
                        let updated_at = item.updated_at ? moment(item.updated_at).format("DD-MMM-YYYY hh:mm a") : 'N/A';
                        let tr = document.createElement('tr');
                        let tds = `<td>${i}</td>
                        <td>${updated_at}</td>
                        <td>${item.name}</td>
                        <td>${item.role_name ? item.role_name : 'Vendor'}</td>
                        <td>${item.business_name ? item.business_name : 'N/A'}</td>
                        <td>
                            <span class="badge badge-${item.read_status == 0 ? 'danger' : 'success'}">${item.read_status == 0 ? 'Unread': 'Read'}</span>
                        </td>`;

                        tr.innerHTML = tds;
                        forward_info_table_body.appendChild(tr);
                        i++;
                    }
                    last_forwarded_info_paragraph.innerHTML = data.last_forwarded_info;
                    modal.show();
                }else{
                    toastr[data.alert_type](data.message);
                }
            })
        }

        // function get_member_login_info(login_type, member_id){
        //     fetch(``).then(response => response.json()).then(data => {
        //         const login_info_modal_body = document.getElementById('login_info_modal_body');
        //         if(data.success == true){
        //             const info = data.info;
        //             login_info_modal_body.querySelector('.li_member_name').innerText = info.team_name ? `${info.team_name} (${info.role})`: 'N/A';
        //             login_info_modal_body.querySelector('.li_request_otp_count').innerText = info.request_otp_count ? info.request_otp_count : 'N/A';
        //             login_info_modal_body.querySelector('.li_last_otp_requested_at').innerText = info.request_otp_at ? moment(info.request_otp_at).format("DD-MMM-YYYY hh:mm a") : 'N/A';
        //             login_info_modal_body.querySelector('.li_login_count').innerText = info.login_count ? info.login_count : 'N/A';
        //             login_info_modal_body.querySelector('.li_last_login_at').innerText = info.login_at ? moment(info.login_at).format("DD-MMM-YYYY hh:mm a") : 'N/A';
        //             login_info_modal_body.querySelector('.li_ip_address').innerText = info.ip_address ? info.ip_address : 'N/A';
        //             login_info_modal_body.querySelector('.li_browser').innerText = info.browser ? info.browser : 'N/A';
        //             login_info_modal_body.querySelector('.li_platform').innerText = info.platform ? info.platform : 'N/A';
        //             login_info_modal_body.querySelector('.li_logout').innerText = info.logout_at ? moment(info.logout_at).format("DD-MMM-YYYY hh:mm a") : 'N/A';
        //         }


        //         const modal = new bootstrap.Modal("#memberLoginInfoModal");
        //         modal.show();
        //     });
        // }
    </script>
</body>

</html>
