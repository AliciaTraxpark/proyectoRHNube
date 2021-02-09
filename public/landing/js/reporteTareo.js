//* FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "single",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "D, j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
});
// * HORAS PARA INSERTAR ENTRADA Y SALIDA
var horasE = {};
var horasS = {};

//* variable para table
var dataT = {};

// * ESTADO DE HORARIO EMPLEADO
var contenidoHorario = [];
$(function () {
    $("#idempleado").select2({
        placeholder: "Seleccionar",
        language: {
            inputTooShort: function (e) {
                return "Escribir nombre o apellido";
            },
            loadingMore: function () {
                return "Cargando más resultados…";
            },
            noResults: function () {
                return "No se encontraron resultados";
            },
        },
        minimumInputLength: 2,
    });
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
    $("#fechaInput").change();
    cambiarF();
});

function cargartabla(fecha) {
    /* ****INICALIZAR VALORES DE SELECTORES****/
    $("#fechaSwitch").prop("checked", false);
    $("#checCodigo").prop("checked", true);
    $("#checnumdoc").prop("checked", true);
    $("#checSexo").prop("checked", false);
    $("#checCargo").prop("checked", false);
    $("#checPuntoc").prop("checked", true);
    $("#checControl").prop("checked", true);
    /* ************************************** */
    var idemp = $("#idempleado").val();
    $.ajax({
        type: "GET",
        url: "/tablaTareo",
        data: {
            fecha,
            idemp,
        },
        async: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
            fechaGlobal = fecha;
            contenidoHorario.length = 0;
            if (data.length != 0) {
                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                // ! *********** CABEZERA DE TABLA**********
                $("#MostarDetalles").show();
                $("#theadD").empty();

                //* CANTIDAD MININO VALOR DE COLUMNAS PARA HORAS
                var cantidadColumnasHoras = 0;
                for (let i = 0; i < data.length; i++) {
                    //* OBTENER CANTIDAD TOTAL DE COLUMNAS
                    if (cantidadColumnasHoras < data.length) {
                        cantidadColumnasHoras = data.length;
                    }
                }
                //*---------------------------- ARMAR CABEZERA-----------------------------------------
                var theadTabla = `<tr>
                                    <th># </th>
                                    <th class="fechaHid" name="tiempoSitHi">Fecha</th>
                                    <th class="codigoHid">Código</th>
                                    <th class="numdocHid">Número de documento </th>
                                    <th>Nombres y Apellidos</th>
                                    <th class="sexoHid" name="tiempoSitHi">Sexo</th>
                                    <th class="cargoHid"  name="tiempoSitHi">Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>`;

                theadTabla += `<th>Cód. Act.</th>
                                    <th>Actividad</th>
                                    <th>Cód. Sub.</th>
                                    <th>Subactividad</th>
                                    <th class="controHidEn">Controlador de entrada</th>
                                    <th>Hora de entrada</th>
                                    <th class="noExport">&nbsp; &nbsp; &nbsp; &nbsp;</th>
                                    <th class="controHidSa">Controlador de salida</th>
                                    <th>Hora de salida</th>
                                    <th >Tiempo en sitio</th>`;

                theadTabla += `
                                   <th class="puntoHid">Punto de control</th>
                                   </tr>`;

                //* DIBUJAMOS CABEZERA
                $("#theadD").html(theadTabla);
                /* --------------------------------FIN DE CABECERA------------------------- */

                // ! *********** BODY DE TABLA**********
                /* ---------------CUERPO DE TABLA TBODY -----------------------------------------*/
                $("#tbodyD").empty();
                var tbody = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {
                    tbody += `<tr>

                                <td>${index + 1}</td>

                                <td class="fechaHid"  name="tiempoSitHi">${moment(
                                    $("#pasandoV").val()
                                ).format("DD/MM/YYYY")}&nbsp;</td>
                                <td class="codigoHid">${
                                    data[index].emple_codigo
                                }&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                                <td class="numdocHid">${
                                    data[index].emple_nDoc
                                }&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                                <td>${data[index].perso_nombre} ${
                        data[index].perso_apPaterno
                    } ${
                        data[index].perso_apMaterno
                    }&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;

                    if (data[index].perso_sexo != null) {
                        tbody += `<td class="sexoHid" name="tiempoSitHi">${data[index].perso_sexo}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    } else {
                        tbody += `<td class="sexoHid"  name="tiempoSitHi">---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    }

                    if (data[index].cargo_descripcion != null) {
                        tbody += `<td class="cargoHid"  name="tiempoSitHi">${data[index].cargo_descripcion}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    } else {
                        tbody += `<td  class="cargoHid" name="tiempoSitHi">---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    }

                    //* ARMAR Y ORDENAR MARCACIONES
                    var tbodyEntradaySalida = "";
                    var sumaTiempos = moment("00:00:00", "HH:mm:ss");
                    /* -------------------------------------------- */
                    //: HORA
                    for (let h = 0; h < 24; h++) {
                        var marcacionData = data[index];

                        /* SI TENGO ENTRADA */
                        if (marcacionData.entrada != 0) {
                            if (
                                h == moment(marcacionData.entrada).format("HH")
                            ) {
                                if (marcacionData.codigoActividad != 0) {
                                    tbodyEntradaySalida += `<td >${marcacionData.codigoActividad} </td>`;
                                } else {
                                    tbodyEntradaySalida += `<td>
                                       --
                                    </td>`;
                                }

                                if (marcacionData.Activi_Nombre != null) {
                                    tbodyEntradaySalida += `<td>${marcacionData.Activi_Nombre}</td>`;
                                } else {
                                    tbodyEntradaySalida += `<td>
                                        <div class=" dropdown">
                                            <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar actividad">
                                                    <img style="margin-bottom: 3px;" src="landing/images/actividad.svg" class="mr-2" height="12"/>
                                                    No tiene actividad
                                                </span>
                                            </a>
                                            <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                                               <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                   <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                       Opciones
                                               </h6>
                                               <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                <div class="dropdown-item" dropdown-itemM noExport>
                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                        <a onclick="agregarActiv(${marcacionData.idMarcacion})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                            <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                            Agregar
                                                        </a>
                                                    </div>
                                                </div>
                                            </ul>
                                        </div>
                                    </td>`;
                                }

                                if (marcacionData.codigoSubactiv != 0) {
                                    tbodyEntradaySalida += `<td >${marcacionData.codigoSubactiv} </td>`;
                                } else {
                                    tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                }

                                if (marcacionData.subAct_nombre != null) {
                                    tbodyEntradaySalida += `<td>${marcacionData.subAct_nombre}</td>`;
                                } else {
                                    tbodyEntradaySalida += `<td>
                                        <div class=" dropdown">
                                            <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar subactividad">
                                                    <img style="margin-bottom: 3px;" src="landing/images/subactividad.svg" class="mr-2" height="12"/>
                                                    No tiene subactividad
                                                </span>
                                            </a>
                                            <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                                               <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                   <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                       Opciones
                                               </h6>
                                               <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                <div class="dropdown-item" dropdown-itemM noExport>
                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                        <a onclick="agregarSubAct(${marcacionData.idMarcacion})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                            <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                            Agregar
                                                        </a>
                                                    </div>
                                                </div>
                                            </ul>
                                        </div>
                                    </td>`;
                                }
                                if (marcacionData.controladorEntrada != 0) {
                                    tbodyEntradaySalida += `
                                                <td class="controHidEn" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo: ${marcacionData.dispositivoEntrada}">  ${marcacionData.controladorEntrada}</td>`;
                                } else {
                                    //*si no tiene controlador
                                    tbodyEntradaySalida += `<td class="controHidEn">
                                        <div class=" dropdown">
                                            <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar controlador de entrada">
                                                    <img style="margin-bottom: 3px;" src="landing/images/contEntrada.svg" class="mr-2" height="12"/>
                                                    No tiene controlador de Ent.
                                                </span>
                                            </a>
                                            <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                                               <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                   <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                       Opciones
                                               </h6>
                                               <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                <div class="dropdown-item" dropdown-itemM noExport>
                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                        <a onclick="agregarControE(${marcacionData.idMarcacion},'${marcacionData.entrada}')" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                            <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="14" />
                                                            Agregar
                                                        </a>
                                                    </div>
                                                </div>
                                            </ul>
                                        </div>
                                    </td>`;
                                }

                                tbodyEntradaySalida += `<td>
                                                        <div class="dropdown">
                                                            <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                                style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                                ${moment(marcacionData.entrada).format("HH:mm:ss")}
                                                            </a>
                                                            <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
                                                                <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                    <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                                    Opciones
                                                                </h6>
                                                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.entrada).format("HH:mm:ss")}',1,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                            Cambiar a entrada
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.entrada).format("HH:mm:ss")}',1,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                            Cambiar a salida
                                                                        </a>
                                                                    </div>
                                                                </div>`;
                                        if (marcacionData.salida != 0) {
                                            tbodyEntradaySalida += `<div class=" dropdown-item dropdown-itemM noExport">
                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                            <a onclick="convertirOrden(${marcacionData.idMarcacion},${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/flechasD.svg"  height="12" />
                                                                                Convertir orden
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                            <a onclick="asignarNuevaM(${marcacionData.idMarcacion},'${moment(marcacionData.entrada).format("HH:mm:ss")}',1,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                                Asignar a nueva marc.
                                                                            </a>
                                                                        </div>
                                                                    </div>`;
                                        }
                                        tbodyEntradaySalida += ` <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="eliminarM(${marcacionData.idMarcacion},1,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/borrarD.svg"  height="12" />
                                                                            Eliminar marc.
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </ul></div></td>`;

                                /* SI  TENGO SALIDA */
                                if (marcacionData.salida != 0) {
                                    tbodyEntradaySalida += `<td class="noExport"></td>`;
                                    if (marcacionData.controladorSalida != 0) {
                                        tbodyEntradaySalida += `
                                                    <td class="controHidSa" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo: ${marcacionData.dispositivoSalida}">  ${marcacionData.controladorSalida}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td class="controHidSa">
                                            <div class=" dropdown">
                                                <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                    <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar controlador de salida">
                                                        <img style="margin-bottom: 3px;" src="landing/images/contSalida.svg" class="mr-2" height="12"/>
                                                        No tiene controlador de Sal.
                                                    </span>
                                                </a>
                                                <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                                                   <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                       <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                           Opciones
                                                   </h6>
                                                   <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                    <div class="dropdown-item" dropdown-itemM noExport>
                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                            <a onclick="agregarControSa(${marcacionData.idMarcacion},'${marcacionData.salida}')" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="14" />
                                                                Agregar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </ul>
                                            </div>
                                        </td>`;
                                    }
                                    tbodyEntradaySalida += `<td >
                                                            <div class="dropdown" >
                                                                <a class="btn dropdown" type="button" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false"
                                                                    style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                    <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                    ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                </a>
                                                                <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
                                                                    <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                        <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                                        Opciones
                                                                    </h6>
                                                                    <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                            <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                                Cambiar a entrada
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                            <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                Cambiar a salida
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                            <a onclick="convertirOrden(${marcacionData.idMarcacion},${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/flechasD.svg"  height="12" />
                                                                                Convertir orden
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                            <a onclick="asignarNuevaM(${marcacionData.idMarcacion},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                                Asignar a nueva marc.
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                            <a onclick="eliminarM(${marcacionData.idMarcacion},2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/borrarD.svg"  height="12" />
                                                                                Eliminar marc.
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </ul>
                                                            </div>
                                                        </td>`;

                                    var horaFinal = moment(
                                        marcacionData.salida
                                    );
                                    var horaInicial = moment(
                                        marcacionData.entrada
                                    );
                                    if (horaFinal.isSameOrAfter(horaInicial)) {
                                        var tiempoRestante =
                                            horaFinal - horaInicial;
                                        var segundosTiempo = moment
                                            .duration(tiempoRestante)
                                            .seconds();
                                        var minutosTiempo = moment
                                            .duration(tiempoRestante)
                                            .minutes();
                                        var horasTiempo = Math.trunc(
                                            moment
                                                .duration(tiempoRestante)
                                                .asHours()
                                        );
                                        if (horasTiempo < 10) {
                                            horasTiempo = "0" + horasTiempo;
                                        }
                                        if (minutosTiempo < 10) {
                                            minutosTiempo = "0" + minutosTiempo;
                                        }
                                        if (segundosTiempo < 10) {
                                            segundosTiempo =
                                                "0" + segundosTiempo;
                                        }
                                        tbodyEntradaySalida += `<td >
                                                                    <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                                                                    <a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                        ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                    </a>
                                                                </td>`;
                                        sumaTiempos = moment(sumaTiempos).add(
                                            segundosTiempo,
                                            "seconds"
                                        );
                                        sumaTiempos = moment(sumaTiempos).add(
                                            minutosTiempo,
                                            "minutes"
                                        );
                                        sumaTiempos = moment(sumaTiempos).add(
                                            horasTiempo,
                                            "hours"
                                        );
                                    }
                                } else {
                                    tbodyEntradaySalida += `<td class="noExport">
                                        <a style="cursor:pointer;" data-toggle="tooltip" data-placement="left" title="Intercambiar" onclick="intercambiarMar(${marcacionData.idMarcacion})"><img style="margin-bottom: 3px;margin-top: 4px;" src="landing/images/intercambiar.svg"  height="15"/></a>
                                        </td>`;
                                    /* SI NO TENGO SALIDA */
                                    if (marcacionData.controladorSalida != 0) {
                                        tbodyEntradaySalida += `
                                                    <td class="controHidSa" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo: ${marcacionData.dispositivoSalida}">  ${marcacionData.controladorSalida}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td class="controHidSa">
                                            <div class=" dropdown">
                                                <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                    <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar controlador de salida">
                                                        <img style="margin-bottom: 3px;" src="landing/images/contSalida.svg" class="mr-2" height="12"/>
                                                        No tiene controlador de Sal.
                                                    </span>
                                                </a>
                                                <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                                                   <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                       <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                           Opciones
                                                   </h6>
                                                   <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                    <div class="dropdown-item" dropdown-itemM noExport>
                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                            <a onclick="agregarControSa(${marcacionData.idMarcacion},'${marcacionData.salida}')" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="14" />
                                                                Agregar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </ul>
                                            </div>
                                        </td>`;
                                    }
                                    tbodyEntradaySalida += `<td>
                                        <div class="dropdown noExport">
                                        <a type="button" class="btn dropdown-toggle" id="dropSalida${
                                            marcacionData.idMarcacion
                                        }" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" data-open-dropdown="dropSalida${
                                                marcacionData.idMarcacion
                                            }" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                            <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                No tiene salida
                                            </span>
                                        </a>
                                        <ul class="dropdown-menu noExport"  aria-labelledby="dropSalida${
                                            marcacionData.idMarcacion
                                        }" style="padding: 0rem 0rem;">
                                             <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                 <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                     Opciones
                                             </h6>
                                             <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                            <div class="dropdown-item noExport">
                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;padding-left: 0px!important;">
                                                    <a onclick="javascript:insertarSalidaModal('${moment(
                                                        marcacionData.entrada
                                                    ).format("HH:mm:ss")}',${
                                        marcacionData.idMarcacion
                                    },${marcacionData.idHE})"
                                                     style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                        <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                        Insertar salida
                                                    </a>
                                                </div>
                                            </div>
                                        </ul>
                                    </div>
                                        </td>`;

                                    tbodyEntradaySalida += `<td >
                                                            <span class="badge badge-soft-secondary">
                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                --:--:--
                                                            </span>
                                                        </td>`;
                                }
                            }
                        } else {
                            /* SI NO TENGO ENTRADA Y SI TENGO SALIDA */
                            if (marcacionData.salida != 0) {
                                if (
                                    h ==
                                    moment(marcacionData.salida).format("HH")
                                ) {
                                    if (marcacionData.codigoActividad != 0) {
                                        tbodyEntradaySalida += `<td >${marcacionData.codigoActividad} </td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td>
                                            --
                                        </td>`;
                                    }

                                    if (marcacionData.Activi_Nombre != null) {
                                        tbodyEntradaySalida += `<td>${marcacionData.Activi_Nombre}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td>
                                            <div class=" dropdown">
                                                <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                    <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar actividad">
                                                        <img style="margin-bottom: 3px;" src="landing/images/actividad.svg" class="mr-2" height="12"/>
                                                        No tiene actividad
                                                    </span>
                                                </a>
                                                <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                                                   <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                       <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                           Opciones
                                                   </h6>
                                                   <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                    <div class="dropdown-item" dropdown-itemM noExport>
                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                            <a onclick="agregarActiv(${marcacionData.idMarcacion})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                Agregar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </ul>
                                            </div>
                                        </td>`;
                                    }

                                    if (marcacionData.codigoSubactiv != 0) {
                                        tbodyEntradaySalida += `<td >${marcacionData.codigoSubactiv} </td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                    if (marcacionData.subAct_nombre != null) {
                                        tbodyEntradaySalida += `<td>${marcacionData.subAct_nombre}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td>
                                            <div class=" dropdown">
                                                <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                    <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar subactividad">
                                                        <img style="margin-bottom: 3px;" src="landing/images/subactividad.svg" class="mr-2" height="12"/>
                                                        No tiene subactividad
                                                    </span>
                                                </a>
                                                <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                                                   <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                       <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                           Opciones
                                                   </h6>
                                                   <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                    <div class="dropdown-item" dropdown-itemM noExport>
                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                            <a onclick="agregarSubAct(${marcacionData.idMarcacion})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                Agregar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </ul>
                                            </div>
                                        </td>`;
                                    }

                                    if (marcacionData.controladorEntrada != 0) {
                                        tbodyEntradaySalida += `
                                                    <td class="controHidEn" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo: ${marcacionData.dispositivoEntrada}">  ${marcacionData.controladorEntrada}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td class="controHidEn">
                                            <div class=" dropdown">
                                                <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                    <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar controlador de entrada">
                                                        <img style="margin-bottom: 3px;" src="landing/images/contEntrada.svg" class="mr-2" height="12"/>
                                                        No tiene controlador de Ent.
                                                    </span>
                                                </a>
                                                <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                                                   <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                       <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                           Opciones
                                                   </h6>
                                                   <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                    <div class="dropdown-item" dropdown-itemM noExport>
                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                            <a onclick="agregarControE(${marcacionData.idMarcacion},'${marcacionData.entrada}')" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="14" />
                                                                Agregar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </ul>
                                            </div>
                                        </td>`;
                                    }

                                    //* COLUMNA DE ENTRADA
                                    tbodyEntradaySalida += `<td >
                                         <div class=" dropdown">
                                             <a class="btn dropdown-toggle" type="button" id="dropEntrada${
                                                 marcacionData.idMarcacion
                                             }" data-toggle="dropdown" aria-haspopup="true"
                                                 aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                 <span class="badge badge-soft-warning" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                     <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                     No tiene entrada
                                                 </span>
                                             </a>
                                             <ul class="dropdown-menu noExport" aria-labelledby="dropEntrada${
                                                 marcacionData.idMarcacion
                                             }" style="padding: 0rem 0rem;">
                                                <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                    <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                        Opciones
                                                </h6>
                                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                 <div class="dropdown-item" dropdown-itemM noExport>
                                                     <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                         <a onclick="javascript:insertarEntradaModal('${moment(
                                                             marcacionData.salida
                                                         ).format(
                                                             "HH:mm:ss"
                                                         )}',${
                                        marcacionData.idMarcacion
                                    },${
                                        marcacionData.idHE
                                    })" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                             <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                             Insertar entrada
                                                         </a>
                                                     </div>
                                                 </div>
                                             </ul>
                                         </div>
                                     </td>`;

                                    tbodyEntradaySalida += `<td class="noExport">
                                        <a style="cursor:pointer;" data-toggle="tooltip" data-placement="left" title="Intercambiar" onclick="intercambiarMar(${marcacionData.idMarcacion})">
                                        <img style="margin-bottom: 3px;margin-top: 4px;" src="landing/images/intercambiar.svg"  height="15"/></a>
                                        </td>`;
                                    //* COLUMNA DE SALIDA

                                    if (marcacionData.controladorSalida != 0) {
                                        tbodyEntradaySalida += `
                                                    <td class="controHidSa" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo: ${marcacionData.dispositivoSalida}">  ${marcacionData.controladorSalida}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td class="controHidSa">
                                            <div class=" dropdown">
                                                <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                    <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar controlador de salida">
                                                        <img style="margin-bottom: 3px;" src="landing/images/contSalida.svg" class="mr-2" height="12"/>
                                                        No tiene controlador de Sal.
                                                    </span>
                                                </a>
                                                <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                                                   <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                       <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                           Opciones
                                                   </h6>
                                                   <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                    <div class="dropdown-item" dropdown-itemM noExport>
                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                            <a onclick="agregarControSa(${marcacionData.idMarcacion},'${marcacionData.salida}')" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="14" />
                                                                Agregar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </ul>
                                            </div>
                                        </td>`;
                                    }

                                    tbodyEntradaySalida += `<td>
                                                        <div class="dropdown">
                                                            <a class="btn dropdown" type="button" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false"
                                                                style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                            </a>
                                                            <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
                                                                <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                    <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                                                    Opciones
                                                                </h6>
                                                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                            Cambiar a entrada
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                            Cambiar a salida
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="eliminarM(${marcacionData.idMarcacion},2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/borrarD.svg"  height="12" />
                                                                            Eliminar marc.
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </ul>
                                                        </div>
                                                    </td>`;

                                    tbodyEntradaySalida += `<td >
                                                            <span class="badge badge-soft-secondary">
                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                --:--:--
                                                            </span>
                                                        </td>`;
                                }
                            }
                        }
                    }

                    /*------- N DE COLUMNAS DE REPETICION---------------- */
                    /*  tbodyEntradaySalida += `<td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td name="tiempoSitHi">---</td>`; */
                    /* --------------------------------------------------- */

                    tbody += tbodyEntradaySalida;

                    /* -----------PUNTO DE CONTRO ------------ */
                    if (marcacionData.puntoControl != null) {
                        tbody += `
                        <td class="puntoHid" > ${marcacionData.puntoControl} </td></tr>`;
                    } else {
                        tbody += `<td class="puntoHid" >
                        <div class=" dropdown">
                            <a class="btn dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar punto de control">
                                    <img style="margin-bottom: 3px;" src="landing/images/puntoCo.svg" class="mr-2" height="12"/>
                                    No tiene punto de C.
                                </span>
                            </a>
                            <ul class="dropdown-menu noExport"  style="padding: 0rem 0rem;">
                               <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                   <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>
                                       Opciones
                               </h6>
                               <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                <div class="dropdown-item" dropdown-itemM noExport>
                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                        <a onclick="agregarPuntoC(${marcacionData.idMarcacion})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                            <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                            Agregar
                                        </a>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </td></tr>`;
                    }

                    /* ----------------------------------------- ------------------*/
                }
                $("#tbodyD").html(tbody);
                $('[data-toggle="tooltip"]').tooltip();
                /* DATOS PARA EXPORTAR TABLA */
                var razonSocial = $("#nameOrganizacion").val();
                var direccion = $("#direccionO").val();
                var ruc = $("#rucOrg").val();

                var fechaAsisteDH = moment($("#pasandoV").val()).format(
                    "DD/MM/YYYY"
                );

                /* ------------------------ */
                /* boton adicional */
                /*    $.fn.dataTable.ext.buttons.alert = {
                    className: 'buttons-alert',

                    action: function ( e, dt, node, config ) {
                        alert( this.text() );
                    }
                }; */
                /* -------------------------- */
                $(".dt-button-collection .buttons-columnVisibility").each(
                    function () {
                        var $li = $(this),
                            $cb = $("<input>", {
                                type: "checkbox",
                                style:
                                    "margin:0 .25em 0 0; vertical-align:middle",
                            }).prop("checked", $(this).hasClass("active"));
                        $li.find("a").prepend($cb);
                    }
                );
                table = $("#tablaReport").DataTable({
                    searching: false,
                    scrollX: true,
                    ordering: false,
                    autoWidth: false,
                    bInfo: false,
                    bLengthChange: true,
                    fixedHeader: true,
                    pageLength: 25,
                    language: {
                        sProcessing: "Procesando...",
                        sLengthMenu: "Mostrar _MENU_ registros",
                        sZeroRecords: "No se encontraron resultados",
                        sEmptyTable: "Ningún dato disponible en esta tabla",
                        sInfo: "Mostrando registros del _START_ al _END_ ",
                        sInfoEmpty:
                            "Mostrando registros del 0 al 0 de un total de 0 registros",
                        sInfoFiltered:
                            "(filtrado de un total de _MAX_ registros)",
                        sInfoPostFix: "",
                        sSearch: "Buscar:",
                        sUrl: "",
                        sInfoThousands: ",",
                        sLoadingRecords: "Cargando...",
                        oPaginate: {
                            sFirst: "Primero",
                            sLast: "Último",
                            sNext: ">",
                            sPrevious: "<",
                        },
                        oAria: {
                            sSortAscending:
                                ": Activar para ordenar la columna de manera ascendente",
                            sSortDescending:
                                ": Activar para ordenar la columna de manera descendente",
                        },
                        buttons: {
                            copy: "Copiar",
                            colvis: "Visibilidad",
                        },
                    },
                    dom: "Blfrtip",
                    buttons: [
                        {
                            extend: "excel",
                            className: "btn btn-sm mt-1",
                            text:
                                "<i><img src='admin/images/excel.svg' height='20'></i> Descargar",
                            customize: function (xlsx) {
                                var sheet = xlsx.xl.worksheets["sheet1.xml"];
                                var downrows = 5;
                                var clRow = $("row", sheet);
                                clRow[0].children[0].remove();
                                //update Row
                                clRow.each(function () {
                                    var attr = $(this).attr("r");
                                    var ind = parseInt(attr);
                                    ind = ind + downrows;
                                    $(this).attr("r", ind);
                                });

                                // Update  row > c
                                $("row c ", sheet).each(function () {
                                    var attr = $(this).attr("r");
                                    var pre = attr.substring(0, 1);
                                    var ind = parseInt(
                                        attr.substring(1, attr.length)
                                    );
                                    ind = ind + downrows;
                                    $(this).attr("r", pre + ind);
                                });

                                function Addrow(index, data) {
                                    msg = '<row r="' + index + '">';
                                    for (i = 0; i < data.length; i++) {
                                        var key = data[i].k;
                                        var value = data[i].v;
                                        var bold = data[i].s;
                                        msg +=
                                            '<c t="inlineStr" r="' +
                                            key +
                                            index +
                                            '" s="' +
                                            bold +
                                            '" >';
                                        msg += "<is>";
                                        msg += "<t>" + value + "</t>";
                                        msg += "</is>";
                                        msg += "</c>";
                                    }
                                    msg += "</row>";
                                    return msg;
                                }
                                var now = new Date();
                                var jsDate =
                                    now.getDate() +
                                    "/" +
                                    (now.getMonth() + 1) +
                                    "/" +
                                    now.getFullYear();
                                //insert
                                var r1 = Addrow(1, [
                                    {
                                        k: "A",
                                        v: "CONTROL REGISTRO DE ASISTENCIA",
                                        s: 2,
                                    },
                                ]);
                                var r2 = Addrow(2, [
                                    { k: "A", v: "Razón Social:", s: 2 },
                                    { k: "C", v: razonSocial, s: 0 },
                                ]);
                                var r3 = Addrow(3, [
                                    { k: "A", v: "Dirección:", s: 2 },
                                    { k: "C", v: direccion, s: 0 },
                                ]);
                                var r4 = Addrow(4, [
                                    { k: "A", v: "Número de Ruc:", s: 2 },
                                    { k: "C", v: ruc, s: 0 },
                                ]);
                                var r5 = Addrow(5, [
                                    { k: "A", v: "Fecha:", s: 2 },
                                    { k: "C", v: fechaAsisteDH, s: 0 },
                                ]);
                                sheet.childNodes[0].childNodes[1].innerHTML =
                                    r1 +
                                    r2 +
                                    r3 +
                                    r4 +
                                    r5 +
                                    sheet.childNodes[0].childNodes[1].innerHTML;
                            },
                            sheetName: "Asistencia",
                            title: "Asistencia",
                            autoFilter: false,
                            exportOptions: {
                                columns: ":visible:not(.noExport)",
                                format: {
                                    body: function (data, row, column, node) {
                                        var cont = $.trim($(node).text());
                                        var cont1 = cont.replace(
                                            "Insertar entrada",
                                            ""
                                        );
                                        var cont2 = cont1.replace(
                                            "Insertar salida",
                                            ""
                                        );
                                        var cont3 = cont2.replace(
                                            "No tiene entrada",
                                            "---"
                                        );
                                        var cont4 = cont3.replace(
                                            "No tiene salida",
                                            "---"
                                        );
                                        var cont5 = cont4.replace(
                                            "No tiene punto de C.",
                                            "---"
                                        );
                                        var cont6 = cont5.replace(
                                            "No tiene actividad",
                                            "---"
                                        );
                                        var cont7 = cont6.replace(
                                            "No tiene subactividad",
                                            "---"
                                        );

                                        var cont8 = cont7.replace(
                                            "Cambiar a entrada",
                                            ""
                                        );

                                        var cont9 = cont8.replace(
                                            "Cambiar a salida",
                                            ""
                                        );

                                        var cont10 = cont9.replace(
                                            "Convertir orden",
                                            ""
                                        );

                                        var cont11 = cont10.replace(
                                            "Asignar a nueva marc.",
                                            ""
                                        );

                                        var cont12 = cont11.replace(
                                            "Eliminar marc.",
                                            ""
                                        );

                                        var cont13 = cont12.replace(
                                            "Agregar",
                                            ""
                                        );
                                        var cont14 = cont13.replace(
                                            "Opciones",
                                            ""
                                        );

                                        return $.trim(cont14);
                                    },
                                },
                            },
                        },
                        {
                            extend: "pdfHtml5",
                            className: "btn btn-sm mt-1",
                            text:
                                "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                            orientation: "landscape",
                            pageSize: "A2",
                            title: "Asistencia",
                            exportOptions: {
                                columns: ":visible:not(.noExport)",
                            },
                            customize: function (doc) {
                                doc["styles"] = {
                                    table: {
                                        width: "100%",
                                    },
                                    tableHeader: {
                                        bold: true,
                                        fontSize: 11,
                                        color: "#ffffff",
                                        fillColor: "#14274e",
                                        alignment: "left",
                                    },
                                    defaultStyle: {
                                        fontSize: 10,
                                        alignment: "center",
                                    },
                                };
                                doc.pageMargins = [20, 120, 20, 30];
                                doc.content[1].margin = [30, 0, 30, 0];
                                var colCount = new Array();
                                var tr = $("#tablaReport tbody tr:first-child");
                                var trWidth = $(tr).width();
                                $("#tablaReport")
                                    .find("tbody tr:first-child td")
                                    .each(function () {
                                        var tdWidth = $(this).width();
                                        var widthFinal = parseFloat(
                                            tdWidth * 130
                                        );
                                        widthFinal =
                                            widthFinal.toFixed(2) /
                                            trWidth.toFixed(2);
                                        if ($(this).attr("colspan")) {
                                            for (
                                                var i = 1;
                                                i <= $(this).attr("colspan");
                                                $i++
                                            ) {
                                                colCount.push("*");
                                            }
                                        } else {
                                            colCount.push(
                                                parseFloat(
                                                    widthFinal.toFixed(2)
                                                ) + "%"
                                            );
                                        }
                                    });
                                var bodyCompleto = [];
                                doc.content[1].table.body.forEach(function (
                                    line,
                                    i
                                ) {
                                    var bodyNuevo = [];
                                    if (i >= 1) {
                                        line.forEach((element) => {
                                            var textOriginal = element.text;
                                            var cambiar = textOriginal.replace(
                                                "Insertar entrada",
                                                ""
                                            );
                                            var cambiar2 = cambiar.replace(
                                                "Insertar salida",
                                                ""
                                            );
                                            var cambiar3 = cambiar2.replace(
                                                "No tiene entrada",
                                                "---"
                                            );
                                            var cambiar4 = cambiar3.replace(
                                                "No tiene salida",
                                                "---"
                                            );
                                            var cambiar5 = cambiar4.replace(
                                                "No tiene punto de C.",
                                                "---"
                                            );
                                            var cambiar6 = cambiar5.replace(
                                                "No tiene actividad",
                                                "---"
                                            );
                                            var cambiar7 = cambiar6.replace(
                                                "No tiene subactividad",
                                                "---"
                                            );
                                            var cambiar8 = cambiar7.replace(
                                                "Cambiar a entrada",
                                                ""
                                            );

                                            var cambiar9 = cambiar8.replace(
                                                "Cambiar a salida",
                                                ""
                                            );

                                            var cambiar10 = cambiar9.replace(
                                                "Convertir orden",
                                                ""
                                            );

                                            var cambiar11 = cambiar10.replace(
                                                "Asignar a nueva marc.",
                                                ""
                                            );

                                            var cambiar12 = cambiar11.replace(
                                                "Eliminar marc.",
                                                ""
                                            );
                                            var cambiar13 = cambiar12.replace(
                                                "Agregar",
                                                ""
                                            );
                                            var cambiar14 = cambiar13.replace(
                                                "Opciones",
                                                ""
                                            );
                                            var cambiar15 = cambiar14.trim();
                                            bodyNuevo.push({
                                                text: cambiar15,
                                                style: "defaultStyle",
                                            });
                                        });
                                        bodyCompleto.push(bodyNuevo);
                                    } else {
                                        bodyCompleto.push(line);
                                    }
                                });
                                doc.content.splice(0, 1);
                                doc.content[0].table.body = bodyCompleto;
                                var objLayout = {};
                                objLayout["hLineWidth"] = function (i) {
                                    return 0.2;
                                };
                                objLayout["vLineWidth"] = function (i) {
                                    return 0.2;
                                };
                                objLayout["hLineColor"] = function (i) {
                                    return "#aaa";
                                };
                                objLayout["vLineColor"] = function (i) {
                                    return "#aaa";
                                };
                                doc.content[0].layout = objLayout;
                                var now = new Date();
                                var jsDate =
                                    now.getDate() +
                                    "/" +
                                    (now.getMonth() + 1) +
                                    "/" +
                                    now.getFullYear();
                                doc["header"] = function () {
                                    return {
                                        columns: [
                                            {
                                                alignment: "left",
                                                italics: false,
                                                text: [
                                                    {
                                                        text:
                                                            "\nCONTROL REGISTRO DE ASISTENCIA",
                                                        bold: true,
                                                    },
                                                    {
                                                        text:
                                                            "\n\nRazon Social:\t\t\t\t\t\t",
                                                        bold: false,
                                                    },
                                                    {
                                                        text: razonSocial,
                                                        bold: false,
                                                    },
                                                    {
                                                        text:
                                                            "\nDireccion:\t\t\t\t\t\t\t",
                                                        bold: false,
                                                    },
                                                    {
                                                        text: "\t" + direccion,
                                                        bold: false,
                                                    },
                                                    {
                                                        text:
                                                            "\nNumero de Ruc:\t\t\t\t\t",
                                                        bold: false,
                                                    },
                                                    { text: ruc, bold: false },
                                                    {
                                                        text:
                                                            "\nFecha:\t\t\t\t\t\t\t\t\t",
                                                        bold: false,
                                                    },
                                                    {
                                                        text: fechaAsisteDH,
                                                        bold: false,
                                                    },
                                                ],

                                                fontSize: 10,
                                                margin: [30, 0],
                                            },
                                        ],
                                        margin: 20,
                                    };
                                };
                            },
                        },
                    ],
                    initComplete: function () {
                        dataT = this;

                        //*fecha
                        if ($("#fechaSwitch").prop("checked")) {
                            dataT.api().columns(".fechaHid").visible(true);
                        } else {
                            dataT.api().columns(".fechaHid").visible(false);
                        }

                        //*sexo
                        if ($("#checSexo").prop("checked")) {
                            dataT.api().columns(".sexoHid").visible(true);
                        } else {
                            dataT.api().columns(".sexoHid").visible(false);
                        }

                        //*cargo
                        if ($("#checCargo").prop("checked")) {
                            dataT.api().columns(".cargoHid").visible(true);
                        } else {
                            dataT.api().columns(".cargoHid").visible(false);
                        }

                        //*controlador entrada
                        if ($("#checControlEn").prop("checked")) {
                            dataT.api().columns(".controHidEn").visible(true);
                        } else {
                            dataT.api().columns(".controHidEn").visible(false);
                        }

                        //*controlador salida
                        if ($("#checControlSa").prop("checked")) {
                            dataT.api().columns(".controHidSa").visible(true);
                        } else {
                            dataT.api().columns(".controHidSa").visible(false);
                        }
                        setTimeout(function () {
                            $("#tablaReport").DataTable().draw();
                        }, 200);
                    },
                });
                $(window).on("resize", function () {
                    $("#tablaReport").css("width", "100%");
                    table.draw(true);
                });
            } else {
                $("#MostarDetalles").hide();
                $("#tbodyD").empty();



                table = $("#tablaReport").DataTable({
                    searching: false,
                    scrollX: true,
                    ordering: false,
                    autoWidth: false,
                    bInfo: false,
                    bLengthChange: true,
                    fixedHeader: true,
                    pageLength: 25,
                    retrieve: true,
                    language: {
                        sProcessing: "Procesando...",
                        sLengthMenu: "Mostrar _MENU_ registros",
                        sZeroRecords: "No se encontraron resultados",
                        sEmptyTable: "Ningún dato disponible en esta tabla",
                        sInfo: "Mostrando registros del _START_ al _END_ ",
                        sInfoEmpty:
                            "Mostrando registros del 0 al 0 de un total de 0 registros",
                        sInfoFiltered:
                            "(filtrado de un total de _MAX_ registros)",
                        sInfoPostFix: "",
                        sSearch: "Buscar:",
                        sUrl: "",
                        sInfoThousands: ",",
                        sLoadingRecords: "Cargando...",
                        oPaginate: {
                            sFirst: "Primero",
                            sLast: "Último",
                            sNext: ">",
                            sPrevious: "<",
                        },
                        oAria: {
                            sSortAscending:
                                ": Activar para ordenar la columna de manera ascendente",
                            sSortDescending:
                                ": Activar para ordenar la columna de manera descendente",
                        },
                        buttons: {
                            copy: "Copiar",
                            colvis: "Visibilidad",
                        },
                    },


                    initComplete: function () {

                    },
                });
                $(window).on("resize", function () {
                    $("#tablaReport").css("width", "100%");
                    table.draw(true);
                });

                if($("#tbodyD").is(':empty')){
                    $("#tbodyD").append(
                        '<tr class="odd"><td valign="top" colspan="16" class="text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>'
                    );
                }

            }
        },
        error: function () {},
    });
    $(".horasEntrada").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true,
    });

    $(".horasSalida").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true,
    });
}

function cambiarF() {
    f1 = $("#fechaInput").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $("#pasandoV").val(f2);
    if ($.fn.DataTable.isDataTable("#tablaReport")) {
        /* $('#tablaReport').DataTable().destroy(); */
    }

    cargartabla(f2);
}

/* --------------INTERCAMBIAR  ENTRADA Y SALIDA--------------------------------- */
function intercambiarMar(id) {
    alertify
        .confirm("¿Desea intercambiar entrada y salida?", function (e) {
            if (e) {
                $.ajax({
                    async: false,
                    type: "POST",
                    url: "/intercambiarTareo",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    statusCode: {
                        /*401: function () {
                            location.reload();
                        },*/
                        419: function () {
                            location.reload();
                        },
                    },
                    success: function (data) {
                        $("#btnRecargaTabla").click();
                        $.notifyClose();
                        $.notify(
                            {
                                message: data,
                                icon: "admin/images/checked.svg",
                            },
                            {
                                icon_type: "image",
                                allow_dismiss: true,
                                newest_on_top: true,
                                delay: 6000,
                                template:
                                    '<div data-notify="container" class="col-xs-8 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 35,
                            }
                        );
                    },
                    error: function () {},
                });
            }
        })
        .setting({
            title: "Intercambiar Marcación",
            labels: {
                ok: "Si",
                cancel: "No",
            },
            modal: true,
            startMaximized: false,
            reverseButtons: true,
            resizable: false,
            closable: false,
            transition: "zoom",
            oncancel: function (closeEvent) {},
        });
}
/* ------------------------------------ */
///////////////////
// * VARIABLES DE MARCACIONES
var newEntrada = {};
var newSalida = {};
/* ---------FUNCION INSERTAR SALIDA.------- */
// * MODAL DE INSERTAR SALIDA
function insertarSalidaModal(hora, id, idH) {
    var estadoH = false;
    contenidoHorario.forEach((element) => {
        if (element.idHorarioE == idH) {
            if (element.estado == 0) {
                $("#actualizarH").modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) return;
    $("#idMarcacionIS").val(id);
    $("#i_hora").text(hora);
    $("#idHorarioIS").val(idH);
    $("#insertarSalida").modal();
    horasS = $("#horaSalidaNueva").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",
        time_24hr: true,
        enableSeconds: true,
        static: true,
    });
}

// * INSERTAR SALIDA
function insertarSalida() {
    var id = $("#idMarcacionIS").val();
    var salida = $("#horaSalidaNueva").val();
    var horario = $("#idHorarioIS").val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoregistrarNSalida",
        data: {
            id: id,
            salida: salida,
            horario: horario,
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data.respuesta != undefined) {
                $("#i_validS").empty();
                $("#i_validS").append(data.respuesta);
                $("#i_validS").show();
                $('button[type="submit"]').attr("disabled", false);
                sent = false;
            } else {
                $("#i_validS").empty();
                $("#i_validS").hide();
                $("#insertarSalida").modal("toggle");
                $('button[type="submit"]').attr("disabled", false);
                fechaValue.setDate(fechaGlobal);
                $("#btnRecargaTabla").click();
                limpiarAtributos();
                $.notifyClose();
                $.notify(
                    {
                        message: "\nMarcación modificada.",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        position: "fixed",
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 35,
                    }
                );
            }
        },
        error: function () {},
    });
}
// * VALIDACION
$("#formInsertarSalida").attr("novalidate", true);
$("#formInsertarSalida").submit(function (e) {
    e.preventDefault();
    if (
        $("#horaSalidaNueva").val() == "00:00:00" ||
        $("#horaSalidaNueva").val() == "00:00:0"
    ) {
        $("#i_validS").empty();
        $("#i_validS").append("Ingresar salida.");
        $("#i_validS").show();
        $('button[type="submit"]').attr("disabled", false);
        return;
    }
    $("#i_validS").empty();
    $("#i_validS").hide();
    $('button[type="submit"]').attr("disabled", true);
    this.submit();
});
/* ---------------------------------------- */

/* -------------------FUNCIONES INSERTAR ENTRADA----------- */
function insertarEntradaModal(hora, id, idH) {
    var estadoH = false;
    contenidoHorario.forEach((element) => {
        if (element.idHorarioE == idH) {
            if (element.estado == 0) {
                $("#actualizarH").modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) return;
    $("#idMarcacionIE").val(id);
    $("#ie_hora").text(hora);
    $("#idHorarioIE").val(idH);
    $("#insertarEntrada").modal();
    horasE = $("#horasEntradaNueva").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",
        time_24hr: true,
        enableSeconds: true,
        static: true,
    });
}
// * INSERTAR ENTRADA
function insertarEntrada() {
    var id = $("#idMarcacionIE").val();
    var entrada = $("#horasEntradaNueva").val();
    var horario = $("#idHorarioIE").val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoregistrarNEntrada",
        data: {
            id: id,
            entrada: entrada,
            horario: horario,
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data.respuesta != undefined) {
                $("#i_validE").empty();
                $("#i_validE").append(data.respuesta);
                $("#i_validE").show();
                $('button[type="submit"]').attr("disabled", false);
                sent = false;
            } else {
                $("#i_validE").empty();
                $("#i_validE").hide();
                $("#insertarEntrada").modal("toggle");
                $('button[type="submit"]').attr("disabled", false);
                fechaValue.setDate(fechaGlobal);
                $("#btnRecargaTabla").click();
                limpiarAtributos();
                $.notifyClose();
                $.notify(
                    {
                        message: "\nMarcación modificada.",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        position: "fixed",
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 35,
                    }
                );
            }
        },
        error: function () {},
    });
}
// * VALIDACION
$("#formInsertarEntrada").attr("novalidate", true);
$("#formInsertarEntrada").submit(function (e) {
    e.preventDefault();
    if (
        $("#horasEntradaNueva").val() == "00:00:00" ||
        $("#horasEntradaNueva").val() == "00:00:0"
    ) {
        $("#i_validE").empty();
        $("#i_validE").append("Ingresar entrada.");
        $("#i_validE").show();
        $('button[type="submit"]').attr("disabled", false);
        return;
    }
    $("#i_validE").empty();
    $("#i_validE").hide();
    $('button[type="submit"]').attr("disabled", true);
    this.submit();
});
/* -------------------------------------------------------- */
// * LIMPIEZA DE CAMPOS
function limpiarAtributos() {
    // ? MODAL DE CAMBIAR ENTRADA
    $("#entradaM").empty();
    $("#e_valid").empty();
    $("#e_valid").hide();
    $("#c_horaE").empty();
    // ? MODAL DE CAMBIAR SALIDA
    $("#salidaM").empty();
    $("#s_valid").empty();
    $("#s_valid").hide();
    $("#c_horaS").empty();
    // ? MODAL DE ASIGNACION A NUEVA MARCACIÓN
    $("#a_valid").empty();
    $("#a_valid").hide();
    $("#horarioM").empty();
    $("#a_hora").empty();
    // ? MODAL DE INSERTAR SALIDA
    $("#i_validS").empty();
    $("#i_validS").hide();
    if (horasS.config != undefined) {
        horasS.setDate("00:00:00");
    }
    // ? MODAL DE INSERTAR ENTRADA
    $("#i_validE").empty();
    $("#i_validE").hide();
    if (horasE.config != undefined) {
        horasE.setDate("00:00:00");
    }
    // ? MODAL DE CAMBIAR HORARIO
    $('#ch_valid').empty();
    $('#ch_valid').hide();
    $('#horarioXE').empty();
    $('#detalleHorarios').empty();
    $('#detalleHorarios').hide();
    // ? MODAL DE NUEVA MARCACION
    $("#v_entrada").prop("checked", false);
    $("#v_salida").prop("checked", false);
    $("#nuevaEntrada").prop("disabled", false);
    $("#nuevaSalida").prop("disabled", false);
    if (newSalida.config != undefined) {
        newSalida.setDate("00:00:00");
    }
    if (newEntrada.config != undefined) {
        newEntrada.setDate("00:00:00");
    }
    $("#rowDatosM").hide();
    $("#r_horarioXE").empty();
}

/* *************************EVENTOS DE SELECTOR DE COLUMNA ********************************* */

//* FECHA
$("#fechaSwitch").change(function (event) {
    if ($("#fechaSwitch").prop("checked")) {
        dataT.api().columns(".fechaHid").visible(true);
    } else {
        dataT.api().columns(".fechaHid").visible(false);
    }
    setTimeout(function () {
        $("#tablaReport").css("width", "100%");
        $("#tablaReport").DataTable().draw(false);
    }, 1);
});

//* CODIGO
$("#checCodigo").change(function (event) {
    if ($("#checCodigo").prop("checked")) {
        dataT.api().columns(".codigoHid").visible(true);
    } else {
        dataT.api().columns(".codigoHid").visible(false);
    }
    setTimeout(function () {
        $("#tablaReport").css("width", "100%");
        $("#tablaReport").DataTable().draw(false);
    }, 1);
});

//* NUM DOCUMENTO
$("#checnumdoc").change(function (event) {
    if ($("#checnumdoc").prop("checked")) {
        dataT.api().columns(".numdocHid").visible(true);
    } else {
        dataT.api().columns(".numdocHid").visible(false);
    }
    setTimeout(function () {
        $("#tablaReport").css("width", "100%");
        $("#tablaReport").DataTable().draw(false);
    }, 1);
});

//* SEXO
$("#checSexo").change(function (event) {
    if ($("#checSexo").prop("checked")) {
        dataT.api().columns(".sexoHid").visible(true);
    } else {
        dataT.api().columns(".sexoHid").visible(false);
    }
    setTimeout(function () {
        $("#tablaReport").css("width", "100%");
        $("#tablaReport").DataTable().draw(false);
    }, 1);
});

//* CARGO
$("#checCargo").change(function (event) {
    if ($("#checCargo").prop("checked")) {
        dataT.api().columns(".cargoHid").visible(true);
    } else {
        dataT.api().columns(".cargoHid").visible(false);
    }
    setTimeout(function () {
        $("#tablaReport").css("width", "100%");
        $("#tablaReport").DataTable().draw(false);
    }, 1);
});

//* PUNTO CONTROL
$("#checPuntoc").change(function (event) {
    if ($("#checPuntoc").prop("checked")) {
        dataT.api().columns(".puntoHid").visible(true);
    } else {
        dataT.api().columns(".puntoHid").visible(false);
    }
    setTimeout(function () {
        $("#tablaReport").css("width", "100%");
        $("#tablaReport").DataTable().draw(false);
    }, 1);
});

//* CONTROLADOR ENTRADA
$("#checControlEn").change(function (event) {
    if ($("#checControlEn").prop("checked")) {
        dataT.api().columns(".controHidEn").visible(true);
    } else {
        dataT.api().columns(".controHidEn").visible(false);
    }
    setTimeout(function () {
        $("#tablaReport").css("width", "100%");
        $("#tablaReport").DataTable().draw(false);
    }, 1);
});

//* CONTROLADOR SALIDA
$("#checControlSa").change(function (event) {
    if ($("#checControlSa").prop("checked")) {
        dataT.api().columns(".controHidSa").visible(true);
    } else {
        dataT.api().columns(".controHidSa").visible(false);
    }
    setTimeout(function () {
        $("#tablaReport").css("width", "100%");
        $("#tablaReport").DataTable().draw(false);
    }, 1);
});
/* ------------------------------------------------------------------ */

//*PARA QUE NO SE CIERRE DROPDOWN
$(document).on("click", ".allow-focus", function (e) {
    e.stopPropagation();
});

/* ************************************************ */

//*FUNCION AGREGAR PUNTO DE CONTROL***************
$("#selectPuntoC").select2({
    placeholder: "Seleccione punto de control",
});
function agregarPuntoC(idMarcacion) {
    $("#idMarcacionPC").val(idMarcacion);

    /* PARA SELECT DE PUNTO DE CONTROL */
    $("#selectPuntoC").empty();
    var container = $("#selectPuntoC");

    $.ajax({
        async: false,
        url: "/listPuntoControl",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            var option = `<option value="" disabled selected>Seleccionar punto de control</option>`;

            /* AGREGANDO OPTIONS*/
            data.forEach((element) => {
                option += `<option value="${element.id}">${element.descripcion} </option>`;
            });
            container.append(option);

            $("#insertarPuntoC").modal("show");
        },
        error: function () {},
    });
}
//*NSERTAR PUNTO DE CONTROL
function insertarPuntoC() {
    let idMarcacion = $("#idMarcacionPC").val();
    let idPunto = $("#selectPuntoC").val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoregistrarPunto",
        data: {
            idMarcacion,
            idPunto,
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#btnRecargaTabla").click();
            $("#insertarPuntoC").modal("hide");
            $.notifyClose();
            $.notify(
                {
                    message: "\nPunto de control agregado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {},
    });
}
/* ********************************************* */

//******************AGREGAR ACTIVIDAD*********************************** */
function agregarActiv(idMarcacion) {
    $("#idMarcacionACT").val(idMarcacion);

    /* PARA SELECT DE ACTIVIDAD */
    $("#selectActiv").empty();
    var container = $("#selectActiv");

    /* PARA SELECT DE SUBACTIVIDAD */
    $("#selectSubActiv").empty();

    $.ajax({
        async: false,
        url: "/listActividadTareo",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            var option = `<option value="" disabled selected>Seleccionar actividad</option>`;

            /* AGREGANDO OPTIONS*/
            data.forEach((element) => {
                if (element.conSub == 1) {
                    option += `<option value="${element.Activi_id}">${element.Activi_Nombre} </option>`;
                } else {
                    option += `<option value="${element.Activi_id}" disabled>${element.Activi_Nombre} (Sin subactividades)</option>`;
                }
            });
            container.append(option);

            $("#selectSubActiv").prop("disabled", true);

            $("#insertarActivMo").modal("show");
        },
        error: function () {},
    });
}
//************************************************************************/

//*******************SELECCIONAR SUBACTIVIDADES POR ACTIVIDAD
$(function () {
    $("#selectActiv").on("change", function () {
        $("#selectSubActiv").empty();
        var containerSub = $("#selectSubActiv");

        let valorActiv = $("#selectActiv").val();
        console.log(valorActiv);
        $.ajax({
            async: false,
            url: "/listActividadTareo",
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                401: function () {
                    location.reload();
                },
                /*419: function () {
                    location.reload();
                }*/
            },
            success: function (data) {
                var option2 = `<option value="" disabled selected>Seleccionar subactividad</option>`;

                /* AGREGANDO OPTIONS*/
                data.forEach((element) => {
                    //*PONIENDO SUBACTIVIDADES
                    element.subactividades.forEach((element2) => {
                        if (element2.Activi_id == valorActiv) {
                            option2 += `<option value="${element2.idsubActividad}" >${element2.subAct_nombre} </option>`;
                        }
                    });
                });

                containerSub.append(option2);
                $("#selectSubActiv").prop("disabled", false);
            },
            error: function () {},
        });
    });
});
//********************************************************* */

//* INSERTAR ACTIVIDAD
function insertarActiv() {
    let idMarcacion = $("#idMarcacionACT").val();
    let idActiv = $("#selectActiv").val();
    let idSubact = $("#selectSubActiv").val();

    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoregistrarActiv",
        data: {
            idMarcacion,
            idActiv,
            idSubact,
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#btnRecargaTabla").click();
            $("#insertarActivMo").modal("hide");
            $.notifyClose();
            $.notify(
                {
                    message: "\nActividad agregada.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {},
    });
}
//****************************************

//*SUBACTIVIDADES-----------------------------------------
function agregarSubAct(idMarcacion) {
    $("#idMarcacionSACT").val(idMarcacion);

    /* PARA SELECT DE ACTIVIDAD */
    $("#selectSubActiv2").empty();
    var container = $("#selectSubActiv2");

    $("#divActi").hide();

    $.ajax({
        async: false,
        url: "/listActividadTareo",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            var option2 = `<option value="" disabled selected>Seleccionar subactividad</option>`;

            /* AGREGANDO OPTIONS*/
            data.forEach((element) => {
                //*PONIENDO SUBACTIVIDADES
                element.subactividades.forEach((element2) => {
                    option2 += `<option value="${element2.idsubActividad}" >${element2.subAct_nombre} </option>`;
                });
            });
            container.append(option2);

            $("#selectSubActiv").prop("disabled", true);

            $("#insertarSubMo").modal("show");
        },
        error: function () {},
    });
}

//*******************SELECCIONAR SUBACTIVIDADES y mostrar ACTIVIDAD
$(function () {
    $("#selectSubActiv2").on("change", function () {
        let valorActiv = $("#selectSubActiv2").val();
        console.log(valorActiv);
        $.ajax({
            async: false,
            url: "/listActividadTareo",
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                401: function () {
                    location.reload();
                },
                /*419: function () {
                    location.reload();
                }*/
            },
            success: function (data) {
                /* AGREGANDO OPTIONS*/
                data.forEach((element) => {
                    //*PONIENDO SUBACTIVIDADES
                    element.subactividades.forEach((element2) => {
                        if (element2.idsubActividad == valorActiv) {
                            $("#idActi").val(element2.Activi_id);
                            $("#actividadSub").text(element.Activi_Nombre);
                        }
                    });
                });
                $("#divActi").show();
            },
            error: function () {},
        });
    });
});

//* INSERTAR SUBACTIVIDAD
function insertarSubac() {
    let idMarcacion = $("#idMarcacionSACT").val();
    let idActiv = $("#idActi").val();
    let idSubact = $("#selectSubActiv2").val();

    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoregistrarActiv",
        data: {
            idMarcacion,
            idActiv,
            idSubact,
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#btnRecargaTabla").click();
            $("#insertarSubMo").modal("hide");
            $.notifyClose();
            $.notify(
                {
                    message: "\nSubactividad agregada.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {},
    });
}
//********************************************************* */

//* AGREGAR CONTROLADOR ENTRADA*****************************

function agregarControE(idMarcacion, entrada) {
    //verificamos si tiene entrada
    if (entrada != 0) {
        let formatEn = moment(entrada).format("HH:mm:ss");

        $("#i_horaContEntrada").text(formatEn);

        $("#idMarcacionContEntrada").val(idMarcacion);

        /* PARA SELECT DE CONTROLADOR */
        $("#selectContEntrada").empty();
        var container = $("#selectContEntrada");

        $.ajax({
            async: false,
            url: "/listControladores",
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                401: function () {
                    location.reload();
                },
                /*419: function () {
                location.reload();
            }*/
            },
            success: function (data) {
                var option = `<option value="" disabled selected>Seleccionar controlador</option>`;

                /* AGREGANDO OPTIONS*/
                data.forEach((element) => {
                    option += `<option value="${element.idcontroladores_tareo}">${element.nombre} </option>`;
                });
                container.append(option);

                $("#insertarContEntradaModal").modal("show");
            },
            error: function () {},
        });
    } else {
        $.notifyClose();
        $.notify(
            {
                message: "\nSin marcacion de entrada",
                icon: "admin/images/warning.svg",
            },
            {
                position: "fixed",
                mouse_over: "pause",
                placement: {
                    from: "top",
                    align: "center",
                },
                icon_type: "image",
                newest_on_top: true,
                delay: 2000,
                template:
                    '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                    "</div>",
                spacing: 35,
            }
        );
    }
}
//**** */

//*INSERTAR A BD CONTROLADOR DE ENTRADA
function insertarContEntrada() {
    let idMarcacion = $("#idMarcacionContEntrada").val();
    let idControl = $("#selectContEntrada").val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoregistrarContE",
        data: {
            idMarcacion,
            idControl,
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#btnRecargaTabla").click();
            $("#insertarContEntradaModal").modal("hide");
            $.notifyClose();
            $.notify(
                {
                    message: "\nControlador agregado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {},
    });
}
//************************************************ */

//*AGREGAR CONTROLADOR SALIDA**********************************
function agregarControSa(idMarcacion, salida) {
    //verificamos si tiene entrada
    if (salida != 0) {
        let formatEn = moment(salida).format("HH:mm:ss");

        $("#i_horaContSalida").text(formatEn);

        $("#idMarcacionContSalida").val(idMarcacion);

        /* PARA SELECT DE CONTROLADOR */
        $("#selectContSalida").empty();
        var container = $("#selectContSalida");

        $.ajax({
            async: false,
            url: "/listControladores",
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                401: function () {
                    location.reload();
                },
                /*419: function () {
                location.reload();
            }*/
            },
            success: function (data) {
                var option = `<option value="" disabled selected>Seleccionar controlador</option>`;

                /* AGREGANDO OPTIONS*/
                data.forEach((element) => {
                    option += `<option value="${element.idcontroladores_tareo}">${element.nombre} </option>`;
                });
                container.append(option);

                $("#insertarContSalidaModal").modal("show");
            },
            error: function () {},
        });
    } else {
        $.notifyClose();
        $.notify(
            {
                message: "\nSin marcacion de salida",
                icon: "admin/images/warning.svg",
            },
            {
                position: "fixed",
                mouse_over: "pause",
                placement: {
                    from: "top",
                    align: "center",
                },
                icon_type: "image",
                newest_on_top: true,
                delay: 2000,
                template:
                    '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                    "</div>",
                spacing: 35,
            }
        );
    }
}

//*INSERTAR A BD CONTROLADOR DE SALIDA
function insertarContSalida() {
    let idMarcacion = $("#idMarcacionContSalida").val();
    let idControl = $("#selectContSalida").val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoregistrarContS",
        data: {
            idMarcacion,
            idControl,
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#btnRecargaTabla").click();
            $("#insertarContSalidaModal").modal("hide");
            $.notifyClose();
            $.notify(
                {
                    message: "\nControlador agregado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {},
    });
}

//*********************************************************** */
// ! ********************************** ELIMINAR MARCACIÓN *****************************************************
// * ELIMINAR MARCACION
function eliminarM(id, tipo, idHE) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idHE) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
    alertify
        .confirm("¿Desea eliminar marcación si o no?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    async: false,
                    type: "POST",
                    url: "/TareoeliminarMarcacion",
                    data: {
                        id: id,
                        tipo: tipo
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    statusCode: {
                        /*401: function () {
                            location.reload();
                        },*/
                        419: function () {
                            location.reload();
                        }
                    },
                    success: function (data) {
                       /*  fechaValue.setDate(fechaGlobal); */
                        $('#btnRecargaTabla').click();
                        $.notifyClose();
                        $.notify({
                            message: data,
                            icon: '/landing/images/alert1.svg',
                        }, {
                            icon_type: 'image',
                            allow_dismiss: true,
                            newest_on_top: true,
                            delay: 6000,
                            template: '<div data-notify="container" class="col-xs-8 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                                '</div>',
                            spacing: 35
                        });
                    },
                    error: function () { }
                });
            }
        })
        .setting({
            title: "Eliminar Marcación",
            labels: {
                ok: "Si",
                cancel: "No",
            },
            modal: true,
            startMaximized: false,
            reverseButtons: true,
            resizable: false,
            closable: false,
            transition: "zoom",
            oncancel: function (closeEvent) {
            },
        });
    $('a').css('pointer-events', 'auto');
}
/* ----------------------------------------------------------------- */
// ! ********************************** ASIGNAR A NUEVA MARCACIÓN *********************************************
// * ASIGNAR NUEVA MARCACION
function asignarNuevaM(id, hora, tipo, horario) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == horario) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
    $('#idMarcacionA').val(id);
    $('#tipoM').val(tipo);
    $('#a_hora').text(hora);
    if (tipo == 1) {
        $('#img_a').attr("src", "landing/images/entradaD.svg");
    } else {
        $('#img_a').attr("src", "landing/images/salidaD.svg");
    }
    $('#horarioM').empty();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareohorariosxMarcacion",
        data: {
            id: id,
            tipo: tipo
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            var option = `<option value="0" selected>Sin horario</option>`;
            for (let index = 0; index < data.length; index++) {
                option += `<option value="${data[index].id}">Horario: ${data[index].horario_descripcion} (${data[index].horaI} - ${data[index].horaF})</option>`;
            }
            $('#horarioM').append(option);
            $('#horarioM').val(horario).trigger("change");
        },
        error: function () { }
    });
    $('#asignacionMarcacion').modal();
    $('#asignacionM').val(tipo).trigger('change');
    $('a').css('pointer-events', 'auto');
    sent = false;
}
// * GUARDAR ASIGNACION
function guardarAsignacion() {
    var id = $('#idMarcacionA').val();
    var idHorario = $('#horarioM').val();
    var tipoM = $('#tipoM').val();
    var tipo = $('#asignacionM').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoasignacionNew",
        data: {
            id: id,
            idHorario: idHorario,
            tipoM: tipoM,
            tipo: tipo
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            if (data.respuesta == undefined) {
                $('#a_valid').empty();
                $('#a_valid').hide();
                /* fechaValue.setDate(fechaGlobal); */
                $('#btnRecargaTabla').click();
                $('#asignacionMarcacion').modal('toggle');
                $('button[type="submit"]').attr("disabled", false);
                limpiarAtributos();
            } else {
                $('#a_valid').empty();
                $('#a_valid').append(data.respuesta);
                $('#a_valid').show();
                sent = false;
                $('button[type="submit"]').attr("disabled", false);
            }
        },
        error: function () { }
    });
}
$('#formGuardarAsignacion').attr('novalidate', true);
$('#formGuardarAsignacion').submit(function (e) {
    e.preventDefault();
    if ($("#horarioM").val() == "" || $("#horarioM").val() == null) {
        $('#a_valid').empty();
        $('#a_valid').append("Seleccionar horario.");
        $('#a_valid').show();
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if ($("#asignacionM").val() == "" || $("#asignacionM").val() == null) {
        $('#a_valid').empty();
        $('#a_valid').append("Seleccionar marcación.");
        $('#a_valid').show();
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!sent) {
        sent = true;
        $('#a_valid').empty();
        $('#a_valid').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
/* ---------------------------------------------------------------------------- */
// ! *********************************** CONVERTIR ORDEN ******************************************************
// * CONVERTIR ORDEN
function convertirOrden(id, idHE) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idHE) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
    alertify
        .confirm("¿Desea Convertir orden si o no?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    async: false,
                    type: "POST",
                    url: "/TareoconvertirTiempos",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    statusCode: {
                        /*401: function () {
                            location.reload();
                        },*/
                        419: function () {
                            location.reload();
                        }
                    },
                    success: function (data) {
                        if (data.respuesta == undefined) {
                          /*   fechaValue.setDate(fechaGlobal); */
                            $('#btnRecargaTabla').click();
                            $.notifyClose();
                            $.notify(
                                {
                                    message: "\nMarcación modificada.",
                                    icon: "admin/images/checked.svg",
                                },
                                {
                                    position: "fixed",
                                    icon_type: "image",
                                    newest_on_top: true,
                                    delay: 5000,
                                    template:
                                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                        '<span data-notify="title">{1}</span> ' +
                                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                        "</div>",
                                    spacing: 35,
                                }
                            );
                        } else {
                            $.notifyClose();
                            $.notify(
                                {
                                    message: data.respuesta,
                                    icon: "admin/images/warning.svg",
                                },
                                {
                                    position: "fixed",
                                    mouse_over: "pause",
                                    placement: {
                                        from: "top",
                                        align: "center",
                                    },
                                    icon_type: "image",
                                    newest_on_top: true,
                                    delay: 2000,
                                    template:
                                        '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                        '<span data-notify="title">{1}</span> ' +
                                        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                        "</div>",
                                    spacing: 35,
                                }
                            );
                        }
                    },
                    error: function () { }
                });
            }
        })
        .setting({
            title: "Modificar Marcación",
            labels: {
                ok: "Si",
                cancel: "No",
            },
            modal: true,
            startMaximized: false,
            reverseButtons: true,
            resizable: false,
            closable: false,
            transition: "zoom",
            oncancel: function (closeEvent) {
            },
        });
    $('a').css('pointer-events', 'auto');
}
/* -------------------------------------------------------------------------------------- */
// ! *********************************** CAMBIAR A SALIDA ****************************************************
// * FUNCION DE LISTA DE ENTRADAS CON SALIDAS NULL
function listaEntrada(id, fecha, idEmpleado, hora, tipo, idHE) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idHE) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
    $('#entradaM').empty();
    $('#c_horaE').text(hora);
    $('#c_tipoE').val(tipo);
    $('#listaEntradasMarcacion').modal();
    $('#idMarcacionE').val(id);
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareolistaMarcacionE",
        data: {
            fecha: fecha,
            idEmpleado: idEmpleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            if (data.length != 0) {
                var container = `<option value="" disabled selected>Seleccionar entrada</option>`;
                for (let index = 0; index < data.length; index++) {
                    container += `<optgroup label="Horario ${data[index].horario}">`;
                    data[index].data.forEach(element => {
                        if (element.id == id) {
                            container += `<option value="${element.id}" selected="selected">
                                    ${moment(element.entrada).format("HH:mm:ss")}
                                </option>`;
                        } else {
                            container += `<option value="${element.id}">
                                    ${moment(element.entrada).format("HH:mm:ss")}
                                </option>`;
                        }
                    });
                    container += `</optgroup>`;
                }
            } else {
                var container = `<option value="" disabled selected>No hay marcaciónes disponibles</option>`;
            }
            console.log(container);
            $('#entradaM').append(container);
            imagenesEntrada();
        },
        error: function () { }
    });
    sent = false;
    $('a').css('pointer-events', 'auto');
}
// * FUNCION DE CAMBIAR SALIDA
function cambiarSalidaM() {
    var idCambiar = $('#idMarcacionE').val();
    var idMarcacion = $('#entradaM').val();
    var tipo = $('#c_tipoE').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareocambiarSM",
        data: {
            idCambiar: idCambiar,
            idMarcacion: idMarcacion,
            tipo: tipo
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            if (data.respuesta == undefined) {
                $('#e_valid').hide();
                $('#listaEntradasMarcacion').modal('toggle');
                $('button[type="submit"]').attr("disabled", false);
               /*  fechaValue.setDate(fechaGlobal); */
                $('#btnRecargaTabla').click();
                $.notifyClose();
                $.notify(
                    {
                        message: "\nMarcación modificada.",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        position: "fixed",
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 35,
                    }
                );
            } else {
                $('#e_valid').empty();
                $('#e_valid').append(data.respuesta);
                $('#e_valid').show();
                $('button[type="submit"]').attr("disabled", false);
                sent = false;
            }
        },
        error: function () {
        }
    });
}
// * COMBOX DE ENTRADA
function imagenesEntrada() {
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "landing/images/entradaD.svg";
        var $state = $(
            `<span>Entrada : <img src="${baseUrl}" height="12" class="ml-1 mr-1" /> ${state.text} </span>`
        );
        return $state;
    };
    $("#entradaM").select2({
        templateResult: formatState
    });
}
// * VALIDACION
$('#formCambiarSalidaM').attr('novalidate', true);
$('#formCambiarSalidaM').submit(function (e) {
    e.preventDefault();
    if ($("#entradaM").val() == "" || $("#entradaM").val() == null) {
        $('#e_valid').empty();
        $('#e_valid').append("Seleccionar marcación.");
        $('#e_valid').show();
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!sent) {
        sent = true;
        $('#e_valid').empty();
        $('#e_valid').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});

/* ----------------------------------------------------------------------------------------- */
// ! *********************************** CAMBIAR A ENTRADA ********************************************
// * FUNCION DE LISTA DE SALIDAS CON ENTRADAS NULL
function listaSalida(id, fecha, idEmpleado, hora, tipo, idHE) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idHE) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
    $('#salidaM').empty();
    $('#c_horaS').text(hora);
    $('#c_tipoS').val(tipo);
    $('#listaSalidasMarcacion').modal();
    $('#idMarcacion').val(id);
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareolistaMarcacionS",
        data: {
            fecha: fecha,
            idEmpleado: idEmpleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            if (data.length != 0) {
                var container = `<option value="" disabled selected>Seleccionar salida</option>`;
                for (let index = 0; index < data.length; index++) {
                    container += `<optgroup label="Horario ${data[index].horario}">`;
                    data[index].data.forEach(element => {
                        if (element.id == id) {
                            container += `<option value="${element.id}" selected="selected">
                                    ${moment(element.salida).format("HH:mm:ss")}
                                </option>`;
                        } else {
                            container += `<option value="${element.id}">
                                    ${moment(element.salida).format("HH:mm:ss")}
                                </option>`;
                        }
                    });
                    container += `</optgroup>`;
                }
            } else {
                var container = `<option value="" disabled selected>No hay marcaciónes disponibles</option>`;
            }
            $('#salidaM').append(container);
            imagenesSalida();
        },
        error: function () { }
    });
    sent = false;
    $('a').css('pointer-events', 'auto');
}
// * FUNCION DE CAMBIAR ENTRADA
function cambiarEntradaM() {
    var idCambiar = $('#idMarcacion').val();
    var idMarcacion = $('#salidaM').val();
    var tipo = $('#c_tipoS').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareocambiarEM",
        data: {
            idCambiar: idCambiar,
            idMarcacion: idMarcacion,
            tipo: tipo
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            if (data.respuesta == undefined) {
                $('#s_valid').hide();
                $('#listaSalidasMarcacion').modal('toggle');
                $('button[type="submit"]').attr("disabled", false);
              /*   fechaValue.setDate(fechaGlobal); */
                $('#btnRecargaTabla').click();
                limpiarAtributos();
                $.notifyClose();
                $.notify(
                    {
                        message: "\nMarcación modificada.",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        position: "fixed",
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 35,
                    }
                );
            } else {
                sent = false;
                $('button[type="submit"]').attr("disabled", false);
                $('#s_valid').append(data.respuesta);
                $('#s_valid').show();
            }
        },
        error: function () {
        }
    });
}
// * COMBOX DE SALIDA
function imagenesSalida() {
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "landing/images/salidaD.svg";
        var $state = $(
            `<span>Salida : <img src="${baseUrl}" height="12" class="ml-1 mr-1" /> ${state.text} </span>`
        );
        return $state;
    };
    $("#salidaM").select2({
        templateResult: formatState
    });
}
// * VALIDACION
$('#formCambiarEntradaM').attr('novalidate', true);
$('#formCambiarEntradaM').submit(function (e) {
    e.preventDefault();
    if ($("#salidaM").val() == "" || $("#salidaM").val() == null) {
        $('#s_valid').empty();
        $('#s_valid').append("Seleccionar marcación.");
        $('#s_valid').show();
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!sent) {
        sent = true;
        $('#s_valid').empty();
        $('#s_valid').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
/* ---------------------------------------------------------------------------------- */
