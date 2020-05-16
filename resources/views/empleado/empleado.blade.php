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
            <button  class="botonN" >Carga masiva</button>

          </div>
        <div class=" col-md-2">

          <button  class="botonN" >Carga masiva fotos</button>
        </div>
    </div>
    </nav>
  </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">

            <div class="row " >
                <div class="col-xl-5">
                    <div class="card">
                        <div class="card-body" style="padding-top: 20px;">
                            <h4 class="header-title mt-0 mb-1">Basic Data Table</h4>
                            <p class="sub-header">
                                DataTables has most features enabled by default, so all you need to do to use it with your own tables is to call the construction
                                function:
                                <code>$().DataTable();</code>.
                            </p>

                            <table id="basic-datatable" class="table dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Age</th>
                                        <th>Start date</th>

                                    </tr>
                                </thead>


                                <tbody>

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
                <div class="col-xl-7">
                    <div class="card">
                        <div class="card-body" style="padding-top: 20px; padding-left: 10px; padding-right: 10px;">


                            <ul class="nav nav-pills navtab-bg nav-justified">
                                <li class="nav-item">
                                    <a href="#home1" data-toggle="tab" aria-expanded="false"
                                        class="nav-link">
                                        <span class="d-block d-sm-none"><i class="uil-home-alt"></i></span>
                                        <span class="d-none d-sm-block">Home</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#profile1" data-toggle="tab" aria-expanded="true"
                                        class="nav-link active">
                                        <span class="d-block d-sm-none"><i class="uil-user"></i></span>
                                        <span class="d-none d-sm-block">Profile</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#messages1" data-toggle="tab" aria-expanded="false"
                                        class="nav-link">
                                        <span class="d-block d-sm-none"><i class="uil-envelope"></i></span>
                                        <span class="d-none d-sm-block">Messages</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content text-muted">
                                <div class="tab-pane" id="home1">
                                    <p>Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean
                                        commodo ligula eget dolor. Aenean massa. Cum sociis natoque
                                        penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                                        Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.
                                        Nulla consequat massa quis enim.</p>

                                </div>
                                <div class="tab-pane show active" id="profile1">
                                    <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.
                                        In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo.
                                        Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras
                                        dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend
                                        tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend
                                        ac, enim.</p>

                                </div>
                                <div class="tab-pane" id="messages1">
                                    <p>Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean
                                        commodo ligula eget dolor. Aenean massa. Cum sociis natoque
                                        penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                                        Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.
                                        Nulla consequat massa quis enim.</p>

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


  <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
  <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
  <script src="{{asset('landing/js/landingpage.js')}}"></script>


  <!-- Vendor js -->
  <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
  <!-- App js -->
  <script src="{{asset('admin/assets/js/app.min.js')}}"></script>

  <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>


</body>
</html>
