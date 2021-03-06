<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Registrate</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet"
            href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
        <link rel="stylesheet"
            href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
        <link rel="stylesheet"
            href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
        <link rel="stylesheet"
            href="{{asset('landing/vendors/aos/css/aos.css')}}">
        <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <link rel="stylesheet"
            href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
        <link rel="shortcut icon" href="{{asset('landing/images/ICONO-LOGO-NUBE-RH.ico')}}">
    </head>

    <body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
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
                <div class="container">
                    <div class="col-md-3 pl-5">
                        <div class="navbar-brand-wrapper d-flex w-100">
                            <img src="{{asset('landing/images/NUBE_SOLA.png')}}" height="69">
                        </div>
                    </div>
                    <div class="col-md-9 text-left pt-2">
                        <h5 style="color: #ffffff">Crear una cuenta</h5>
                        <label for="" class="blanco font-italic">Tienes 2 minutos</label>

                    </div>
                </div>
            </nav>
        </header>

        <div class="content-wrapper">
            <div class="container" style="padding-left: 5%;padding-right: 4%">
                <section class="features-overview" id="features-section">
                     {{--  Modal doble email --}}
                   <div id="myModalEmail" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" style="overflow-y: auto;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="padding-bottom:
                                3px; padding-top:
                                10px;background: #ecebeb">
                                <h5 class="" id="myModalLabel"
                                    style="font-size: 14px">Email ya registrado
                                </h5>
                               {{--  <button type="button" class="close"
                                    data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button> --}}
                            </div>
                            <div class="modal-body">
                                <form method="POST"
                                    action="javascript:confirmarEmail()">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="col-lg-12
                                                col-form-label"
                                                for="simpleinput">El email:  </label>

                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" disabled class="col-md-8" id="email2">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="col-lg-12
                                            col-form-label"
                                            for="simpleinput" for="">pertenece a un usuario
                                            registrado,para confirmar su invitacion como invitado escriba su contrase??a:</label>
                                        </div>
                                        <div class="col-md-12">
                                            <span  id="spanInc" style="color: #d41616;display: none;font-size: 12px!important;font-weight: 500;">Contrase??a incorrecta</span>
                                            <input type="password" class="form-control" id="claveCon" placeholder="escriba contrase??a">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">

                                    <button type="submit" style="background:
                                        #302f56;color: #ecebeb" class="btn
                                        btn-sm" id="confirmar"> &nbsp;
                                        &nbsp; Confirmar email
                                        &nbsp; &nbsp; </button>
                                </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

                    <!--MODAL FECHA-->
                    <div id="myModal" class="modal fade" tabindex="-1"
                        role="dialog" aria-labelledby="myModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="padding-bottom:
                                    3px; padding-top:
                                    10px;background: #ecebeb">
                                    <h5 class="" id="myModalLabel"
                                        style="font-size: 14px">Confirma tu
                                        fecha de nacimiento
                                    </h5>
                                    <button type="button" class="close"
                                        data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST"
                                        action="javascript:registerP()">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label class="col-lg-12
                                                    col-form-label"
                                                    for="simpleinput">Naciste el<input
                                                        type="text"
                                                        id="diaN" class="inp
                                                        col-md-1" disabled>de<input
                                                        type="text" class="inp
                                                        col-md-3" id="mesN"
                                                        disabled>de<input
                                                        type="text" id="anoN"
                                                        class="inp col-md-2"
                                                        disabled>?</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" style="background:
                                            #f0f0f0" class="btn btn-sm"
                                            data-dismiss="modal">&nbsp; &nbsp;No
                                            &nbsp; &nbsp;</button>
                                        <button type="submit" style="background:
                                            #302f56;color: #ecebeb" class="btn
                                            btn-sm" id="confirmar"> &nbsp;
                                            &nbsp; S??
                                            &nbsp; &nbsp; </button>
                                    </div>
                                </form>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <!--MODAL GENERO-->
                    <div id="generoModal" class="modal fade" tabindex="-1"
                        role="dialog" aria-labelledby="generoModal"
                        aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header"
                                    style="padding-bottom:3px;
                                    padding-top:10px;background: #ecebeb">
                                    <h5 class="modal-title" id="myModalLabel"
                                        style="font-size: 14px">
                                        Personalizar g??nero</h5>
                                    <button type="button" class="close"
                                        data-dismiss="modal" aria-label="Close"
                                        onclick="javascript:limpiartextSexo()">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    {{ csrf_field() }}
                                    <div class="col-md-12">
                                        <label for="">G??nero</label>
                                    </div>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control"
                                            id="textSexo" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm"
                                        style="background:
                                        #f0f0f0" data-dismiss="modal"
                                        onclick="javascript:limpiartextSexo()">Cerrar</button>
                                    <button type="button" class="btn btn-sm"
                                        style="background:
                                        #302f56;color: #ecebeb" class="btn
                                        btn-sm"
                                        onclick="javascript:personalizadoGenero()"
                                        id="guardarPersonalizarSexo">Guardar</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <form method="POST" action="javascript:agregarempleado()">
                        {{ csrf_field() }}
                        <div class="row">
                            @php
                            use App\organizacion;
                            use App\invitado;
                            use App\persona;

                            $invitado=invitado::where('idinvitado','=',$idInvit)->get()->first();
                        @endphp
                            <input type="hidden" name="idInvit" value="{{$idInvit}}" id="idInvit">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-9">
                                        <input class="form-control" disabled
                                            placeholder="Correo electr??nico"
                                            name="email" id="email" value="{{$invitado->email_inv}}" required>
                                        {{$errors->first('email')}}
                                    </div><br><br>

                                    <div class="col-md-9">
                                        <input class="form-control"
                                            type="password"
                                            placeholder="Contrase??a nueva"
                                            name="password" id="password"
                                            value="{{old ('password')}}"
                                            required>
                                        {{$errors->first('password')}}
                                    </div> <div class="col-md-3"></div> <br><br>
                                    <div class="col-md-9">
                                        <input type="number" maxlength="12" min="1"  class="form-control"
                                            placeholder="Celular" name="n_celular" onkeypress="return isNumeric(event)"
                                            oninput="maxLengthCheck(this)"
                                            id="n_celular"
                                            required>
                                        {{$errors->first('nombres')}}
                                    </div><br><br> <div class="col-md-3"></div>
                                    <div class="col-md-3">
                                        <input class="form-control"
                                            placeholder="Nombres" name="nombres"
                                            id="nombres"
                                            value="{{old ('nombres')}}"
                                            required>
                                        {{$errors->first('nombres')}}
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control"
                                            placeholder="Apellido Paterno"
                                            name="apPaterno"
                                            id="apPaterno" value="{{old
                                            ('apellidos')}}" required>
                                        {{$errors->first('apellidos')}}
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control"
                                            placeholder="Apellido Materno"
                                            name="apMaterno"
                                            id="apMaterno" value="{{old
                                            ('apellidos')}}" required>
                                        {{$errors->first('apellidos')}}
                                    </div> <br><br>

                                    <div class="col-md-9">
                                        <input class="form-control"
                                            placeholder="Direccion"
                                            name="direccion" id="direccion"
                                            value="{{old ('direccion')}}"
                                            required>
                                        {{$errors->first('direccion')}}
                                    </div>
                                    <div class="col-md-9" id="validCelCorreo"
                                        style="display: none">
                                        <br>
                                        <span style="color:red;"> Correo
                                            electr??nico incorrecto.</span><br></div><br><br>

                                </div>
                                <div class="row">

                                    <div class="col-md-12" style="color:
                                        #d03310; font-size: 14px;" id="Mensaje"></div>
                                    <div class="col-md-12 mt-2">
                                        <label class="normal" for="">Fecha de
                                            nacimiento:</label>
                                    </div>
                                    <div class="col-md-9">
                                        {{-- <div class="datepicker date
                                            input-group p-0 shadow-sm">
                                            <input type="text"
                                                placeholder="elegir fecha"
                                                class="form-control" id="fecha"
                                                name="fecha" value="{{old('fecha')}}"
                                                required>
                                            {{$errors->first('fecha')}}
                                            <div class="input-group-append"><span
                                                    class="input-group-text"><i
                                                        class="fa fa-calendar"></i></span></div>
                                        </div> --}}
                                        <div class="row">
                                            <div class="col-md-3">
                                                <select class="form-control"
                                                    name="dia_fecha"
                                                    id="dia_fecha" required="">
                                                    <option value="">D??a</option>
                                                    @for ($i = 1; $i <32; $i++)
                                                    <option class=""
                                                        value="{{$i}}">{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <select class="form-control"
                                                    name="mes_fecha"
                                                    id="mes_fecha" required="">
                                                    <option value="">Mes</option>
                                                    <option class="" value="1">Enero</option>
                                                    <option class="" value="2">Febrero</option>
                                                    <option class="" value="3">Marzo</option>
                                                    <option class="" value="4">Abril</option>
                                                    <option class="" value="5">Mayo</option>
                                                    <option class="" value="6">Junio</option>
                                                    <option class="" value="7">Julio</option>
                                                    <option class="" value="8">Agosto</option>
                                                    <option class="" value="9">Setiembre</option>
                                                    <option class="" value="10">Octubre</option>
                                                    <option class="" value="11">Noviembre</option>
                                                    <option class="" value="12">Diciembre</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <select class="form-control"
                                                    name="ano_fecha"
                                                    id="ano_fecha" required="">
                                                    <option value="">A??o</option>
                                                    @for ($i = 1960; $i <2011;
                                                        $i++) <option class=""
                                                        value="{{$i}}">{{$i}}
                                                    </option>
                                                    @endfor
                                                </select>
                                            </div>

                                        </div>

                                    </div>
                                   <br><br>
                                    <div class="col-md-12">
                                        <label class="normal" for="">G??nero:</label>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="control">
                                            <label class="radio normal">
                                                <input type="radio" name="sexo"
                                                    id="sexo" value="Mujer"
                                                    required>
                                                Mujer
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="control">
                                            <label class="radio normal">
                                                <input type="radio" name="sexo"
                                                    id="sexo" value="Hombre"
                                                    required>
                                                Hombre
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="control">
                                            <label class="radio normal"
                                                data-toggle="tooltip"
                                                data-placement="bottom" title=""
                                                data-original-title="Puedes
                                                elegir personalizado si no
                                                deseas especificar tu sexo.">
                                                <input type="radio" name="sexo"
                                                    id="sexo"
                                                    value="Personalizado"
                                                    required>
                                                Personalizado
                                            </label>
                                            &nbsp;
                                            <a data-toggle="modal"
                                                id="generoPersonalizado">
                                                <img
                                                    src="{{asset('landing/images/plus.svg')}}"
                                                    style="cursor: pointer"
                                                    height="15">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <br><br>

                            </div>
                            <div class="container col-md-3">
                                <img
                                    src="{{asset('landing/images/career.gif')}}"
                                    alt="" class="img-fluid"><br><br><br><br>
                                <div class="col-md-12 text-center">

                                    <button type="submit" class="btn
                                        btn-opacity-primary mr-1">Registrarme</button>
                                </div> <br><br>
                            </div>
                        </div>
                    </form>
                    <div class="modal" id="modalInv" role="dialog" tabindex="-1"   aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;"  >
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="padding-top: 8px;
                                            padding-bottom: 5px;background-color:
                                            #163552;color:#ffffff">
                                    <h5 style="font-size: 14px" class="modal-title">CONFIRMACION</h5>

                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1 mt-2">
                                    <p>Bien hecho, estas registrado! Te hemos enviado un correo de verificaci??n.</p>
                                </div>
                                <div class="modal-footer" style="padding-top: 8px;
                                            padding-bottom: 8px;">
                                    <button type="button" class="btn
                                    btn-sm" style="background-color:
                                    #163552;color:#ffffff" onclick="cerrarModal()">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal" id="modalInvR" role="dialog" tabindex="-1"   aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;"  >
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="padding-top: 8px;
                                            padding-bottom: 5px;background-color:
                                            #163552;color:#ffffff">
                                    <h5 style="font-size: 14px" class="modal-title">CONFIRMACION</h5>

                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1 mt-2">
                                    <p>Bien hecho, estas registrado! Ahora tienes registrada una nueva organizacion.</p>
                                </div>
                                <div class="modal-footer" style="padding-top: 8px;
                                            padding-bottom: 8px;">
                                    <button type="button" class="btn
                                    btn-sm" style="background-color:
                                    #163552;color:#ffffff" onclick="cerrarModal()">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal" id="modalInvRegistra" role="dialog" tabindex="-1"   aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;"  >
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="padding-top: 8px;
                                    padding-bottom: 5px;background-color:
                                    #163552;color:#ffffff">
                            <h6 style="font-size: 14px" class="modal-title">Advertencia</h6>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{asset('landing/images/alert.svg')}}" height="25" class="mr-1 mt-2">
                            <p>Usted ya utillizo este enlace para registrase.</p>
                        </div>
                        <div class="modal-footer text-center" style="padding-top: 5px;
                                    padding-bottom: 5px;">
                            <button type="button" onclick="cerrarModalAdvertencia()" class="btn
                                        btn-sm" style="background-color:
                                        #163552;color:#ffffff" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

                </section>
                <footer class="border-top">
                    <p class="text-center text-muted pt-4">?? <?php echo date("
                            Y" ); ?> - RH nube Corp - USA | Todos los derechos reservados.</p>
                    </footer>

                </div>
            </div>

            <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
            <script
                src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
            <script
                src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
            <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
            <script src="{{asset('landing/js/landingpage.js')}}"></script>
            <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
            <script src="{{
                URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')
                }}"></script>
            <script src="{{
                URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')
                }}"></script>
            <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
            <!-- App js -->
            <script src="{{asset('admin/assets/js/app.min.js')}}"></script>
            <script src="{{asset('landing/js/registroInviPerso.js')}}"></script>
            <script
                src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
            <script
                src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
            <script>
        function limpiartextSexo(){
            $('#textSexo').val("");
            $('#guardarPersonalizarSexo').prop('disabled', true);
        }
        $('#validCelCorreo').hide();
        $('#generoPersonalizado').hide();
        $('#guardarPersonalizarSexo').prop('disabled', true);
        $("input[name=sexo]") // select the radio by its id
        .change(function () { // bind a function to the change event
        if ($(this).is(":checked")) { // check if the radio is checked
            var val = $(this).val(); // retrieve the value
            console.log(val);
            if(val == "Personalizado"){
            $('#generoPersonalizado').show();
            }else{
                $('#generoPersonalizado').hide();
                limpiartextSexo();
            }
        }
    });
    $('#generoPersonalizado').on("click", function(){
            $('#generoModal').modal();
    });
    $('#textSexo').keyup( function(){
        if($(this).val() != ''){
            $('#guardarPersonalizarSexo').prop('disabled', false);
        }else{
            $('#guardarPersonalizarSexo').prop('disabled', true);
        }
    });
    </script>
            <script>
        $(function () {

            // INITIALIZE DATEPICKER PLUGIN
            $('.datepicker').datepicker({
                clearBtn: true,
                format: "dd/mm/yyyy",
                locale: 'es-es',
            });


            // FOR DEMO PURPOSE
            $('#fecha').on('change', function () {
                var pickedDate = $('input').val();
                $('#pickedDate').html(pickedDate);
            });
        });

    var URLactual = window.location;
    var texto = '"'+URLactual+'';
    separador = "registroInvitado/";
   textoseparado = texto.split(separador);
/* alert(textoseparado[1]); */
    </script>
    <script>
        $( document ).ready(function() {
            var email=$('#email').val();
            var idinvitad=$('#idInvit').val();
            $.ajax({
                    type: "POST",
                    url: "/verificarInvitadoreg",
                    data: {
                        idinvitado:idinvitad

                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {

                        if(data==1){
                            $('.modal').modal('hide');
                        $('#modalInvRegistra').modal({backdrop: 'static', keyboard: false});
                        $('#modalInvRegistra').modal('show');
                        }
                        else{
                        $.ajax({
                            type: "get",
                            url: "/persona/comprobar",
                            data: {
                                email: email
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (data) {
                                if (data == 1) {
                                    $('#myModalEmail').modal({backdrop: 'static', keyboard: false})
                                    $('#email2').val(email);
                                    $('#myModalEmail').modal('show');

                                    return false;

                                } else {

                                }
                            },

                });

                        }


                    }

                });
            ///////////////////////////////////

    });
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
        </body>

    </html>
