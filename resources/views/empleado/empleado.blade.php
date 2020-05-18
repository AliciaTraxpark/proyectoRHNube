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

    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard_theme_arrows.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard_theme_circles.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/smartwizard/smart_wizard_theme_dots.min.css')}}" type="text/css" />
    <link href="{{asset('admin/assets/libs/dropzone/dropzone.min.css')}}" type="text/css" />
</head>
<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
<style>
    .fc-time{
        display: none;
    }
    .v-divider{
    border-right:5px solid #ebebeb;
    }
</style>
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
            <a href="{{('/empleado/cargar')}}"> <button style="background-color: #2f5597;border-color:#2f5597 "  class="btn btn-sm btn-rounded btn-primary "><i data-feather="users" class="mr-1"></i>Carga masiva</button></a>

          </div>
        <div class=" col-md-3">
            <button  class="btn btn-sm" style="background: #A0C6F6;"><i data-feather="camera" class="mr-1"></i>Carga masiva fotos</button>
        </div>
    </div>
    </nav>
  </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">
            <div class="row row-divided">
                <div class="col-xl-6 v-divider">
                    <div class="card">
                        <div class="card-body" style="padding-top: 20px; background: #f8f8f8;">
                            <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                            <table id="tablaEmpleado" class="table nowrap" style="font-size: 13px">
                                <thead style="background: #4C5D73;color: white;">
                                    <tr>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th>Cargo</th>
                                        <th>Área</th>
                                        <th>Centro Costo</th>

                                    </tr>
                                </thead>


                                <tbody style="background: #f3f4f7;color: #2c2c2c;">

                                    <tr>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>
                                        <td>London</td>
                                        <td>47</td>
                                        <td>2011/03/21</td>

                                    </tr>
                                    <tr>
                                        <td>Lael Greer</td>
                                        <td>Systems Administrator</td>
                                        <td>London</td>
                                        <td>21</td>
                                        <td>2009/02/27</td>

                                    </tr>
                                    <tr>
                                        <td>Jonas Alexander</td>
                                        <td>Developer</td>
                                        <td>San Francisco</td>
                                        <td>30</td>
                                        <td>2010/07/14</td>

                                    </tr>
                                    <tr>
                                        <td>Shad Decker</td>
                                        <td>Regional Director</td>
                                        <td>Edinburgh</td>
                                        <td>51</td>
                                        <td>2008/11/13</td>

                                    </tr>
                                    <tr>
                                        <td>Michael Bruce</td>
                                        <td>Javascript Developer</td>
                                        <td>Singapore</td>
                                        <td>29</td>
                                        <td>2011/06/27</td>

                                    </tr>
                                    <tr>
                                        <td>Donna Snider</td>
                                        <td>Customer Support</td>
                                        <td>New York</td>
                                        <td>27</td>
                                        <td>2011/01/25</td>

                                    </tr>
                                </tbody>
                            </table>

                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body" style="background: #fbfbfb">
                            <h4 class="header-title mt-0 mb-1"><i data-feather="bookmark" class="mr-1"></i>Datos</h4>
                            <div id="smartwizard">
                                <ul>
                                    <li><a href="#sw-default-step-1">Personales</a></li>
                                    <li><a href="#sw-default-step-2">Empresarial</a></li>
                                    <li><a href="#sw-default-step-3">Foto</a></li>
                                </ul>

                                <div class="p-3">
                                    <div id="sw-default-step-1">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default-password">Tipo Documento</label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Seleccionar</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password">Ap. Paterno</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password">Lugar Nacimiento</label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Departamento</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password">Dirección Domiciliara</label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Departamento</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default-password">Num. Documento</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password">Ap. Materno</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password"><br></label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Provincia</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password"><br></label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Provincia</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default-password">Fecha Nacimiento</label>
                                                    <input class="form-control" id="example-date" type="date" name="date">
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password">Nombre</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password"><br></label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Distrito</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password"><br></label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Distrito</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="sw-default-password">Dirección</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="normal" for="">Genero</label>
                                                    <label class="custom-control custom-radio">
                                                        <input type="radio" name="tipo" id="tipo" value="Genero" required>
                                                        Femenino
                                                      </label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="normal" for=""><br></label>
                                                    <label class="custom-control custom-radio">
                                                        <input type="radio" name="tipo" id="tipo" value="Genero" required>
                                                        Masculino
                                                      </label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="normal" for=""><br></label>
                                                    <label class="custom-control custom-radio">
                                                        <input type="radio" name="tipo" id="tipo" value="Genero" required>
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
                                                    <label for="sw-default-first-name">Cargo</label>
                                                    <input type="text" id="sw-default-first-name" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password">Contrato</label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Seleccionar</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default-first-name">Área</label>
                                                    <input type="text" id="sw-default-first-name" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password">Nivel</label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Seleccionar</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="sw-default-first-name">Centro Costo</label>
                                                    <input type="text" id="sw-default-first-name" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default-password">Local</label>
                                                    <select  class="form-control" placeholder="Tipo Documento " name="documento" id="documento" required>
                                                        <option value="">Seleccionar</option>
                                                          <option class="" value=""></option>
                                                    </select>
                                                </div>
                                            </div> <!-- end col -->
                                        </div> <!-- end row -->

                                    </div>
                                    <div id="sw-default-step-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="text-center">
                                                    <form action="/" method="post" class="dropzone" id="myAwesomeDropzone">
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple />
                                                        </div>

                                                        <div class="dz-message needsclick">
                                                            <i class="h1 text-muted  uil-cloud-upload"></i>
                                                            <h3>Suelte imagen aquí o haga clic para cargar.</h3>
                                                        </div>
                                                    </form>
                                                    <div class="clearfix text-center mt-3">
                                                        <button type="button" class="btn btn-success"><i class="uil uil-arrow-right mr-1"></i>Guardar</button>
                                                    </div>
                                                </div>
                                            </div> <!-- end col -->
                                        </div> <!-- end row -->
                                    </div>
                                </div>
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

    <script src="{{asset('admin/assets/libs/smartwizard/jquery.smartWizard.min.js') }}"></script>

    <script src="{{asset('admin/assets/js/pages/form-wizard.init.js') }}"></script>
    <script src="{{asset('admin/assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{asset('landing/js/tabla.js')}}"></script>
    <script src="{{asset('landing/js/smartwizard.js')}}"></script>

</body>
</html>
