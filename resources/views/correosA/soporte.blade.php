@extends('layouts.vertical')
@section('css')
<link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{asset('admin/assets/libs/summernote/summernote-bs4.min.css')}}" rel="stylesheet" />
<link href="{{asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
        </div>
    </div>
</div>
<style>
    .form-control:disabled {
        background-color: #fcfcfc !important;
    }

    .combodate {
        display: flex;
        justify-content: space-between;
    }

    .day {
        max-width: 32%;
    }

    .month {
        max-width: 38%;
    }

    .year {
        max-width: 42%;
    }

    .file {
        visibility: hidden;
        position: absolute;
    }

    .rowAlert {
        background-color: #ffffff;
        box-shadow: 3px 3px 20px rgba(48, 48, 48, 0.5);
    }


    body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-footer>button {
        background-color: #163552;
        border-color: #163552;
        zoom: 85%;
    }
</style>
<!-- compose -->
<div class="row justify-content-center">
    <div class="col-md-8 pt-5">
        <div class="card border"
            tyle="border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
            <div class="card-header"
                style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <div class="row pl-2">
                    <span style="color: #163552;font-weight: bold;font-size: 14px"><img
                            src="{{asset('landing/images/email (1).svg')}}" height="20" class="mr-1">Ticket de
                        Soporte</span>
                </div>
            </div>

            <div class="card-body border">
                <div class="row justify-content-center pb-2">
                    <div class="inbox-rightbar">
                        <div>
                            <form action="javascript:disabledS();">
                                <div class="form-group">
                                    <label for="email" style="font-weight: bold">Para:</label>
                                    <input type="email" class="form-control" value="{{$correo}}" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="text" style="font-weight: bold">Asunto:</label>
                                    <input type="text" class="form-control" maxlength="50" id="asunto" required>
                                </div>
                                <div class="form-group">
                                    <div class="summernote" id="summernote">
                                    </div>
                                </div>

                                <div class="form-group pt-2" style="display: none" id="mostrarBoton">
                                    <div class="text-right">
                                        <button type="submit" class="btn" style="background-color: #163552">
                                            <span>Enviar</span> <i class="uil uil-message ml-2"></i>
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div> <!-- end card-->

                    </div>
                    <!-- end inbox-rightbar-->
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<!--Summernote js-->
<script src="{{URL::asset('admin/assets/libs/summernote/summernote-bs4.min.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/summernote/langsummernote-es-ES.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('landing/js/app-menu.js')}}"></script>
<script src="{{asset('landing/js/correosdeMantenimiento.js')}}"></script>
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@endsection