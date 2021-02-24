@extends('layouts.vertical')

@section('css')

    <!-- Plugin css  CALENDAR-->
    <link href="{{ asset('admin/packages/core/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/daygrid/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/timegrid/main.css') }}" rel="stylesheet" />

    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <link href="{{ URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet"
        type="text/css" />
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
            <h4 class="header-title mt-0 "></i>Horarios </h4>
        </div>
    </div>
@endsection


@section('content')
    <style>
        div.fc-bg>table>tbody>tr>td.fc-day.fc-widget-content.fc-sun {

            background-color: rgb(255, 239, 239) !important;
        }

        div.fc-bg>table>tbody>tr>td.fc-day.fc-widget-content.fc-mon,
        td.fc-day.fc-widget-content.fc-tue,
        td.fc-day.fc-widget-content.fc-wed,
        td.fc-day.fc-widget-content.fc-thu,
        td.fc-day.fc-widget-content.fc-fri,
        td.fc-day.fc-widget-content.fc-sat {

            background-color: #ffffff !important;
        }

        .fc-event,
        .fc-event-dot {
            background-color: #d1c3c3;
            font-size: 12.2px !important;
            margin: 2px 2px;
            cursor: url("../landing/images/cruz1.svg"), auto;

        }

        a:not([href]):not([tabindex]) {
            color: #000;
            cursor: pointer;
            font-size: 12px;

        }

        .fc-event-container>a {
            border: 1px solid #fff;
        }

        .fc-toolbar.fc-header-toolbar {
            zoom: 80%;
        }

        #calendar>div.fc-toolbar.fc-footer-toolbar>div.fc-left>button,
        #calendar>div.fc-toolbar.fc-footer-toolbar>div.fc-center,
        #calendar>div.fc-toolbar.fc-footer-toolbar>div.fc-right>button {
            zoom: 90%;
        }

        .buttonc {
            color: #121b7a;
            background-color: #e7e1f7;
            border-color: #e7e1f7;
        }

        #calendar>div.fc-toolbar.fc-header-toolbar>div.fc-center {
            margin-right: 200px;
        }

        .fc-time {
            display: none;
        }


        .sw-theme-default>ul.step-anchor>li.active>a {
            color: #1c68b1 !important;
        }

        .sw-theme-default>ul.step-anchor>li.done>a,
        .sw-theme-default>ul.step-anchor>li>a {
            color: #0b1b29 !important;
        }

        .btn-group {
            width: 100%;
            justify-content: space-between;
        }

        .btn-secondary {
            max-width: 9em;
        }

        body {
            background-color: #ffffff;
        }

        .hasTime {
            width: 125px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #52565b;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fdfdfd;
        }

        tr:first-child>td>.fc-day-grid-event {
            margin-top: 0px;
            padding-top: 0px;
            padding-bottom: 0px;
            margin-bottom: 0px;
            margin-left: 2px;
            margin-right: 2px;
        }

        .fc th.fc-widget-header {
            background: #dfe6f2;
            font-size: 13px;
            color: #163552;
            line-height: 20px;
            padding: 5px 0;
            text-transform: uppercase;
            font-weight: 600;
        }

        .custom-select:disabled {
            color: #3f3a3a;
            background-color: #fcfcfc;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background: #ced0d3;
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

        .form-control:disabled {
            background-color: #f1f0f0;
        }

        .large.tooltip-inner {
            max-width: 185px;
            width: 185px;
        }

        .btnhora {
            font-size: 12px;
            padding-top: 1px;
            padding-bottom: 1px;
        }

        .table {
            width: 100% !important;
        }

        .dataTables_scrollHeadInner {
            width: 100% !important;
        }

        .table th,
        .table td {
            padding: 0.4rem;
            border-top: 1px solid #edf0f1;
        }

        body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-header {
            background-color: #163552;
        }

        body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-header>h5 {
            color: #fff;
            font-size: 15px !important;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header {
            background-color: #163552;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header>h5 {
            color: #fff;
            font-size: 15px !important;
        }

        .borderColor {
            border-color: red;
        }
        .loader {
        position: fixed;
         left: 40%;
        top: 30%;
      /*   width: 50%; */
        height: 30%; 
        z-index: 9999;
        opacity: .8;
        background: rgb(252,252,252);
        }

    </style>
    @include('horarios.horarioPlantilla')
@endsection
@section('script')
    <script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
    <!-- Plugins Js -->
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>

    <script src="{{ URL::asset('admin/assets/libs/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('landing/js/horarioNuevo.js') }}"></script>
    <script src="{{ asset('admin/packages/core/main.js') }}"></script>
    <script src="{{ asset('admin/packages/core/locales/es.js') }}"></script>
    <script src="{{ asset('admin/packages/daygrid/main.js') }}"></script>
    <script src="{{ asset('admin/packages/timegrid/main.js') }}"></script>
    <script src="{{ asset('admin/packages/interaction/main.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
@endsection

@section('script-bottom')
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection
