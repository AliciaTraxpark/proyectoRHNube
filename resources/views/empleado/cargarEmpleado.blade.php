@php
use Illuminate\Support\Facades\Auth;
use App\User;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Cargar Empleados</title>
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
    <link rel="shortcut icon" href="{{asset('landing/images/ICONO-LOGO-NUBE-RH.ico')}}">

    <!-- App css -->
    <link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />


</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/notification.svg')}}" height="100">
                    <h4 class="text-danger mt-4">Su sesión expiró</h4>
                    <p class="w-75 mx-auto text-muted">Por favor inicie sesión nuevamente.</p>
                    <div class="mt-4">
                        <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i class="uil uil-arrow-right mr-1"></i> Iniciar sesión</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <style>
        body{
            font-family: Poppins, sans-serif;
            padding: 0px !important;
            margin: 0px !important;
        }
        .fc-time {
            display: none;
        }

        .table td {
            border-top: 1px solid #c7c7c7;
        }

        .alert-danger {
            color: #b23232;
            background-color: #f4c8ce;
            border-color: #d77985;
        }

        .btn-rounded {
            border-radius: 1em;
        }
        @media (max-width: 767px){
            .colResp {
                justify-content: center !important;
                padding: 10px 0px !important;
            }
        }

        #navbar{
            padding: 0 40px !important;
            padding-bottom: 8px !important;
        }

        @media (min-width: 768px) {
            footer {
              font-size: 15px;
              color: #555;
              background: #eee;
              text-align: center;
              position: fixed;
              display: block;
              width: 100%;
              bottom: 0;
              margin-top: 0px;
            }
        }

        @media(min-width: 1025px){
            body{
                padding-bottom: 15px !important;
            }
        }

        @media(max-width: 575px){
            .btn_rh{
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
                <div class="col-sm-3 col-md-2 col-xl-2 logo_rh">
                    <div class="navbar-brand-wrapper d-flex w-100 colResp">
                        <a href="{{ route('principal') }}"><img src="{{asset('landing/images/NUBE_SOLA.png')}}" class="" height="69"></a>
                    </div>
                </div>
                <div class="col-sm-6 col-md-8 col-xl-8 text-center">
                    <h5  style="color: #ffffff">Gestión de empleados</h5>
                    <p for="" class="blanco">Validaremos los datos antes de cargarlos :)</p>
                </div>

                <div class="col-sm-3 col-md-2 col-xl-2 text-center">
                    <a href="{{'/export'}}"> <button id="export" style="background-color: #155E5B;border-color: #155E5B" class="btn btn-sm  btn-primary ">
                        <img src="{{asset('admin/images/excel.svg')}}" height="25">Descargar plantilla</button>
                    </a>
                </div>
        </nav>
    </header>

    <div class="content-page" style="margin-top: 20px;margin-left: 0px !important;">
        <div class="content">
            <div id="modalInformacion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row pt-2">
                                <div class="col-md-12 text-center">
                                    <img src="{{asset('landing/images/alert.svg')}}" height="20" class="mr-1">
                                    <span>
                                        Recuerda "Habilitar Edición" para ingresar los datos correspodientes.
                                    </span>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-md-12 text-center">
                                    <img src="{{asset('landing/images/excelR.PNG')}}" height="100">
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-md-12 text-left">
                                    <span>
                                        <img src="{{asset('landing/images/alert1.svg')}}" height="20" class="mr-1">
                                        Los campos en rojo&nbsp;
                                        <img src="{{asset('landing/images/rectangulo.svg')}}" height="18">&nbsp;
                                        son obligatorios.
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button style="background-color: #163552;color: white;" type="button"
                                            id="cerrarE" class="btn btn-light btn-sm" data-dismiss="modal">
                                            Entendido
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->


            <div class="col-xl-12">
                <div class="col-xl-12 ">
                    <div class="card " style="background:#ffffff;">
                        @if ( $errors->any() )
                        <div class="alert alert-danger">
                            @foreach( $errors->all() as $error )<li>{{ $error }}</li>@endforeach
                        </div>
                        @endif

                        <div class="card-body " style="padding-top: 10px;padding-bottom: 10px;">
                            <form action="{{ route('importEmpleado') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="col-md-12" style="padding-left: 0px;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            @if (session('alertE'))
                                                <div class="alert alert-danger" style="padding-top: 4px; padding-bottom: 4px;padding-left: 0px;font-size: 16px; font-weight: 700; color: #b23232; background-color: #f7f6f6;border-color: #f7f6f6;">
                                                    {{ session('alertE') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-8"></div>
                                        <div class="col-md-12">
                                            @if (isset($alert))
                                                <div class="alert alert-danger">
                                                    {{ $alert }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-12">
                                            @if (isset($filas))
                                                <div class="alert alert-info alert-dismissible fade show" role="alert">Se importaron {{$filas }} empleados.
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-12 col-sm-7 col-md-6 mb-2">
                                            <input type="file" name="file" class="form-control">
                                        </div>
                                        <div class="col-12 col-sm-5 col-md-4">
                                            <div class="form-group">
                                                <button class="btn btn-sm btn_rh" type="submit" style="background-color: #e1eae5; color: #61886c;"><img src="{{ URL::asset('admin/assets/images/users/importar.png') }}" height="20" class=" " alt="" />Importar empleados
                                            </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-12 text-right mt-3">
                                @if (isset($empleados))
                                    <button type="button" style="font-size: 12.25px; padding-top: 5.5px; padding-bottom: 4.5px;" id="btnRegistraBD" class="boton btn-sm " onclick="agregar()">Validar y registrar</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12" style="padding: 10px !important; margin: 2px !important; color: #1b1b1b;">
                    <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                        <table id="basic-datatable1" class="table" style="font-size: 13px!important;">
                        <thead style="background: #4C5D73;color: white;">
                            <tr>
                                <th>Tipo de Doc.</th>
                                <th>Nro de documento</th>
                                <th>Código</th>
                                <th>Nombres</th>
                                <th>Ap. Paterno</th>
                                <th>Ap. Materno</th>
                                <th>Correo</th>
                                <th>Celular</th>
                                <th>Género</th>
                                <th>Fecha nacimiento</th>
                                <th>Departamento Nac.</th>
                                <th>Provincia Nac.</th>
                                <th>Distrito Nac.</th>
                                <th>Direccion</th>
                                <th>Departamento</th>
                                <th>Provincia</th>
                                <th>Distrito</th>
                                <th>Contrato</th>
                                <th>Inicio contrato</th>
                                <th>Fin de contrato</th>
                                <th>Dias de notificación</th>
                                <th>Local</th>
                                <th>Nivel</th>
                                <th>Cargo</th>
                                <th>Área</th>
                                <th>Código de centro de costo</th>
                                <th>Centro de costo</th>
                                <th>Condicion de pago</th>
                                <th>Monto de pago</th>
                            </tr>
                        </thead>
                        <tbody style="background:#f7f7f7;color: #2c2c2c;">
                            @if(isset($empleados))
                                @foreach ($empleados as $item)
                                    <tr>
                                        <td>{{$item[0]}}</td>
                                        <td>{{$item[1]}}</td>
                                        <td>{{$item[25]}}</td>
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
                                        <td>{{$item[24]}}</td>
                                        <td>{{$item[26]}}</td>
                                        <td>{{$item[27]}}</td>
                                        <td>{{$item[17]}}</td>
                                        <td>{{$item[18]}}</td>
                                        <td>{{$item[19]}}</td>
                                        <td>{{$item[20]}}</td>
                                        <td>{{$item[28]}}</td>
                                        <td>{{$item[21]}}</td>
                                        <td>{{$item[22]}}</td>
                                        <td>{{$item[23]}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table><br>
                </div> <!-- end card body-->

                <div id="cargar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog  modal-dialog" style="margin-top: 130px">

                        <div class="modal-content" style="background: #f1f2f3;">

                            <div class="modal-body" style="padding-top: 0px;  padding-bottom: 0px;">
                                <div class="text-center">

                                    <div id="contenido">
                                        <h4>Cargando empleados a la base de datos.</h4>
                                        <img src="{{asset('landing/images/load.gif')}}" height="100">
                                    </div>
                                    <div id="cargaCompleta" style="display: none"><br>
                                        <h4><img src="{{asset('landing/images/exito.svg')}}" height="22">&nbsp;¡Carga de
                                            empleados exitosa!</h4>

                                        @if ($usuario==0)
                                        <a href="{{('/empleado')}}">
                                            @else
                                            <a href="{{('/empleados')}}">
                                                @endif
                                                <button class="boton btn btn-default mr-1 btn-sm">&nbsp; Aceptar
                                                    &nbsp;</button></a>
                                            <br><br>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </div> <!-- end card -->
        </div>
    </div>
    <footer class="border-top" style="background:#163552;">
        <div class="col-md-12 text-center"
            style="margin-top: 10px;border-top: 1.5px solid #ded9d9;padding-top: 10px;bottom: 10px;">
            <span style="color: #faf3f3;font-size: 12px!important">
                © <?php echo date("
                    Y" ); ?> - RH nube Corp - USA | Todos los derechos
                reservados &nbsp; |
            </span>
            <a style="font-size: 12px!important; color:#faf3f3;" href="/politicas">Política de privacidad | </a>
            <span style="color: #faf3f3;font-size: 12px!important">Central Perú: 017482415 | +51 914480786 | info@rhnube.com.pe</span>
        </div>
    </footer>

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
        scrollX: true,
        responsive: false,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ ",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": ">",
                "sPrevious": "<"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad"
            }
        },
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
   @if(isset($empleados))
   const empleada = {!! json_encode($empleados) !!};
    console.log(empleada);
   var zoektermen_json;
   var emplead=[];

   zoektermen_json = {!! json_encode($empleados,JSON_FORCE_OBJECT) !!};
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
            $('#contenido').hide();
            $('#cargaCompleta').show();
            $('#btnRegistraBD').prop('disabled', true);
        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
@endif
}

    </script>
    @if (Auth::user())
    <script>
        $(function() {
    setInterval(function checkSession() {
      $.get('/check-session', function(data) {
        // if session was expired
        if (data.guest==false) {
            $('.modal').modal('hide');
           $('#modal-error').modal('show');

        }
      });
    },7202000);
  });
    </script>
    @endif
</body>

</html>
