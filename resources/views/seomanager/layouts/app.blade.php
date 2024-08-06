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
    @include('seomanager.layouts.navbar')
    @include('seomanager.layouts.sidebar')

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
    </script>
</body>

</html>
