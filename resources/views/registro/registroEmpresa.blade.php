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
    <link rel="shortcut icon"
        href="https://rhsolution.com.pe/wp-content/uploads/2019/06/small-logo-rh-solution-64x64.png" sizes="32x32">
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
<style>
    input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number] { -moz-appearance:textfield; }
</style>
    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
            <div class="container">
                <div class="col-md-3">
                    <div class="navbar-brand-wrapper d-flex w-100">
                        <img src="{{asset('landing/images/logo.png')}}" height="100">
                    </div>
                </div>
                <div class="col-md-9 text-left">
                    <h5 style="color: #ffffff">Ahora registra tu organización: Empresarial, Gobierno, Ong…</h5>
                    <label for="" class="blanco font-italic">Tienes 2 minutos</label>

                </div>
            </div>
        </nav>
    </header>

    <div class="content-wrapper">
        <div class="container">
            @if (session('errors'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 14px!important;
            padding-top: 8px; padding-bottom: 8px;" >
             {{ session('errors') }}
                <button type="button" class="close" data-dismiss="alert"aria-label="Close" style="padding-top: 8px;  padding-bottom: 8px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <section class="features-overview" id="features-section">
                <form method="POST" action="{{route('registerOrganizacion')}}">
                    @csrf
                    <div class="row">
                        <div class="col-md-9">
                            <label for="" style="color:rgb(204, 5, 5);font-size:14px;font-weight: 600;display:none" id="errorRUC">Ruc o ID ya registrado!</label>
                            <div class="row">
                                <input type="hidden" name="iduser" id="iduser" value="{{$userid}}">
                                <div class="col-md-4">
                                    <input type="number" maxlength="11" min="1" max="" class="form-control "required
                                        placeholder="RUC o ID" name="ruc" id="ruc" value="{{ old('ruc') }}"  onkeypress="return isNumeric(event)"
                                        oninput="maxLengthCheck(this)">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" placeholder="Razón social " value="{{ old('razonSocial') }}" name="razonSocial"
                                        id="razonSocial" required>
                                </div> <br><br>
                                <div class="col-md-9">
                                    <input class="form-control " placeholder="Direccion legal " name="direccion"  value="{{ old('direccion') }}"
                                        id="direccion" required>
                                </div><br><br>
                            </div>
                            <div class="row">

                                <div class="col-md-3">
                                    <select class="form-control" placeholder="Departamento " name="departamento"
                                        id="departamento" required>
                                        <option value="">DEPARTAMENTO</option>
                                        @foreach ($departamento as $departamentos)
                                        <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control " placeholder="Provincia " name="provincia"
                                        id="provincia" required>
                                        <option value="">PROVINCIA</option>

                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control " placeholder="Distrito " name="distrito" id="distrito"
                                        required>
                                        <option value="">DISTRITO</option>
                                    </select>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-4">
                                    <input class="form-control" type="number" placeholder="Num de empleados"
                                        name="nempleados" id="nempleados" value="{{ old('nempleados') }}" required>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-9">
                                    <input class="form-control "  placeholder="Página web o dominio(opcional)"
                                        name="pagWeb" id="pagWeb" value="{{ old('pagWeb') }}">
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="normal" for="">Tipo de organización</label>
                                </div>
                                <div class="col-md-2">
                                    <div class="control">
                                        <label class="radio normal">
                                            <input type="radio" name="tipo" id="tipo" value="Empresa" required>
                                            Empresa
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
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
                                <div class="col-md-2">
                                    <div class="control">
                                        <label class="radio normal">
                                            <input type="radio" name="tipo" id="tipo" value="Asociación" required>
                                            Asociación
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="control">
                                        <label class="radio normal">
                                            <input type="radio" name="tipo" id="tipo" value="Otros" required>
                                            Otros
                                        </label>
                                    </div>
                                </div>

                                <br><br>

                            </div>
                            <br><br>

                        </div>
                        <div class="container col-md-3">
                            <img src="{{asset('landing/images/webdevelopment10.gif')}}" alt=""
                                class="img-fluid"><br><br><br><br>
                            <div class="col-md-12 text-right">
                                <button class="btn btn-opacity-primary mr-1" type="submit">Registrar empresa </button>
                            </div> <br><br>
                        </div>
                    </div>
                </form>
            </section>



            <footer class="border-top">
                <p class="text-center text-muted pt-4">© <?php echo date("Y"); ?> - RH Solution | Todos los derechos
                    reservados.</p>
            </footer>

        </div>
    </div>
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


    </script>
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>
    <script src="{{asset('landing/js/seleccionarDepProv.js')}}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
</body>

</html>
