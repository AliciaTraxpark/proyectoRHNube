@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<link href="{{ URL::asset('admin/assets/libs/chart/Chart.min.css') }}" rel="stylesheet" type="text/css" />
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
<div class="row page-title align-items-center">
</div>
@endsection
@if ($variable==0)
@section('content')
<div class="row">
    <div class="col-md-12  text-center">
        <a href="{{route('calendario')}}"><button class="boton btn btn-default mr-1"><span
                    class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                COMIENZA AHORA</button></a>
    </div>
</div>
<div class="row" style="opacity: 0.2;">
</div>
<!-- end row -->
@endsection
@else
@section('content')
<div class="row">
    <canvas class="col-md-4" id="area"></canvas>
</div>

@endsection
@endif
@section('script')
<!-- optional plugins -->
<script src="{{ URL::asset('admin/assets/libs/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.js') }}"></script>
<script src="{{asset('landing/js/dashboard.js')}}"></script>
@endsection
