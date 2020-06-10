<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gestion de  empleados</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}"> --}}
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('admin/assets/images/favicon.ico')}}">

    <!-- App css -->
    <link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
</head>
<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100" style="background-color: #fdfdfd;">
<style>


    .container{
        margin-left: 40px;
    margin-right: 28px;
    }
    .fc-time{
        display: none;
    }
    .v-divider{
    border-right:5px solid #4C5D73;
    }
    .table th, .table td{
        padding: 0.55rem;

    border-top: 1px solid #c9c9c9;

    }

    .sw-theme-default > ul.step-anchor > li.active > a{
        color: #1c68b1 !important;
    }
    .sw-theme-default > ul.step-anchor > li.done > a, .sw-theme-default > ul.step-anchor > li > a {
        color: #0b1b29!important;
    }
    .combodate{
        display: flex;
        justify-content: space-between;
    }
    .day{
        max-width: 25%;
    }
    .month{
        max-width: 35%;
    }
    .year{
        max-width: 40%;
    }
    .btn-group{
        width: 100%;
        justify-content: space-between;
    }
    .btn-secondary{
        max-width: 9em;
    }
    .form-control:disabled{
        background-color: #fcfcfc;
    }
    body{
        background-color: #f8f8f8;
    }
</style>
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
        </div>
    </div>
</div>
  <header id="header-section">
    <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
    <div class="container">
        <div class="col-md-2 col-xl-2" >
            <div class="navbar-brand-wrapper d-flex w-100">
                <img src="{{asset('landing/images/logo.png')}}" height="100" >
              </div>
        </div>
        <div class="col-md-9 col-xl-9">
          <h5 style="color: #ffffff">Gestión de empleados</h5>
          <label for="" class="blanco font-italic">Asignemos los turnos y horarios
        </label>
        </div>
    </div>
    </nav>
  </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">
            <div class="row row-divided">
                <div class="col-md-12 col-xl-12">
                    <div class="card">
                        <div class="card-body" style="padding-top: 0px; background: #fdfdfd; font-size: 12.8px;
                        color: #222222;   padding-left: 60px; padding-right: 80px; ">
                            <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="header-title mt-0 "></i>Búsqueda de empleado</h4>
                                </div>
                                <div class=" col-md-6 col-xl-6 text-right">

                                    <button id="formNuevoEl" style="background-color: #183b5d;border-color:#62778c" class="btn btn-sm btn-primary delete_all" data-url="">Eliminar seleccion </button>
                                    <button class="btn btn-sm btn-primary" id="formNuevoEd" style="background-color: #183b5d;border-color:#62778c">Editar</button>
                                    <button class="btn btn-sm btn-primary" id="formNuevoE" style="background-color: #183b5d;border-color:#62778c">Nuevo</button>
                                </div>
                            </div>
                                <div id="tabladiv">
                                </div>
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div>


            </div>
        <footer class="border-top">
            <p class="text-center text-muted pt-4">© <?php echo date("Y"); ?> - RH Solution | Todos los derechos reservados.</p>
        </footer>
      </div>
    </div>


    <!-- Vendor js -->
    {{-- <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script> --}}
    <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
    <!-- App js -->
    <script src="{{asset('admin/assets/js/app.min.js')}}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{asset('landing/js/tabla.js')}}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>

</body>
</html>
