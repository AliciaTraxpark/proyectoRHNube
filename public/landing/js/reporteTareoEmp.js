//* FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
    conjunction: " a ",
    minRange: 1,
    onChange: function (selectedDates) {
        var _this = this;
        var dateArr = selectedDates.map(function (date) { return _this.formatDate(date, 'Y-m-d'); });
        $('#ID_START').val(dateArr[0]);
        $('#ID_END').val(dateArr[1]);
    },
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 1) {
            var fm = moment(selectedDates[0]).add("day", -1).format("YYYY-MM-DD");
            instance.setDate([fm, selectedDates[0]], true);
        }
    }
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
   
  
});

function cargartabla(fecha1,fecha2) {
    /* ****INICALIZAR VALORES DE SELECTORES****/
    $("#fechaSwitch").prop("checked", true);
    $("#checCodigo").prop("checked", true);
    $("#checnumdoc").prop("checked", true);
    $("#checSexo").prop("checked", false);
    $("#checCargo").prop("checked", false);
    $("#checPuntoc").prop("checked", true);
    $("#checPuntocDescrip").prop("checked", false);
    $("#checControl").prop("checked", true);
    /* ************************************** */
    var idemp = $("#idempleado").val(); console.log('id'+idemp)
    if (idemp == 0) { alert('no empleado');
        $.notifyClose();
        $.notify({
            message: '\nSeleccione empleado.',
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
        return false;
    }
    $.ajax({
        type: "GET",
        url: "/reporteTareoEmpleado",
        data: {
            fecha1,
            fecha2,
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
           
            contenidoHorario.length = 0;
            if (data.length != 0) {
                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                // ! *********** CABEZERA DE TABLA**********
                $("#MostarDetalles").show();
                $("#theadD").empty();


                 //*CANTIDAD DE NOMBRES DE DETALLE
                 var cantidadColumnasDetalle=0
                 for (let i = 0; i < data.length; i++) {
                    //* OBTENER CANTIDAD TOTAL DE COLUMNAS
                    if (cantidadColumnasDetalle < data[i].detalleNombres.length) {
                        cantidadColumnasDetalle = data[i].detalleNombres.length;
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

                                    <th class="controHidSa">Controlador de salida</th>
                                    <th>Hora de salida</th>
                                    <th >Tiempo en sitio</th>`;

                theadTabla += `     <th class="puntoHid">Cód. Punto C.</th>
                                   <th class="puntoHid">Punto de control</th>
                                   `;

                for (let j = 0; j < cantidadColumnasDetalle; j++) {
                theadTabla += `<th class="puntoDescripHid">Descripcion ${j + 1} </th>`;
                }
                theadTabla += `</tr>`;

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
                                    data[index].fechaMarcacion
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
                    var tbodyDetalle = "";
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
                                            <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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
                                            <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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
                                            <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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

                                tbodyEntradaySalida += `<td data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo: ${marcacionData.dispositivoEntrada}">
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
                                                                        <a  style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                            Cambiar a entrada
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a 
                                                                         style="cursor:pointer; font-size:12px;padding-top: 2px;">
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
                                                <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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
                                    tbodyEntradaySalida += `<td data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo: ${marcacionData.dispositivoSalida}" >
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
                                                                            <a  style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                                Cambiar a entrada
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                            <a  style="cursor:pointer; font-size:12px;padding-top: 2px;">
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
                                                <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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
                                        <a type="button" class="btn dropdown-toggle" id="dropSalida${
                                            marcacionData.idMarcacion
                                        }" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" data-open-dropdown="dropSalida${
                                                marcacionData.idMarcacion
                                            }" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                            <span  class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                No tiene salida
                                            </span>
                                        </a>
                                        <ul class="dropdown-menu  scrollable-menu noExport"  aria-labelledby="dropSalida${
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
                                                <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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
                                                <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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
                                                <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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
                                             <ul class="dropdown-menu scrollable-menu noExport" aria-labelledby="dropEntrada${
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
                                                <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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

                                    tbodyEntradaySalida += `<td data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo: ${marcacionData.dispositivoSalida}">
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
                                                                        <a  style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                            Cambiar a entrada
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a  style="cursor:pointer; font-size:12px;padding-top: 2px;">
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

                    if(marcacionData.idpuntoControl!=null){
                        tbody += `
                        <td class="puntoHid" > ${marcacionData.idpuntoControl} </td>`;
                    }
                    else{
                        tbody += `
                        <td class="puntoHid" > -- </td>`;
                    }
                    if (marcacionData.puntoControl != null) {
                        tbody += `
                        <td class="puntoHid" > ${marcacionData.puntoControl} </td>`;
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
                            <ul class="dropdown-menu scrollable-menu noExport"  style="padding: 0rem 0rem;">
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
                    </td>`;
                    }
                    for (let j = 0; j < data[index].detalleNombres.length; j++) {
                        var detalleData = data[index].detalleNombres[j];
                        tbodyDetalle += `<td class="puntoDescripHid" >${detalleData.descripcion}</td>`;
                    }
                    for (let m = data[index].detalleNombres.length; m < cantidadColumnasDetalle; m++) {
                        tbodyDetalle += `<td class="puntoDescripHid" >---</td>`;
                    }
                    tbody += tbodyDetalle;
                    tbody += `
                    </tr>`;

                    /* ----------------------------------------- ------------------*/
                }
                $("#tbodyD").html(tbody);
                $('[data-toggle="tooltip"]').tooltip();
                if (data.length <= 4) {
                    var tbodyTR = '';
                    tbodyTR += '<tr>';

                    tbodyTR += `<td ><br><br><br><br><br><br><br><br><br><br></td>
                                <td class="fechaHid"></td>
                                <td class="codigoHid"></td>
                                <td class="numdocHid"></td>
                                <td ></td>
                                <td class="sexoHid"></td>
                                <td class="cargoHid"></td>`;

                    tbodyTR += `<td ><br><br></td>
                                <td ></td>
                                <td ></td>
                                <td></td>
                                <td class="controHidEn"></td>
                                <td ></td>
                                <td class="controHidSa" ></td>
                                <td ></td>
                                <td></td>
                                <td class="puntoHid" ></td>
                                <td class="puntoHid" ></td>
                                `;
                    for(cc=0;  cc < cantidadColumnasDetalle; cc++){
                        tbodyTR +='<td class="puntoDescripHid"></td>';
                    }
                    tbodyTR +=`</tr>`;
                    $('#tbodyD').append(tbodyTR);
                }
                /* DATOS PARA EXPORTAR TABLA */
                var razonSocial = $("#nameOrganizacion").val();
                var direccion = $("#direccionO").val();
                var ruc = $("#rucOrg").val();
                 dni=data[0].emple_nDoc;
                nombre = data[0].perso_nombre + "\t" + data[0].perso_apPaterno + "\t" + data[0].perso_apMaterno;
                area = (data[0].area_descripcion == null) ? "------" : data[0].area_descripcion;
                cargo = (data[0].cargo_descripcion == null) ? "------" : data[0].cargo_descripcion;

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
                                var downrows = 10;
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
                                    msg = `<row r="${index}">`;
                                    for (i = 0; i < data.length; i++) {
                                        var key = data[i].k;
                                        var value = data[i].v;
                                        var bold = data[i].s;
                                        msg += `<c t="inlineStr" r="${key} ${index}" s="${bold}" wpx="149">`;
                                        msg += `<is>`;
                                        msg += `<t>${value}</t>`;
                                        msg += `</is>`;
                                        msg += `</c>`;
                                    }
                                    msg += `</row>`;
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
                                var fechas = 'Desde ' + moment($('#ID_START').val()).format('DD/MM/YYYY') + ' Hasta ' + moment($('#ID_END').val()).format('DD/MM/YYYY');

                                var r1 = Addrow(1, [{ k: 'A', v: 'REGISTRO DE TAREO', s: 51 }]);
                                var r2 = Addrow(2, [{ k: 'A', v: fechas, s: 2 }]);
                                var r3 = Addrow(3, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                                var r4 = Addrow(4, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                                var r5 = Addrow(5, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                                var r6 = Addrow(7, [{ k: 'A', v: 'DNI:', s: 2 }, { k: 'C', v: dni, s: 0 }]);
                                var r7 = Addrow(8, [{ k: 'A', v: 'Apellidos y Nombres:', s: 2 }, { k: 'C', v: nombre, s: 0 }]);
                                var r8 = Addrow(9, [{ k: 'A', v: 'Área:', s: 2 }, { k: 'C', v: area, s: 0 }]);
                                var r9 = Addrow(10, [{ k: 'A', v: 'Cargo:', s: 2 }, { k: 'C', v: cargo, s: 0 }]);
                                sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + r6 + r7 + r8 + r9 + sheet.childNodes[0].childNodes[1].innerHTML;
                            },
                            sheetName: "Registro de Tareo",
                            title: "Registro de Tareo",
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

                                        var cont13=cont12.replace(
                                            "No tiene controlador de Ent.",
                                            "---"
                                        )
                                        var cont14=cont13.replace(
                                            "No tiene controlador de Sal.",
                                            "---"
                                        )
                                        
                                        var cont15 = cont14.replace(
                                            "Agregar",
                                            ""
                                        );
                                        var cont16 = cont15.replace(
                                            "Opciones",
                                            ""
                                        );

                                        return $.trim(cont16);
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
                            title: "Registro de Tareo",
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
                                doc.pageMargins = [20, 150, 20, 30];
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
                                                "No tiene controlador de Ent.",
                                            "---"
                                            );

                                            var cambiar14 = cambiar13.replace(
                                                "No tiene controlador de Sal.",
                                                "---"
                                            );
                                            var cambiar15 = cambiar14.replace(
                                                "Agregar",
                                                ""
                                            );
                                            var cambiar16 = cambiar15.replace(
                                                "Opciones",
                                                ""
                                            );
                                            var cambiar17 = cambiar16.trim();
                                            bodyNuevo.push({
                                                text: cambiar17,
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
                                    var fechas = 'Desde ' + moment($('#ID_START').val()).format('DD/MM/YYYY') + ' Hasta ' + moment($('#ID_END').val()).format('DD/MM/YYYY');
                                doc["header"] = function () {
                                    return {
                                        columns: [
                                            {
                                                alignment: "left",
                                                italics: false,
                                                text: [
                                                    {
                                                        text:
                                                            "\n REGISTRO DE TAREO",
                                                        bold: true,
                                                    },
                                                    { text: '\n\Fechas:\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: fechas, bold: false },
                                                    {
                                                        text:
                                                            "\n\Razón Social:\t\t\t\t\t\t\t\t",
                                                        bold: false,
                                                    },
                                                    {
                                                        text: razonSocial,
                                                        bold: false,
                                                    },
                                                    {
                                                        text:
                                                            "\nDirección:\t\t\t\t\t\t\t\t\t",
                                                        bold: false,
                                                    },
                                                    {
                                                        text: "\t" + direccion,
                                                        bold: false,
                                                    },
                                                    {
                                                        text:
                                                            "\nNúmero de Ruc:\t\t\t\t\t\t\t",
                                                        bold: false,
                                                    },
                                                    { text: ruc, bold: false },
                                                    { text: '\nDNI:\t\t\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: dni, bold: false },
                                                    { text: '\nApellidos y Nombres:\t\t\t\t\t', bold: false }, { text: nombre, bold: false },
                                                    { text: '\nÁrea:\t\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: area, bold: false },
                                                    { text: '\nCargo:\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: cargo, bold: false }
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

                          //*DESCRIPCIONES DE PUNTO C

                        if ($("#checPuntocDescrip").prop("checked")) {
                            dataT.api().columns(".puntoDescripHid").visible(true);
                        } else {
                            dataT.api().columns(".puntoDescripHid").visible(false);
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

ocultarModif();

}
function ocultarModif() {
    var permisoMo=$('#modifReporte').val();
    if(permisoMo==0){
        $('.noExport').hide();
         $('a>span').tooltip('dispose') 
        /* $('.notooltipS').html(`<span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora">
        <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
        No tiene salida
        </span>`); */
        /*  $("span[title]").each(function() {
            var text =$("span[title]").text();
            console.log(text);
             text.replace("Agregar","");
            }); */

    }
}


function cambiarF() {

    f1 = $("#ID_START").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $('#pasandoV').val(f2);
    f3 = $("#ID_END").val();
    if ($('#idempleado').val() == "" || $('#idempleado').val() == null) {
        $.notifyClose();
        $.notify(
            {
                message:
                    "\nElegir empleado.",
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
    } else {
        cargartabla(f2, f3);
    }

}



/* ---------------------------------------- */




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

//*DESCRIPCIONES DE PUNTO C
$("#checPuntocDescrip").change(function (event) {
    if ($("#checPuntocDescrip").prop("checked")) {
        dataT.api().columns(".puntoDescripHid").visible(true);
    } else {
        dataT.api().columns(".puntoDescripHid").visible(false);
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
    var permisoMo=$('#modifReporte').val();
    if(permisoMo==0){
        $('.noExport').hide();
         $('span').tooltip('dispose') 
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
    var permisoMo=$('#modifReporte').val();
    if(permisoMo==0){
        $('.noExport').hide();
         $('span').tooltip('dispose') 
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


