@extends('admin.layouts.app')
@section('title', 'CRM Configration')
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between mb-2">
                        <h1 class="m-0">CRM Configration</h1>
                        <button class="btn btn-sm text-light" onclick="handle_clear_cache()" style="background-color: var(--wb-renosand);">Clear Cache</button>
                        {{-- href="{{route('admin.optimize.crm')}}" --}}
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card text-sm">
                    <div class="card-header text-light" style="background-color: var(--wb-renosand)">
                        <h3 class="card-title">Configration Details</h3>
                    </div>
                    <form action="{{route('admin.updateEnv')}}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="mb-3">Mailer Details</h1>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="name_inp">MAIL MAILER <span class="text-danger">*</span></label>
                                        <select type="text" class="form-control" id="name_inp"
                                            name="MAIL_MAILER" required>
                                            <option value="smtp" {{ $envValues->MAIL_MAILER == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                            <option value="tls" {{ $envValues->MAIL_MAILER == 'tls' ? 'selected' : '' }}>TLS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="MAIL_HOST">MAIL HOST <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="MAIL_HOST" placeholder="Enter Mail host"
                                            name="MAIL_HOST" required value="{{$envValues->MAIL_HOST}}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="MAIL_PORT">MAIL PORT <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="MAIL_PORT" placeholder="Enter Mail port"
                                            name="MAIL_PORT" required value="{{$envValues->MAIL_PORT}}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="MAIL_EHLO_DOMAIN">MAIL LOCAL DOMAIN <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="MAIL_EHLO_DOMAIN" placeholder="Enter Mail local domain"
                                            name="MAIL_EHLO_DOMAIN" required value="{{$envValues->MAIL_EHLO_DOMAIN}}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="MAIL_ENCRYPTION">MAIL ENCRYPTION <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="MAIL_ENCRYPTION" placeholder="Enter Mail Encryption"
                                            name="MAIL_ENCRYPTION" required value="{{$envValues->MAIL_ENCRYPTION}}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="MAIL_STATUS">Mail Status<span class="text-danger">*</span></label>
                                        <select class="form-control" name="MAIL_STATUS" id="MAIL_STATUS" required>
                                            <option value="true" @if(filter_var($envValues->MAIL_STATUS, FILTER_VALIDATE_BOOLEAN) === true) selected @endif>On</option>
                                            <option value="false" @if(filter_var($envValues->MAIL_STATUS, FILTER_VALIDATE_BOOLEAN) === false) selected @endif>Off</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- br --}}
                            </div>
                            <hr style="margin-top: 0px">
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5 class="mb-3">Mail 1 Details</h1>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="MAIL_USERNAME">MAIL USERNAME <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="MAIL_USERNAME" placeholder="Enter Mail Username"
                                                name="MAIL_USERNAME" required value="{{$envValues->MAIL_USERNAME}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="MAIL_PASSWORD">MAIL PASSWORD <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="MAIL_PASSWORD" placeholder="Enter Mail Password"
                                                name="MAIL_PASSWORD" required value="{{$envValues->MAIL_PASSWORD}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="MAIL_FROM_ADDRESS">MAIL FROM ADDRESS <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="MAIL_FROM_ADDRESS" placeholder="Enter MAIL FROM ADDRESS"
                                                name="MAIL_FROM_ADDRESS" required value="{{$envValues->MAIL_FROM_ADDRESS}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="MAIL_FROM_NAME">MAIL FROM NAME <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="MAIL_FROM_NAME" placeholder="Enter Mail Encryption"
                                                name="MAIL_FROM_NAME" required value="{{$envValues->MAIL_FROM_NAME}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin-top: 0px">
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5 class="mb-3">Mail 2 Details</h1>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="SMTP2_MAIL_USERNAME">MAIL USERNAME <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="SMTP2_MAIL_USERNAME" placeholder="Enter Mail Username"
                                                name="SMTP2_MAIL_USERNAME" required value="{{$envValues->SMTP2_MAIL_USERNAME}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="SMTP2_MAIL_PASSWORD">MAIL PASSWORD <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="SMTP2_MAIL_PASSWORD" placeholder="Enter Mail Password"
                                                name="SMTP2_MAIL_PASSWORD" required value="{{$envValues->SMTP2_MAIL_PASSWORD}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="SMTP2_MAIL_FROM_ADDRESS">MAIL FROM ADDRESS <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="SMTP2_MAIL_FROM_ADDRESS" placeholder="Enter MAIL FROM ADDRESS "
                                                name="SMTP2_MAIL_FROM_ADDRESS" required value="{{$envValues->SMTP2_MAIL_FROM_ADDRESS}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="MAIL_FROM_NAME">MAIL FROM NAME <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="MAIL_FROM_NAME" placeholder="Enter Mail Encryption"
                                                name="MAIL_FROM_NAME" required value="{{$envValues->MAIL_FROM_NAME}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin-top: 0px">
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5 class="mb-3">Tata Whatsapp Message</h1>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="TATA_WHATSAPP_MSG_STATUS">Tata Status<span class="text-danger">*</span></label>
                                            <select class="form-control" name="TATA_WHATSAPP_MSG_STATUS" id="TATA_WHATSAPP_MSG_STATUS" required>
                                                <option value="true" @if(filter_var($envValues->TATA_WHATSAPP_MSG_STATUS, FILTER_VALIDATE_BOOLEAN) === true) selected @endif>On</option>
                                                <option value="false" @if(filter_var($envValues->TATA_WHATSAPP_MSG_STATUS, FILTER_VALIDATE_BOOLEAN) === false) selected @endif>Off</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="TATA_AUTH_KEY">Tata Auth Key <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="TATA_AUTH_KEY" placeholder="Enter Mail Encryption"
                                                name="TATA_AUTH_KEY" required value="{{$envValues->TATA_AUTH_KEY}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin-top: 0px">
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5 class="mb-3">ChatGPT API</h1>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="CHATGPT_API_STATUS">ChatGPT API Status<span class="text-danger">*</span></label>
                                            <select class="form-control" name="CHATGPT_API_STATUS" id="CHATGPT_API_STATUS" required>
                                                <option value="true" @if(filter_var($envValues->CHATGPT_API_STATUS, FILTER_VALIDATE_BOOLEAN) === true) selected @endif>On</option>
                                                <option value="false" @if(filter_var($envValues->CHATGPT_API_STATUS, FILTER_VALIDATE_BOOLEAN) === false) selected @endif>Off</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="CHATGPT_API_KEY">ChatGPT Auth Key<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="CHATGPT_API_KEY" placeholder="Enter ChatGPT Auth Key"
                                                name="CHATGPT_API_KEY" required value="{{$envValues->CHATGPT_API_KEY}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col">
                                    <p>
                                        <span class="text-danger">*</span>
                                        Fields are required.
                                    </p>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('admin.team.list') }}" class="btn btn-sm bg-secondary m-1">Back</a>
                                    <button type="submit" class="btn btn-sm m-1 text-light"
                                        style="background-color: var(--wb-dark-red);">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
@endsection
