@php
use App\proyecto_empleado;
@endphp

@extends('layouts.vertical')

@section('css')


    {{-- <link
    href="{{asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.css')}}"
    rel="stylesheet"
    type="text/css" /> --}}
    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/packages/core/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/daygrid/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/timegrid/main.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
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

    <style>
        /*  body > div.bootbox.modal.fade.show > div > div > div{
                background: #131313;
            color: #fbfbfb;
            }
            body > div.bootbox.modal.fade.show > div{
                top: 100px;
            left: 75px;
            } */

        div.fc-bg>table>tbody>tr>td.fc-day.fc-widget-content.fc-mon,
        td.fc-day.fc-widget-content.fc-tue,
        td.fc-day.fc-widget-content.fc-wed,
        td.fc-day.fc-widget-content.fc-thu,
        td.fc-day.fc-widget-content.fc-fri,
        td.fc-day.fc-widget-content.fc-sat {

            background-color: #ffffff;
        }

        .fc-time {
            display: none;
        }

        .fc-Descanso-button {
            color: #fff;
            background-color: #162029;
        }

        .fc-NoLaborales-button {
            color: #fff;
            background-color: #162029;
        }

        .fc-Feriado-button {
            color: #fff;
            background-color: #162029;
        }

        .fc-nuevoAño-button {
            left: 10px;
            font-size: 12px;
            padding-left: 6px;
            padding-right: 6px;

        }

        .fc-Asignar-button {
            left: 10px;
            font-size: 12px;
            padding-left: 6px;
            padding-right: 6px;
            padding-bottom: 7px;
            padding-top: 8px;

        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #52565b;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fdfdfd;
        }

        .col-md-6 .select2-container .select2-selection {
            height: 50px;
            font-size: 12.2px;
            overflow-y: scroll;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background: #ced0d3;
        }

        .table td {
            padding-top: 0.3rem;
            padding-bottom: 0.3rem;
        }

        .fc-button {
            background: #163552;
            color: #ffffff;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-primary.bootbox-accept {
            background-color: #163552;
            border-color: #163552;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header {
            background-color: #163552;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header>h5 {
            color: #fff;
            font-size: 15px !important;
        }

        .botonesD {
            padding-bottom: 10px !important;
            padding-top: 10px !important;
            padding-right: 10px !important;
            padding-left: 10px !important;
        }

    </style>
    <div class="row page-title" style="padding-right: 20px;">
        <div class="col-md-7">

            <h4 class="mb-1 mt-0" style="font-weight: bold">Calendarios</h4>

        </div>

        <div class="col-md-3 ">
            <select name="" id="selectCalendario" class="form-control">
                @foreach ($calendario as $calendarios)
                    <option class="" value="{{ $calendarios->calen_id }}">{{ $calendarios->calendario_nombre }}</option>
                @endforeach
            </select>

        </div>
        <div class="col-md-2">
            <button onclick="abrirNcalendario()" class="boton" style="font-size: 12px;padding: 4px">+ Nuevo
                calendario</button>
        </div>
        <br><br><br><br>
        <div class="col-md-1"></div>
        <div class="col-md-9" id="calendar">

        </div>
        &nbsp;&nbsp;&nbsp;
        <div class="col-md-12"><br></div>
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-9"><label style="font-size: 13px; font-weight:600 " for="">Programación de:
                        {{ $fechaEnvi }} hasta: <label style="font-size: 13px;font-weight:600" for=""
                            id="fechaHasta"></label></label></div>
                <div class="col-md-3 text-right"><label style="font-size: 12px" for="" id="fechaHasta"></label></div>
            </div>
        </div>


        <div class="col-md-4 text-right">
            <label for="" style="font-style:oblique">Creación de la empresa: {{ $fechaOrga->format('d/m/Y') }}</label>
        </div>

    </div>
@endsection

@section('content')

    <div class="row ">


        <input type="hidden" id="pruebaStar">
        <input type="hidden" id="pruebaEnd">
        @include('calendario.calendarioPlantilla')

    @endsection
    @section('script')
        <script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
        <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
        <script src="{{ asset('landing/js/SeleccionarPais.js') }}"></script>

        <!-- Vendor js -->
        {{-- <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script> --}}
        <!-- plugin js -->
        <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
        <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
        <script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>
        <script src="{{ asset('admin/packages/core/main.js') }}"></script>
        <script src="{{ asset('admin/packages/core/locales/es.js') }}"></script>
        <script src="{{ asset('admin/packages/daygrid/main.js') }}"></script>
        <script src="{{ asset('admin/packages/timegrid/main.js') }}"></script>
        <script src="{{ asset('admin/packages/interaction/main.js') }}"></script>
        <script src="{{ asset('landing/js/calendario.js') }}"></script>
        <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
        <script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
        <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    @endsection
