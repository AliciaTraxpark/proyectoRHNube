<input type="hidden" name="idorgani" id="idorgani" value="{{ session('sesionidorg') }}">
<input type="hidden" name="" id="AñoOrgani" value="{{ $fechaEnviJS }}">
<input type="hidden" id="fechaEnviF">
<style>
    input[type="text"]:disabled {
  background: #f9f9f9;
}
</style>
{{-- MODAL DESCANSO --}}
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días de
                    descanso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>¿Asignar días de descanso?</h5>
                <input type="hidden" id="fechaDa" name="fechaDa">
                {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Inicial:</label> --}}
                <input type="hidden" name="start" class="form-control" id="start" readonly>
                {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Final:</label> --}}
                <input type="hidden" name="end" class="form-control" id="end" readonly>
                <input type="hidden" name="title" id="title" value="Descanso">
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                            <button type="button" id="guardarDescanso" name="guardarDescanso"
                                class="btn btn-secondary">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- MODAL FESTIVO --}}
<div id="myModalFestivo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días no
                    laborales</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>¿Asignar días no laborales?</h5>
                <input type="hidden" id="fechaDa2" name="fechaDa2">
                {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Inicial:</label> --}}
                <input type="hidden" name="startF" class="form-control" id="startF" readonly>
                {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Final:</label> --}}
                <input type="hidden" name="endF" class="form-control" id="endF" readonly>
                <input type="hidden" name="titleN" id="titleN" value="No laborable">
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                            <button type="button" id="guardarNoLab" name="guardarNoLab"
                                class="btn btn-secondary">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- ELIMINAR --}}
<div id="myModalEliminarD" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días de descanso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 style="font-size: 14px" class="modal-title" id="myModalLabel">¿Desea eliminar días descanso?
                    </h5>
                    <input type="hidden" id="idDescansoEl">
                </form>


            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                            <button type="button" onclick="EnviarDescansoE()" style="background-color: #163552;"
                                class="btn btn-sm">Eliminar</button>
                        </div>

                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- MODAL FERIADO --}}
<div id="myModalFeriado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar nuevo feriado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="javascript:registrarDferiado()">
                    <div class="row col-md-12">
                        <div class="col-md-6">
                            <label for="">Nombre de día feriado:</label>
                        </div>
                        <div class="col-md-10" id="divFeriadoSe">

                            <select id="nombreFeriado" data-plugin="customselect" class="form-control form-control-sm"
                                data-placeholder="seleccione" required>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary btn-sm" id="btnAgregaNFeri"
                                style="background-color: #183b5d;border-color:#62778c;margin-top: 5px;"
                                onclick="nuevoFeriadoReg()">
                                +
                            </button>
                        </div>

                        <div id="divFeriadoNuevo" class="col-md-12" style="display: none">
                            <input type="text" id="inputNuevoFeriado" class="form-control form-control-sm" >
                        </div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Código:</label>
                                <input type="text" class="form-control form-control-sm" disabled
                                    id="codigoFeriado">
                            </div>
                        </div>
                        <div class="col-md-4 text-right" >
                            <div class="custom-control custom-switch" style="margin-top: 34px;">
                                <input type="checkbox" class="custom-control-input" id="sepagaFCheck" disabled>
                                <label class="custom-control-label" for="sepagaFCheck"
                                    style=""> Se paga</label><br>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="startFeriado" class="form-control" id="startFeriado" readonly>

                    <input type="hidden" name="endFeriado" class="form-control" id="endFeriado" readonly>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-secondary btn-sm">Aceptar</button>
                        </div>
                        </form>

                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- MODAL DESCANSO --}}
<div id="myModalDescanso" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar descanso
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="javascript:registrarDdescanso()">
                    <div class="col-md-12">
                        <div class="row">

                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="">Nombre de descanso:</label>
                                    <div id="divDescansoSe">
                                        <select id="nombreDescanso" data-plugin="customselect" class="form-control form-control-sm"
                                            data-placeholder="seleccione" required>
                                        </select>
                                    </div>
                                    <div id="divDescansoNuevo" style="display: none">
                                        <input type="text" id="inputNuevoDescanso" class="form-control form-control-sm" >
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm" id="btnAgregaNDescanso"
                                    style="background-color: #183b5d;border-color:#62778c;margin-top: 35px;"
                                    onclick="nuevoDescansoReg()">
                                    +
                                </button>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Código:</label>
                                    <input type="text" class="form-control form-control-sm" disabled
                                        id="codigoDescanso">
                                </div>
                            </div>
                            <div class="col-md-4 text-right" >
                                <div class="custom-control custom-switch" style="margin-top: 34px;">
                                    <input type="checkbox" class="custom-control-input" id="sepagaCheck" disabled>
                                    <label class="custom-control-label" for="sepagaCheck"
                                        style=""> Se paga</label><br>
                                </div>
                            </div>

                        </div>
                    </div>
                    <input type="hidden" name="startDescanso" class="form-control" id="startDescanso" readonly>

                    <input type="hidden" name="endDescanso" class="form-control" id="endDescanso" readonly>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-secondary btn-sm">Aceptar</button>
                        </div>
                        </form>

                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- elimibar no lab --}}
<div id="myModalEliminarN" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días no
                    Laborales</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 style="font-size: 14px" class="modal-title" id="myModalLabel">¿Desea eliminar días
                        no laborales?</h5>
                    <input type="hidden" id="idnolabEliminar">
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                            <button type="button" onclick="eliminarEvNL()" style="background-color: #163552;"
                                class="btn btn-sm">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- ELIMINAR FERIADO --}}
<div id="myModalEliminarFeriado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Día feriado
                    de usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 style="font-size: 14px" class="modal-title" id="myModalLabel">¿Desea eliminar día
                        feriado?</h5>
                    <input type="hidden" id="idFeriadoeliminar">
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                            <button type="button" onclick="eliminarEvF()" style="background-color: #163552;"
                                class="btn btn-sm">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="agregarCalendarioN" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Nuevo
                    calendario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <form  action="javascript:agregarcalendario()">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="text" class="form-control" id="nombreCalen"
                                        placeholder="Nombre nuevo calendario" required><br>
                                </div>
                                <div class="col-md-4 form-check" style="padding-left: 32px; margin-top: 4px;">
                                    <input type="checkbox" class="form-check-input" id="clonarCheck">
                                    <label class="form-check-label" for="clonarCheck">Clonar calendario
                                        de:</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="" id="selectClonar" class="form-control form-control-sm" disabled>
                                        <option hidden selected>Seleccione calendario</option>
                                        @foreach ($calendario as $calendarios)
                                            <option class="" value="{{ $calendarios->calen_id }}">
                                                {{ $calendarios->calendario_nombre }}</option>
                                        @endforeach
                                    </select>
                                </div><br><br>
                                <div class="col-md-12" id="añosCalen">
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">Cancelar</button>
                            <button type="submit" id="guardarCalm" name="" style="background-color: #163552;"
                                class="btn btn-sm ">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="añadirNuevoa" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;">

        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Añadir año</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">

                    <div class="col-md-12">
                        <form  action="javascript:editarfinC()">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="text" id="textoNuevoAño" class="col-md-12" style="font-size: 15px; background-color: rgb(255, 255, 255);
                        border: 0;">
                                    <input type="hidden" id="añotNuevo">
                                </div>
                            </div>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light  " data-dismiss="modal">Cancelar</button>
                            <button type="submit" name="" style="background-color: #163552;"
                                class="btn">Aceptar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- CALRNDARIO EMP --}}

<div id="calendarioEmple" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex modal-dialog-scrollable justify-content-center " style="width: 790px;">

        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar empleados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <label for="" id="textCalend" style="font-size: 14px;  font-weight: 600;"></label> &nbsp;
                        <button type="button" class="btn btn-sm mt-1" onclick="$( '#tableDataem' ).toggle();"
                            style="background-color: #163552;color: #f9f9f9;margin-bottom: 6px;"><i style="height: 16px"
                                data-feather="eye"></i>ver empleados
                        </button>
                    </div>
                    <div class="col-md-12" id="tableDataem" style="display: none">
                        <br><label style="font-size: 14px; ">Lista de empleados:</label>
                        <div class="col-md-12" style="    padding-left: 0px;">
                            <table id='tabEmpleado' width='100%' class="table  nowrap">
                                <thead>
                                    <tr>
                                        <td>Nombres</td>
                                        <td>Apellido paterno </td>
                                        <td>Apellido materno </td>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                    <div class="col-md-12" style="border-bottom: 1px solid #f1f1f1;
               padding-bottom: 12px;">

                        <form id="asignacionCa" action="javascript:asignarCalendario()">
                            <div class="row">
                                <div class="col-md-9" style="zoom:90%;">
                                    <input type="hidden" id="fechaDa" name="fechaDa">
                                    <label for="" style="font-weight: 600;">Seleccionar empleado(s):</label>
                                </div>
                                <div class="col-md-7" style="zoom:90%;">
                                    <div class="row" style="margin-left: 6px;">
                                        <div class="col-md-5 form-check">
                                            <input type="checkbox" class="form-check-input" id="selectTodoCheck">
                                            <label class="form-check-label" for="selectTodoCheck"
                                                style="font-style: oblique;margin-top: 2px;">Seleccionar todos.</label>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <select class="form-control wide" data-plugin="customselect" multiple
                                        id="nombreEmpleado" required>
                                        @foreach ($empleado as $empleados)
                                            <option value="{{ $empleados->emple_id }}">
                                                {{ $empleados->perso_nombre }}
                                                {{ $empleados->perso_apPaterno }} {{ $empleados->perso_apMaterno }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-2">
                                    <label for="" style="margin-top: 9px;">Seleccionar por:</label>
                                </div>
                                <div class="row col-md-4">
                                    <select data-plugin="customselect" id="selectEmpresarial" name="selectEmpresarial"
                                        class="form-control" data-placeholder="seleccione">
                                        <option value=""></option>
                                        @foreach ($area as $areas)
                                            <option value="{{ $areas->idarea }}">Area : {{ $areas->descripcion }}.
                                            </option>
                                        @endforeach
                                        @foreach ($cargo as $cargos)
                                            <option value="{{ $cargos->idcargo }}">Cargo :
                                                {{ $cargos->descripcion }}.
                                            </option>
                                        @endforeach
                                        @foreach ($local as $locales)
                                            <option value="{{ $locales->idlocal }}">Local :
                                                {{ $locales->descripcion }}.</option>
                                        @endforeach
                                    </select>
                                </div><br>

                            </div>

                    </div>

                    <div id="espera" class="col-md-12 text-center" style="display: none">

                        <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                    </div>
                    <br><br>
                    <div class="col-md-6"><br><label style="font-size: 14px; ">Empleados seleccionados:</label></div>
                    <div class="col-md-6 text-right" style="padding-right: 24px;margin-bottom: 10px;"><br><button
                            type="submit" class="btn  btn-sm" style="background-color: #163552;color: #f9f9f9;">Asignar
                            calendario</button></div>
                    <br><br> </form>
                    <div class="col-md-12">

                        <div class="col-md-12" style="    padding-left: 0px;">
                            <table id='empleadosSele' width='100%' class="table  nowrap">
                                <thead>
                                    <tr>
                                        <td><input type="checkbox" class="ml-4" name="" id="selectEmps"></td>
                                        <td>Nombres</td>
                                        <td>Apellido paterno </td>
                                        <td>Apellido materno </td>
                                        <td>Calendario</td>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>


                    </div>


                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


{{-- CALENDARIO ASIGNAR --}}
<div id="calendarioAsignar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex justify-content-center "
        style="width: 400px;  margin-top: 185px; left: 94px;">

        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552; padding-bottom: 4px;
               padding-top: 4px;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar día a
                    calendario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
                <div class="row">
                    <div class="col-md-5 text-center">
                        <img src="{{ asset('admin/images/dormir.svg') }}" width="100" height="30">
                        <button class="btn btn-soft-primary btn-block btn-sm" style="color: #16588d;margin-top: 10px;
                    background-color: #c1cee0;" onclick="agregarMDescanso()">
                    <i class="uil uil-arrow-right mr-1"></i>
                    Descanso</button>
                    </div>


                    <div class="col-md-2"></div>

                    <div class="col-md-5 text-center">
                        <img src="{{ asset('admin/images/calendario.svg') }}" width="100" height="25" style="margin-top: 5px;">
                        <button class="btn btn-soft-primary btn-block btn-sm" style="color: #16588d;margin-top: 10px;
                    background-color: #c1cee0;" onclick="agregarMFeriado()">
                    <i class="uil uil-arrow-right mr-1"></i>
                    Feriado</button>
                    </div>

                </div>
            </div>
            {{-- <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;background: #f1f0f0;">
               <div class="col-md-12">
                   <div class="row">
                       <div class="col-md-12 text-right" >
                        <button type="button"  class="btn btn-soft-primary btn-sm " data-dismiss="modal">Cancelar</button>

                    </form>
                       </div>
                   </div>
               </div>
           </div> --}}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
