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
    .table td{
        border-top: 1px solid #c7c7c7;
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

            <a href="{{'/export'}}"> <button id="export" style="background-color: #155E5B;border-color: #155E5B"  class="btn btn-sm  btn-primary "> <img src="{{asset('admin/images/excel.svg')}}" height="25" ></i>  Descargar plantilla</button></a>
        </div>



    </div>
    </nav>
  </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">
            <div id="modalInformacion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog">
               <div class="modal-content">
                   <div class="modal-header" style="background-color:#163552;">
                       <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Recomendaciones</h5>
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                   </div>
                   <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-12 text-center">
                            <img src="{{asset('landing/images/excelR.PNG')}}" height="100">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <img src="{{asset('landing/images/alert.svg')}}" height="25" class="mr-1"><span>Recuerda que necesitas Habilitar Edición para poder llenar tus datos.</span>
                        </div>

                    </div>
                   </div>
                   <div class="modal-footer">
                       <div class="col-md-12">
                           <div class="row">
                               <div class="col-md-12 text-right">
                                   <button style="background-color: #024079;color: white;" type="button" id="cerrarE" class="btn btn-light" data-dismiss="modal">Entendido</button>
                               </div>
                           </div>
                       </div>
                   </div>
               </div><!-- /.modal-content -->
           </div><!-- /.modal-dialog -->
         </div><!-- /.modal -->
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
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="file" name="file" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-sm" style="background-color: #e1eae5; color: #61886c;"><img src="{{ URL::asset('admin/assets/images/users/importar.png') }}" height="20" class=" mr-2" alt="" />Importar empleados</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body" style="padding-top: 20px;color: #1b1b1b;">
                            <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                            <table id="basic-datatable1" class="table  nowrap" style="font-size: 13px!important">
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
                                        {{-- <th>Ciudad Nac</th> --}}
                                        <th>Departamento Nac.</th>
                                        <th>Provincia Nac.</th>
                                        <th>Distrito Nac.</th>
                                        <th>Sexo</th>
                                    </tr>
                                </thead>
                                <tbody style="background:#f7f7f7;color: #2c2c2c;">
                                @php
                                $empleado = DB::table('empleado as e')
                                ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->leftJoin('tipo_documento as tipoD', 'e.emple_tipoDoc', '=', 'tipoD.tipoDoc_id')
                                ->leftJoin('ubigeo_peru_departments as depar', 'e.emple_departamento', '=', 'depar.id')
                                ->leftJoin('ubigeo_peru_provinces as provi', 'e.emple_provincia', '=', 'provi.id')
                                ->leftJoin('ubigeo_peru_districts as dist', 'e.emple_distrito', '=', 'dist.id')
                                ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                                ->leftJoin('ubigeo_peru_departments as para', 'e.emple_departamentoN', '=', 'para.id')
                                ->leftJoin('ubigeo_peru_provinces as proviN', 'e.emple_provinciaN', '=', 'proviN.id')
                                ->leftJoin('ubigeo_peru_districts as distN', 'e.emple_distritoN', '=', 'distN.id')
                                ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                                ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                                ->select('e.emple_id','p.perso_id','p.perso_nombre','tipoD.tipoDoc_descripcion','e.emple_nDoc','p.perso_apPaterno',
                                'p.perso_apMaterno', 'p.perso_fechaNacimiento' ,'p.perso_direccion','p.perso_sexo',
                                'depar.id as depar','depar.name as deparNom','provi.id as proviId','provi.name as provi','dist.id as distId','dist.name as distNo',
                                'c.cargo_descripcion', 'a.area_descripcion','cc.centroC_descripcion','para.id as iddepaN',
                                'para.name as depaN','proviN.id as idproviN','proviN.name as proviN','distN.id as iddistN',
                                'distN.name as distN','e.emple_id','c.cargo_id','a.area_id', 'cc.centroC_id','e.emple_tipoContrato',
                                'e.emple_local','e.emple_nivel','e.emple_departamento','e.emple_provincia','e.emple_distrito','e.emple_foto as foto',
                                'e.emple_celular','e.emple_telefono','e.emple_fechaIC','e.emple_fechaFC','e.emple_Correo')
                                ->get();
                                @endphp
                                @if(isset($empleado))
                                  @foreach ($empleado as $empleados)
                                  <tr>
                                    <td>{{$empleados->tipoDoc_descripcion}}</td>
                                    <td>{{$empleados->emple_nDoc}}</td>
                                    <td>{{$empleados->perso_nombre}}</td>
                                    <td>{{$empleados->perso_apPaterno}}</td>
                                    <td>{{$empleados->perso_apMaterno}}</td>
                                    <td>{{$empleados->perso_direccion}}</td>
                                    <td>{{$empleados->deparNom}}</td>
                                    <td>{{$empleados->provi}}</td>
                                    <td>{{$empleados->distNo}}</td>
                                    <td>{{$empleados->cargo_descripcion}}</td>
                                    <td>{{$empleados->area_descripcion}}</td>
                                    <td>{{$empleados->centroC_descripcion}}</td>
                                    <td>{{$empleados->perso_fechaNacimiento}}</td>
                                    <td>{{$empleados->depaN}}</td>
                                    <td>{{$empleados->proviN}}</td>
                                    <td>{{$empleados->distN}}</td>
                                    <td>{{$empleados->perso_sexo}}</td>
                                  </tr>
                                  @endforeach
                                 @endif
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


<script>
    $(document).ready(function() {
    $('#basic-datatable1').DataTable( {
        "scrollX": true
    } );
} );

$('#export').click('change',function(){
    $('#modalInformacion').modal('show');
});

</script>

</body>
</html>
