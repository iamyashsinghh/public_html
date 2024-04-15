<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in | Vendor CRM | {{env('APP_NAME')}}</title>
    <link rel="shortcut icon" href="{{asset('favicon.jpg')}}" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{asset('plugins/fontawesome/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
</head>

<body class="login-page" style="background-color: #a06b14">
    <div class="login-box">
        <div class="card card-outline">
            <div class="card-header text-center" style="background: #891010">
                {{-- <a href="javascript:void(0);" class="h3"> <b>{{env('APP_NAME')}}</b></a> --}}
                <img src="{{asset('wb-logo2.webp')}}" alt="AdminLTE Logo" style="width: 89% !important;">
            </div>
            <div class="card-body">
                <h4 class="text-center text-bold">Login | Vendor CRM</h4>
                <form id="login_verify_form" onsubmit="handle_login_verify(event)" method="get">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="hidden" name="login_type" value="vendor">
                        <input type="text" class="form-control" name="phone_number" placeholder="Enter your phone no." required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-phone-alt"></span>
                            </div>
                        </div>
                    </div>
                    <div id="verification_code_col" class="input-group mb-3 d-none">
                        <input type="text" class="form-control" name="verification_code" placeholder="Enter verification code.">
                        <input type="hidden" name="verified_phone_number">
                        <input type="hidden" name="verified_login_type">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-user-lock"></span>
                            </div>
                        </div>
                    </div>
                    <button id="submit_btn" type="submit" class="btn btn-block text-light" style="background-color: #891010">Continue
                        <i id="custom_spinner" class="fa fa-spinner fa-spin ml-1 d-none" style="font-size: 13px;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('adminlte/js/adminlte.js')}}"></script>
    <script src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
        };
    </script>
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
    <script>
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

        function handle_login_verify(e) {
            e.preventDefault();

            custom_spinner.classList.remove('d-none');
            submit_btn.disabled = true;
            const phone_number = document.querySelector(`input[name="phone_number"]`);
            const login_type = document.querySelector(`input[name="login_type"]`);
            const formBody = JSON.stringify({
                phone_number: phone_number.value,
                login_type: login_type.value,
            });
            if (phone_number.value != "") {
                common_ajax(
                    `{{route('login.verify')}}`,
                    "post",
                    formBody
                ).then(response => response.json()).then(data => {
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true,
                        "timeOut": "10000",
                    };
                    setTimeout(() => {
                        toastr[data.alert_type](data.message);
                        custom_spinner.classList.add('d-none');
                        submit_btn.disabled = false;
                        
                        if (data.success == true) {
                            verification_code_col.classList.remove('d-none');
                            submit_btn.innerHTML = `Login`;
                            login_verify_form.action = `{{route('login.process')}}`;
                            login_verify_form.method = "post";
                            login_verify_form.removeAttribute('onsubmit');
                            document.querySelector(`input[name="verified_phone_number"]`).value = phone_number.value;
                            document.querySelector(`input[name="verified_login_type"]`).value = login_type.value;
                            phone_number.disabled = true;
                        }
                    }, 2000);
                })
            } else {
                toastr.error("Phone number cannot be blank.")
            }
        }

      
    </script>
</body>

</html>