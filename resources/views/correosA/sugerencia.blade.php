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
</style>
<!-- compose -->
<div class="row justify-content-center">
    <div class="col-md-8 pt-5">
        <div class="card">
            <div class="card-header"
                style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <div class="row pl-2">
                    <span style="color: #163552;font-weight: bold;font-size: 14px"><img src="{{asset('landing/images/email (1).svg')}}" height="20" class="mr-1">Enviar Sugerencia</span>
                </div>
            </div>

            <div class="card-body border">
                <div class="row justify-content-center pb-2">
                    <div class="inbox-rightbar">
                        <div>
                            <form action="javascript:enviarS();">
                                <div class="form-group">
                                    <label for="email" style="font-weight: bold">Para:</label>
                                    <input type="email" class="form-control" value="info@rhnube.com.pe" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="text" style="font-weight: bold">Asunto:</label>
                                    <input type="text" class="form-control" maxlength="50" id="asuntoS" required>
                                </div>
                                <div class="form-group">
                                    <div class="summernote" id="summernoteS">
                                    </div>
                                </div>

                                <div class="form-group pt-2" style="display: none" id="mostrarBotonS">
                                    <div class="text-right">
                                        <button type="submit" class="btn" style="background-color: #163552"> <span>Enviar</span> <i
                                                class="uil uil-message ml-2"></i>
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
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@endsection