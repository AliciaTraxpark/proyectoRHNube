<!DOCTYPE html>
<html lang="en">
<head>
  <title>Elegir organizacion</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
  <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
  <meta name="csrf-token" content="{{ csrf_token() }}">



  <!-- App css -->
  <link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />

</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/notification.svg')}}" height="100" >
                    <h4 class="text-danger mt-4">Su sesion expiró</h4>
                    <p class="w-75 mx-auto text-muted">Por favor inicie sesion nuevamente.</p>
                    <div class="mt-4">
                        <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i class="uil uil-arrow-right mr-1"></i> Iniciar sesion</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<style>
   /*  body > div.bootbox.modal.fade.show > div > div > div{
        background: #131313;
    color: #fbfbfb;
    }
    body > div.bootbox.modal.fade.show > div{
        top: 100px;
    left: 75px;
    } */

    .card .card-body{
        padding: 20px 20px;
    }
    .body{
        background-color: #fbfbfb;
    }
</style>

  <header id="header-section">
    <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
        <div class="container">
            <div class="col-md-2">
                <div class="navbar-brand-wrapper d-flex w-100">
                    <img src="{{asset('landing/images/Recurso 23.png')}}" alt=""
                    height="45" />
                </div>
            </div>
            <div class="col-md-6 text-left">
            <h5 style="color: #ffffff">Elige una de tus  organizaciones para gestionar.</h5>




        </div>
    </nav>
  </header>

    <div class="content-page" style="margin-top: 40px; margin-left: 120px; margin-right: 55px;">
        <div class="content">
            <div class="row">
                @foreach ($organizacion as $organizaciones)
                <div class="col-xl-3 col-lg-4" >
                    <div class="card" style="border: 1px solid #f1f1f1;">
                        <div class="card-body">
                            <div class="badge badge-info float-right" style="color: #85919c;
                            background-color: #e5eaf0;">{{$organizaciones->rol_nombre}}</div>
                            <p class=" text-uppercase font-size-12 mb-2" style="font-weight: 600;color:#4a6d8d!important">{{$organizaciones->organi_tipo}}</p>
                            <h5><a  class="text-dark">{{$organizaciones->organi_razonSocial}}</a></h5>
                            <p class="text-muted" style="font-size:12px!important "><label style="font-weight: 600" for=""> RUC/ID:</label> {{$organizaciones->organi_ruc}}</p>

                            <button class="btn btn-soft-primary btn-block btn-sm" style="color: #16588d;
                            background-color: #c1cee0;" onclick="ingresarOrganiza({{$organizaciones->organi_id}})"><i class="uil uil-arrow-right mr-1"></i>Ingresar a organizacion</button>
                        </div>
                        <div class="card-body border-top" style="padding: 10px 20px;">
                            <div class="row align-items-center">
                                <div class="col-sm-auto">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item pr-2">
                                            <a  class="text-muted d-inline-block"
                                                data-toggle="tooltip" data-placement="top" title=""
                                                data-original-title="Due date">
                                                @php
                                                $timestamp = strtotime( $organizaciones->created_at);
                                                $fechaSola= date('d/m/Y', $timestamp );

                                                @endphp
                                                <i class="uil uil-calender mr-1"></i>Fecha de registro: {{$fechaSola}}
                                            </a>
                                        </li>


                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                </div>
                @endforeach

            </div>


         <div class="col-md-12 text-center">
            <footer class=" border-top">
                <p class="text-center text-muted pt-4">© <?php echo date("Y"); ?> - RH Solution | Todos los derechos reservados.</p>
            </footer>
         </div>

        </div>
    </div>
  <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
  <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
  <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
  <script src="{{asset('landing/js/landingpage.js')}}"></script>


  <!-- Vendor js -->
  {{-- <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script> --}}

  <!-- plugin js -->
>
  <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>

  <script>
function ingresarOrganiza(idorganiza){
    $.ajax({
        type: "post",
        url: "/enviarIDorg",
        data: {
            idorganiza
        },
        statusCode: {
            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            window.location.replace(
                    location.origin + "/dashboard"
                );
            },
    });

}

  </script>
  @if (Auth::user())
  <script>
    $(function() {
      setInterval(function checkSession() {
        $.get('/check-session', function(data) {

          // if session was expired
          if (data.guest==false) {
             $('#modal-error').modal('show');


          }
        });
      },7202000); // every minute
    });
  </script>
@endif
</body>
</html>
