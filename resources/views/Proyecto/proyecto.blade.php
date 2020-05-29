@extends('layouts.vertical')

@section('css')
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-4">
        <br><br>
        <div class="card">
            <div class="card-headers text-center">
                <br>
                <a href="" class="btn btn-primary"><i data-feather="plus"></i>Nuevo Proyecto</a>
            </div>
            <div class="card-body">
                <div class="row justify-content-sm-between">
                    <div class="col-lg-6 mb-2 mb-lg-0">
                        <div class="mail-list mt-1">
                            <a href="" class="list-group-item border-0">Proyecto 1</a>
                        </div> <!-- end checkbox -->
                    </div> <!-- end col -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="inbox-rightbar">
            <div class="mt-2">
                <h5 class="mt-3 mb-2 font-size-16">Empleados</h5>
                <ul class="message-list">
                    <li class="unread">
                        <div class="col-mail col-mail-1">
                            <div class="checkbox-wrapper-mail">
                                <input type="checkbox" id="chk1">
                                <label for="chk1" class="toggle"></label>
                            </div>
                            <a class="title">Lucas Kriebel (via Twitter)</a>
                        </div>
                        <div class="col-mail col-mail-2">
                            <a class="subject">Lucas Kriebel (@LucasKriebel)</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection