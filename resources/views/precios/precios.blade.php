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
        border-radius: 20px;
        background: #448ef6;
        padding: 1em 0 4em;
        position: relative
    }

    .pricing-dividerD {
        border-radius: 20px;
        background: #3161a3;
        padding: 1em 0 4em;
        position: relative
    }

    .pricing-dividerT {
        border-radius: 20px;
        background: #264e70;
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
        background: #448ef6;
        color: #fff;
        border-radius: 20px
    }

    .btn-customD {
        background: #3161a3;
        color: #fff;
        border-radius: 20px
    }

    .btn-customT {
        background: #264e70;
        color: #fff;
        border-radius: 20px
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
<div class="row justify-content-center pt-5 pr-5 pl-5 pb-5">
    <div class="col-xl-12">
        <div class="row">
            <div class="col-md-3 pt-2">
                <strong style="color: #163552">PRICING RH NUBE</strong>
            </div>
            <div class="col-md-9" style="border-bottom: 3px solid #0ea5c6"></div>
        </div>
        <div class="row p-4">
            <div class="container-fluid">
                <div class="row m-auto text-center w-40">
                    <div class="col-4 princing-item">
                        <div class="pricing-divider ">
                            <h5 class="text-light">PYME</h5>
                            <h6 class="my-0 display-2 text-light font-weight-normal mb-2" style="font-size: 45px"> $3
                            </h6> <span class="h6 mb-2" style="color: #ffffff">AL MES-HASTA 200 EMP</span> <svg
                                class='pricing-divider-img' enable-background='new 0 0 300 100' height='100px'
                                id='Layer_1' preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100'
                                width='300px' x='0px' xml:space='preserve' y='0px'>
                                <path class='deco-layer deco-layer--4'
                                    d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428	c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z'
                                    fill='#FFFFFF'></path>
                            </svg>
                        </div>
                        <div class="card-body bg-white mt-0 shadow">
                            <ul class="list-unstyled mb-3 position-relative ulC">
                                <li class="ilC text-left" style="color: #448ef6"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18" class="mr-1">$2.5 un
                                    pago semestral</li>
                                <li class="ilC text-left" style="color: #448ef6"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18" class="mr-1">$2 un pago
                                    anual</li>
                                <li class="ilC text-left" style="color: #448ef6"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18"
                                        class="mr-1">Administrador de personal Ilimitado</li>
                                <li class="ilC text-left" style="color: #448ef6;"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18"
                                        class="mr-1">Multiempresa
                                </li>
                                <li class="ilC" style="color: #448ef6;font-size: 9px">(5$Por cada empresa nueva
                                    administrada por la misma cuenta)
                                </li>
                            </ul> <button type="button" class="btn btn-lg btn-block btn-custom"
                                style="font-size: 16px">SUSCRIBIRSE</button>
                        </div>
                    </div>
                    <div class="col-4 princing-item">
                        <div class="pricing-dividerD">
                            <h5 class="text-light">PROFESIONAL</h5>
                            <h6 class="my-0 display-2 text-light font-weight-normal mb-2" style="font-size: 45px">$2.5
                            </h6> <span class="h6" style="color: #ffffff">AL MES - DE 200 A 5000 EMP</span> <svg
                                class='pricing-divider-img' enable-background='new 0 0 300 100' height='100px'
                                id='Layer_1' preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100'
                                width='300px' x='0px' xml:space='preserve' y='0px'>
                                <path class='deco-layer deco-layer--4'
                                    d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428	c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z'
                                    fill='#FFFFFF'></path>
                            </svg>
                        </div>
                        <div class="card-body bg-white mt-0 shadow">
                            <ul class="list-unstyled mb-3 pb-4 position-relative ulC">
                                <li class="ilC text-left" style="color: #3161a3"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18" class="mr-1">$2 un pago
                                    semestral</b></li>
                                <li class="ilC text-left" style="color: #3161a3"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18" class="mr-1">$1.8 un
                                    pago anual</li>
                                <li class="ilC text-left" style="color: #3161a3"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18"
                                        class="mr-1">Administrador de personal Ilimitado</li>
                                <li class="ilC text-left" style="color: #3161a3"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18"
                                        class="mr-1">Multiempresa Ilimitado</li>
                            </ul> <button type="button" class="btn btn-lg btn-block btn-customD"
                                style="font-size: 16px">SUSCRIBIRSE</button>
                        </div>
                    </div>
                    <div class="col-4 princing-item">
                        <div class="pricing-dividerT">
                            <h5 class="text-light">ENTERPRISE</h5>
                            <h6 class="my-0 display-2 text-light font-weight-normal mb-2" style="font-size: 45px">$2
                            </h6><span class="h6" style="color: #ffffff">AL MES-MAYOR A 5000 EMP</span> <svg
                                class='pricing-divider-img' enable-background='new 0 0 300 100' height='100px'
                                preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100' width='300px' x='0px'
                                xml:space='preserve' y='0px'>
                                <path class='deco-layer deco-layer--4'
                                    d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428	c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z'
                                    fill='#FFFFFF'></path>
                            </svg>
                        </div>
                        <div class="card-body bg-white mt-0 shadow">
                            <ul class="list-unstyled mb-3 pb-4 position-relative ulC">
                                <li class="ilC text-left" style="color: #264e70"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18" class="mr-1">$1.8 un
                                    pago semestral</li>
                                <li class="ilC text-left" style="color: #264e70"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18" class="mr-1">$1.5 un
                                    pago anual</li>
                                <li class="ilC text-left" style="color: #264e70"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18"
                                        class="mr-1">Administrador de personal Ilimitado</li>
                                <li class="ilC text-left" style="color: #264e70"><img
                                        src="{{asset('landing/images/check.svg')}}" height="18"
                                        class="mr-1">Multiempresa Ilimitado</li>
                            </ul> <button type="button" class="btn btn-lg btn-block btn-customT"
                                style="font-size: 16px">SUSCRIBIRSE</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="row">
            <div class="col-md-3">
                <strong style="color: #163552">Modalidad de Control</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center">
                    <div class="card rounded" style="background-color: #a7d129;">
                        <div class="card-header"
                            style="background-color: #a7d129;border-bottom: 3px solid #ffffff;border-top-right-radius: 5px; border-top-left-radius: 5px;height: 40px;">
                            <h6 class="card-title font-weight-bold mb-2" style="color: #ffffff">Al mes - Hasta 200
                                emp.</h6>
                        </div>
                        <div class="card-body text-center mt-0" style="height: 50px;">
                            <strong style="color: #ffffff">PYME</strong>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <strong style="color: #a7d129;font-size: 18px">$3</strong>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #a7d129;font-size: 14px"><strong style="font-size: 16px">$2,50</strong> Un pago
                        semestral</p>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #a7d129;font-size: 14px"><strong style="font-size: 16px">$2</strong> Un pago anual
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center">
                    <div class="card rounded" style="background-color: #5fdde5;">
                        <div class="card-header"
                            style="background-color: #5fdde5;border-bottom: 3px solid #ffffff;border-top-right-radius: 5px; border-top-left-radius: 5px;height: 40px;">
                            <h6 class="card-title font-weight-bold mb-2" style="color: #ffffff">Al mes - de 200 a 5000
                                emp.</h6>
                        </div>
                        <div class="card-body text-center mt-0" style="height: 50px;">
                            <strong style="color: #ffffff">PROFESIONAL</strong>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <strong style="color: #5fdde5;font-size: 18px">$2,50</strong>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #5fdde5;font-size: 14px"><strong style="font-size: 16px">$2</strong> Un pago
                        semestral</p>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #5fdde5;font-size: 14px"><strong style="font-size: 16px">$1,80</strong> Un pago
                        anual</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center">
                    <div class="card rounded" style="background-color: #2c7873;">
                        <div class="card-header"
                            style="background-color: #2c7873;border-bottom: 3px solid #ffffff;border-top-right-radius: 5px; border-top-left-radius: 5px;height: 40px;">
                            <h6 class="card-title font-weight-bold mb-2" style="color: #ffffff">Al mes - Mayor a 5000
                                emp.</h6>
                        </div>
                        <div class="card-body text-center mt-0" style="height: 50px;">
                            <strong style="color: #ffffff">ENTERPRISE</strong>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <strong style="color: #2c7873;font-size: 18px">$2</strong>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #2c7873;font-size: 14px"><strong style="font-size: 16px">$1,80</strong> Un pago
                        semestral</p>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #2c7873;font-size: 14px"><strong style="font-size: 16px">$1,50</strong> Un pago
                        anual</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <strong style="color: #797a7e">Administradores de personal</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3 mb-3" style="background-color: #e1ffc2;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Ilimitado</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3 mb-3" style="background-color: #def4f0;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Ilimitado</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3 mb-3" style="background-color: #dae1e7;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Ilimitado</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 pt-2">
                <strong style="color: #797a7e">Multiempresa</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center pr-4 pl-4">
                    <p class="text-center" style="color: #797a7e;font-size: 13px">5$ Por cada empresa nueva administrada
                        por la misma cuenta
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center pr-4 pl-4 pt-2">
                    <p class="text-center" style="color: #797a7e;font-size: 13px">Ilimitado
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center pr-4 pl-4 pt-2">
                    <p class="text-center" style="color: #797a7e;font-size: 13px">Ilimitado
                    </p>
                </div>
            </div>
        </div> --}}
        <div class="row">
            <div class="col-md-3">
                <strong style="color: #163552">Modo: Control Remoto / Home and office</strong>
            </div>
            <div class="col-md-9 mb-3" style="border-bottom: 3px solid #0ea5c6"></div>
        </div>
        <div class="row pt-5">
            <div class="col-md-3">
                <strong style="color: #797a7e">Captura de actividad diaria</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #def4f0;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #dae1e7;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #b9ceeb;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-md-3">
                <strong style="color: #797a7e">Control normal (cada 10 min)</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-md-3">
                <strong style="color: #797a7e">Calidad de captura</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #def4f0;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Estándar</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #dae1e7;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Estándar</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #b9ceeb;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Estándar</p>
                </div>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-md-3">
                <strong style="color: #797a7e">Eliminación de capturas (*e)</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <img src="{{asset('landing/images/close (4).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Estándar</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Estándar</p>
                </div>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-md-3">
                <strong style="color: #797a7e">Permitir actividad fuera de horario</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #def4f0;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #dae1e7;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #b9ceeb;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-md-3">
                <strong style="color: #797a7e">Control intensivo (cada 5min)</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <img src="{{asset('landing/images/close (4).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">2 emp.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">10 emp.</p>
                </div>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-md-3">
                <strong style="color: #797a7e">Control superintensivo (cada1min)</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #def4f0;height: 30px;">
                    <img src="{{asset('landing/images/close (4).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #dae1e7;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">1 emp.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #b9ceeb;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">1 emp.</p>
                </div>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-md-3">
                <strong style="color: #797a7e">Control de tareas diarias</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #f1fff1;height: 30px;">
                    <img src="{{asset('landing/images/tick (3).svg')}}" height="22" class="mt-1">
                </div>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-md-3">
                <strong style="color: #797a7e">Capturas en video basic (*v)Aleatorio</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #def4f0;height: 30px;">
                    <img src="{{asset('landing/images/close (4).svg')}}" height="15" class="mt-2">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #dae1e7;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">2 emp.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3" style="background-color: #b9ceeb;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">4 emp.</p>
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