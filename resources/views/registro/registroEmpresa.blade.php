<!DOCTYPE html>
<html lang="en">

<head>
    <title>Registra tu organizacion</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{asset('landing/images/ICONO-LOGO-NUBE-RH.ico')}}">
    <img height="1" width="1" style="display:none;" alt=""
        src="https://px.ads.linkedin.com/collect/?pid=2668442&conversionId=3456930&fmt=gif" />
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .tipoOrga_responsive{
            display: flex;
        }

        @media (max-width: 767px) {
            .navbar {
                padding: 0% !important;
            }

            .container {
                padding-bottom: 3% !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            .colResp {
                justify-content: center !important;
                padding: 0% !important;
            }

            .textResp {
                text-align: center !important;
            }

            .content-page {
                margin-right: 10px !important;
                margin-left: 10px !important;
                margin-top: 10px !important;
            }

            .align-items-center {
                text-align: center !important;
            }

            .inputResp {
                padding-bottom: 1rem !important;
            }

            .rowFecha {
                flex-wrap: nowrap !important;
            }

            .imgResp {
                display: none !important;
            }

            .content-wrapper {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .modal {
                text-align: center;
                padding: 0 !important;
            }

            .modal:before {
                content: '';
                display: inline-block;
                height: 100%;
                vertical-align: middle;
                margin-right: -4px;
                /* Adjusts for spacing */
            }

            .modal-dialog {
                display: inline-block;
                text-align: left;
                vertical-align: middle;
            }

            #diaN {
                width: 15% !important;
            }

            #mesN {
                width: 36% !important;
            }

            #anoN {
                width: 25% !important;
            }

            .col-form-label {
                text-align: center !important;
            }

            .textC {
                text-align: center !important;
            }

            .tipoOrga_responsive{
                display: block;
            }
        }

        @media (min-width: 0px) {
            footer {
              font-size: 15px;
              color: #555;
              background: #eee;
              text-align: center;
              position: fixed;
              display: block;
              width: 100%;
              bottom: 0;
            }
        }

    </style>
    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
            <div class="container">
                <div class="col-md-3 pl-5 colResp">
                    <div class="navbar-brand-wrapper d-flex w-100 colResp">
                        <img src="{{asset('landing/images/NUBE_SOLA.png')}}" height="69">
                    </div>
                </div>
                <div class="col-md-9 text-left pt-2 textResp">
                    <h5 style="color: #ffffff">Ahora registra tu organización: Empresarial, Gobierno, Ong…</h5>
                    <label for="" class="blanco font-italic">Tienes 2 minutos</label>

                </div>
            </div>
        </nav>
    </header>
    <div class="content-wrapper">
        <div class="container" style="padding-left: 5%;padding-right: 4%">
            @if (session('errors'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 14px!important;
            padding-top: 8px; padding-bottom: 8px;">
                {{ session('errors') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                    style="padding-top: 8px;  padding-bottom: 8px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <section class="features-overview" id="features-section">
                <!--MODAL ORGANIZACION-->
                <div id="organizacionModal" class="modal fade" tabindex="-1" role="dialog"
                    aria-labelledby="organizacionModal" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="padding-bottom:3px;padding-top:10px;background: #ecebeb">
                                <h5 class="modal-title" id="myModalLabel" style="font-size:14px">
                                    Personalizar organización
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:limpiartextOrganizacion()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                {{ csrf_field() }}
                                <div class="col-md-12">
                                    <label for="">Organización</label>
                                </div>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" id="textOrganizacion" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm"
                                    style="background-color: #f8f9fa!important;color: #343a40!important;font-size:14px;padding: 4px 8px 4px 8px"
                                    data-dismiss="modal" onclick="javascript:limpiartextOrganizacion()">Cerrar</button>
                                <button type="button"
                                    style="background:#163552;color: #ecebeb;font-size:14px;padding: 4px 8px 4px 8px"
                                    class="btn btn-sm" onclick="javascript:personalizadoOrganizacion()"
                                    id="guardarPersonalizarOrganizacion">Guardar</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <!-- MODAL DE ENVIO -->
                <div id="modalCargando" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCargando"
                    aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog   modal-lg" style="padding-left: 9%;">
                        <div class="modal-content" style="background: #ffffff;">
                            <div class="modal-body" style="padding-top: 8px;  padding-bottom: 0px;">
                                <div class="text-center">
                                    <h6 style="color: #163552;">Enviando datos, espere por favor.</h6>
                                    <div class="row">
                                        <div class="col-3"></div>
                                        <div class="col-6">
                                            <img src="{{asset('landing/images/loading.gif')}}" height="100">
                                        </div>
                                        <div class="col-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <form method="POST" action="{{route('registerOrganizacion')}}" onsubmit="javascript:validate(event)">
                    @csrf
                    <div class="row">
                        <div class="col-md-9">
                            <label for="" style="color:rgb(204, 5, 5);font-size:14px;font-weight: 600;display:none"
                                id="errorRUC">Ruc o ID ya registrado!</label>
                            <div class="row">
                                <input type="hidden" name="iduser" id="iduser" value="{{$userid}}">
                                <div class="col-md-4 inputResp">
                                    <input type="number" maxlength="11" min="1" max="" class="form-control " required
                                        placeholder="RUC o ID" name="ruc" id="ruc" value="{{ old('ruc') }}"
                                        onkeypress="return isNumeric(event)" oninput="maxLengthCheck(this)">
                                </div>
                                <div class="col-md-5 pb-3">
                                    <input class="form-control" placeholder="Razón social "
                                        value="{{ old('razonSocial') }}" name="razonSocial" id="razonSocial" required>
                                </div>
                                <div class="col-md-9 pb-3">
                                    <input class="form-control " placeholder="Direccion legal " name="direccion"
                                        value="{{ old('direccion') }}" id="direccion" required>
                                </div>
                            </div>
                            <div class="row pb-3">
                                <div class="col-md-3 inputResp">
                                    <select class="form-control" placeholder="Departamento " name="departamento"
                                        id="departamento" required>
                                        <option value="">DEPARTAMENTO</option>
                                        @foreach ($departamento as $departamentos)
                                        <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 inputResp">
                                    <select class="form-control " placeholder="Provincia " name="provincia"
                                        id="provincia" required>
                                        <option value="">PROVINCIA</option>

                                    </select>
                                </div>
                                <div class="col-md-3 inputResp">
                                    <select class="form-control " placeholder="Distrito " name="distrito" id="distrito"
                                        required>
                                        <option value="">DISTRITO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row pb-3">
                                <div class="col-md-4">
                                    <input class="form-control" type="number" placeholder="Núm de empleados"
                                        name="nempleados" id="nempleados" value="{{ old('nempleados') }}" required>
                                </div>
                            </div>
                            <div class="row pb-3">
                                <div class="col-md-9">
                                    <input class="form-control " placeholder="Página web o dominio(opcional)"
                                        name="pagWeb" id="pagWeb" value="{{ old('pagWeb') }}">
                                </div>
                            </div>
                            <div class="row pb-3" >
                                <div class="col-md-12">
                                    <label class="normal" for="">Tipo de organización</label>
                                </div>
                                <div class="container tipoOrga_responsive">
                                    <div class="col-md-3">
                                        <div class="control">
                                            <input type="hidden" class="form-control" id="inputOrgani" name="inputOrgani">
                                            <label class="radio normal">
                                                <input type="radio" name="tipo" id="tipo" value="Empresa" required>
                                                Empresa
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="control">
                                            <label class="radio normal">
                                                <input type="radio" name="tipo" id="tipo" value="Gobierno" required>
                                                Gobierno
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="control">
                                            <label class="radio normal">
                                                <input type="radio" name="tipo" id="tipo" value="ONG" required>
                                                ONG
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="control">
                                            <label class="radio normal">
                                                <input type="radio" name="tipo" id="tipo" value="Asociación" required>
                                                Asociación
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 pb-3">
                                        <div class="control">
                                            <label class="radio normal">
                                                <input type="radio" name="tipo" id="tipo" value="Otros" required>
                                                Otros
                                            </label>
                                            &nbsp;
                                            <a data-toggle="modal" id="organizacionPersonalizado">
                                                <img class="mt-0" style="cursor: pointer"
                                                    src="{{asset('landing/images/plus.svg')}}" height="15">
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="container col-md-3">
                            <img src="{{asset('landing/images/webdevelopment10.gif')}}" alt=""
                                class="img-fluid pb-5 imgResp">
                            <div class="col-md-12 text-right pb-3 textC">
                                <button class="btn btn-opacity-primary mr-1" type="submit">Registrar empresa </button>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
    <footer class="border-top" style="background:#163552; bottom: 0 !important; z-index: 100 !important;">
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

        function disabledForm(){
            $('button[type="submit"]').attr("disabled",true);
            $('#modalCargando').modal();
        }

        function validate(e){
            if(e.isTrusted == true){
                disabledForm();
            }
        }

    </script>
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/seleccionarDepProv.js')}}"></script>
    <script>
        function limpiartextOrganizacion() {
            $('#textOrganizacion').val("");
            $('#inputOrgani').val("");
            $('#guardarPersonalizarOrganizacion').prop('disabled', true);
        }
        $('#organizacionPersonalizado').hide();
        $('#guardarPersonalizarOrganizacion').prop('disabled', true);
        $("input[name=tipo]") // select the radio by its id
        .change(function () { // bind a function to the change event
            if ($(this).is(":checked")) { // check if the radio is checked
                var val = $(this).val(); // retrieve the value
                console.log(val);
                if(val == "Otros"){
                    $('#organizacionPersonalizado').show();
                }else{
                    $('#organizacionPersonalizado').hide();
                    limpiartextOrganizacion();
                }
            }
        });
        $('#organizacionPersonalizado').on("click", function () {
            var valor = $('#textOrganizacion').val();
            $('#organizacionModal').modal();
        });
        $('#textOrganizacion').keyup(function () {
            if ($(this).val() != '') {
                $('#guardarPersonalizarOrganizacion').prop('disabled', false);
            } else {
                $('#guardarPersonalizarOrganizacion').prop('disabled', true);
            }
        });
        function personalizadoOrganizacion() {
            var valor = $('#textOrganizacion').val();
            $('#inputOrgani').val(valor);
            console.log($('#inputOrgani').val());
            $('#organizacionModal').modal('toggle');
        }
    </script>
    <script>
        $(document).ready(function() {
        $('#ruc').focus();

        /* $('#ruc').on("change keyup paste click",function() { */
       /*  $('#ruc').mouseleave(function(){ */
        $('#ruc').on("blur",function() {
        consulta = $('#ruc').val();

         $.ajax({
            type: "post",
            url: "/organizacion/busquedaRuc",
            data: {"_token": "{{ csrf_token() }}",
                consulta
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            success: function (data) {
                if(data==1){
                    $('#errorRUC').show();
                } else{$('#errorRUC').hide();}
            },
            error: function (data) {
                alert('Error');
            }
                });
});
})
    </script>
    <script>
        function agregarEmpresa(){

        }
    </script>
</body>

</html>
