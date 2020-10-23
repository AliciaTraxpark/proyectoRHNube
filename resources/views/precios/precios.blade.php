@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{ URL::asset('admin/assets/libs/apexcharts/apexcharts.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<style>
    .ulC .liC {
        margin-bottom: 1.4rem
    }

    .pricing-divider {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        background: rgb(34, 193, 195);
        background: linear-gradient(0deg, rgba(34, 193, 195, 1) 0%, rgba(68, 177, 204, 1) 100%);
        padding: 1em 0 4em;
        position: relative;
    }

    .pricing-dividerD {
        border-radius: 20px;
        background: rgb(43, 89, 187);
        background: linear-gradient(0deg, rgba(43, 89, 187, 1) 0%, rgba(60, 111, 156, 1) 100%);
        padding: 1em 0 4em;
        position: relative
    }

    .pricing-dividerT {
        border-radius: 20px;
        background: rgb(51, 75, 125);
        background: linear-gradient(0deg, rgba(51, 75, 125, 1) 0%, rgba(64, 112, 136, 1) 100%);
        padding: 1em 0 4em;
        position: relative
    }

    .pricing-divider-img {
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 80px
    }

    .deco-layer {
        -webkit-transition: -webkit-transform 0.5s;
        transition: transform 0.5s
    }

    .btn-custom {
        background: rgb(34, 193, 195);
        background: linear-gradient(0deg, rgba(34, 193, 195, 1) 0%, rgba(68, 177, 204, 1) 100%);
        color: #fff;
        border-radius: 20px
    }

    .btn-custom:hover {
        background: #fff;
        color: #44b1cc;
        border-color: #44b1cc;
        border-radius: 20px;
        -webkit-transition: color 0.5s ease-in-out;
        transition: color 0.5s ease-in-out;
    }

    .btn-custom::after {
        -webkit-transition: height 0.5s ease-in-out;
    }

    .btn-customD {
        background: rgb(43, 89, 187);
        background: linear-gradient(0deg, rgba(43, 89, 187, 1) 0%, rgba(60, 111, 156, 1) 100%);
        color: #fff;
        border-radius: 20px
    }

    .btn-customD:hover {
        color: #3c6f9c;
        background: #fff;
        border-radius: 20px;
        border-color: #3c6f9c;
    }

    .btn-customT {
        background: rgb(51, 75, 125);
        background: linear-gradient(0deg, rgba(51, 75, 125, 1) 0%, rgba(64, 112, 136, 1) 100%);
        color: #fff;
        border-radius: 20px
    }

    .btn-customT:hover {
        color: #407088;
        background: #fff;
        border-radius: 20px;
        border-color: #407088;
    }

    .img-float {
        width: 50px;
        position: absolute;
        top: -3.5rem;
        right: 1rem
    }

    .princing-item {
        transition: all 150ms ease-out
    }

    .princing-item:hover {
        transform: scale(1.05)
    }

    .princing-item:hover .deco-layer--1 {
        -webkit-transform: translate3d(15px, 0, 0);
        transform: translate3d(15px, 0, 0)
    }

    .princing-item:hover .deco-layer--2 {
        -webkit-transform: translate3d(-15px, 0, 0);
        transform: translate3d(-15px, 0, 0)
    }

    .botonF1 {
        width: 80px;
        height: 80px;
        border-radius: 100%;
        background: #f4eeff;
        right: 0;
        bottom: 0;
        position: absolute;
        top: -40px;
        left: 80px;
        border: none;
        outline: none;
        color: #30475e;
        font-size: 36px;
        box-shadow: 0 8px 6px rgba(0, 0, 0, 0.16), 0 8px 6px rgba(0, 0, 0, 0.23);
        transition: .3s;
    }

    .svgTriangulo {
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 40px
    }

    @media (max-width: 767.98px) {
        .containerResp {
            padding-left: 0% !important;
        }

        .rowPrincipalResp {
            padding: 0% !important;
            padding-top: 2% !important;
        }

        .preciosResponsive,
        .content-page {
            width: 100% !important;
            display: flex !important;
            overflow: auto !important;
            flex-wrap: initial !important;
        }

        .col-4 {
            flex: 100% !important;
            max-width: 100% !important;
            padding-top: 8% !important;
        }

        .pt-5 {
            padding-top: 2rem !important;
        }

        .pricing-divider,
        .pricing-dividerD,
        .pricing-dividerT,
        .bodyPrecio {
            width: 250px !important;
        }

        .row {
            flex-wrap: nowrap !important;
        }

        .tituloResponsive {
            max-width: 30% !important;
        }
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
<div class="row justify-content-center pt-5 pl-5 pb-5 rowPrincipalResp">
    <div class="col-xl-12">
        <div class="row pr-5">
            <div class="col-md-3 pt-2 tituloResponsive">
                <strong style="color: #000839">PLANES Y PRECIOS - PERÚ</strong>
            </div>
            <div class="col-md-9 tituloResponsive" style="border-bottom: 3px solid #12cad6"></div>
        </div>
        <div class="row justify-content-end pb-5 pt-5">
            <div class="container-fluid containerResp" style="padding-left: 22%">
                <div class="row m-auto text-center preciosResponsive" style="width: 56vw;">
                    <div class="col-4 princing-item">
                        <div class="pricing-divider">
                            <h6 class="font-weight-normal botonF1 pt-3 pb-1"
                                style="font-size: 43px;color:#407088;font-family: 'Calibri'">$3</h6>
                            <h5 class="text-light pt-5" style="font-family: 'Poppins'">PYME</h5>
                            <span class="h6 mb-1" style="color: #ffffff;font-family: 'Poppins'">Hasta 200 empleados al
                                mes</span>
                            <svg class="svgTriangulo" width="100%" height="100" viewBox="0 0 100 102"
                                preserveAspectRatio="none">
                                <path d="M0 0 L50 90 L100 0 V100 H0" fill="#ffffff" />
                            </svg>
                        </div>
                        <div class="card-body bg-white mt-0 shadow bodyPrecio"
                            style="border-bottom-left-radius: 25px;border-bottom-right-radius: 25px">
                            <ul class="list-unstyled mb-3 position-relative ulC" style="font-family: 'Roboto';">
                                <li class="ilC text-left" style="color: #44b1cc"><img
                                        src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">$2.50 un
                                    pago semestral</li>
                                <li class="ilC text-left" style="color: #44b1cc"><img
                                        src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">$2.00 un
                                    pago
                                    anual</li>
                                <li class="ilC text-left" style="color: #44b1cc"><img
                                        src="{{asset('landing/images/tick (4).svg')}}" height="15"
                                        class="mr-2">Administrador de personal Ilimitado</li>
                                <li class="ilC text-left" style="color: #44b1cc;"><img
                                        src="{{asset('landing/images/tick (4).svg')}}" height="15"
                                        class="mr-2">Multiempresa
                                </li>
                                <li class="ilC" style="color: #44b1cc;font-size: 9px">(5$ Por cada empresa nueva
                                    administrada por la misma cuenta)
                                </li>
                            </ul> <button type="button" class="btn btn-lg btn-block btn-custom"
                                style="font-size: 16px;font-weight: bold">SUSCRIBIRSE</button>
                        </div>
                    </div>
                    <div class="col-4 princing-item">
                        <div class="pricing-dividerD">
                            <h6 class="font-weight-normal botonF1 pt-3 pb-1"
                                style="font-size: 43px;color:#407088;font-family: 'Calibri'">$2.5
                            </h6>
                            <h5 class="text-light pt-5" style="font-family: 'Poppins'">PROFESIONAL</h5>
                            <span class="h6" style="color: #ffffff;font-family: 'Poppins'">De 200 a 5000 empleados al
                                mes</span>
                            <svg class="svgTriangulo" width="100%" height="100" viewBox="0 0 100 102"
                                preserveAspectRatio="none">
                                <path d="M0 0 L50 90 L100 0 V100 H0" fill="#ffffff" />
                            </svg>
                        </div>
                        <div class="card-body bg-white mt-0 shadow bodyPrecio"
                            style="border-bottom-left-radius: 25px;border-bottom-right-radius: 25px">
                            <ul class="list-unstyled mb-3 pb-4 position-relative ulC">
                                <li class="ilC text-left" style="color: #3161a3">
                                    <img src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">
                                    $2.00 un pago semestral
                                </li>
                                <li class="ilC text-left" style="color: #3161a3">
                                    <img src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">
                                    $1.80 un pago anual
                                </li>
                                <li class="ilC text-left" style="color: #3161a3">
                                    <img src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">
                                    Administrador de personal Ilimitado
                                </li>
                                <li class="ilC text-left" style="color: #3161a3">
                                    <img src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">
                                    Multiempresa Ilimitado
                                </li>
                            </ul> <button type="button" class="btn btn-lg btn-block btn-customD"
                                style="font-size: 16px;font-weight: bold">SUSCRIBIRSE</button>
                        </div>
                    </div>
                    <div class="col-4 princing-item">
                        <div class="pricing-dividerT">
                            <h6 class="font-weight-normal botonF1 pt-3 pb-1"
                                style="font-size: 43px;color:#407088;font-family: 'Calibri'">$2
                            </h6>
                            <h5 class="text-light pt-5" style="font-family: 'Poppins'">ENTERPRISE</h5>
                            <span class="h6" style="color: #ffffff;font-family: 'Poppins'">Desde 5001 empleados al
                                mes</span>
                            <svg class="svgTriangulo" width="100%" height="100" viewBox="0 0 100 102"
                                preserveAspectRatio="none">
                                <path d="M0 0 L50 90 L100 0 V100 H0" fill="#ffffff" />
                            </svg>
                        </div>
                        <div class="card-body bg-white mt-0 shadow bodyPrecio"
                            style="border-bottom-left-radius: 25px;border-bottom-right-radius: 25px">
                            <ul class="list-unstyled mb-3 pb-4 position-relative ulC">
                                <li class="ilC text-left" style="color: #264e70">
                                    <img src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">
                                    $1.80 un pago semestral
                                </li>
                                <li class="ilC text-left" style="color: #264e70">
                                    <img src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">
                                    $1.50 un pago anual</li>
                                <li class="ilC text-left" style="color: #264e70">
                                    <img src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">
                                    Administrador de personal Ilimitado
                                </li>
                                <li class="ilC text-left" style="color: #264e70">
                                    <img src="{{asset('landing/images/tick (4).svg')}}" height="15" class="mr-2">
                                    Multiempresa Ilimitado
                                </li>
                            </ul> <button type="button" class="btn btn-lg btn-block btn-customT"
                                style="font-size: 16px;font-weight: bold">SUSCRIBIRSE</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <strong style="color: #000839">Modo: Control Remoto / Home and office</strong>
            </div>
            <div class="col-md-9 mb-3" style="border-bottom: 3px solid #12cad6"></div>
        </div>
        <div class="row pt-3 pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Captura de actividad diaria</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #44b1cc60;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #3c6f9c60;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #40708860;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Control normal (cada 10 min)</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Calidad de captura</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #44b1cc60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Estándar</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #3c6f9c60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Estándar</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color:#40708860;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Estándar</p>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Eliminación de capturas (*e)</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">50$ x empresa</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">50$ x empresa</p>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Permitir actividad fuera de horario</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #44b1cc60;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #3c6f9c60;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #40708860;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Control intensivo (cada 5min)</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">2 emp.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">10 emp.</p>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Control superintensivo (cada1min)</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #44b1cc60;height: 30px;">
                    <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #3c6f9c60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">1 emp.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #40708860;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">1 emp.</p>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Control de tareas diarias</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Capturas en video basic (*v)Aleatorio</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #44b1cc60;height: 30px;">
                    <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color:#3c6f9c60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">2 emp.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #40708860;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">4 emp.</p>
                </div>
            </div>
        </div>
        <div class="row pt-3 pr-5">
            <div class="col-md-3 pt-4">
                <strong style="color: #000839;">Modo:Control en móvil en ruta
                    (Disponible en Android)</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <div class="pricing-divider">
                        <h6 class="text-light text-center m-0" style="font-weight: bold">DISPONIBLE
                            A PARTIR DE DICIEMBRE
                        </h6>
                        <svg class="svgTriangulo" width="100%" height="100" viewBox="0 0 100 102"
                            preserveAspectRatio="none">
                            <path d="M0 0 L50 90 L100 0 V100 H0" fill="#ffffff" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <div class="pricing-dividerD">
                        <h6 class="text-light text-center m-0" style="font-weight: bold">DISPONIBLE
                            A PARTIR DE DICIEMBRE
                        </h6>
                        <svg class="svgTriangulo" width="100%" height="100" viewBox="0 0 100 102"
                            preserveAspectRatio="none">
                            <path d="M0 0 L50 90 L100 0 V100 H0" fill="#ffffff" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <div class="pricing-dividerT">
                        <h6 class="text-light text-center m-0" style="font-weight: bold">DISPONIBLE
                            A PARTIR DE DICIEMBRE
                        </h6>
                        <svg class="svgTriangulo" width="100%" height="100" viewBox="0 0 100 102"
                            preserveAspectRatio="none">
                            <path d="M0 0 L50 90 L100 0 V100 H0" fill="#ffffff" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pt-5 pr-5">
            <div class="col-md-3">
                <strong style="color: #000839">Modo: Control de asistencia en Puerta</strong>
            </div>
            <div class="col-md-9 mb-3" style="border-bottom: 3px solid #12cad6"></div>
        </div>
        <div class="row pt-3 pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">En dispositivos Android</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #44b1cc60;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #3c6f9c60;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color:#40708860;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Biométricos ZKTECO</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Próximamente</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Próximamente</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Próximamente</p>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Biométricos Suprema V1 y V2</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #44b1cc60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Próximamente</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #3c6f9c60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Próximamente</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #40708860;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Próximamente</p>
                </div>
            </div>
        </div>
        <div class="row pt-2 pr-5">
            <div class="col-md-3 mt-2">
                <strong style="color: #000839">Opciones de pago</strong>
            </div>
            <div class="col-md-9 pb-2 pt-2 text-center" style="background-color:#12cad6">
                <strong class="mt-2" style="color:#ffffff">CUANDO REQUIERE MAYOR AUDITORÍA A UN EMPLEADO ESPECÍFICO
                    (MENSUAL)
                </strong>
            </div>
        </div>
        <div class="row pt-3 pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Control intensivo</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color:#44b1cc60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$1.5</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #3c6f9c60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$1.5</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #40708860;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$1.5</p>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Control superintensivo</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$3</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$3</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$3</p>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Capturas en video basic (*) Aleatorio</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #44b1cc60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$20</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color:#3c6f9c60;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$20</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #40708860;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$20</p>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Calidad de captura HD</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$1</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$1</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <p class="mt-1" style="color:#797a7e">$1</p>
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <strong style="color: #000839">Facturación</strong>
            </div>
            <div class="col-md-9 mb-2" style="border-bottom: 3px solid #12cad6"></div>
        </div>
        <div class="row pt-3 pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Factura de origen, USA</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color:#44b1cc60;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #3c6f9c60;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #40708860;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Facturación local (*f)</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pr-5">
            <div class="col-md-3">
                <p style="color: #000839;">Soporte local</p>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #44b1cc60;height: 30px;">
                    <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color:#3c6f9c60;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #40708860;height: 30px;">
                    <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img src="{{asset('landing/images/notification.svg')}}" height="100">
                <h4 class="text-danger mt-4">Su sesión expiró</h4>
                <p class="w-75 mx-auto text-muted">Por favor inicie sesión nuevamente.</p>
                <div class="mt-4">
                    <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i
                            class="uil uil-arrow-right mr-1"></i> Iniciar sesión</a>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
@endsection
@section('script')
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{asset('landing/js/app-menu.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<!-- optional plugins -->
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/gauge/gauge.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/apexcharts/apexcharts.js') }}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection