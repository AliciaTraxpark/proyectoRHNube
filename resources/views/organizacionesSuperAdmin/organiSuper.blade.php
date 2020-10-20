@extends('layouts.verticalAd')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css')
    }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')
    }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('breadcrumb')

@endsection

@section('content')
<style>

    #body > div.bootbox.modal.fade.bootbox-confirm.show > div > div > div.modal-footer > button.btn.btn-light.bootbox-cancel{
        background: #e2e1e1;
        color: #000000;
        border-color:#e2e1e1;
        zoom: 85%;
    }
    .btn-primary{
        background-color: #163552!important;
        border-color: #163552!important;

    }
</style>
<div class="row justify-content-center pt-5" style="padding-top: 20px!important;">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"
                style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <div class="row">
                    <h4 class="header-title col-12 mt-0" style="margin-bottom: 0px;">Lista de organizaciones</h4>
                </div>
            </div>
            <div class="card-body border">
                <div class="row justify-content-center">



                </div>
                <div class="row justify-content-center">
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="table-responsive-xl">
                            <table id="tablaOrgan" class="table dt-responsive nowrap" style="font-size: 12.8px;">
                                <thead style=" background: #edf0f1;color: #6c757d;">
                                    <tr>
                                        <th></th>
                                        <th>Organizacion</th>
                                        <th>Tipo</th>
                                        <th>F. de creacion</th>
                                        <th>Usuarios</th>
                                        <th>Empleados</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{asset('landing/js/organizacion.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/es.js')}}"></script>
<!-- datatable js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js')
    }}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js')
    }}"></script>

@endsection
