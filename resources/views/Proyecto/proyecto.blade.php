@extends('layouts.vertical')

@section('css')
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Proyecto - Empleado</h4>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
             <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Proyecto nuevo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <form class="form-horizontal col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12 row">
                                                <label class="col-lg-6 col-form-label" for="simpleinput">Nombre de proyecto</label>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control" id="simpleinput" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12 row">
                                                <label class="col-lg-4 col-form-label" for="simpleinput">Miembros de proyecto</label>
                                                <div class="col-lg-8">
                                                    <select class="form-control wide" data-plugin="customselect" multiple>
                                                        <option value="0" selected>Miembro1</option>
                                                        <option value="1">Miembro2</option>
                                                        <option value="2">Miembro3</option>
                                                        <option value="3" >Miembro4</option>
                                                        <option value="4">Miembro5</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary">Guardar</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

                <div class="button-list">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#myModal">+ Agregar proyecto</button>
                </div><br><br>

                <table id="basic-datatable" class="table dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Miembros</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Proyecto1</td>
                            <td><span>Miembro1</span> ,<span>Miembro2</span></td>
                            <td><button class="btn btn-success btn-sm">Agregar miembro</button></td>
                        </tr>
                        <tr>
                            <td>Proyecto2</td>
                            <td><span>Miembro1</span> ,<span>Miembro2</span></td>
                            <td><button class="btn btn-success btn-sm">Agregar miembro</button></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
  {{--   <div class="col-xl-8">
        <div class="inbox-rightbar">
            <div class="">
                <ul class="message-list">
                    @foreach ($empleado as  $empleados)
                    <li class="unread">
                        <div class="col-mail col-mail-1">

                            <div class="checkbox-wrapper-mail">
                                <input type="checkbox" id="chk1">
                                <label for="chk1" class="toggle"></label>
                            </div>
                                <a class="title">{{$empleados->perso_nombre}} {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</a>
                        </div>
                        <div class="col-mail col-mail-1">
                            <a class="subject badge badge-success">Proyecto 1</a>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div> --}}
</div>
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
@endsection
