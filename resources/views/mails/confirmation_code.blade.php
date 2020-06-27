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
                <div style="background-color: #163552;padding-top: 10px;padding-right: 25px;padding-left: 25px;">
                    <img src="{{'https://ibb.co/hyxrDF0'}}" width="30%" height="30%">
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
        style="background-color: #f7f8fa;text-align: center;padding-bottom: 10px;padding-right: 25px;padding-left: 25px;">
        <div style="padding-bottom: 10px;padding-top: 15px">
            <h4
                style="font-family: Poppins,sans-serif;color: black;text-align: center;font-weight: 600;text-transform: uppercase;">
                HOLA {{$persona->perso_nombre}} {{$persona->perso_apPaterno}}</h4>
            <span style="font-family: Poppins,sans-serif;color: black;text-align: center">Creaste una cuenta con RH
                SOLUTION</span>
            <span style="font-family: Poppins,sans-serif;color: black;text-align: center;font-weight: 600;">¡Gracias por
                registrarte!</span>
            <div>
                <div style="font-family: Poppins,sans-serif;color: black;text-align: center;">
                    <span>Para mayor seguridad necesitamos que verifiques tu correo electornico
                        antes de continuar en nustra plataforma.
                    </span>
                </div>
                <div>
                    <div class="col-md-12"> <br><br>
                        <a href="{{'http://3.208.88.131:8000/register/verify/' . $datos->confirmation_code}}"><button
                                class="btn btn-opacity-primary mr-1">
                                VERIFICA TU CORREO ELECTRONICO </button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
