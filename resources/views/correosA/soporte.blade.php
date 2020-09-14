@extends('layouts.vertical')
@section('css')
<link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{asset('admin/assets/libs/summernote/summernote.min.css')}}" rel="stylesheet" />
<link href="{{asset('admin/assets/libs/summernote/monokai.css')}}" rel="stylesheet" />
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
    <div class="col-md-8 py-5">
        <div class="card">
            <div class="card-body">
                <div class="inbox-rightbar">
                    <div>
                        <form>
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="To">
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Subject">
                            </div>
                            <div class="form-group">
                                <div class="summernote">
                                    <h6>Hello Summernote</h6>
                                    <ul>
                                        <li>
                                            Select a text to reveal the toolbar.
                                        </li>
                                        <li>
                                            Edit rich document on-the-fly, so elastic!
                                        </li>
                                    </ul>
                                    <p>
                                        End of air-mode area
                                    </p>

                                </div>
                            </div>

                            <div class="form-group pt-2">
                                <div class="text-right">
                                    <button type="button" class="btn btn-success mr-1"><i
                                            class="uil uil-envelope-edit"></i>
                                        Draft</button>
                                    <button class="btn btn-primary"> <span>Send</span> <i
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
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<!--Summernote js-->
<script src="{{URL::asset('admin/assets/libs/summernote/summernote.min.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/summernote/langsummernote-es-ES.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('landing/js/app-menu.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
<script>
    $('.summernote').summernote({
        lang: 'es-ES',
        height: 230,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,                 // set focus to editable area after initializing summernote
        codemirror: { // codemirror options
            theme: 'monokai'
        },
    });
    $('.summernote').summernote('fontName', 'Arial');
</script>
@endsection
@endsection