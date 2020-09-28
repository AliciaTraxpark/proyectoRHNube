<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>RH nube</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('landing/images/ICONO-LOGO-NUBE-RH.ico')}}">

    <!-- App css -->
    <link href="admin/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="admin/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="admin/assets/css/app.min.css" rel="stylesheet" type="text/css" />

    @include('layouts.shared.head')
    <style>
        .danger {
            color: red;
        }
    </style>
</head>

<body>
    @yield('content')
    @include('layouts.shared.footer-script')
</body>

</html>
