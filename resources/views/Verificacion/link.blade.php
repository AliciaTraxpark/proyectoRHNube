<!DOCTYPE html>
<html lang="en">

<head>
    <title>Inicio</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{
        URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')
        }}" rel="stylesheet" />
    <link rel="shortcut icon"
        href="https://rhsolution.com.pe/wp-content/uploads/2019/06/small-logo-rh-solution-64x64.png" sizes="32x32">
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100" style=" background: #f7f8fa;">
    <style>
        .btn-group-sm>.btn,
        .btn-sm {
            padding: .25rem .5rem !important;
            font-size: 14px !important;
        }

        .inp {
            border: 0;
            font-weight: 550;
            background: white;
            padding-left: 8px;
            padding-right: 8px;
            text-align: center;
        }

    </style>
    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
            <div class="container" style="color: #ffffff;">
                <div class="col-md-2">
                    <div class="navbar-brand-wrapper d-flex w-100">
                        <img src="{{asset('landing/images/logo.png')}}" height="80">
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="container">
        <br><br>
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div>
                    <div>
                        <div class="text-center">
                            <br>
                            <div class="mx-auto">
                                <img src="{{asset('landing/images/link (1).svg')}}" alt="" height="80" />
                            </div>
                            <p class="text-muted mt-3 mb-3">Este link ya no se encuentra disponible.
                            </p>
                        </div>
                    </div> <!-- end card-body -->
                </div>
                <!-- end card -->
            </div> <!-- end col -->
        </div>
    </div>
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>
</body>
