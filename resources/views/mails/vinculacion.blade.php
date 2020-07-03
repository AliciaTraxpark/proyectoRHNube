<html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

    </head>

    <body>
        <header>
            <div>
                <div class="col-md-5">
                    <div style="background-color: #163552;padding-top:
                        10px;padding-right: 25px;padding-left: 25px;">
                        <img
                            src="https://www.google.com/url?sa=i&url=https%3A%2F%2Frhsolution.com.pe%2F&psig=AOvVaw2LgZPx0H2YNVD09elO7e-E&ust=1593357711900000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCKDMy5qmouoCFQAAAAAdAAAAABAD"
                            width="10%" height="10%">
                    </div>
                </div>
            </div>
        </header>
        <style>
        .btn {
            font-family: Poppins, sans-serif;
            color: #ffffff;
            background-color: #163552;
            border: 2px solid #163552;
        }

        .btn:hover {
            cursor: pointer;
            color: #163552;
            background-color: #fff;
            border: 2px solid #163552;
        }

    </style>
        <div
            style="background-color: #f7f8fa;text-align: center;padding-bottom:
            10px;padding-right: 25px;padding-left: 25px;">
            <div style="padding-bottom: 10px;padding-top: 15px">
                <h4
                    style="font-family: Poppins,sans-serif;color:
                    black;text-align: center;font-weight: 600;text-transform:
                    uppercase;">
                    HOLA {{$persona->perso_nombre}} {{$persona->perso_apPaterno}}
                    {{$persona->perso_apMaterno}}</h4>
                <span style="font-family: Poppins,sans-serif;color:
                    black;text-align: center">Te damos la Bienvenida RH
                    SOLUTION</span>
                <div>
                    <div style="font-family: Poppins,sans-serif;color:
                        black;text-align: center;padding-bottom: 10px;">
                        <span>Para empezar usar nuestra plataforma, necesitamos
                            que descargue nuestra aplicación que se
                            encuentra disponible en el siguiente enlace.
                        </span>
                    </div>
                    <div class="col-md-12"> <br><br>
                        <a href="{{url('download/' . $vinculacion->descarga)}}"><button
                                class="btn btn-opacity-primary mr-1">
                                Descargar </button></a>
                    </div>
                    <div class="col-md-12"> <br><br>
                        <a href="{{url('download/' . $vinculacion->descarga)}}"><button
                                class="btn btn-opacity-primary mr-1">
                                Descargar </button></a>
                    </div>
                    <div style="font-family: Poppins,sans-serif;color:
                        black;text-align: center; padding-bottom: 10px;">
                        <span style="text-transform: uppercase;">Licencia de
                            Producto </span><span
                            style="background-color: #b7efcd; padding: 5px;">{{$licencia_empleado->licencia}}</span>
                    </div>
                    <div style="font-family: Poppins,sans-serif;color:
                        black;text-align: center; padding-bottom: 10px;">
                        <span style="text-transform: uppercase;">Codigo </span><span
                            style="background-color: #b7efcd; padding: 5px;">{{$vinculacion->hash}}</span>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
