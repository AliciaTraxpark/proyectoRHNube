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
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />

</head>
<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
<style>
    .fc-time{
        display: none;
    }
    .table td{
        border-top: 1px solid #c7c7c7;
    }
    .alert-danger{
    color: #b23232;
    background-color: #f4c8ce;
    border-color: #d77985;
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
        <div class="col-md-4 text-left">
          <h5 style="color: #ffffff">Gestión de empleados</h5>
          <label for="" class="blanco">Validaremos los datos antes de cargarlos  :)</label>
        </div>
        <div class="col-md-6 text-right" style="margin-left: 14%" >

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
                        <div class="col-md-12 text-center">
                            <img src="{{asset('landing/images/alert.svg')}}" height="25" class="mr-1"><span>Recuerda que necesitas Habilitar Edición para poder llenar tus datos.</span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <img src="{{asset('landing/images/excelR.PNG')}}" height="100">
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



                        <div class="card-body" style="padding-top: 10px;padding-bottom: 0px;">
                            <form action="{{ route('importEmpleado') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="col-md-12" style="padding-left: 0px;">
                                    <div class="row">

                                        <div class="col-md-4">
                                            @if (session('alertE'))
                                            <div class="alert alert-danger" style="padding-top: 4px; padding-bottom: 4px;padding-left: 0px;font-size: 16px; font-weight: 700; color: #b23232;
                                            background-color: #f7f6f6;border-color: #f7f6f6;">
                                                {{ session('alertE') }}
                                            </div>
                                           @endif
                                        </div> <div class="col-md-8"></div>
                                        <div class="col-md-12">
                                            @if (session('alert'))
                                            <div class="alert alert-danger">
                                                {{ session('alert') }}
                                            </div>
                                           @endif
                                        </div>
                                        <div class="col-md-12">
                                            @if (session('filas'))
                                            <div class="alert alert-sucess" style="padding-left: 0px; padding-top: 0px; margin-bottom: 0px;">
                                                Se importaron  {{ session('filas') }} registros.
                                            </div>

                                        @endif
                                        </div>

                                        <div class="col-md-6">
                                            <input type="file" name="file" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <button class="btn btn-sm" type="submit" style="background-color: #e1eae5; color: #61886c;"><img src="{{ URL::asset('admin/assets/images/users/importar.png') }}" height="20" class=" mr-2" alt="" />Importar empleados</button>
                                        </div>
                                    </form>
                                        <div class="col-md-3 text-right">

                                            @if (session('empleados'))

                                            <button type="button" id="btnRegistraBD" class="boton btn-sm" onclick="agregar()">Validar y registrar</button>
                                            @endif

                                        </div>
                                    </div>
                                </div>

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
                                        <th>Contrato</th>
                                        <th>Local</th>
                                        <th>Nivel</th>
                                    </tr>
                                </thead>
                                <tbody style="background:#f7f7f7;color: #2c2c2c;">

                                    @if (session('empleados'))
                                    @foreach (session('empleados') as $item)
                                      <tr>
                                        <td>{{$item[0]}}</td>
                                        <td>{{$item[1]}}</td>
                                        <td>{{$item[2]}}</td>
                                        <td>{{$item[3]}}</td>
                                        <td>{{$item[4]}}</td>
                                        <td>{{$item[5]}}</td>
                                        <td>{{$item[6]}}</td>
                                        <td>{{$item[7]}}</td>
                                        <td>{{$item[8]}}</td>
                                        <td>{{$item[9]}}</td>
                                        <td>{{$item[10]}}</td>
                                        <td>{{$item[11]}}</td>
                                        <td>{{$item[12]}}</td>
                                        <td>{{$item[13]}}</td>
                                        <td>{{$item[14]}}</td>
                                        <td>{{$item[15]}}</td>
                                        <td>{{$item[16]}}</td>
                                        <td>{{$item[17]}}</td>
                                        <td>{{$item[18]}}</td>
                                        <td>{{$item[19]}}</td>



                                      </tr>
                                      @endforeach
                                     @endif
                                    </tbody>
                            </table><br>
                            <div class="col-md-12 text-right" style="padding-right: 0px;">
                                <a href="{{('/empleado')}}"><button  class="boton btn btn-default mr-1" > < Continuar </button></a>
                            </div>
                        </div> <!-- end card body-->

                        <div id="cargar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                            <div class="modal-dialog  modal-dialog-centered" >

                            <div class="modal-content" style="background: #f1f2f3;">

                               <div class="modal-body" style="padding-top: 0px;  padding-bottom: 0px;">
                                   <div class="text-center">

                                   <div id="contenido">
                                       <h4>Cargando empleados a la base de datos.</h4>
                                    <img src="{{asset('landing/images/load.gif')}}" height="120" >
                                   </div>

                                   </div>

                               </div>
                           </div><!-- /.modal-content -->
                         </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>


<script>
    $(document).ready(function() {
    $('#basic-datatable1').DataTable( {
        "scrollX": true
    } );
} );
$('#export').click('change',function(){
    $('#modalInformacion').modal('show');
});
function agregar(){
       $('#cargar').modal('show');
    $('#contenido').show();
    /* var cuotaNo = [];
    $('#basic-datatable1 tbody tr').each(function () {
        var $dentro=[]
   cuotaNo.push($(this).find('td').eq(0).html());
    var interes = $(this).find('td').eq(1).html();
    var abonoCapital = $(this).find('td').eq(2).html();
    var valorCuota = $(this).find('td').eq(3).html();
    var saldoCapital = $(this).find('td').eq(4).html();
   });  console.log(cuotaNo); */
   @if(session()->has('empleados'))
   var zoektermen_json;
   var emplead=[];
   zoektermen_json = {!! json_encode(session()->get('empleados'),JSON_FORCE_OBJECT) !!};
                  for(var property in zoektermen_json) {
                    emplead.push({location:zoektermen_json[property],stopover:false});
                    }
    // empleados =JSON.parse(JSON.stringify());
    $.ajax({
            type:"post",
            url:"/importBDExcel",
            data:{emplead:emplead},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                $('#cargar').modal('hide');
                $('#btnRegistraBD').prop('disabled', true);
                $.notify("Empleados registrados", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

            },
            error: function (data) {
                alert('Ocurrio un error');
            }
        });

@endif
}

</script>

</body>
</html>
