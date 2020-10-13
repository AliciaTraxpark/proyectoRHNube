@extends('layouts.verticalAd')

@section('css')
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
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Organizaciones</h4>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center pt-5">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"
                style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <div class="row">
                    <h4 class="header-title col-12 mt-0" style="margin-bottom: 0px;">Trazabilidad de capturas</h4>
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
                                        <th>Descripcion</th>
                                        <th>Tolerancia</th>
                                        <th>Hora inicio</th>
                                        <th>Hora fin</th>
                                        <th>En uso</th>
                                        <th></th>
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

<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/es.js')}}"></script>
<!-- datatable js -->

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
