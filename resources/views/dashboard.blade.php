@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumb')

<div class="row page-title align-items-center">
</div>
@endsection
    @if ($variable==0)
        @section('content')
        <div class="row">
            <div class="col-md-12  text-center">
                <a href="{{route('calendario')}}"><button class="boton btn btn-default mr-1" ><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    COMIENZA AHORA</button></a>
            </div>
        </div>
        <!-- end row -->
        @endsection
    @else
        @section('content')

        <!-- products -->
        <div class="card">
            <div class="row">
                <div class="col-xl-4">
                    <h5 class="card-title mt-0 mb-0">Sales By Category</h5>
                    <div id="chart" class="apex-charts mb-0 mt-4"></div>
                </div> <!-- end col-->
                <div class="col-xl-4">
                    <h5 class="card-title mt-0 mb-0">Sales By Category</h5>
                    <div id="chart2" class="apex-charts mb-0 mt-4"></div>
                </div> <!-- end col-->
                <div class="col-xl-4">
                    <h5 class="card-title mt-0 mb-0">Sales By Category</h5>
                    <div id="chart3" class="apex-charts mb-0 mt-4"></div>
                </div> <!-- end col-->
            </div>
            <!-- end row -->
            <div class="row">
                <div class="col-xl-4">
                    <h5 class="card-title mt-0 mb-0">Sales By Category</h5>
                    <div id="chart4" class="apex-charts mb-0 mt-4"></div>
                </div> <!-- end col-->
                <div class="col-xl-4">
                    <h5 class="card-title mt-0 mb-0">Sales By Category</h5>
                    <div id="chart5" class="apex-charts mb-0 mt-4"></div>
                </div> <!-- end col-->
                <div class="col-xl-4">
                    <h5 class="card-title mt-0 mb-0">Sales By Category</h5>
                    <div id="chart6" class="apex-charts mb-0 mt-4"></div>
                </div> <!-- end col-->
            </div>
            <!-- end row -->
        </div>
        @endsection
    @endif
@section('script')
<!-- optional plugins -->
<script src="{{ URL::asset('admin/assets/libs/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{asset('admin/assets/js/chart.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
<script src="{{ URL::asset('admin/assets/js/pages/dashboard.init.js') }}"></script>
@endsection
