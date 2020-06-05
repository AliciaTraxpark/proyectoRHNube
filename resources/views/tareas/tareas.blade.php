@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}" rel="stylesheet"
    type="text/css" />
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">

        <h4 class="mb-1 mt-0">Actividad de Captura de Pantalla</h4>
    </div>
</div>
@endsection

@section('content')
<style>
    .border{
        border: 1px solid #d6d6d6 !important;
        padding-bottom: 5px;
    }



</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label style="font-weight: 700">Búsqueda por fecha</label>
                    </div>

                    <div class="col-md-2">
                        <label style="font-weight: 700"><br></label><br>
                        <button type="button" class="btn btn-light"><i class="uil uil-arrow-left"></i></button>
                        <button type="button" class="btn btn-light"><i class="uil uil-arrow-right"></i></button>
                    </div>
                    <div class="col-md-4 text-left">
                        <label><br> </label>
                        <div class="input-group col-md-6">
                            <input type="text" id="humanfd-datepicker" class="form-control" placeholder="May 25, 2020">
                            <div class="input-group-prepend">
                                <div class="input-group-text form-control "><i class="uil uil-calender"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mt-3 mt-sm-0 col-md-10">
                            <label>Empleado</label>
                            <select id="empleado" data-plugin="customselect" class="form-control">
                                <option value="">SELECCIONAR</option>
                                @foreach ($empleado as $empleados)
                                    <option class="" value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}} {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group  col-md-10">
                            <label>Área</label>
                            <select data-plugin="customselect" class="form-control">
                                <option value="0">Shreyu</option>
                                <option value="1">Greeva</option>
                                <option value="2">Dhyanu</option>
                                <option value="3" disabled>Disabled</option>
                                <option value="4">Mannat</option>
                            </select>
                        </div>
                    </div>


                </div>
                <div class="card" id="card">
                        <!--<div class="card-body" style="padding-left: 0px;">
                            <label id="hora"></label>  &nbsp;&nbsp;&nbsp; <label>Tiempo trabajado 0:59:53</label><br><br>
                            <div class="custom-accordion accordion ml-4" id="customaccordion_exa" style="margin-left: 0px!important;">
                                <div class="card mb-1" style="padding-left: 20px;">
                                    <a href="" class="text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                        aria-expanded="true" aria-controls="customaccorcollapseOne">
                                    </a>
                                    <div id="customaccorcollapseOne" class="collapse show" aria-labelledby="customaccorheadingOne"
                                        data-parent="#customaccordion_exa">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class=" text-center col-md-12 col-sm-6" style="background-color: rgb(234, 234, 234);padding-top: 8px;
                                            padding-bottom: 8px;">
                                                <h5 class="m-0 font-size-14" > Área 1  </h5>
                                            </div>  <br>
                                            <div class="col-md-12 col-sm-6 border" style="padding-left: 0px;">
                                            <img id="imagen" height="79">
                                            &nbsp;  <label style="font-size: 12px" for="">9:00 am - 9:10 am</label>
                                            <div class="progress" style="background-color: #d4d4d4;">
                                                &nbsp;  <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25"
                                                    aria-valuemin="0" aria-valuemax="100">25%</div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->







</div>
<!-- end row -->


@endsection

@section('script')
<!-- Plugins Js -->
<script src="{{ URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js') }}"></script>
@endsection

@section('script-bottom')
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{asset('landing/js/tareas.js')}}"></script>
@endsection

