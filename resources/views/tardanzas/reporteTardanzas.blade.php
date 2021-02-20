@extends('layouts.vertical')

@section('css')
<!-- Plugin css  CALENDAR-->

<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="{{asset('admin/assets/js/html2pdf.bundle.min.js')}}"></script>
<script src="{{asset('admin/assets/js/Blob.js')}}"></script>
<script src="{{asset('admin/assets/js/FileSaver.js')}}"></script>
<script src="{{asset('admin/assets/js/Shim.min.js')}}"></script>
<script src="{{asset('admin/assets/js/xlsx.full.min.js')}}"></script>
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
{{-- plugin de ALERTIFY --}}
<link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ URL::asset('admin/assets/libs/alertify/bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
<!-- Semantic UI theme -->
<link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.6/jspdf.plugin.autotable.min.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js"></script> --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
        </div>
    </div>
</div>
<div class="row page-title">
    <div class="col-md-12">
        {{-- <h4 class="mb-1 mt-0">Horarios</h4> --}}
        <h4 class="header-title mt-0 ">Reporte de Tardanzas por empleados</h4>
    </div>
</div>
@endsection


@section('content')
<style>
    body {
        background-color: #ffffff;
    }

    .flatpickr-calendar.static.open {
        width: 124px !important;
    }

    .botonsms {
        background-color: #ffffff;
        border-color: #ffffff;
        color: #62778c;
        padding-top: 0px;
        padding-bottom: 0px;
        border-top-width: 0px;
        border-bottom-width: 0px;
        padding-right: 0px;
        padding-left: 0px;
    }

    .badge-soft-secondary {
        background-color: rgb(207 209 223 / 20%);
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #52565b;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fdfdfd;
    }

    .custom-select:disabled {
        color: #3f3a3a;
        background-color: #fcfcfc;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #ced0d3;
    }

    .badge {
        font-size: 11.5px !important;
        font-weight: 500 !important;
    }

    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-light.bootbox-cancel {
        background: #e2e1e1;
        color: #000000;
        border-color: #e2e1e1;
        zoom: 85%;
    }

    body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-footer>button,
    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-success.bootbox-accept {
        background-color: #163552;
        border-color: #163552;
        zoom: 85%;
    }

    .col-md-6 .select2-container .select2-selection {
        height: 50px;
        font-size: 12.2px;
        overflow-y: scroll;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 31px;
    }
    .table {
        width: 100% !important;
    }
    .table th,
    .table td {
        padding: 0.4rem;
        border-top: 1px solid #edf0f1;
    }
    @media(max-width: 991px){
        .btnR{
            display: flex !important;
            align-items: end !important; 
            padding-bottom: 5px !important;
            padding-top: 40px !important;
        }
    }
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        opacity: .8;
        background: rgb(252,252,252);
    }
</style>


<div class="row justify-content-center pt-5" style="padding-top: 20px!important;">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <div class="row">
                    <h4 class="header-title col-12 mt-0" style="margin-bottom: 0px;">{{$organizacion}}</h4>
                </div>
            </div>
            <input type="hidden" id="pasandoV">
            <div class="card-body border">
                <div class="row justify-content-center mb-2">
                    <div class="col-xl-6 col-lg-5 col-sm-6 col-12">
                        <div class="row">
                            <label class="col-lg-4 col-form-label">Rango de fechas:</label>
                            <input type="hidden" id="ID_START">
                            <input type="hidden" id="ID_END">
                            <div class="input-group col-md-12 col-lg-8 col-xl-8 text-center" style="padding-bottom: 5px;" id="fechaSelec">
                                <input type="text" id="fechaInput" {{-- onchange="cambiarF()" --}} class="form-control" data-input>
                                <div class="input-group-prepend">
                                    <div class="input-group-text form-control flatpickr">
                                        <a class="input-button" data-toggle>
                                            <i class="uil uil-calender"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-6 col-sm-5 col-10">
                        <div class="row">
                            <label class="col-lg-3 col-form-label">Empleado</label>
                            <div class="col-lg-9">
                                <select id="idempleado" style="height: 50px!important" data-plugin="customselect"
                                    class="form-control form-control-sm" data-placeholder="Seleccione empleado">
                                    <option value="0" selected>Todos los empleados</option>
                                    @foreach ($empleado as $empleados)
                                    <option value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}}
                                        {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-1 col-lg-1 col-sm-1 col-2 btnR" >
                        <button type="button" id="btnRecargaTabla" class="btn btn-sm" style="background-color: #163552; height: 30px!important" onclick="javascript:cambiarFCR(1)"> 
                            <img src="{{asset('landing/images/loupe (1).svg')}}" height="15"></button>
                    </div>
                </div>

                <div class="row justify-content-center">
                    {{-- GIF DE ESPERA --}}
                    <div class="loader" class="text-center"  style="display: flex !important; justify-content: center !important; align-items: center;">
                        <img src="{{ asset('landing/images/logo_animado.gif') }}" height="300" class="img-load" style="display: none">
                    </div>

                    <div id="tableZoom" class="col-md-12">
                        <table id="tablaReport" class="table  nowrap" style="font-size: 12.8px;">
                            <thead id="theadD" style=" background: #edf0f1;color: #6c757d;">
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Número de documento</th>
                                    <th>Nombres y apellidos</th>
                                    <th>Cargo</th>
                                    <th>Área</th>
                                    <th>Tiempos de tardanzas</th>   
                                    <th>Cantidad de tardanzas</th>                 
                                </tr>
                            </thead>
                            <tbody id="tbodyD">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    {{-- modificar --}}
    @if (isset($modifReporte))
    @if ($modifReporte==1)
    <input type="hidden" id="modifReporte" value="0">
    @else
    <input type="hidden" id="modifReporte" value="0">
    @endif
    @else
    <input type="hidden" id="modifReporte" value="0">
    @endif
@endsection
@section('script')
<script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
<!-- Plugins Js -->


<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>


<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>

<script src="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/buttons.html5.min.js')
    }}"></script>

<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/vfs_fonts.js') }}"></script>
<script src="{{ asset('landing/js/reporteTardanzas.js') }}"></script>

@endsection

@section('script-bottom')
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection
