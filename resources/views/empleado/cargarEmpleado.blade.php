<!DOCTYPE html>
<html lang="en">
<head>
    <title>Simple landing page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
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


</head>
<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
<style>
    .fc-time{
        display: none;
    }
</style>
  <header id="header-section">
    <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
    <div class="container" style="margin-left: 20px; margin-right: 40px;">
        <div class="col-md-2">
            <div class="navbar-brand-wrapper d-flex w-100">
                <img src="{{asset('landing/images/logo.png')}}" height="100" >
              </div>
        </div>
        <div class="col-md-7 text-left">
          <h5 style="color: #ffffff">Gestión de empleados</h5>
          <label for="" class="blanco">Validaremos los datos antes de cargarlos  :)</label>
        </div>
        <div class="col-md-3 " style="margin-left: 23%;">

            <a href=""> <button style="background-color: #155E5B;border-color: #155E5B"  class="btn btn-sm  btn-primary "> <img src="{{asset('admin/images/excel.svg')}}" height="25" ></i>  Descargar plantilla</button></a>
        </div>



    </div>
    </nav>
  </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">
            <div class="row " >
                <div class="col-xl-12">
                    <div class="card" style="background:#f7f6f6;">
                        @if ( $errors->any() )

                        <div class="alert alert-danger">
                            @foreach( $errors->all() as $error )<li>{{ $error }}</li>@endforeach
                        </div>
                    @endif

                    @if(isset($numRows))
                        <div class="alert alert-sucess">
                            Se importaron {{$numRows}} registros.
                        </div>
                    @endif
                        <div class="card-body">
                            <form action="{{ route('importEmpleado') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="file" class="form-control">
                                <br>
                                <button class="btn btn-success">Import User Data</button>

                            </form>
                        </div>
                        <div class="card-body" style="padding-top: 20px;color: #1b1b1b;">
                            <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                            <table id="basic-datatable1" class="table  nowrap">
                                <thead style="background: #4C5D73;color: white;">
                                    <tr>
                                        <th>Tipo de Doc.</th>
                                        <th>Nro de documento</th>
                                        <th>Nombres</th>
                                        <th>Ap. Paterno</th>
                                        <th>Ap. Materno</th>
                                        <th>Direccion</th>
                                        <th>Departamento</th>
                                        <th>Provincia</th>
                                        <th>Distrito</th>
                                        <th>Cargo</th>
                                        <th>Área</th>
                                        <th>Centro de costo</th>
                                        <th>Fecha nacimiento</th>
                                        <th>Ciudad Nac</th>
                                        <th>Departamento Nac.</th>
                                        <th>Provincia Nac.</th>
                                        <th>Distrito Nac.</th>
                                        <th>Sexo</th>



                                    </tr>
                                </thead>


                                <tbody style="background: #f3f4f7;color: #2c2c2c;">

                                    <tr>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>
                                        <td>London</td>
                                        <td>47</td>
                                        <td>2011/03/21</td>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>
                                        <td>London</td>
                                        <td>47</td>
                                        <td>2011/03/21</td>
                                        <td>2011/03/21</td>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>
                                        <td>London</td>
                                        <td>47</td>
                                        <td>2011/03/21</td>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>

                                    </tr>



                                    <tr>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>
                                        <td>London</td>
                                        <td>47</td>
                                        <td>2011/03/21</td>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>
                                        <td>London</td>
                                        <td>47</td>
                                        <td>2011/03/21</td>
                                        <td>2011/03/21</td>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>
                                        <td>London</td>
                                        <td>47</td>
                                        <td>2011/03/21</td>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>

                                    </tr>

                                </tbody>
                            </table>

                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div>


            </div>
        <footer class="border-top">
            <p class="text-center text-muted pt-4">© <?php echo date("Y"); ?> - RH Solution | Todos los derechos reservados.</p>
        </footer>
      </div>
    </div>


    <!--<script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>-->
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <!--<script src="{{asset('landing/js/landingpage.js')}}"></script>-->
    <script></script>


    <!-- Vendor js -->
    <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
    <!-- App js -->
    <script src="{{asset('admin/assets/js/app.min.js')}}"></script>

    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>

    <script src="{{asset('landing/js/tabla.js')}}"></script>
<script>
    $(document).ready(function() {
    $('#basic-datatable1').DataTable( {
        "scrollX": true
    } );
} );
</script>

</body>
</html>
