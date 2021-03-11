<style>
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
    color: #163552;
    }
    .titulo{
    font-weight: 600!important;
    color: #0d161f;
    font-size: 12.5px;
    }
</style>
<div class="row row-divided">

    <div class="col-md-12 col-xl-12">
        <div class="card">
            <div class="card-body"
                style="padding-top: 0px; background: #ffffff; font-size: 12.8px;color: #222222;padding-left:0px; padding-right: 20px;">
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-sm btn-primary" id="btnasignar"
                            style="background-color: #e3eaef;border-color:#e3eaef;color:#37394b"
                            onclick="javascript:obtenerHorarios()">
                            <img src="{{ asset('admin/images/calendarioHor.svg') }}" height="15">
                            &nbsp; Asignar horarios
                        </button>
                    </div>
                    <div class=" col-md-6 col-xl-6 text-right">
                        <button class="btn btn-sm btn-primary" onclick="modalRegistrar()" id="btnNuevoHorario"
                            style="background-color: #183b5d;border-color:#62778c">
                            + Nuevo Horario
                        </button>
                    </div>
                </div>
                <div id="tabladiv"> <br>
                    <table id="tablaEmpleado" class="table dt-responsive nowrap" style="font-size: 12.8px;">
                        <thead style=" background: #edf0f1;color: #6c757d;">
                            <tr>
                                <th></th>
                                <th>Descripcion</th>
                                <th>Tolerancia</th>
                                <th>Hora de inicio</th>
                                <th>Hora de fin</th>
                                <th>En uso</th>
                                <th></th>
                            </tr>
                        </thead>

                    </table>
                </div><br><br><br><br>
            </div>
        </div>
        <div id="asignarHorario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" data-backdrop="static" style="overflow-y: auto;">
            <div class="modal-dialog  modal-lg d-flex modal-dialog-scrollable justify-content-center"
                style="margin-top: 15px;max-width:900px!important;">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                            Asignar horarios masivamente
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="font-size: 13x!important;padding-top: 4px;padding-bottom: 8px;">

                        <div class="loader" class="text-center">
                            <img src="{{ URL::asset('landing/images/logo_animado.gif') }}" height="200"
                                class="img-load" style="display: none">
                        </div>
                        <input type="hidden" id="horario1">
                        <input type="hidden" id="horario2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-9" style="zoom:90%;">
                                        <input type="hidden" id="fechaDa" name="fechaDa">
                                        <label for="" style="font-weight: 600;">Seleccionar empleado(s):</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="" style="margin-top: 0px;margin-bottom: 1px;">
                                                    Seleccionar por:
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row col-md-12">
                                            <select data-plugin="customselect" id="selectEmpresarial"
                                                name="selectEmpresarial" class="form-control"
                                                data-placeholder="seleccione">
                                                <option value=""></option>
                                                @foreach ($area as $areas)
                                                    <option value="{{ $areas->idarea }}">Area :
                                                        {{ $areas->descripcion }}.
                                                    </option>
                                                @endforeach
                                                @foreach ($cargo as $cargos)
                                                    <option value="{{ $cargos->idcargo }}">Cargo :
                                                        {{ $cargos->descripcion }}.
                                                    </option>
                                                @endforeach
                                                @foreach ($local as $locales)
                                                    <option value="{{ $locales->idlocal }}">Local :
                                                        {{ $locales->descripcion }}.
                                                    </option>
                                                @endforeach
                                                @foreach ($nivel as $niveles)
                                                    <option value="{{ $niveles->idnivel }}">Nivel :
                                                        {{ $niveles->descripcion }}.
                                                    </option>
                                                @endforeach
                                                @foreach ($centroc as $centrocs)
                                                    <option value="{{ $centrocs->idcentro }}">Centro costo :
                                                        {{ $centrocs->descripcion }}.
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-5 form-check" style="padding-left: 55px;">
                                                <input type="checkbox" class="form-check-input" id="selectTodoCheck">
                                                <label class="form-check-label" for="selectTodoCheck"
                                                    style="margin-top: 2px;font-size: 11px!important">
                                                    Seleccionar todos.
                                                </label>
                                            </div>
                                            <div class="col-md-7 text-right">
                                                <span style="font-size: 11px!important">
                                                    *Se visualizará empleados con calendario
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="padding-left: 24px;
                                        padding-right: 0px;">
                                            <select class="form-control wide" data-plugin="customselect" multiple
                                                id="nombreEmpleado">
                                                @foreach ($empleado as $empleados)
                                                    <option value="{{ $empleados->emple_id }}">
                                                        {{ $empleados->perso_nombre }}
                                                        {{ $empleados->perso_apPaterno }}
                                                        {{ $empleados->perso_apMaterno }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="" style="font-weight: 600">Seleccionar días de calendario</label>
                                    </div>

                                    {{-- <div class="col-md-6 text-right">
                                        <label for="" style="font-weight: 600">Leyenda:   </label>
                                        <span class="badge " style="background-color:#e2e2e2;color:#3a3535 ">Horarios no guardados</span>
                                        <span class="badge " style="background-color: #9E9E9E;color:#3a3535 ">Incidencias no guardadas</span>

                                    </div> --}}

                                    <div class="col-md-12 text-center" id="Datoscalendar" style=" max-width: 100%;">
                                        <div id="calendar"></div>
                                    </div>

                                    <input type="hidden" id="horarioEnd">
                                    <input type="hidden" id="horarioStart">
                                </div>

                            </div>


                        </div>
                    </div>
                    <div class="modal-footer" style="padding-top: 8px;padding-bottom: 8px;">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-9 text-right" style="left: 60px;" >
                                    <div style="display: none; padding-top: 10px;" id="divCambios">
                                        <img src="{{ asset('admin/images/warning.svg') }}" height="15">
                                        <label for="" style="font-weight: 600;font-size: 12px;
                                        color: #353100;">Tienes cambios en el calendario por guardar</label>

                                    </div>

                                </div>
                                <div class="col-md-3 text-right" style="padding-right: 0px;">
                                    <button type="button" id="" class="btn btn-light" data-dismiss="modal">
                                        Cerrar
                                    </button>
                                    <button type="button" id="guardarTodoHorario" style="background-color: #163552;"
                                        class="btn">
                                        Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="verhorarioEmpleado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" data-backdrop="static" style="overflow-y: auto;">
            <div class="modal-dialog d-flex justify-content-center" style="margin-top: 100px;width: 650px">
                <div class="modal-content">

                    <div class="modal-body" style="padding: 0px">
                        <div class="col-xl-12" style="padding: 0px">
                                    <ul class="nav nav-pills navtab-bg nav-justified">
                                        <li class="nav-item">
                                            <a href="#horario" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                                <span class="d-block d-sm-none"><i class="uil-home-alt"></i></span>
                                                <img src="{{ asset('admin/images/calendarioHorario.svg') }}" height="18"><span class="d-none d-sm-block">Horario</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#incidencia" data-toggle="tab" aria-expanded="true" class="nav-link ">
                                                <span class="d-block d-sm-none"><i class="uil-user"></i></span>
                                                <img src="{{ asset('admin/images/calendarioIncidencia.svg') }}" height="18"> <span class="d-none d-sm-block">Incidencias</span>
                                            </a>
                                        </li>

                                    </ul>
                                    <div class="tab-content text-muted">
                                        <div class="tab-pane show active" id="horario">
                                            <div class="modal-body" style="font-size:12px!important;background: #ffffff; padding-top: 0px;">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="" class="titulo">Seleccione horario:</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <span id=errorSel style="color: #8b3a1e;display:none">Seleccione un horario</span>
                                                        <select data-plugin="customselect"
                                                            class="form-control custom-select custom-select-sm  col-md-10" name="selectHorario"
                                                            id="selectHorario">
                                                            <option hidden selected disabled>Asignar horario</option>
                                                            @foreach ($horario as $horarios)
                                                                <option class="" value="{{ $horarios->horario_id }}">
                                                                    {{ $horarios->horario_descripcion }}
                                                                    <span style="font-size: 11px;font-style: oblique">
                                                                        ({{ $horarios->horaI }}-{{ $horarios->horaF }})
                                                                    </span>
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        &nbsp;
                                                    </div>
                                                    <div class="col-md-3 text-left" style="padding-left: 0px;">
                                                        <button class="btn btn-primary btn-sm"
                                                            style="background-color: #183b5d;border-color:#62778c;margin-top: 5px;"
                                                            onclick="modalRegistrar()">
                                                            +
                                                        </button>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="custom-control custom-switch mb-2">
                                                            <input type="checkbox" class="custom-control-input" id="fueraHSwitch">
                                                            <label class="custom-control-label" for="fueraHSwitch" style="color: #4b4b5a !important;">
                                                                Permite marcar fuera del horario.
                                                            </label>
                                                        </div>
                                                        <div class="row">
                                                            <div class="custom-control custom-switch mb-2" style="left: 12px;">
                                                                <input type="checkbox" class="custom-control-input" id="horAdicSwitch">
                                                                <label class="custom-control-label" for="horAdicSwitch" style="color: #4b4b5a !important;">
                                                                    Permite marcar horas adicionales.
                                                                </label>
                                                            </div>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <select id="nHorasAdic" style="display: none;bottom: 3px;"
                                                                class="form-control form-control-sm col-md-3">
                                                                <option value="0.5">0.5 hora </option>
                                                                <option value="1">1 hora </option>
                                                                <option value="2">2 horas </option>
                                                                <option value="3">3 horas </option>
                                                                <option value="4">4 horas </option>
                                                                <option value="5">5 horas </option>
                                                                <option value="6">6 horas </option>
                                                                <option value="7">7 horas </option>
                                                                <option value="8">8 horas </option>
                                                                <option value="9">9 horas </option>
                                                                <option value="10">10 horas </option>
                                                                <option value="11">11 horas </option>
                                                                <option value="12">12 horas </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="padding-top: 15px; padding-bottom: 15px;background: #ffffff;">
                                                <div class="col-md-12 text-right" style="padding-right: 0px;">
                                                    <button type="button" class="btn btn-light  btn-sm"
                                                        style="background:#f3f3f3;border-color: #f3f3f3;"
                                                        onclick="$('#verhorarioEmpleado').modal('hide');$('*').removeClass('fc-highlight');">
                                                        Cancelar
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        style="background-color: #183b5d;border-color:#62778c;" onclick="agregarHorarioSe()">
                                                        Registrar
                                                    </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane " id="incidencia">
                                            <div class="modal-body" style="font-size:12px!important;background: #ffffff; padding-top: 0px;">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="" class="titulo">Seleccione tipo de incidencia:</label>
                                                    </div>
                                                    <div class="col-md-12">

                                                        <select data-plugin="customselect"
                                                            class="form-control custom-select custom-select-sm  col-md-10" name="selectTipoIn"
                                                            id="selectTipoIn" data-placeholder="seleccione">
                                                            @foreach ($tipo_incidencia as $tipoI)
                                                                <option value=""></option>
                                                                <option value="{{ $tipoI->idtipo_incidencia }}">
                                                                    {{ $tipoI->tipoInc_descripcion }}</option>
                                                             @endforeach
                                                        </select>
                                                        &nbsp;
                                                    </div>
                                                    <div class="col-md-12">

                                                        <select data-plugin="customselect"
                                                            class="form-control custom-select custom-select-sm  col-md-10" name="incidenciaSelect"
                                                            id="incidenciaSelect" data-placeholder="escoger incidencia" disabled>

                                                        </select>
                                                        &nbsp;
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer" style="padding-top: 15px; padding-bottom: 15px;background: #ffffff;">
                                                <div class="col-md-12 text-right" style="padding-right: 0px;">
                                                    <button type="button" class="btn btn-light  btn-sm"
                                                        style="background:#f3f3f3;border-color: #f3f3f3;"
                                                        onclick="$('#verhorarioEmpleado').modal('hide');$('*').removeClass('fc-highlight');">
                                                        Cancelar
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        style="background-color: #183b5d;border-color:#62778c;" onclick="registrarIncidencia()">
                                                        Registrar
                                                    </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="asignarIncidencia" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog  modal-lg d-flex justify-content-center">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                            Asignar Incidencia
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="frmIncidencia" action="javascript:registrarIncidencia()">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Asignar empleado(s):</label>
                                                <select class="form-control wide" data-plugin="customselect" multiple
                                                    id="empIncidencia" required>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="">Descripcion:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="descripcionInci" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4"><label for=""><br></label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="descuentoCheck">
                                                <label class="form-check-label" for="descuentoCheck">
                                                    Aplicar descuento
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for=""><br></label>
                                            <div class="custom-control custom-switch mb-2">
                                                <input type="checkbox" class="custom-control-input" id="customSwitch1">
                                                <label class="custom-control-label" for="customSwitch1">
                                                    Asignar mas de 1 día
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Fecha inicio:</label>
                                                <input type="date" id="fechaI" class="form-control form-control-sm"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="divFfin">
                                            <div class="form-group">
                                                <label for="">fecha fin:</label>
                                                <input type="date" id="fechaF" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light " data-dismiss="modal">Cancelar</button>
                                    <button type="submit" name="" style="background-color: #163552;" class="btn">
                                        Guardar
                                    </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="asignarIncidenciaHorario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog  modal-lg d-flex justify-content-center" style="width: 500px">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                            Asignar Incidencia
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="frmIncidenciaHo" action="javascript:registrarIncidenciaHo()">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Descripcion:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="descripcionInciHo" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6"><label for=""><br></label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="descuentoCheckHo">
                                                <label class="form-check-label" for="descuentoCheckHo">Aplicar
                                                    descuento</label>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button type="submit" name="" style="background-color: #163552;" class="btn btn-sm">
                                        Guardar
                                    </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="asignarIncidenciaHorarioEmp" class="modal fade" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog  modal-lg d-flex justify-content-center" style="width: 500px">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                            Asignar Incidencia
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="horario1em">
                            <input type="hidden" id="horario2em">
                            <div class="col-md-12">
                                <form id="frmIncidenciaHoEm" action="javascript:registrarIncidenciaHoEm()">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Descripcion:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="descripcionInciHoEm" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for=""><br></label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="descuentoCheckHoEm">
                                                <label class="form-check-label" for="descuentoCheckHoEm">
                                                    Aplicar descuento
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button type="submit" name="" style="background-color: #163552;" class="btn btn-sm">
                                        Guardar
                                    </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- REGISTRAR NUEVO HORARIO --}}
        <div id="horarioAgregar" class="modal fade" role="dialog" aria-labelledby="horarioAgregar" aria-hidden="true"
            data-backdrop="static">
            <div class="modal-dialog  modal-lg d-flex justify-content-center modal-dialog-scrollable"
                style="width: 850px;">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                            Asignar horario
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="font-size:12px!important">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="frmHor" action="javascript:registrarNuevoHorario()">
                                    <div class="row">
                                        <br>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Descripción del horario:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="descripcionCa" maxlength="60" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Hora de inicio(24h):</label>
                                                <input type="text" id="horaI" class="form-control form-control-sm"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Hora de fin(24h):</label>
                                                <input type="text" id="horaF" class="form-control form-control-sm"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Horas obligadas:</label>
                                                <div class="input-group form-control-sm"
                                                    style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="horaOblig" required>
                                                    <div class="input-group-prepend ">
                                                        <div class="input-group-text form-control-sm"
                                                            style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                            Horas
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="" >
                                            <div class="form-check mt-4 mb-4">
                                                <input type="checkbox"  class="form-check-input"
                                                    id="tmIngreso" >
                                                <label class="form-check-label" for="tmIngreso"
                                                    style="margin-top: 2px;">
                                                    Tiempo muerto para ingreso
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Tolerancia al ingreso(Min):</label>
                                                <div class="input-group form-control-sm "
                                                    style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                    <input type="number" value="0" class="form-control form-control-sm"
                                                        id="toleranciaH"
                                                        oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                                        onchange="javascript:toleranciasValidacion()" required>
                                                    <div class="input-group-prepend  ">
                                                        <div class="input-group-text form-control-sm "
                                                            style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                            Minutos
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="" >
                                            <div class="form-check mt-4 mb-4">
                                                <input type="checkbox"  class="form-check-input"
                                                    id="tmSalida" >
                                                <label class="form-check-label" for="tmSalida"
                                                    style="margin-top: 2px;">
                                                    Tiempo muerto para salida
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Tolerancia a la salida(Min):</label>
                                                <div class="input-group form-control-sm "
                                                    style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                    <input type="number" value="0" class="form-control form-control-sm"
                                                        id="toleranciaSalida"
                                                        oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                                        onchange="javascript:toleranciasValidacion()" required>
                                                    <div class="input-group-prepend  ">
                                                        <div class="input-group-text form-control-sm "
                                                            style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                            Minutos
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Seleccione regla de horas extras:</label>
                                                <select data-plugin="customselect" id="idReglaHora"
                                                    name="idReglaHora" class="form-control"
                                                     required>
                                                    @foreach ($reglasHExtras as $regla)

                                                        <option value="{{ $regla->idreglas_horasExtras }}" selected>
                                                            {{ $regla->reglas_descripcion }}({{$regla->tipo_regla}}) </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Seleccione regla de horas extras nocturnas:</label>
                                                <select data-plugin="customselect" id="idReglaHoraNocturna"
                                                    name="idReglaHoraNocturna" class="form-control"
                                                    required>
                                                    @foreach ($reglasHExtrasNocturno as $reglaN)

                                                        <option value="{{ $reglaN->idreglas_horasExtras }}">
                                                            {{ $reglaN->reglas_descripcion }}({{$reglaN->tipo_regla}})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="divOtrodia" style="display: none">
                                            <div class="form-check mt-4 mb-4">
                                                <input type="checkbox" style="font-weight: 600" class="form-check-input"
                                                    id="smsCheck" checked disabled>
                                                <label class="form-check-label" for="smsCheck"
                                                    style="margin-top: 2px;font-weight: 700">
                                                    La hora fin de este horario pertenece al siguiente día.
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="custom-control custom-switch mb-2">
                                                <input type="checkbox" class="custom-control-input" id="SwitchPausa">
                                                <label class="custom-control-label" for="SwitchPausa"
                                                    style="font-weight: bold;padding-top: 1px">
                                                    Pausas en el horario
                                                </label>
                                                &nbsp;
                                                <span id="fueraRango" style="color: #80211e;display: none">
                                                    Hora no esta dentro de rango de horario
                                                </span>
                                                <span id="errorenPausas" style="color: #80211e;display: none">
                                                    - Fin de pausa debe ser mayor a inicio pausa
                                                </span>
                                                <span id="errorenPausasCruzadas" style="color: #80211e;display: none">
                                                    - Los rangos de pausas no pueden cruzarse, revísalo e inténtalo
                                                    nuevamente.
                                                </span>
                                                &nbsp;
                                                <span id="vacioHoraF" style="color: #80211e;display: none">
                                                    Agregar Hora de inicio o Hora de fin
                                                </span>
                                            </div>
                                        </div>
                                        <div id="divPausa" class="col-md-12" style="display: none">
                                            <div class="col-md-12">
                                                <span id="validP" style="color: red;display:none">
                                                    *Campos Obligatorios
                                                </span>
                                            </div>
                                            <div id="inputPausa"></div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal"
                                        onclick="javascript:limpiarHorario();">
                                        Cancelar
                                    </button>
                                    <button type="submit" id="btnGuardaHorario" style="background-color: #163552;"
                                        class="btn btn-sm">
                                        Guardar
                                    </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- FINALIZAR --}}
        {{-- EDITAR HORARIO --}}
        <div id="horarioEditar" class="modal fade" role="dialog" aria-labelledby="horarioEditar" aria-hidden="true"
            data-backdrop="static">
            <div class="modal-dialog  modal-lg d-flex justify-content-center modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                            Editar horario
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="font-size:12px!important">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" id="idhorario_ed">
                                <form id="frmHorEditar" action="javascript:editarHorarioDatos()">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Descripción del horario:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="descripcionCa_ed" maxlength="40" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Hora de inicio(24h):</label>
                                                <input type="text" id="horaI_ed" class="form-control form-control-sm"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Hora de fin(24h):</label>
                                                <input type="text" id="horaF_ed" class="form-control form-control-sm"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Horas obligadas:</label>
                                                <div class="input-group form-control-sm"
                                                    style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="horaOblig_ed" required>
                                                    <div class="input-group-prepend ">
                                                        <div class="input-group-text form-control-sm"
                                                            style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                            Horas
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="" >
                                            <div class="form-check mt-4 mb-4">
                                                <input type="checkbox"  class="form-check-input"
                                                    id="tmIngreso_ed" >
                                                <label class="form-check-label" for="tmIngreso_ed"
                                                    style="margin-top: 2px;">
                                                    Tiempo muerto para ingreso
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Tolerancia al ingreso(Min):</label>
                                                <div class="input-group form-control-sm "
                                                    style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                    <input type="number" class="form-control form-control-sm"
                                                        oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                                        onchange="javascript:e_toleranciasValidacion()"
                                                        id="toleranciaH_ed" required>
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text form-control-sm"
                                                            style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                            Minutos
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="" >
                                            <div class="form-check mt-4 mb-4">
                                                <input type="checkbox"  class="form-check-input"
                                                    id="tmSalida_ed" >
                                                <label class="form-check-label" for="tmSalida_ed"
                                                    style="margin-top: 2px;">
                                                    Tiempo muerto para salida
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Tolerancia a la salida(Min):</label>
                                                <div class="input-group form-control-sm"
                                                    style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                    <input type="number" class="form-control form-control-sm"
                                                        oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                                        onchange="javascript:e_toleranciasValidacion()"
                                                        id="toleranciaSalida_ed" required>
                                                    <div class="input-group-prepend  ">
                                                        <div class="input-group-text form-control-sm "
                                                            style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                            Minutos
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Seleccione regla de horas extras:</label>
                                                <select data-plugin="customselect" id="idReglaHora_ed"
                                                    name="idReglaHora_ed" class="form-control"
                                                     required>
                                                    @foreach ($reglasHExtras as $regla)
                                                        <option value="{{ $regla->idreglas_horasExtras }}">
                                                            {{ $regla->reglas_descripcion }}({{$regla->tipo_regla}})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Seleccione regla de horas extras nocturnas:</label>
                                                <select data-plugin="customselect" id="idReglaHoraNocturna_ed"
                                                    name="idReglaHoraNocturna_ed" class="form-control"
                                                    required>
                                                    @foreach ($reglasHExtrasNocturno as $reglaN)
                                                        <option value="{{ $reglaN->idreglas_horasExtras }}">
                                                            {{ $reglaN->reglas_descripcion }}({{$reglaN->tipo_regla}})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="divOtrodia_ed" style="display: none">
                                            <label for=""><br></label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="smsCheck_ed" checked
                                                    disabled>
                                                <label class="form-check-label" for="smsCheck_ed"
                                                    style="margin-top: 2px;font-weight: 700">
                                                    La hora fin de este horario pertenece al siguiente día.
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="custom-control custom-switch mb-2">
                                                <input type="checkbox" class="custom-control-input" id="SwitchPausa_ed">
                                                <label class="custom-control-label" for="SwitchPausa_ed"
                                                    style="font-weight: bold;padding-top: 1px">
                                                    Pausas en el horario
                                                </label>
                                                &nbsp;
                                                <span id="fueraRango_ed" style="color: #80211e;display: none">
                                                    Hora no esta dentro de rango de horario
                                                </span>
                                                <span id="errorenPausas_ed" style="color: #80211e;display: none">
                                                    - Fin de pausa debe ser mayor a inicio pausa
                                                </span>
                                                <span id="errorenPausasCruzadas_ed"
                                                    style="color: #80211e;display: none">
                                                    - Los rangos de pausas no pueden cruzarse, revísalo e inténtalo
                                                    nuevamente.
                                                </span>
                                                &nbsp;
                                                <span id="vacioHoraF_ed" style="color: #80211e;display: none">
                                                    Agregar Hora de inicio o Hora de fin
                                                </span>
                                            </div>
                                        </div>
                                        <div id="pausas_edit" style="display: none" class="col-md-12">
                                            <div class="col-md-12">
                                                <span id="validP_ed" style="color: red;display:none">
                                                    *Campos Obligatorios
                                                </span>
                                            </div>
                                            <div id="PausasHorar_ed"></div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button type="submit" style="background-color: #163552;" class="btn btn-sm"
                                        id="btnEditarHorario">
                                        Guardar
                                    </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- FINALIZAR --}}
        <div id="horarioAgregaren" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;">

                <div class="modal-content">
                    <div class="modal-header" style="background-color:#163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                            Asignar horario
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="font-size:12px!important">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="frmHoren" action="javascript:registrarHorarioen()">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Tipo de horario:</label>
                                                <select class="form-control custom-select custom-select-sm"
                                                    id="tipHorarioen">
                                                    <option>Normal</option>
                                                    <option>Guardía</option>
                                                    <option>Nocturno</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6"><label for=""><br></label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="exampleCheck1en">
                                                <label class="form-check-label" for="exampleCheck1en">
                                                    Aplicar sobretiempo
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Descripcion:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="descripcionCaen" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Tolerancia(Min):</label>
                                                <input type="number" value="0" class="form-control form-control-sm"
                                                    min="0" id="toleranciaHen" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Hora de inicio(24h):</label>
                                                <input type="text" id="horaIen" class="form-control form-control-sm"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Hora de fin(24h):</label>
                                                <input type="text" id="horaFen" class="form-control form-control-sm"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button type="submit" style="background-color: #163552;" class="btn btn-sm ">
                                        Guardar
                                    </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="borrarincide" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog  d-flex justify-content-center">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Incidencias</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tablaBorrarI" class="table">
                                    <thead>
                                        <tr>
                                            <th>Nombre de incidencia</th>
                                            <th>Descuento</th>
                                            <th>*</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 12px"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding-top: 6px;padding-bottom: 6px;">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="modalEmpleadosHo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog  modal-lg d-flex justify-content-center" style="max-width:800px!important;">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#163552;">
                        <h5 class="modal-title" style="color:#ffffff;font-size:15px">
                            Alerta de inconsistencia de horarios
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label>
                                    Los siguientes empleados ya presentan un horario asignado en este rango de horas,
                                    revise y vuelva a intentar.
                                </label>
                            </div>
                            <div class="col-md-12">
                                <table id="tablaEmpleadoExcel" class="table nowrap" style="font-size: 12.8px;">
                                    <thead style=" background: #edf0f1;color: #6c757d;">
                                        <tr>
                                            <th></th>
                                            <th>DNI</th>
                                            <th>Nombres</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyExcel"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light " data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL CONFIGIRACION HORARIO --}}
        <div id="editarConfigHorario_re" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" style="max-width: 400px; margin-top: 150px;">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Editar
                            configuración o eliminar horario
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
                        <div class="row">
                            <input type="hidden" id="idHoraEmpleado_re">
                            <input type="hidden" id="tipoHorario">
                            <div class="col-md-12">
                                <form action="javascript:actualizarConfigHorario_re()">
                                    <div class="row">
                                        <div class="col-md-12"><br>
                                            <div class="custom-control custom-switch mb-2">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="fueraHSwitch_Actualizar_re">
                                                <label class="custom-control-label"
                                                    for="fueraHSwitch_Actualizar_re">Trabajar fuera de horario</label>
                                            </div>

                                            <div class="row">
                                                <div class="custom-control custom-switch mb-2" style="left: 12px;">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="horAdicSwitch_Actualizar_re">
                                                    <label class="custom-control-label"
                                                        for="horAdicSwitch_Actualizar_re">Permite marcar horas
                                                        adicionales.</label>

                                                </div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <select id="nHorasAdic_Actualizar_re" style="display: none;bottom: 3px;"
                                                    class="form-control form-control-sm col-md-3">
                                                    <option value="0.5">0.5 hora </option>
                                                    <option value="1">1 hora </option>
                                                    <option value="2">2 horas </option>
                                                    <option value="3">3 horas </option>
                                                    <option value="4">4 horas </option>
                                                    <option value="5">5 horas </option>
                                                    <option value="6">6 horas </option>
                                                    <option value="7">7 horas </option>
                                                    <option value="8">8 horas </option>
                                                    <option value="9">9 horas </option>
                                                    <option value="10">10 horas </option>
                                                    <option value="11">11 horas </option>
                                                    <option value="12">12 horas </option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer" style="background: #f1f0f0;">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 text-left">
                                    <button type="button" class="btn btn-sm"
                                        style="background-color: #ad4145; color:white" id="eliminaHorarioDia_re">
                                        <i style="height: 15px !important;width: 15px !important;color:#ffffff !important;margin-bottom: 2px;"
                                            data-feather="trash-2"></i></button>

                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-light btn-sm "
                                        data-dismiss="modal">Cancelar</button>
                                    <button type="submit" name="" style="background-color: #163552;"
                                        class="btn btn-sm">Guardar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        {{-- MODAL HORARIOS POR EMPELADO --}}
        <div id="modalHorarioEmpleados" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog  modal-dialog-scrollable" style="max-width: 670px;">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Horarios de
                            empleados
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input type="hidden" id="modalidsEmpleado">
                    <input type="hidden" id="fechaSelectora">
                    <div class="modal-body" style="font-size:12px!important;background: #ffffff;">
                        <div class="row" id="rowdivs">


                        </div>

                    </div>
                    <div class="modal-footer" style="background: #ffffff;">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light btn-sm "
                                        data-dismiss="modal">Cerrar</button>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        {{-- MODAL CLONAR POR EMPELADO --}}
        <div id="modalHorarioClonar" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-scrollable" style="max-width: 670px; margin-top: 150px;">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Clonar horarios
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" style="font-size:12px!important;background: #ffffff;">
                        <form action="javascript:registrarClonacionH()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-5" style="  padding-top: 8px; font-weight: 600">Seleccione
                                            empleado:</label>
                                        <div class="col-md-7" style="padding-left: 0px;">
                                            <select class="" data-plugin="customselect" id="nombreEmpleadoClonar"
                                                required>
                                                @foreach ($empleado as $empleados)
                                                    <option value="{{ $empleados->emple_id }}">
                                                        {{ $empleados->perso_nombre }}
                                                        {{ $empleados->perso_apPaterno }}
                                                        {{ $empleados->perso_apMaterno }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-lg-5 col-form-label" style="font-weight: 600">Rango de
                                            fechas:</label>
                                        <input type="hidden" id="ID_START">
                                        <input type="hidden" id="ID_END">
                                        <div class="input-group col-md-7 text-center"
                                            style="padding-left: 0px;padding-right: 12px;" id="fechaSelec">
                                            <input type="text" id="fechaInput" class="col-md-12 form-control"
                                                data-input>
                                            <div class="input-group-prepend">
                                                <div class="input-group-text form-control flatpickr">
                                                    <a class="input-button" data-toggle>
                                                        <i class="uil uil-calender"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-12" id="divClonacionElegir" style="display: none">
                                    <span style="color:#911818">*Eliga al menos una opcion.</span>
                                </div>

                                <div class="col-md-6">
                                    <div class="custom-control custom-switch" style="padding-bottom: 10px;">
                                        <input type="checkbox" class="custom-control-input" id="asignarNuevoHorarioC">
                                        <label class="custom-control-label" for="asignarNuevoHorarioC"
                                            style="margin-top: 2px;font-weight: 600">Asignar como nuevo</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <span></span>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom-control custom-switch" style="padding-bottom: 10px;">
                                        <input type="checkbox" class="custom-control-input"
                                            id="reemplazarNuevoHorarioC">
                                        <label class="custom-control-label" for="reemplazarNuevoHorarioC"
                                            style="margin-top: 2px;font-weight: 600">Reemplazar existente</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <span id="alertReemplazar" style="color:#911818;display:none">Se borrará horarios
                                        existentes y crearan nuevos</span>
                                </div>

                            </div>

                    </div>
                    <div class="modal-footer" style="background: #ffffff;">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-light btn-sm "
                                        data-dismiss="modal">Cerrar</button>
                                    <button type="submit" name="" style="background-color: #163552;"
                                        class="btn btn-sm">Guardar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
</div>

{{-- MODAL INCIDENCIAS POR EMPELADO --}}
<div id="modalIncidenciasEmpleados" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
aria-hidden="true">
<div class="modal-dialog  modal-dialog-scrollable" style="max-width: 670px;">
    <div class="modal-content">
        <div class="modal-header" style="background-color: #163552;">
            <h5 class="modal-title" id="" style="color:#ffffff;font-size:15px">Incidencias de
                empleados
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <input type="hidden" id="modalidsEmpleadoIncid">
        <input type="hidden" id="fechaSelectoraIncid">
        <div class="modal-body" style="font-size:12px!important;background: #ffffff;">
            <div class="row" id="rowdivsIncid">


            </div>

        </div>
        <div class="modal-footer" style="background: #ffffff;">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-light btn-sm "
                            data-dismiss="modal">Cerrar</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
