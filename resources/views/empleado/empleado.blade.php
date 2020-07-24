<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gestion de empleados</title>
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
    <link rel="shortcut icon"
        href="https://rhsolution.com.pe/wp-content/uploads/2019/06/small-logo-rh-solution-64x64.png" sizes="32x32">

    <!-- App css -->
    <link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.css')}}" rel="stylesheet"
        type="text/css" />

    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
     <!-- Plugin css  CALENDAR-->
 <link href="{{asset('admin/packages/core/main.css')}}" rel="stylesheet" />
 <link href="{{asset('admin/packages/daygrid/main.css')}}" rel="stylesheet" />
 <link href="{{asset('admin/packages/timegrid/main.css')}}" rel="stylesheet" />

    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard_theme_arrows.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard_theme_circles.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard_theme_dots.min.css')}}" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100" style="background-color: #fdfdfd;">
    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/notification.svg')}}" height="100">
                    <h4 class="text-danger mt-4">Su sesion expiró</h4>
                    <p class="w-75 mx-auto text-muted">Por favor inicie sesion nuevamente.</p>
                    <div class="mt-4">
                        <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i
                                class="uil uil-arrow-right mr-1"></i> Iniciar sesion</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <style>
        .container {
            margin-left: 40px;
            margin-right: 28px;
        }
        #calendarInv > div.fc-view-container > div > table > tbody{
        background: #f4f4f4;
    }
    .fc-event, .fc-event-dot {
    font-size: 12.2px!important;
    margin: 2px 2px;
}
.flatpickr-calendar{
        width: 220px!important;
    }

        .fc-time {
            display: none;
        }

        .v-divider {
            border-right: 5px solid #4C5D73;
        }

        .table th,
        .table td {
            padding: 0.55rem;

            border-top: 1px solid #c9c9c9;

        }

        .sw-theme-default>ul.step-anchor>li.active>a {
            color: #1c68b1 !important;
        }

        .sw-theme-default>ul.step-anchor>li.done>a,
        .sw-theme-default>ul.step-anchor>li>a {
            color: #0b1b29 !important;
        }

        .combodate {
            display: flex;
            justify-content: space-between;
        }

        .day {
            max-width: 30%;
        }

        .month {
            max-width: 35%;
        }

        .year {
            max-width: 40%;
        }

        .btn-group {
            width: 100%;
            justify-content: space-between;
        }

        .btn-secondary {
            max-width: 9em;
        }

        .form-control:disabled {
            background-color: #fcfcfc;
        }

        body {
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
                <div class="col-md-2 col-xl-2">
                    <div class="navbar-brand-wrapper d-flex w-100">
                        <img src="{{asset('landing/images/logo.png')}}" height="100">
                    </div>
                </div>
                <div class="col-md-7 col-xl-7 text-left">
                    <h5 style="color: #ffffff">Gestión de empleados</h5>
                    <label for="" class="blanco font-italic">Tienes 2 minutos para registrar tu primer empleado</label>
                </div>
                <div class=" col-md-2 col-xl-2">
                    <a href="{{('/empleado/cargar')}}"> <button class="btn btn-sm btn-primary"
                            style="background-color: #183b5d;border-color:#62778c"><img
                                src="{{asset('admin/images/subir.ico')}}" height="25" class="mr-1">Carga
                            masiva</button></a>
                </div>
                <div class=" col-md-3 col-xl-3">
                    <button class="btn btn-sm btn-primary" style="background-color: #183b5d;border-color:#62778c"
                        id="cargaMasivaF"><img src="{{asset('admin/images/image.ico')}}" height="25" class="mr-1">Carga
                        masiva fotos</button>
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

                                    <button id="" style="background-color: #183b5d;border-color:#62778c"
                                        onclick="eliminarEmpleado()" class="btn btn-sm btn-primary delete_all"
                                        data-url="">Eliminar seleccion
                                    </button>
                                    <button class="btn btn-sm btn-primary" id="formNuevoEd"
                                        style="background-color: #183b5d;border-color:#62778c">Editar</button>
                                    <button class="btn btn-sm btn-primary" id="formNuevoE"
                                        style="background-color: #183b5d;border-color:#62778c">Nuevo</button>
                                </div>
                            </div>
                            <div id="espera" class="text-center" style="display: none">

                                <img src="{{asset('landing/images/loading.gif')}}" height="100">
                            </div>
                            <div id="tabladiv">
                            </div>
                            <div class="text-right"><br><br>
                                <a href="{{('/horario')}}"><button
                                        class="boton btn btn-default mr-1">CONTINUAR</button></a>
                            </div>
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div>
                <div id="modalInformacionF" class="modal fade" tabindex="-1" role="dialog"
                    aria-labelledby="modalInformacionF" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <span class="mr-2 text-center">Despues de haber registrado o cargado tus empleados,
                                        puedes cargar sus fotos de una manera más simple.</span>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-xl-5 text-center">
                                        <img src="{{asset('landing/images/photo (3).svg')}}" height="100">
                                        <br>
                                        <span class="mr-2 text-center" style="color: #024079;font-weight: bold;">DNI
                                            Empleado</span>
                                    </div>
                                    <div class="col-xl-2 text-left">
                                        <img src="{{asset('landing/images/right-arrow.svg')}}" height="80">
                                    </div>
                                    <div class="col-xl-5 text-center">
                                        <img src="{{asset('landing/images/photo (3).svg')}}" height="100">
                                        <br>
                                        <span class="mr-2 text-center"
                                            style="color: #024079;font-weight: bold;">12345678</span>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <img src="{{asset('landing/images/alert.svg')}}" height="25"
                                            class="mr-1"><span>Puedes guardar tus fotos en una carpeta
                                            especifica.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <button style="background-color: #024079;color: white;" type="button"
                                                id="cerrarIF" class="btn btn-light"
                                                data-dismiss="modal">Entendido</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="modalMasivaFoto" class="modal fade" tabindex="-1" role="dialog"
                    aria-labelledby="modalMasivaFoto" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Carga
                                    Masiva de Fotos a
                                    Empleados</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <br>
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <div class="file-loading">
                                            {{ csrf_field() }}
                                            <input id="fileMasiva" name="fileMasiva[]" type="file" multiple
                                                webkitdirectory accept="image/*">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <button type="button" id="cerrarMFotos" class="btn btn-light"
                                                data-dismiss="modal"
                                                style="background-color:#163552;color:#ffffff;">Finalizar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="modalEliminar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalEliminar"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Eliminar
                                    empleado</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="form-horizontal">
                                    <h5 class="modal-title" id="myModalLabel" style="font-size: 15px">¿Desea eliminar al
                                        empleado?</h5>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-7 text-right">
                                            <button type="button" id="cerrarE" class="btn btn-light btn-sm"
                                                data-dismiss="modal">Cancelar</button>
                                        </div>
                                        <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                                            <button type="button" id="confirmarE" name="confirmarE"
                                                onclick="confirmarEliminacion()" style="background-color: #163552;"
                                                class="btn btn-sm ">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="areamodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="areamodal"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    área</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarArea()">
                                    {{ csrf_field() }}
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="">Área</label>
                                            </div>
                                            <div id="editarArea" class="col-md-6"></div>
                                            <div class="col-md-4">
                                                <a id="buscarArea" data-toggle="tooltip" data-placement="right"
                                                    title="Editar Área." data-original-title="Editar Área."
                                                    style="cursor: pointer;"><img
                                                        src="{{asset('landing/images/search.svg')}}" height="18">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="textArea" id="textArea" required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" id="guardarArea" class="btn btn-sm"
                                    style="background-color: #163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="cargomodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cargomodal"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Cargo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarcargo()">
                                    {{ csrf_field() }}
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="">Cargo</label>
                                            </div>
                                            <div id="editarCargo" class="col-md-6"></div>
                                            <div class="col-md-4">
                                                <a id="buscarCargo" data-toggle="tooltip" data-placement="right"
                                                    title="Editar Cargo." data-original-title="Editar Cargo."
                                                    style="cursor: pointer;"><img
                                                        src="{{asset('landing/images/search.svg')}}" height="18">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" id="textCargo" required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm" id="guardarCargo"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="centrocmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="centrocmodal"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Centro Costo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarcentro()">
                                    {{ csrf_field() }}
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">Centro Costo</label>
                                            </div>
                                            <div id="editarCentro" class="col-md-6"></div>
                                            <div class="col-md-3">
                                                <a id="buscarCentro" data-toggle="tooltip" data-placement="right"
                                                    title="Editar Centro Costo."
                                                    data-original-title="Editar Centro Costo."
                                                    style="cursor: pointer;"><img
                                                        src="{{asset('landing/images/search.svg')}}" height="18">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" id="textCentro" required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm" id="guardarCentro"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="localmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="localmodal"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Local</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarlocal()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="">Local</label>
                                        </div>
                                        <div id="editarLocal" class="col-md-6"></div>
                                        <div class="col-md-4">
                                            <a id="buscarLocal" data-toggle="tooltip" data-placement="right"
                                                title="Editar Local." data-original-title="Editar Local."
                                                style="cursor: pointer;"><img
                                                    src="{{asset('landing/images/search.svg')}}" height="18">
                                            </a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="textLocal" id="textLocal"
                                            required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" id="guardarLocal" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="nivelmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="nivelmodal"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Nivel</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarnivel()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="">Nivel</label>
                                        </div>
                                        <div id="editarNivel" class="col-md-6"></div>
                                        <div class="col-md-4">
                                            <a id="buscarNivel" data-toggle="tooltip" data-placement="right"
                                                title="Editar Nivel." data-original-title="Editar Nivel."
                                                style="cursor: pointer;"><img
                                                    src="{{asset('landing/images/search.svg')}}" height="18">
                                            </a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="textNivel" id="textNivel"
                                            required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" id="guardarNivel" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
                <div id="contratomodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="contratomodal"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Contrato</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarContrato()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="">Contrato</label>
                                        </div>
                                        <div id="editarContrato" class="col-md-6"></div>
                                        <div class="col-md-4">
                                            <a id="buscarContrato" data-toggle="tooltip" data-placement="right"
                                                title="Editar Contrato." data-original-title="Editar Contrato."
                                                style="cursor: pointer;"><img
                                                    src="{{asset('landing/images/search.svg')}}" height="18">
                                            </a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="textContrato" id="textContrato"
                                            required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" id="guardarContrato" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
                <div id="fechasmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fechasmodal"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Indicar
                                    fechas de Contrato</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-registrar').modal('show');">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarFechas()">
                                    {{ csrf_field() }}
                                    <div class="col-md-12">
                                        <label for="">Fecha Inicial</label>
                                        <input type="text" data-custom-class="form-control" id="m_fechaI"
                                            data-format="YYYY-MM-DD" data-template="D MMM YYYY" name="date" required>
                                        <br>
                                        <div class="form-group">
                                            <label for="">Fecha Indifinida</label>
                                            <input type="checkbox" id="checkboxFechaI" name="FechaI">
                                        </div>
                                        <div id="ocultarFecha">
                                            <label id="labelfechaF">Fecha Final</label>
                                            <input type="text" data-custom-class="form-control" id="m_fechaF"
                                                data-format="YYYY-MM-DD" data-template="D MMM YYYY" name="date">
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-registrar').modal('show');"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <!-- /.modal -->
                <!-----Modales Editar-->
                <div id="areamodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="areamodalE"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    área</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarAreaA()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="">Área</label>
                                        </div>
                                        <div id="editarAreaA" class="col-md-6"></div>
                                        <div class="col-md-4">
                                            <a id="buscarAreaA" data-toggle="tooltip" data-placement="right"
                                                title="Editar Area." data-original-title="Editar Area."
                                                style="cursor: pointer;"><img
                                                    src="{{asset('landing/images/search.svg')}}" height="18">
                                            </a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="textAreaE" id="textAreaE"
                                            required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="cargomodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cargomodalE"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Cargo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarcargoA()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="">Cargo</label>
                                        </div>
                                        <div id="editarCargoA" class="col-md-6"></div>
                                        <div class="col-md-4">
                                            <a id="buscarCargoA" data-toggle="tooltip" data-placement="right"
                                                title="Editar Cargo." data-original-title="Editar Cargo."
                                                style="cursor: pointer;"><img
                                                    src="{{asset('landing/images/search.svg')}}" height="18">
                                            </a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" id="textCargoE" required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="centrocmodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="centrocmodalE"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Centro Costo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarcentroA()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Centro Costo</label>
                                        </div>
                                        <div id="editarCentroA" class="col-md-6"></div>
                                        <div class="col-md-3">
                                            <a id="buscarCentroA" data-toggle="tooltip" data-placement="right"
                                                title="Editar Centro Costo." data-original-title="Editar Centro Costo."
                                                style="cursor: pointer;"><img
                                                    src="{{asset('landing/images/search.svg')}}" height="18">
                                            </a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" id="textCentroE" required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="localmodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="localmodalE"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Local</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarlocalA()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="">Local</label>
                                        </div>
                                        <div id="editarLocalA" class="col-md-6"></div>
                                        <div class="col-md-4">
                                            <a id="buscarLocalA" data-toggle="tooltip" data-placement="right"
                                                title="Editar Local." data-original-title="Editar Local."
                                                style="cursor: pointer;"><img
                                                    src="{{asset('landing/images/search.svg')}}" height="18">
                                            </a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="textLocalE" id="textLocalE"
                                            required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="nivelmodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="nivelmodalE"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Nivel</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarnivelA()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="">Nivel</label></div>
                                        <div id="editarNivelA" class="col-md-6"></div>
                                        <div class="col-md-4">
                                            <a id="buscarNivelA" data-toggle="tooltip" data-placement="right"
                                                title="Editar Nivel." data-original-title="Editar Nivel."
                                                style="cursor: pointer;"><img
                                                    src="{{asset('landing/images/search.svg')}}" height="18">
                                            </a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="textNivelE" id="textNivelE"
                                            required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
                <div id="contratomodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="contratomodalE"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                                    Contrato</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarContratoA()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="">Contrato</label>
                                        </div>
                                        <div id="editarContratoA" class="col-md-6"></div>
                                        <div class="col-md-4">
                                            <a id="buscarContratoA" data-toggle="tooltip" data-placement="right"
                                                title="Editar Contrato." data-original-title="Editar Contrato."
                                                style="cursor: pointer;"><img
                                                    src="{{asset('landing/images/search.svg')}}" height="18">
                                            </a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="textAreaE" id="textContratoE"
                                            required>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
                <div id="fechasmodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fechasmodalE"
                    aria-hidden=" true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Indicar
                                    fechas de Contrato</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="$('#form-ver').modal('show');">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:agregarFechasA()">
                                    {{ csrf_field() }}
                                    <div class="col-md-12">
                                        <label for="">Fecha Inicial</label>
                                        <input type="text" data-custom-class="form-control" id="m_fechaIE"
                                            data-format="YYYY-MM-DD" data-template="D MMM YYYY" name="date" required>
                                        <br>
                                        <div class="form-group">
                                            <label for="">Fecha Indifinida</label>
                                            <input type="checkbox" id="checkboxFechaIE" name="FechaIE">
                                        </div>
                                        <div id="ocultarFechaE">
                                            <label for="">Fecha Final</label>
                                            <input type="text" data-custom-class="form-control" id="m_fechaFE"
                                                data-format="YYYY-MM-DD" data-template="D MMM YYYY" name="date"
                                                required>
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#form-ver').modal('show');"
                                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-sm"
                                    style="background-color:#163552;">Guardar</button>
                            </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <!-- /.modal -->
                <!---->
                <div class="modal fade" style="font-size: 13px" id="form-registrar" tabindex="-1" role="dialog"
        aria-labelledby="form-registrar" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #163552;">
                    <h4 class="header-title mt-0 " style="color: #f0f0f0"></i>Datos de empleado</h4>
                    <button type="button" class="close" id="cerrarModalEmpleado" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 0px">
                    <div class="setup-panel" id="smartwizard" style="background: #ffffff; color:#3d3d3d;">
                        <ul style="background: #fdfdfd!important;">
                            <li><a href="#sw-default-step-1">Personales</a></li>
                            <li><a href="#sw-default-step-2">Empresarial</a></li>
                            <li><a href="#sw-default-step-3">Foto</a></li>
                            <li><a href="#sw-default-step-4">Calendario</a></li>
                            <li><a href="#sw-default-step-5">Horario</a></li>

                        </ul>
                        <div class="p-3">
                            <div id="sw-default-step-1" class="setup-content">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Tipo Documento</label>
                                            <span id="validDocumento" style="color: red;">*Campo
                                                Obligatorio</span>
                                            <select class="form-control" placeholder="Tipo Documento " name="documento"
                                                id="documento" tabindex="1" required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($tipo_doc as $tipo_docs)
                                                <option class="" value="{{$tipo_docs->tipoDoc_id}}">
                                                    {{$tipo_docs->tipoDoc_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Apellido Paterno</label>
                                            <span id="validApPaterno" style="color: red;">*Campo
                                                Obligatorio</span>
                                            <input type="text" class="form-control" name="apPaterno" id="apPaterno"
                                                tabindex="4" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Correo Electronico</label>
                                            <span id="validCorreo" style="color: red;">*Campo
                                                Obligatorio</span>
                                            <span id="emailR" style="color: red;">*Correo registrado</span>
                                            <input type="email" class="form-control" id="email" name="email"
                                                tabindex="7">
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Num. Documento</label>
                                            <span id="validNumDocumento" style="color: red;">*Campo
                                                Obligatorio</span>
                                            <span id="numR" style="color: red;">*Num. registrado</span>
                                            <input type="text" class="form-control" name="numDocumento"
                                                id="numDocumento" tabindex="2" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Apellido Materno</label>
                                            <span id="validApMaterno" style="color: red;">*Campo
                                                Obligatorio</span>
                                            <input type="text" class="form-control" name="apMaterno" id="apMaterno"
                                                tabindex="5" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Celular</label>
                                            <span id="validCel" style="color: red;">*Número
                                                incorrecto.</span>
                                            <div class="row">
                                                <div class="col-4">
                                                    <select class="form-control" id="codigoCelular">
                                                        <option value="+51" selected>+51</option>
                                                    </select>
                                                </div>
                                                <div class="col-8">
                                                    <input type="number" class="form-control" name="celular"
                                                        id="celular" tabindex="8" maxlength="9"
                                                        onkeypress="return isNumeric(event)"
                                                        oninput="maxLengthCheck(this)" pattern="/^9{1}|[0-9]{8,8}+">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Fecha Nacimiento</label>
                                            <input type="text" data-custom-class="form-control" id="fechaN" tabindex="3"
                                                data-format="YYYY-MM-DD" data-template="D MMM YYYY" name="date">
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Nombres</label>
                                            <span id="validNombres" style="color: red;">*Campo
                                                Obligatorio</span>
                                            <input type="text" class="form-control" name="nombres" id="nombres"
                                                tabindex="6" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Telefono</label>
                                            <input type="number" class="form-control" name="telefono" id="telefono"
                                                tabindex="9" maxlength="9" onkeypress="return isNumeric(event)"
                                                oninput="maxLengthCheck(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="sw-default">Dirección</label>
                                            <input type="text" class="form-control" name="direccion" id="direccion"
                                                tabindex="10" required>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Dirección Domiciliara</label>
                                            <select class="form-control" placeholder="Departamento" name="departamento"
                                                id="dep" tabindex="11" required>
                                                <option value="">Departamento</option>
                                                @foreach ($departamento as $departamentos)
                                                <option class="" value="{{$departamentos->id}}">
                                                    {{$departamentos->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Lugar Nacimiento</label>
                                            <select class="form-control" placeholder="Departamento" name="departamento"
                                                id="departamento" tabindex="14" required>
                                                <option value="">Departamento</option>
                                                @foreach ($departamento as $departamentos)
                                                <option class="" value="{{$departamentos->id}}">
                                                    {{$departamentos->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="normal" for="">Genero</label>
                                            <span id="validGenero" style="color: red;">*Campo
                                                Obligatorio</span>
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="tipo" id="tipo" value="Femenino" required>
                                                Femenino
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default"><br></label>
                                            <select class="form-control " placeholder="Provincia " name="provincia"
                                                id="prov" tabindex="12" required>
                                                <option value="">Provincia</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default"><br></label>
                                            <select class="form-control " placeholder="Provincia " name="provincia"
                                                id="provincia" tabindex="15" required>
                                                <option value="">Provincia</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="normal" for=""><br></label>
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="tipo" id="tipo" value="Masculino" required>
                                                Masculino
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default"><br></label>
                                            <select class="form-control " placeholder="Distrito " name="distrito"
                                                id="dist" tabindex="13" required>
                                                <option value="">Distrito</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default"><br></label>
                                            <select class="form-control " placeholder="Distrito " name="distrito"
                                                id="distrito" tabindex="16" required>
                                                <option value="">Distrito</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="normal" for=""><br></label>
                                            <label class="custom-control custom-radio" data-toggle="tooltip"
                                                data-placement="right" title=""
                                                data-original-title="Puedes elegir personalizado si no deseas especificar tu sexo.">
                                                <input type="radio" name="tipo" id="tipo" value="Personalizado"
                                                    required>
                                                Personalizado
                                            </label>
                                        </div>
                                    </div>
                                </div> <!-- end row -->
                            </div>
                            <div id="sw-default-step-2" class="setup-content">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Codigo Empleado</label>
                                            <input type="text" class="form-control" name="codigoEmpleado"
                                                id="codigoEmpleado" tabindex="1"
                                                onfocus="javascript:valorCodigoEmpleado()" data-toggle="tooltip"
                                                data-placement="right"
                                                title="Número de documento por defecto o Ingrese un código interno"
                                                data-original-title="Número de documento por defecto o Ingrese un código interno">
                                        </div>
                                    </div>
                                    <div class="col-4"><br></div>
                                    <div class="col-4"><br></div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Cargo <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#cargomodal"
                                                    data-toggle="modal" data-target="#cargomodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="cargo" id="cargo" tabindex="2">
                                                <option value="">Seleccionar</option>
                                                @foreach ($cargo as $cargos)
                                                <option class="" value="{{$cargos->cargo_id}}">
                                                    {{$cargos->cargo_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Contrato <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#contratomodal"
                                                    data-toggle="modal" data-target="#contratomodal"><i
                                                        class="uil uil-plus"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a id="detalleContrato" onclick="$('#form-registrar').modal('hide');"
                                                    href="#fechasmodal" data-toggle="modal" data-target="#fechasmodal"
                                                    data-toggle="tooltip" data-placement="right"
                                                    title="Detalle de Contrato."
                                                    data-original-title="Detalle de Contrato."
                                                    style="cursor: pointer;"><img
                                                        src="{{asset('landing/images/adaptive.svg')}}"
                                                        height="18"></a></label>
                                            <select class="form-control" name="contrato" id="contrato"
                                                onchange="$('#detalleContrato').show();" tabindex="5" required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($tipo_cont as $tipo_conts)
                                                <option value="{{$tipo_conts->contrato_id}}">
                                                    {{$tipo_conts->contrato_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!--<div class="form-group">
                                            <label for="sw-default" style="color: darkblue;">Fecha Inicio
                                                <label for="sw-default" id="c_fechaI"></label></label>
                                            <label for="sw-default" style="color: red;">Fecha Final <label
                                                    for="sw-default" id="c_fechaF"></label></label>
                                        </div>-->
                                    </div> <!-- end col -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Área <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#areamodal"
                                                    data-toggle="modal" data-target="#areamodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="area" id="area" tabindex="3">
                                                <option value="">Seleccionar</option>
                                                @foreach ($area as $areas)
                                                <option class="" value="{{$areas->area_id}}">
                                                    {{$areas->area_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Nivel <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#nivelmodal"
                                                    data-toggle="modal" data-target="#nivelmodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="nivel" id="nivel" tabindex="6">
                                                <option value="">Seleccionar</option>
                                                @foreach ($nivel as $niveles)
                                                <option class="" value="{{$niveles->nivel_id}}">
                                                    {{$niveles->nivel_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Centro Costo <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#centrocmodal"
                                                    data-toggle="modal" data-target="#centrocmodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="centroc" id="centroc" tabindex="4">
                                                <option value="">Seleccionar</option>
                                                @foreach ($centro_costo as $centro_costos)
                                                <option class="" value="{{$centro_costos->centroC_id}}">
                                                    {{$centro_costos->centroC_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Local <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#localmodal"
                                                    data-toggle="modal" data-target="#localmodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="local" id="local" tabindex="7">
                                                <option value="">Seleccionar</option>
                                                @foreach ($local as $locales)
                                                <option class="" value="{{$locales->local_id}}">
                                                    {{$locales->local_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->

                            </div>
                            <div id="sw-default-step-3" class="setup-content">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <input type="file" name="file" id="file" accept="image/*">
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->

                            </div>
                            <div id="sw-default-step-4" class="setup-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label style="font-weight: 600;font-size: 14px;"  class="col-lg-5 col-form-label text-right"
                                                for="simpleinput">Calendario de empleado:</label>
                                            <div class="col-lg-5">
                                                <select name="" id="selectCalendario" class="form-control form-control-sm" style="margin-top: 4px;">
                                                    <option hidden selected>Asignar calendario</option>
                                                    @foreach ($calendario as $calendarios)
                                                        <option class="" value="{{$calendarios->calen_id}}">{{$calendarios->calendario_nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>



                                    </div>
                                    <div class="col-md-1"><br></div>
                                    <div class="col-md-10" id="calendarInv"></div> <input type="hidden" id="pruebaEnd"> <input type="hidden" id="pruebaStar">
                                    <div class="col-md-10" id="calendar" style="display: none"></div>
                                    <div class="col-md-1"><br></div>
                                    <div id="calendarioAsignar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                                        <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width:580px;  margin-top: 150px; left: 30px;" >

                                        <div class="modal-content">

                                           <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
                                             <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                     <button type="button" style=" max-width: 18em!important;" class="btn btn-secondary btn-sm" onclick="laborableTem()" ><img src="{{asset('admin/images/calendariolab.svg')}}" height="20">  Dia laborable</button>
                                                    </div>
                                                    <div class="col-md-4">
                                                     <button type="button" style=" max-width: 18em!important;" class="btn btn-secondary btn-sm" onclick="nolaborableTem()" ><img src="{{asset('admin/images/evento.svg')}}" height="20">  Dia no laborable</button>
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                    {{--  <button type="button" style=" max-width: 18em!important;" class="btn btn-secondary btn-sm " onclick="registrarDdescanso()" ><img src="{{asset('admin/images/calendarioInc.svg')}}" height="20"> Incidencia</button> --}}
                                                     <button style=" max-width: 18em!important;" class="btn btn-secondary btn-sm" onclick="agregarinciden()"><img src="{{asset('admin/images/calendarioInc.svg')}}" height="20"> Incidencia</button>
                                                    </div>
                                                </div>
                                             </div>

                                           </div>
                                           <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;background: #f1f0f0;">
                                               <div class="col-md-12">
                                                   <div class="row">
                                                       <div class="col-md-12 text-right" >
                                                        <button type="button"  class="btn btn-soft-primary btn-sm " onclick="$('#calendarioAsignar').modal('hide')">Cancelar</button>

                                                    </form>
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                       </div><!-- /.modal-content -->
                                     </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                </div>
                            </div>

                            <div id="sw-default-step-5" class="setup-content">
                                <div class="row">
                                    <div class="col-md-12 text-center" id="detallehorario" ></div>
                                    <div class="col-md-1"><br></div>
                                    <div class="col-md-10" id="mensajeOc"><label for="">Aún no ha seleccionado un calendario en el paso anterior.</label></div>
                                   <div class="col-md-10" id="calendar2" style="display: none"></div>
                                   <div class="col-md-1"><br></div>
                                </div>
                                <div class="row">
                                    <br><br>
                                    <div class="col-md-12  text-right">
                                        <button type="button" id="guardarEmpleado"
                                            class="btn btn-light">Guardar</button>
                                    </div>
                                </div>
                                <div id="horarioAsignar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                                    <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width:330px;  margin-top: 150px; left: 30px;" >

                                    <div class="modal-content">

                                       <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
                                         <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <select class="form-control custom-select custom-select-sm" name="selectHorario" id="selectHorario">
                                                        <option hidden selected>Seleccionar horario</option>
                                                        @foreach ($horario as $horarios)
                                                        <option class="" value="{{$horarios->horario_id}}">{{$horarios->horario_descripcion}}</option>
                                                         @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <button class="btn btn-primary btn-sm" style="background-color: #183b5d;border-color:#62778c" onclick="abrirHorario()">+</button>
                                                </div>

                                            </div>

                                         </div>

                                       </div>
                                       <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;background: #f1f0f0;">
                                           <div class="col-md-12">
                                               <div class="row">
                                                   <div class="col-md-12 text-right" >
                                                    <button type="button"  class="btn btn-soft-primary btn-sm " onclick="$('#horarioAsignar').modal('hide')">Cancelar</button>

                                                </form>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div><!-- /.modal-content -->
                                 </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                                <div id="horarioAgregar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                                    <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;" >

                                    <div class="modal-content">
                                       <div class="modal-header" style="background-color:#163552;">
                                           <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar horario</h5>
                                           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                               <span aria-hidden="true">&times;</span>
                                           </button>
                                       </div>
                                       <div class="modal-body" style="font-size:12px!important">
                                           <div class="row">

                                               <div class="col-md-12">
                                                <form id="frmHor" action="javascript:registrarHorario()">
                                                   <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                           <label for="">Tipo de horario:</label>
                                                           <select class="form-control custom-select custom-select-sm" id="tipHorario">
                                                             <option>Normal</option>
                                                             <option>Guardía</option>
                                                             <option>Nocturno</option>
                                                           </select>
                                                        </div>
                                                      </div>
                                                    <div class="col-md-6"><label for=""><br></label>
                                                        <div class="form-check">

                                                          <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                                          <label class="form-check-label" for="exampleCheck1">Aplicar sobretiempo</label>
                                                        </div>
                                                     </div>

                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                           <label for="">Descripcion:</label>
                                                           <input type="text" class="form-control form-control-sm" id="descripcionCa" required>
                                                        </div>
                                                     </div>
                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                           <label for="">Tolerancia(Min):</label>
                                                           <input type="number" value="0" class="form-control form-control-sm" min="0" id="toleranciaH" required>
                                                        </div>
                                                     </div>
                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                           <label for="">Hora de inicio(24h):</label>
                                                           <input type="text" id="horaI" class="form-control form-control-sm" required>
                                                        </div>
                                                     </div>
                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                           <label for="">Hora de fin(24h):</label>
                                                           <input type="text" id="horaF" class="form-control form-control-sm" required>
                                                        </div>
                                                     </div>


                                                   </div>

                                               </div>



                                           </div>

                                       </div>
                                       <div class="modal-footer">
                                           <div class="col-md-12">
                                               <div class="row">
                                                   <div class="col-md-12 text-right" >
                                                    <button type="button"  class="btn btn-light btn-sm " data-dismiss="modal">Cancelar</button>
                                                    <button type="submit"  name="" style="background-color: #163552;" class="btn btn-sm ">Guardar</button>
                                                </form>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div><!-- /.modal-content -->
                                 </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                <div class="modal fade" id="form-ver" style="font-size: 13px" tabindex="-1" role="dialog"
                    aria-labelledby="form-ver" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background: #163552;">
                                <h4 class="header-title mt-0 " style="color: #f0f0f0">Datos de empleado</h4><br>
                                <button type="button" class="close" id="cerrarEd" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" style="padding: 0px;">
                                <div id="smartwizard1" style="background: #ffffff; color:#3d3d3d;">
                                    <ul style="background: #fdfdfd!important;">
                                        <li><a href="#persona-step-1">Personales</a></li>
                                        <li><a href="#sw-default-step-2">Empresarial</a></li>
                                        <li><a href="#sw-default-step-3">Foto</a></li>
                                        <!--<div class="col-md-4 text-left" id="navActualizar" style="display: flex;
                                        align-items: center;cursor: pointer;"><a style="color: #3d3d3d;"
                                                id="actualizarEmpleado">
                                                <img src="{{asset('admin/images/processing.svg')}}" height="18">
                                                <span style="font-weight: 600">Actualizar Empleado</span></i></a>-->
                                    </ul>
                                    <div class="p-3" id="form-registrar">
                                        <div id="persona-step-1">
                                            <div class="row">
                                                <div class="col-4">
                                                    <input style="display: none;" name="v_id" id="v_id">
                                                    <div class="form-group">
                                                        <label for="sw-default">Tipo Documento</label>
                                                        <input type="text" class="form-control" name="v_tipoDoc"
                                                            id="v_tipoDoc" disabled style="background-color: #fcfcfc;"
                                                            tabindex="1">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Apellido Paterno</label>
                                                        <span id="v_validApPaterno" style="color: red;">*Campo
                                                            Obligatorio</span>
                                                        <input type="text" class="form-control" name="v_apPaterno"
                                                            id="v_apPaterno" tabindex="4" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Correo Electronico</label>
                                                        <span id="v_validCorreo" style="color: red;">*Campo
                                                            Obligatorio</span>
                                                        <span id="v_emailR" style="color: red;">*Correo
                                                            registrado</span>
                                                        <input type="email" class="form-control" id="v_email"
                                                            name="email" tabindex="7">
                                                    </div>
                                                </div> <!-- end col -->
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="sw-default">Num. Documento</label>
                                                        <span id="v_validNumDocumento" style="color: red;">*Campo
                                                            Obligatorio</span>
                                                        <input type="text" class="form-control" name="v_numDocumento"
                                                            id="v_numDocumento" required disabled
                                                            style="background-color: #fcfcfc;" tabindex="2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Apellido Materno</label>
                                                        <span id="v_validApMaterno" style="color: red;">*Campo
                                                            Obligatorio</span>
                                                        <input type="text" class="form-control" name="v_apMaterno"
                                                            id="v_apMaterno" tabindex="5" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Celular</label>
                                                        <span id="v_validCel" style="color: red;">*Número
                                                            incorrecto.</span>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <select class="form-control" id="v_codigoCelular">
                                                                    <option value="+51" selected>+51</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-8">
                                                                <input type="text" class="form-control" name="v_celular"
                                                                    id="v_celular" tabindex="8" maxlength="9"
                                                                    onkeypress="return isNumeric(event)"
                                                                    oninput="maxLengthCheck(this)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="sw-default">Fecha Nacimiento</label>
                                                        <input type="text" data-custom-class="form-control"
                                                            id="v_fechaN" data-format="YYYY-MM-DD"
                                                            data-template="D MMM YYYY" name="date" tabindex="3">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Nombres</label>
                                                        <span id="v_validNombres" style="color: red;">*Campo
                                                            Obligatorio</span>
                                                        <input type="text" class="form-control" name="v_nombres"
                                                            id="v_nombres" tabindex="6" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Telefono</label>
                                                        <input type="text" class="form-control" name="v_telefono"
                                                            id="v_telefono" tabindex="9" maxlength="9"
                                                            onkeypress="return isNumeric(event)"
                                                            oninput="maxLengthCheck(this)">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="sw-default">Dirección</label>
                                                        <input type="text" class="form-control" name="v_direccion"
                                                            id="v_direccion" tabindex="10" required>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="sw-default">Dirección Domiciliara</label>
                                                        <select class="form-control" placeholder="Departamento"
                                                            name="v_dep" id="v_dep" tabindex="11" required>
                                                            <option value="">Departamento</option>
                                                            @foreach ($departamento as $departamentos)
                                                            <option class="" value="{{$departamentos->id}}">
                                                                {{$departamentos->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Lugar Nacimiento</label>
                                                        <select class="form-control" placeholder="Departamento"
                                                            name="v_departamento" id="v_departamento" tabindex="14">
                                                            <option value="">Departamento</option>
                                                            @foreach ($departamento as $departamentos)
                                                            <option class="" value="{{$departamentos->id}}">
                                                                {{$departamentos->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="normal" for="">Genero</label>
                                                        <label class="custom-control custom-radio">
                                                            <input type="radio" name="v_tipo" id="v_tipo"
                                                                value="Femenino">
                                                            Femenino
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="sw-default"><br></label>
                                                        <select class="form-control " placeholder="Provincia "
                                                            name="v_prov" id="v_prov" tabindex="12" required>
                                                            <option value="">Provincia</option>
                                                            @foreach ($provincia as $provincias)
                                                            <option class="" value="{{$provincias->id}}">
                                                                {{$provincias->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default"><br></label>
                                                        <select class="form-control " placeholder="Provincia "
                                                            name="v_provincia" id="v_provincia" tabindex="15">
                                                            <option value="">Provincia</option>
                                                            @foreach ($provincia as $provincias)
                                                            <option class="" value="{{$provincias->id}}">
                                                                {{$provincias->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="normal" for=""><br></label>
                                                        <label class="custom-control custom-radio">
                                                            <input type="radio" name="v_tipo" id="v_tipo"
                                                                value="Masculino">
                                                            Masculino
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="sw-default"><br></label>
                                                        <select class="form-control " placeholder="Distrito "
                                                            name="v_dist" id="v_dist" tabindex="13" required>
                                                            <option value="">Distrito</option>
                                                            @foreach ($distrito as $distritos)
                                                            <option class="" value="{{$distritos->id}}">
                                                                {{$distritos->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default"><br></label>
                                                        <select class="form-control " placeholder="Distrito "
                                                            name="v_distrito" id="v_distrito" tabindex="16">
                                                            <option value="">Distrito</option>
                                                            @foreach ($distrito as $distritos)
                                                            <option class="" value="{{$distritos->id}}">
                                                                {{$distritos->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="normal" for=""><br></label>
                                                        <label class="custom-control custom-radio">
                                                            <input type="radio" name="v_tipo" id="v_tipo"
                                                                value="Personalizado">
                                                            Personalizado
                                                        </label>
                                                    </div>
                                                </div>
                                            </div> <!-- end row -->
                                        </div>
                                        <div id="sw-default-step-2">
                                            <div class="row">

                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="sw-default">Codigo Empleado</label>
                                                        <input type="text" class="form-control" name="v_codigoEmpleado"
                                                            id="v_codigoEmpleado" tabindex="1" required>
                                                    </div>
                                                </div>
                                                <div class="col-4"><br></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="sw-default">Cargo <a
                                                                onclick="$('#form-ver').modal('hide');$('#cargomodalE').modal('show')"
                                                                data-toggle="modal"><i class="uil uil-plus"
                                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                                        <select class="form-control" name="v_cargo" id="v_cargo"
                                                            tabindex="2" required>
                                                            <option value="">Seleccionar</option>

                                                            @foreach ($cargo as $cargos)
                                                            <option class="" value="{{$cargos->cargo_id}}">
                                                                {{$cargos->cargo_descripcion}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Contrato <a
                                                                onclick="$('#form-ver').modal('hide');$('#contratomodalE').modal('show');"
                                                                data-toggle="modal"><i class="uil uil-plus"
                                                                    style="color: darkblue;cursor: pointer;"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a id="detalleContratoE"
                                                                onclick="$('#form-ver').modal('hide');"
                                                                href="#fechasmodalE" data-toggle="modal"
                                                                data-target="#fechasmodalE" data-toggle="tooltip"
                                                                data-placement="right" title="Detalle de Contrato."
                                                                data-original-title="Detalle de Contrato."
                                                                style="cursor: pointer;"><img
                                                                    src="{{asset('landing/images/adaptive.svg')}}"
                                                                    height="18"></a></label>
                                                        <select class="form-control" name="v_contrato" id="v_contrato"
                                                            onchange="$('#detalleContratoE').show();" tabindex="5"
                                                            required>
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($tipo_cont as $tipo_conts)
                                                            <option class="" value="{{$tipo_conts->contrato_id}}">
                                                                {{$tipo_conts->contrato_descripcion}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!--<div class="form-group" style="display: none;">
                                                        <label for="sw-default" style="color: darkblue;">Fecha Inicio
                                                            <label for="sw-default" id="v_fechaIC"></label></label>
                                                        <label for="sw-default" style="color: red;">Fecha Final <label
                                                                for="sw-default" id="v_fechaFC"></label></label>
                                                    </div>-->
                                                </div> <!-- end col -->
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="sw-default">Área <a
                                                                onclick="$('#form-ver').modal('hide');$('#areamodalE').modal('show');"
                                                                data-toggle="modal"><i class="uil uil-plus"
                                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                                        <select class="form-control" name="v_area" id="v_area"
                                                            tabindex="3" required>
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($area as $areas)
                                                            <option class="" value="{{$areas->area_id}}">
                                                                {{$areas->area_descripcion}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Nivel del Colaborador<a
                                                                onclick="$('#form-ver').modal('hide');$('#nivelmodalE').modal('show');"
                                                                data-toggle="modal"><i class="uil uil-plus"
                                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                                        <select class="form-control" name="v_nivel" id="v_nivel"
                                                            tabindex="6">
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($nivel as $niveles)
                                                            <option class="" value="{{$niveles->nivel_id}}">
                                                                {{$niveles->nivel_descripcion}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> <!-- end col -->
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="sw-default">Centro Costo <a
                                                                onclick="$('#form-ver').modal('hide');$('#centrocmodalE').modal('show');"
                                                                data-toggle="modal"><i class="uil uil-plus"
                                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                                        <select class="form-control" name="v_centroc" id="v_centroc"
                                                            tabindex="4" required>
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($centro_costo as $centro_costos)
                                                            <option class="" value="{{$centro_costos->centroC_id}}">
                                                                {{$centro_costos->centroC_descripcion}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sw-default">Local <a
                                                                onclick="$('#form-ver').modal('hide');$('#localmodalE').modal('show');"
                                                                data-toggle="modal"><i class="uil uil-plus"
                                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                                        <select class="form-control" name="v_local" id="v_local"
                                                            tabindex="7">
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($local as $locales)
                                                            <option class="" value="{{$locales->local_id}}">
                                                                {{$locales->local_descripcion}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> <!-- end col -->
                                            </div> <!-- end row -->
                                        </div>
                                        <div id="sw-default-step-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group text-center">
                                                        {{ csrf_field() }}
                                                        <!--<img  alt="" id="v_foto" width="300" height="200">-->
                                                        <input type="file" name="file" id="file2" accept="image/*">
                                                    </div>
                                                </div> <!-- end col -->
                                            </div> <!-- end row -->
                                            <br>
                                            <br>
                                            <div class="row">
                                                <div class="col-12 text-center">
                                                    <button type="button" id="actualizarEmpleado"
                                                        style="background-color: #163552;color: #ffffff;"
                                                        class="btn btn-light btn-sm">Actualizar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!--VER EMPLEADO-->
<div id="verEmpleadoDetalles" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="verEmpleado" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #163552;">
                <h4 class="header-title mt-0 " style="color: #f0f0f0">Datos de empleado</h4><br>
                <button type="button" class="close" id="cerrarEd" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0px;">
                <div id="smartwizardVer" style="background: #ffffff; color:#3d3d3d;">
                    <ul style="background: #fdfdfd!important;">
                        <li><a href="#persona-step-1">Personales</a></li>
                        <li><a href="#sw-default-step-2">Empresarial</a></li>
                        <li><a href="#sw-default-step-3">Calendario-Horario</a></li>
                        <div class="col-md-4 text-left" style="display: flex;
                        align-items: center;cursor: pointer;">
                            <a style="color: #3d3d3d;"
                                onclick="$('#verEmpleadoDetalles').modal('toggle');$('#form-ver').modal();">
                                <img src="{{asset('admin/images/edit.svg')}}" height="15">
                                <span style="font-weight: 600">Editar Empleado</span>
                            </a>
                        </div>
                    </ul>
                    <div class="p-3" id="form-registrar">
                        <div id="persona-step-1">
                            <div class="row">
                                <div class="col-4 text-center">
                                    <input style="display: none;" name="v_idV" id="v_idV">
                                    <div class="form-group">
                                        <label for="sw-default">Tipo Documento</label>
                                        <input type="text" class="form-control text-center" name="v_tipoDocV"
                                            id="v_tipoDocV" style="background-color: #fcfcfc;border: none" tabindex="1"
                                            disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Apellido Paterno</label>
                                        <input type="text" class="form-control text-center" name="v_apPaternoV"
                                            id="v_apPaternoV" tabindex="4" style="border: none" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Correo Electronico</label>
                                        <input type="email" class="form-control text-center" id="v_emailV"
                                            name="v_emailV" tabindex="7" style="border: none" disabled>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-4 text-center">
                                    <div class="form-group">
                                        <label for="sw-default">Num. Documento</label>
                                        <input type="text" class="form-control text-center" name="v_numDocumentoV"
                                            id="v_numDocumentoV" style="background-color: #fcfcfc;border: none"
                                            tabindex="2" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Apellido Materno</label>
                                        <input type="text" class="form-control text-center" name="v_apMaternoV"
                                            id="v_apMaternoV" tabindex="5" style="border: none" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Celular</label>
                                        <input type="text" class="form-control text-center" name="v_celularV"
                                            id="v_celularV" style="border: none" disabled>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="form-group">
                                        <label for="sw-default">Fecha Nacimiento</label>
                                        <input type="text" class="form-control text-center" id="v_fechaNV" name="date"
                                            tabindex="3" style="border: none" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Nombres</label>
                                        <input type="text" class="form-control text-center" name="v_nombresV"
                                            id="v_nombresV" tabindex="6" style="border: none" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Telefono</label>
                                        <input type="text" class="form-control text-center" name="v_telefonoV"
                                            id="v_telefonoV" style="border: none" disabled>
                                    </div>
                                </div>
                                <div class="col-12 text-center">
                                    <div class="form-group">
                                        <label for="sw-default">Dirección</label>
                                        <input type="text" class="form-control text-center" name="v_direccionV"
                                            id="v_direccionV" tabindex="10" style="border: none" disabled>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="form-group">
                                        <label for="sw-default">Dirección
                                            Domiciliara</label>
                                        <input class="form-control text-center" placeholder="Departamento" name="v_depV"
                                            id="v_depV" tabindex="11" style="border: none" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Lugar Nacimiento</label>
                                        <input class="form-control text-center" placeholder="Departamento"
                                            name="v_departamentoV" id="v_departamentoV" tabindex="14"
                                            style="border: none" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label class="normal" for="">Genero</label>
                                        <label class="custom-control custom-radio text-center">
                                            <input type="radio" name="v_tipoV" id="v_tipoV" value="Femenino" disabled>
                                            Femenino
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="form-group">
                                        <label for="sw-default"><br></label>
                                        <input class="form-control text-center" placeholder="Provincia " name="v_provV"
                                            id="v_provV" tabindex="12" style="border: none" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default"><br></label>
                                        <input class="form-control text-center" placeholder="Provincia "
                                            name="v_provinciaV" id="v_provinciaV" tabindex="15" style="border: none"
                                            disabled>
                                    </div>
                                    <div class="form-group">
                                        <label class="normal" for=""><br></label>
                                        <label class="custom-control custom-radio text-center">
                                            <input type="radio" name="v_tipoV" id="v_tipoV" value="Masculino"
                                                style="border: none" disabled>
                                            Masculino
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="form-group">
                                        <label for="sw-default"><br></label>
                                        <input class="form-control text-center" placeholder="Distrito " name="v_distV"
                                            id="v_distV" tabindex="13" style="border: none" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default"><br></label>
                                        <input class="form-control text-center" placeholder="Distrito "
                                            name="v_distritoV" id="v_distritoV" tabindex="16" style="border: none"
                                            disabled>
                                    </div>
                                    <div class="form-group">
                                        <label class="normal" for=""><br></label>
                                        <label class="custom-control custom-radio text-center">
                                            <input type="radio" name="v_tipoV" id="v_tipoV" value="Personalizado"
                                                disabled>
                                            Personalizado
                                        </label>
                                    </div>
                                </div>
                            </div> <!-- end row -->
                        </div>
                        <div id="sw-default-step-2">
                            <div class="row">

                                <div class="col-4 text-center">
                                    <div class="form-group">
                                        <label for="sw-default">Codigo Empleado</label>
                                        <input type="text" class="form-control text-center" name="v_codigoEmpleadoV"
                                            id="v_codigoEmpleadoV" tabindex="1" style="border: none" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="sw-default-step-3">
                            <div class="row">
                                <input type="hidden" name="" id="idempleado">
                            <div class="col-md-1"><br></div>
                            <div class="col-md-10" id="calendar3"></div>
                            <div class="col-md-1"><br></div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
<div id="modalIncidencia" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar nueva incidencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                     <form id="frmIncidenciaCa" action="javascript:modalIncidencia()">
                        <div class="row">
                         <div class="col-md-12">
                             <div class="form-group">
                                <label for="">Descripcion:</label>
                                <input type="text" class="form-control form-control-sm" id="descripcionInciCa" required>
                             </div>
                         </div>
                         <div class="col-md-6"><label for=""><br></label>
                             <div class="form-check">
                               <input type="checkbox" class="form-check-input" id="descuentoCheckCa">
                               <label class="form-check-label" for="descuentoCheckCa">Aplicar descuento</label>
                             </div>
                          </div>
                          <div class="col-md-6" id="divhoraCa">
                             <div class="form-group">
                                <label for="">Hora de salida(24h):</label>
                                <input type="text" id="horaIncidenCa" class="form-control form-control-sm" >
                             </div>
                          </div>


                        </div>

                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right" >
                         <button type="button"  class="btn btn-light btn-sm " data-dismiss="modal">Cancelar</button>
                         <button type="submit"  name="" style="background-color: #163552;" class="btn btn-sm">Guardar</button>
                     </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
            </div>
            <footer class="border-top">
                <p class="text-center text-muted pt-4">© <?php echo date("Y"); ?> - RH Solution | Todos los derechos
                    reservados.</p>
            </footer>
        </div>
    </div>
    <script>
        var urlFoto = "";
        var hayFoto = false;
        var id_empleado = '';
    </script>
    <!-- Vendor js -->
    {{-- <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script> --}}
    <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
    <!-- App js -->
    <script src="{{asset('admin/assets/js/app.min.js')}}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <script src="{{asset('admin/assets/libs/smartwizard/jquery.smartWizard.min.js') }}"></script>
    <script>
        function maxLengthCheck(object) {
            if (object.value.length > object.maxLength)
                object.value = object.value.slice(0, object.maxLength)
        }

        function isNumeric(evt) {
            var theEvent = evt || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
            var regex = /[0-9]|\./;
            if (!regex.test(key)) {
                theEvent.returnValue = false;
                if (theEvent.preventDefault) theEvent.preventDefault();
            }
        }

    </script>
    <script src="{{asset('admin/assets/libs/bootstrap-fileinput/piexif.min.js')}}"></script>
    <script src="{{asset('admin/assets/libs/bootstrap-fileinput/sortable.min.js')}}"></script>
    <script src="{{asset('admin/assets/libs/bootstrap-fileinput/purify.min.js')}}"></script>
    <script src="{{asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.js')}}"></script>
    <script src="{{asset('admin/assets/libs/bootstrap-fileinput/theme.js')}}"></script>
    <script src="{{asset('admin/assets/libs/bootstrap-fileinput/es.js')}}"></script>
    <script src="{{asset('admin/assets/libs/combodate-1.0.7/combodate.js')}}"></script>
    <script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
    <script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>
    <script src="{{asset('admin/assets/libs/moment/moment.min.js')}}"></script>
    <script src="{{asset('admin/packages/core/main.js')}}"></script>
    <script src="{{asset('admin/packages/core/locales/es.js')}}"></script>
    <script src="{{asset('admin/packages/daygrid/main.js')}}"></script>
    <script src="{{asset('admin/packages/timegrid/main.js')}}"></script>
    <script src="{{asset('admin/packages/interaction/main.js')}}"></script>
    <script src="{{asset('admin/assets/js/pages/form-wizard.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{asset('landing/js/tabla.js')}}"></script>
    <script src="{{asset('landing/js/smartwizard.js')}}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{asset('landing/js/seleccionarDepProv.js')}}"></script>
    <script src="{{asset('landing/js/cargaMasivaF.js')}}"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
    <script src="{{asset('landing/js/empleado.js')}}"></script>
    <script src="{{asset('landing/js/empleadoA.js')}}"></script>
    @if (Auth::user())
    <script>
        $(function () {
            setInterval(function checkSession() {
                $.get('/check-session', function (data) {
                    // if session was expired
                    if (data.guest == false) {
                        $('#modal-error').modal('show');

                    }
                });
            }, 7202000);
        });

    </script>
    @endif
</body>

</html>
