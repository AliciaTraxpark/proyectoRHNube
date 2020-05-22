<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gestion de  empleados</title>
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

    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard_theme_arrows.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard_theme_circles.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard_theme_dots.min.css')}}" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
</head>
<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
<style>
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
    color: #ffffff !important;
}
.sw-theme-default > ul.step-anchor > li.done > a, .sw-theme-default > ul.step-anchor > li > a {
    color: #d2d2d2!important;
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
        <div class="col-md-2">
            <div class="navbar-brand-wrapper d-flex w-100">
                <img src="{{asset('landing/images/logo.png')}}" height="100" >
              </div>
        </div>
        <div class="col-md-6 text-left">
          <h5 style="color: #ffffff">Gestión de empleados</h5>
          <label for="" class="blanco">Tienes 2 minutos para registrar tu primer empleado</label>
        </div>

        <div class=" col-md-2">
            <a href="{{('/empleado/cargar')}}"> <button class="btn btn-sm btn-primary" style="background-color: #174f7d;border-color:#174f7d"><img src="{{asset('admin/images/avatar.svg')}}" height="25" class="mr-1" >Carga masiva</button></a>

          </div>
        <div class=" col-md-3">
            <button  class="btn btn-sm btn-primary" style="background-color: #174f7d;border-color:#174f7d"><img src="{{asset('admin/images/photo.svg')}}" height="25" class="mr-1" >Carga masiva fotos</button>
        </div>
    </div>
    </nav>
  </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">
            <div class="row row-divided">
                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-body" style="padding-top: 20px; background: #f8f8f8; font-size: 12.8px;
                        color: #222222;">
                            <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                            <h4 class="header-title mt-0 "></i>Búsquedad de empleado</h4>
                            <div id="tabladiv">

                            </div>


                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div>
                <div id="areamodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="areamodal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModalLabel">Agregar área</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                {{ csrf_field() }}
                                <div class="col-md-12">
                                  <label for="">Área</label>
                                  <input type="text" class="form-control" name="textArea" id="textArea" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="button" id="guardarArea" class="btn btn-primary">Guardar</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="cargomodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="areamodal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModalLabel">Agregar Cargo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12">
                                  <label for="">Cargo</label>
                                  <input type="text" class="form-control" id="textCargo" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" id="guardarCargo">Guardar</button>
                            </div>
                        </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="centrocmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="areamodal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModalLabel">Agregar Centro Costo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12">
                                  <label for="">Centro Costo</label>
                                  <input type="text" class="form-control" id="textCentro" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-primary" id="guardarCentro">Guardar</button>
                            </div>
                        </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="localmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="localmodal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModalLabel">Agregar Local</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="col-md-12">
                                  <label for="">Local</label>
                                  <input type="text" class="form-control" name="textLocal" id="textLocal" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="button" id="guardarLocal" class="btn btn-primary">Guardar</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="nivelmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="nivelmodal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModalLabel">Agregar Nivel</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="col-md-12">
                                  <label for="">Nivel</label>
                                  <input type="text" class="form-control" name="textNivel" id="textNivel" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                                <button type="button" id="guardarNivel" class="btn btn-primary">Guardar</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div class="col-md-6 col-xl-6" style="font-size: 13px" id="form-registrar">
                            <br>
                            <h4 class="header-title mt-0 "></i>Datos de empleado</h4>
                            <div id="smartwizard" style="background: #f8f8f8; color:#3d3d3d;">
                                <ul style="background: #566879!important;" >
                                    <li><a href="#sw-default-step-1">Personales</a></li>
                                    <li><a href="#sw-default-step-2">Empresarial</a></li>
                                    <li><a href="#sw-default-step-3">Foto</a></li>
                                </ul>
                                <div class="p-3" id="form-registrar">
                                    <div id="sw-default-step-1">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default">Tipo Documento</label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Seleccionar</option>
                                                        @foreach ($tipo_doc as $tipo_docs)
                                                        <option class="" value="{{$tipo_docs->tipoDoc_id}}">{{$tipo_docs->tipoDoc_descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Apellido Paterno</label>
                                                    <input type="text" class="form-control" name="apPaterno" id="apPaterno" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Lugar Nacimiento</label>
                                                    <select  class="form-control" placeholder="Departamento" name="departamento" id="departamento" required>
                                                        <option value="">Departamento</option>
                                                        @foreach ($departamento as $departamentos)
                                                          <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}</option>
                                                          @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Dirección Domiciliara</label>
                                                    <select  class="form-control" placeholder="Departamento" name="departamento" id="dep" required>
                                                        <option value="">Departamento</option>
                                                        @foreach ($departamento as $departamentos)
                                                          <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}</option>
                                                          @endforeach
                                                    </select>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default">Num. Documento</label>
                                                    <input type="text" class="form-control" name="numDocumento" id="numDocumento" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Apellido Materno</label>
                                                    <input type="text" class="form-control" name="apMaterno" id="apMaterno" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default"><br></label>
                                                    <select  class="form-control " placeholder="Provincia " name="provincia" id="provincia" required>
                                                        <option value="">Provincia</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default"><br></label>
                                                    <select  class="form-control " placeholder="Provincia " name="provincia" id="prov" required>
                                                        <option value="">Provincia</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default">Fecha Nacimiento</label>
                                                    <input class="form-control" id="fechaN" type="date" name="fechaN" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Nombres</label>
                                                    <input type="text" class="form-control" name="nombres" id="nombres" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default"><br></label>
                                                    <select  class="form-control " placeholder="Distrito " name="distrito" id="distrito" required>
                                                        <option value="">Distrito</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default"><br></label>
                                                    <select  class="form-control " placeholder="Distrito " name="distrito" id="dist" required>
                                                        <option value="">Distrito</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="sw-default">Dirección</label>
                                                    <input type="text" class="form-control" name="direccion" id="direccion" required>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="normal" for="">Genero</label>
                                                    <label class="custom-control custom-radio">
                                                        <input type="radio" name="tipo" id="tipo" value="Femenino" required>
                                                        Femenino
                                                      </label>
                                                </div>
                                            </div>
                                            <div class="col-4">
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
                                                    <label class="normal" for=""><br></label>
                                                    <label class="custom-control custom-radio">
                                                        <input type="radio" name="tipo" id="tipo" value="Personalizado" required>
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
                                                    <label for="sw-default">Cargo<a  href="#cargomodal" data-toggle="modal" data-target="#cargomodal">(+)</a></label>
                                                    <select  class="form-control" name="cargo" id="cargo" required>
                                                        <option value="">Seleccionar</option>

                                                          @foreach ($cargo as $cargos)
                                                          <option class="" value="{{$cargos->cargo_id}}">{{$cargos->cargo_descripcion}}</option>
                                                          @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Contrato</label>
                                                    <select  class="form-control" name="contrato" id="contrato" required>
                                                        <option value="">Seleccionar</option>
                                                        @foreach ($tipo_cont as $tipo_conts)
                                                        <option class="" value="{{$tipo_conts->contrato_id}}">{{$tipo_conts->contrato_descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default">Área<a href="#areamodal" data-toggle="modal" data-target="#areamodal">(+)</a></label>
                                                    <select  class="form-control" name="area" id="area" required>
                                                        <option value="">Seleccionar</option>
                                                        @foreach ($area as $areas)
                                                        <option class="" value="{{$areas->area_id}}">{{$areas->area_descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Nivel<a  href="#nivelmodal" data-toggle="modal" data-target="#nivelmodal">(+)</a></label>
                                                    <select  class="form-control" name="nivel" id="nivel">
                                                        <option value="">Seleccionar</option>
                                                        @foreach ($nivel as $niveles)
                                                        <option class="" value="{{$niveles->nivel_id}}">{{$niveles->nivel_descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default">Centro Costo<a  href="#centrocmodal" data-toggle="modal" data-target="#centrocmodal">(+)</a></label>
                                                    <select  class="form-control" name="centroc" id="centroc" required>
                                                        <option value="">Seleccionar</option>
                                                        @foreach ($centro_costo as $centro_costos)
                                                        <option class="" value="{{$centro_costos->centroC_id}}">{{$centro_costos->centroC_descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Local<a  href="#localmodal" data-toggle="modal" data-target="#localmodal">(+)</a></label>
                                                    <select  class="form-control" name="local" id="local">
                                                        <option value="">Seleccionar</option>
                                                        @foreach ($local as $locales)
                                                        <option class="" value="{{$locales->local_id}}">{{$locales->local_descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> <!-- end col -->
                                        </div> <!-- end row -->

                                    </div>
                                    <div id="sw-default-step-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="sw-default">Imagen</label>
                                                    <input type="file" name="file" id="file">
                                                </div>
                                            </div> <!-- end col -->
                                        </div> <!-- end row -->
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12  text-center">
                                                <button type="button" id="guardarEmpleado" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                        <br>
                                    </div>
                                </div>

                            </div>

                </div>
                <div class="col-md-6 col-xl-6" id="form-ver" style="font-size: 13px">
                    <br>
                    <h4 class="header-title mt-0 "></i>Datos de empleado</h4>
                    <div id="smartwizard1" style="background: #f8f8f8; color:#3d3d3d;">
                        <ul style="background: #566879!important;" >
                            <li><a href="#persona-step-1">Personales</a></li>
                            <li><a href="#sw-default-step-2">Empresarial</a></li>
                            <li><a href="#sw-default-step-3">Foto</a></li>
                        </ul>
                        <div class="p-3" id="form-registrar">
                            <div id="persona-step-1">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Tipo Documento</label>
                                            <input type="text" class="form-control" name="v_tipoDoc" id="v_tipoDoc" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Apellido Paterno</label>
                                            <input type="text" class="form-control" name="v_apPaterno" id="v_apPaterno" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Lugar Nacimiento</label>
                                            <select  class="form-control" placeholder="Departamento" name="departamento" id="departamento" required>
                                                <option value="">Departamento</option>
                                                @foreach ($departamento as $departamentos)
                                                  <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}</option>
                                                  @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Dirección Domiciliara</label>
                                            <select  class="form-control" placeholder="Departamento" name="departamento" id="dep" required>
                                                <option value="">Departamento</option>
                                                @foreach ($departamento as $departamentos)
                                                  <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}</option>
                                                  @endforeach
                                            </select>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Num. Documento</label>
                                            <input type="text" class="form-control" name="v_numDocumento" id="v_numDocumento" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Apellido Materno</label>
                                            <input type="text" class="form-control" name="v_apMaterno" id="v_apMaterno" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default"><br></label>
                                            <select  class="form-control " placeholder="Provincia " name="provincia" id="provincia" required>
                                                <option value="">Provincia</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default"><br></label>
                                            <select  class="form-control " placeholder="Provincia " name="provincia" id="prov" required>
                                                <option value="">Provincia</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Fecha Nacimiento</label>
                                            <input class="form-control" id="v_fechaN" name="v_fechaN" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Nombres</label>
                                            <input type="text" class="form-control" name="v_nombres" id="v_nombres" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default"><br></label>
                                            <select  class="form-control " placeholder="Distrito " name="distrito" id="distrito" required>
                                                <option value="">Distrito</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default"><br></label>
                                            <select  class="form-control " placeholder="Distrito " name="distrito" id="dist" required>
                                                <option value="">Distrito</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="sw-default">Dirección</label>
                                            <input type="text" class="form-control" name="v_direccion" id="v_direccion" required>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="normal" for="">Genero</label>
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="tipo" id="tipo" value="Femenino" required>
                                                Femenino
                                              </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
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
                                            <label class="normal" for=""><br></label>
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="tipo" id="tipo" value="Personalizado" required>
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
                                            <label for="sw-default">Cargo<a  href="#cargomodal" data-toggle="modal" data-target="#cargomodal">(+)</a></label>
                                            <select  class="form-control" name="cargo" id="cargo" required>
                                                <option value="">Seleccionar</option>

                                                  @foreach ($cargo as $cargos)
                                                  <option class="" value="{{$cargos->cargo_id}}">{{$cargos->cargo_descripcion}}</option>
                                                  @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Contrato</label>
                                            <select  class="form-control" name="contrato" id="contrato" required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($tipo_cont as $tipo_conts)
                                                <option class="" value="{{$tipo_conts->contrato_id}}">{{$tipo_conts->contrato_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Área<a href="#areamodal" data-toggle="modal" data-target="#areamodal">(+)</a></label>
                                            <select  class="form-control" name="area" id="area" required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($area as $areas)
                                                <option class="" value="{{$areas->area_id}}">{{$areas->area_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Nivel<a  href="#nivelmodal" data-toggle="modal" data-target="#nivelmodal">(+)</a></label>
                                            <select  class="form-control" name="nivel" id="nivel">
                                                <option value="">Seleccionar</option>
                                                @foreach ($nivel as $niveles)
                                                <option class="" value="{{$niveles->nivel_id}}">{{$niveles->nivel_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sw-default">Centro Costo<a  href="#centrocmodal" data-toggle="modal" data-target="#centrocmodal">(+)</a></label>
                                            <select  class="form-control" name="centroc" id="centroc" required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($centro_costo as $centro_costos)
                                                <option class="" value="{{$centro_costos->centroC_id}}">{{$centro_costos->centroC_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Local<a  href="#localmodal" data-toggle="modal" data-target="#localmodal">(+)</a></label>
                                            <select  class="form-control" name="local" id="local">
                                                <option value="">Seleccionar</option>
                                                @foreach ($local as $locales)
                                                <option class="" value="{{$locales->local_id}}">{{$locales->local_descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->

                            </div>
                            <div id="sw-default-step-3">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="sw-default">Imagen</label>
                                            <input type="file" name="file" id="file">
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->
                                <br>
                                <div class="row">
                                    <div class="col-md-12  text-center">
                                        <button type="button" id="guardarEmpleado" class="btn btn-primary">Guardar</button>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>

                    </div>

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
    <script src="{{asset('admin/assets/libs/smartwizard/jquery.smartWizard.min.js') }}"></script>

    <script src="{{asset('admin/assets/js/pages/form-wizard.init.js') }}"></script>
    <script src="{{asset('landing/js/tabla.js')}}"></script>
    <script src="{{asset('landing/js/smartwizard.js')}}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{asset('landing/js/seleccionarDepProv.js')}}"></script>
    <script src="{{asset('landing/js/empleado.js')}}"></script>
</body>
</html>
