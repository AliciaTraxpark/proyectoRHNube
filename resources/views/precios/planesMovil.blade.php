@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
@endsection
@section('content')
<style>
    table {
        border: none;
    }

    .table thead th {
        border-bottom: none;
    }

    .table th,
    .table td {
        border-top: none;
    }

    .divContainer {
        border-radius: 5px;
        box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);
    }

    .pyme {
        border-top: 5px solid rgb(34, 193, 195);
    }

    .profesional {
        border-top: 5px solid rgb(43, 89, 187);
    }

    .enterprise {
        border-top: 5px solid rgb(51, 75, 125);
    }

    .titulo {
        padding-top: 0.5rem;
        font-weight: bold;
    }

    .tituloPyme {
        color: rgba(68, 177, 204, 1);
    }

    .tituloProfesional {
        color: rgba(60, 111, 156, 1);
    }

    .tituloEnterprise {
        color: rgba(64, 112, 136, 1);
    }
</style>
<div class="row p-5">
    <div class="col-md-12">
        <table id="planesMovil" class="table nowrap" style="font-size: 13px!important;width:100%;">
            <thead>
                <tr>
                    <th class="text-center">
                        <div class="divContainer pyme">
                            <p class="titulo tituloPyme">PYME</p>
                            <h4>$3</h4>
                            <span style="font-weight: 100">Hasta 200 empleados al mes</span>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="divContainer profesional">
                            <p class="titulo tituloProfesional">PROFESIONAL</p>
                            <h4>$3</h4>
                            <span style="font-weight: 100">Hasta 200 empleados al mes</span>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="divContainer enterprise">
                            <p class="titulo tituloEnterprise">ENTERPRISE</p>
                            <h4>$3</h4>
                            <span style="font-weight: 100">Hasta 200 empleados al mes</span>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center">Modalidad de control</th>
                </tr>
                <tr>
                    <td class="text-center"><span>$2.50 un pago semestral</span></td>
                    <td class="text-center"><span>$2.50 un pago semestral</span></td>
                    <td class="text-center"><span>$2.50 un pago semestral</span></td>
                </tr>
                <tr>
                    <td class="text-center"><span>$2.00 un pago anual</span></td>
                    <td class="text-center"><span>$2.00 un pago anual</span></td>
                    <td class="text-center"><span>$2.00 un pago anual</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center">Administrador de personal</th>
                </tr>
                <tr>
                    <td class="text-center"><span>Ilimitado</span></td>
                    <td class="text-center"><span>Ilimitado</span></td>
                    <td class="text-center"><span>Ilimitado</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center">Multiempresa</th>
                </tr>
                <tr>
                    <td class="text-center"><span>$2.00</span></td>
                    <td class="text-center"><span>Ilimitado</span></td>
                    <td class="text-center"><span>Ilimitado</span></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center">Modo: Control Remoto / Home and office</th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center">Captura de actividad diaria</th>
                </tr>
                <tr>
                    <td class="text-center">si</td>
                    <td class="text-center">si</td>
                    <td class="text-center">si</td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center">Captura de actividad diaria</th>
                </tr>
                <tr>
                    <td class="text-center">si</td>
                    <td class="text-center">si</td>
                    <td class="text-center">si</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('script')
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{asset('landing/js/app-menu.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<!-- optional plugins -->
<script src="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js')}}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection