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
                                    <th>CC&nbsp;</th>
                                    <th class="fechaHid" name="tiempoSitHi">Fecha</th>
                                    <th class="codigoHid">Código</th>
                                    <th class="numdocHid">Número de documento </th>
                                    <th>Nombres y Apellidos</th>
                                    <th class="sexoHid" name="tiempoSitHi">Sexo</th>
                                    <th class="cargoHid"  name="tiempoSitHi">Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>`;


                    theadTabla += `<th>Código –</th>
                                    <th>Actividad</th>
                                    <th>Código –</th>
                                    <th>Subactividad</th>
                                    <th>Hora de entrada</th>
                                    <th class="noExport">&nbsp; &nbsp; &nbsp; &nbsp;</th>
                                    <th>Hora de salida</th>
                                    <th >Tiempo en sitio</th>`;


                theadTabla += `
                                   <th class="puntoHid">Punto de control</th>
                                   <th class="controHid">Controlador</th></tr>`;

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

                                <td>${index + 1}&nbsp;</td>

                                <td class="fechaHid"  name="tiempoSitHi">${moment($('#pasandoV').val()).format('DD/MM/YYYY')}&nbsp;</td>
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
                                    h ==
                                    moment(marcacionData.entrada).format("HH")
                                ) {

                                    if (marcacionData.codigoActividad != 0) {
                                        tbodyEntradaySalida += `<td >${marcacionData.codigoActividad} </td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                    if (marcacionData.Activi_Nombre != 0) {
                                        tbodyEntradaySalida += `<td>${marcacionData.Activi_Nombre}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                    if (marcacionData.codigoSubactiv != 0) {
                                        tbodyEntradaySalida += `<td >${marcacionData.codigoSubactiv} </td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                    if (marcacionData.subAct_nombre != 0) {
                                        tbodyEntradaySalida += `<td>${marcacionData.subAct_nombre}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                    tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>${moment(
                                        marcacionData.entrada
                                    ).format("HH:mm:ss")}</td>`;

                                    /* SI  TENGO SALIDA */
                                    if (marcacionData.salida != 0) {
                                        tbodyEntradaySalida += `<td class="noExport"></td>`;
                                        tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> ${moment(
                                            marcacionData.salida
                                        ).format("HH:mm:ss")}</td>`;

                                        var horaFinal = moment(
                                            marcacionData.salida
                                        );
                                        var horaInicial = moment(
                                            marcacionData.entrada
                                        );
                                        if (
                                            horaFinal.isSameOrAfter(horaInicial)
                                        ) {
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
                                                minutosTiempo =
                                                    "0" + minutosTiempo;
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
                                            sumaTiempos = moment(
                                                sumaTiempos
                                            ).add(segundosTiempo, "seconds");
                                            sumaTiempos = moment(
                                                sumaTiempos
                                            ).add(minutosTiempo, "minutes");
                                            sumaTiempos = moment(
                                                sumaTiempos
                                            ).add(horasTiempo, "hours");
                                        }
                                    } else {
                                        tbodyEntradaySalida += `<td class="noExport">
                                        <a style="cursor:pointer;" data-toggle="tooltip" data-placement="left" title="Intercambiar" onclick="intercambiarMar(${marcacionData.idMarcacion})"><img style="margin-bottom: 3px;margin-top: 4px;" src="landing/images/intercambiar.svg"  height="15"/></a>
                                        </td>`;
                                        /* SI NO TENGO SALIDA */
                                        tbodyEntradaySalida += `<td>
                                        <div class="dropdown noExport">
                                        <a type="button" class="btn dropdown-toggle" id="dropSalida${marcacionData.idMarcacion}" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" data-open-dropdown="dropSalida${marcacionData.idMarcacion}" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                            <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                No tiene salida
                                            </span>
                                        </a>
                                        <ul class="dropdown-menu noExport"  aria-labelledby="dropSalida${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">

                                            <div class="dropdown-item noExport">
                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                    <a onclick="javascript:insertarSalidaModal('${moment(marcacionData.entrada).format("HH:mm:ss")}',${marcacionData.idMarcacion},${marcacionData.idHE})"
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
                                        moment(marcacionData.salida).format(
                                            "HH"
                                        )
                                    ) {

                                        if (marcacionData.codigoActividad != 0) {
                                            tbodyEntradaySalida += `<td >${marcacionData.codigoActividad} </td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }

                                        if (marcacionData.Activi_Nombre != 0) {
                                            tbodyEntradaySalida += `<td>${marcacionData.Activi_Nombre}</td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }

                                        if (marcacionData.codigoSubactiv != 0) {
                                            tbodyEntradaySalida += `<td >${marcacionData.codigoSubactiv} </td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }

                                        if (marcacionData.subAct_nombre != 0) {
                                            tbodyEntradaySalida += `<td>${marcacionData.subAct_nombre}</td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }

                                         //* COLUMNA DE ENTRADA
                                         tbodyEntradaySalida += `<td >
                                         <div class=" dropdown">
                                             <a class="btn dropdown-toggle" type="button" id="dropEntrada${marcacionData.idMarcacion}" data-toggle="dropdown" aria-haspopup="true"
                                                 aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                 <span class="badge badge-soft-warning" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                     <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                     No tiene entrada
                                                 </span>
                                             </a>
                                             <ul class="dropdown-menu noExport" aria-labelledby="dropEntrada${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">

                                                 <div class="dropdown-item">
                                                     <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                         <a onclick="javascript:insertarEntradaModal('${moment(marcacionData.salida).format("HH:mm:ss")}',${marcacionData.idMarcacion},${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
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

                                        tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> ${moment(
                                            marcacionData.salida
                                        ).format("HH:mm:ss")}</td>`;

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

                    /* -----------PUNTO DE CONTRO Y CONTROLADOR------------- */
                    tbody += `

                              <td class="puntoHid" > ${marcacionData.puntoControl} </td>
                                <td class="controHid">  ${marcacionData.contrT_nombres}  ${marcacionData.contrT_ApPaterno}  ${marcacionData.contrT_ApMaterno}</td></tr>`;
                    /* ----------------------------------------- ------------------*/
                }
                $("#tbodyD").html(tbody);
                $('[data-toggle="tooltip"]').tooltip();
                /* DATOS PARA EXPORTAR TABLA */
                var razonSocial=$('#nameOrganizacion').val();
                var direccion=$('#direccionO').val();
                var ruc=$('#rucOrg').val();

                var fechaAsisteDH = moment($('#pasandoV').val()).format('DD/MM/YYYY');

                /* ------------------------ */
                 /* boton adicional */
              /*    $.fn.dataTable.ext.buttons.alert = {
                    className: 'buttons-alert',

                    action: function ( e, dt, node, config ) {
                        alert( this.text() );
                    }
                }; */
                /* -------------------------- */
                $('.dt-button-collection .buttons-columnVisibility').each(function(){
                    var $li = $(this),
                        $cb = $('<input>', {
                                type:'checkbox',
                                style:'margin:0 .25em 0 0; vertical-align:middle'}
                              ).prop('checked', $(this).hasClass('active') );
                    $li.find('a').prepend( $cb );
                  });
                table = $("#tablaReport").DataTable({
                    searching: false,
                    scrollX: true,
                    ordering: false,
                    autoWidth: false,
                    bInfo: false,
                    bLengthChange: false,
                    fixedHeader: true,
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
                    dom: 'Bfrtip',
                    buttons: [
                          {
                            extend: 'collection',
                            text: 'Elegir columnas',
                            className: 'btn btn-sm mt-1',
                            buttons: [
                                {
                                    text:''+
                                    '<input type="checkbox" type="checkbox" value="0"  class="form-check-input" id="chec">'+
                                    '<label class="form-check-label" for="chec">Fecha'+
                                        '</label>',
                                    action: function ( e, dt, node, config ) {

                                        if ($("#chec").val()==1) {
                                            $("#chec").val("0");
                                            $('#chec').prop("checked",false);
                                            $('.fechaHid').hide();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);


                                        }
                                        else{
                                            $("#chec").val("1");
                                            $('#chec').prop("checked",true);
                                            $('.fechaHid').show();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);

                                        }
                                    }
                                },
                                {
                                    text:''+
                                    '<input type="checkbox" type="checkbox" value="1"  class="form-check-input" id="checCodigo" checked>'+
                                    '<label class="form-check-label" for="checCodigo">Código'+
                                        '</label>',
                                    action: function ( e, dt, node, config ) {

                                        if ($("#checCodigo").val()==1) {
                                            $("#checCodigo").val("0");
                                            $('#checCodigo').prop("checked",false);
                                            $('.codigoHid').hide();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);


                                        }
                                        else{
                                            $("#checCodigo").val("1");
                                            $('#checCodigo').prop("checked",true);
                                            $('.codigoHid').show();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);

                                        }
                                    }
                                },
                                {
                                    text:''+
                                    '<input type="checkbox" type="checkbox" value="1"  class="form-check-input" id="checnumdoc" checked>'+
                                    '<label class="form-check-label" for="checnumdoc">Número de documento'+
                                        '</label>',
                                    action: function ( e, dt, node, config ) {

                                        if ($("#checnumdoc").val()==1) {
                                            $("#checnumdoc").val("0");
                                            $('#checnumdoc').prop("checked",false);
                                            $('.numdocHid').hide();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);


                                        }
                                        else{
                                            $("#checnumdoc").val("1");
                                            $('#checnumdoc').prop("checked",true);
                                            $('.numdocHid').show();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);

                                        }
                                    }
                                },
                                {
                                    text:''+
                                    '<input type="checkbox" type="checkbox" value="0"  class="form-check-input" id="checSexo">'+
                                    '<label class="form-check-label" for="checSexo">Sexo'+
                                        '</label>',
                                    action: function ( e, dt, node, config ) {

                                        if ($("#checSexo").val()==1) {
                                            $("#checSexo").val("0");
                                            $('#checSexo').prop("checked",false);
                                            $('.sexoHid').hide();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);


                                        }
                                        else{
                                            $("#checSexo").val("1");
                                            $('#checSexo').prop("checked",true);
                                            $('.sexoHid').show();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);

                                        }



                                    }
                                },
                                {
                                    text:''+
                                    '<input type="checkbox" type="checkbox" value="0"  class="form-check-input" id="checCargo">'+
                                    '<label class="form-check-label" for="checCargo">Cargo'+
                                        '</label>',
                                    action: function ( e, dt, node, config ) {

                                        if ($("#checCargo").val()==1) {
                                            $("#checCargo").val("0");
                                            $('#checCargo').prop("checked",false);
                                            $('.cargoHid').hide();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);


                                        }
                                        else{
                                            $("#checCargo").val("1");
                                            $('#checCargo').prop("checked",true);
                                            $('.cargoHid').show();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);

                                        }



                                    }
                                },
                                {
                                    text:''+
                                    '<input type="checkbox" type="checkbox" value="1"  class="form-check-input" id="checPuntoc" checked>'+
                                    '<label class="form-check-label" for="checPuntoc"> Punto de control'+
                                        '</label>',
                                    action: function ( e, dt, node, config ) {

                                        if ($("#checPuntoc").val()==1) {
                                            $("#checPuntoc").val("0");
                                            $('#checPuntoc').prop("checked",false);
                                            $('.puntoHid').hide();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);


                                        }
                                        else{
                                            $("#checPuntoc").val("1");
                                            $('#checPuntoc').prop("checked",true);
                                            $('.puntoHid').show();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);

                                        }


                                    }
                                },
                                {
                                    text:''+
                                    '<input type="checkbox" type="checkbox" value="1"  class="form-check-input" id="checControl" checked>'+
                                    '<label class="form-check-label" for="checControl"> Controlador '+
                                        '</label>',
                                    action: function ( e, dt, node, config ) {

                                        if ($("#checControl").val()==1) {
                                            $("#checControl").val("0");
                                            $('#checControl').prop("checked",false);
                                            $('.controHid').hide();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);


                                        }
                                        else{
                                            $("#checControl").val("1");
                                            $('#checControl').prop("checked",true);
                                            $('.controHid').show();
                                            setTimeout(function () {
                                                $("#tablaReport").css("width", "100%");
                                                $("#tablaReport").DataTable().draw(true);
                                            }, 200);

                                        }


                                    }
                                }
                            ]
                        },


                        {
                        extend: 'excel',
                        className: 'btn btn-sm mt-1',
                        text: "<i><img src='admin/images/excel.svg' height='20'></i> Descargar",
                        customize: function (xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            var downrows = 5;
                            var clRow = $('row', sheet);
                            clRow[0].children[0].remove();
                            //update Row
                            clRow.each(function () {
                                var attr = $(this).attr('r');
                                var ind = parseInt(attr);
                                ind = ind + downrows;
                                $(this).attr("r", ind);
                            });

                            // Update  row > c
                            $('row c ', sheet).each(function () {
                                var attr = $(this).attr('r');
                                var pre = attr.substring(0, 1);
                                var ind = parseInt(attr.substring(1, attr.length));
                                ind = ind + downrows;
                                $(this).attr("r", pre + ind);
                            });

                            function Addrow(index, data) {
                                msg = '<row r="' + index + '">'
                                for (i = 0; i < data.length; i++) {
                                    var key = data[i].k;
                                    var value = data[i].v;
                                    var bold = data[i].s;
                                    msg += '<c t="inlineStr" r="' + key + index + '" s="' + bold + '" >';
                                    msg += '<is>';
                                    msg += '<t>' + value + '</t>';
                                    msg += '</is>';
                                    msg += '</c>';
                                }
                                msg += '</row>';
                                return msg;
                            }
                            var now = new Date();
                            var jsDate = now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear();
                            //insert
                            var r1 = Addrow(1, [{ k: 'A', v: 'CONTROL REGISTRO DE ASISTENCIA', s: 2 }]);
                            var r2 = Addrow(2, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                            var r3 = Addrow(3, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                            var r4 = Addrow(4, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                            var r5 = Addrow(5, [{ k: 'A', v: 'Fecha:', s: 2 }, { k: 'C', v: fechaAsisteDH, s: 0 }]);
                            sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                        },
                        sheetName: 'Asistencia',
                        title: 'Asistencia',
                        autoFilter: false,
                        exportOptions: {
                            columns: ":visible:not(.noExport)",
                            format: {
                                body: function (data, row, column, node) {
                                    var cont = $.trim($(node).text());
                                    var cont1 = cont.replace('Insertar entrada', '');
                                    var cont2 = cont1.replace('Insertar salida', '');
                                    var cont3 = cont2.replace('No tiene entrada', '---');
                                    var cont4 = cont3.replace('No tiene salida', '---');

                                    return $.trim(cont4);
                                }
                            }
                        },
                    }, {
                        extend: "pdfHtml5",
                        className: 'btn btn-sm mt-1',
                        text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                        orientation: 'landscape',
                        pageSize: 'A2',
                        title: 'Asistencia',
                        exportOptions: {
                           columns: ":visible:not(.noExport)"
                        },
                        customize: function (doc) {
                            doc['styles'] = {
                                table: {
                                    width: '100%'
                                },
                                tableHeader: {
                                    bold: true,
                                    fontSize: 11,
                                    color: '#6c757d',
                                    fillColor: '#ffffff',
                                    alignment: 'left'
                                },
                                defaultStyle: {
                                    fontSize: 10,
                                    alignment: 'center'
                                }
                            };
                            doc.pageMargins = [20, 120, 20, 30];
                            doc.content[1].margin = [30, 0, 30, 0];
                            var colCount = new Array();
                            var tr = $('#tablaReport tbody tr:first-child');
                            var trWidth = $(tr).width();
                            $('#tablaReport').find('tbody tr:first-child td').each(function () {
                                var tdWidth = $(this).width();
                                var widthFinal = parseFloat(tdWidth * 130);
                                widthFinal = widthFinal.toFixed(2) / trWidth.toFixed(2);
                                if ($(this).attr('colspan')) {
                                    for (var i = 1; i <= $(this).attr('colspan'); $i++) {
                                        colCount.push('*');
                                    }
                                } else {
                                    colCount.push(parseFloat(widthFinal.toFixed(2)) + '%');
                                }
                            });
                            var bodyCompleto = [];
                            doc.content[1].table.body.forEach(function (line, i) {
                                var bodyNuevo = [];
                                if (i >= 1) {
                                    line.forEach(element => {
                                        var textOriginal = element.text;
                                        var cambiar = textOriginal.replace('Insertar entrada', '');
                                        var cambiar2 = cambiar.replace('Insertar salida', '');
                                        var cambiar3 = cambiar2.replace('No tiene entrada', '---');
                                        var cambiar4 = cambiar3.replace('No tiene salida', '---');
                                        var cambiar5 = cambiar4.trim();
                                        bodyNuevo.push({ text: cambiar5, style: 'defaultStyle' });
                                    });
                                    bodyCompleto.push(bodyNuevo);
                                } else {
                                    bodyCompleto.push(line);
                                }
                            });
                            doc.content.splice(0, 1);
                            doc.content[0].table.body = bodyCompleto;
                            var objLayout = {};
                            objLayout['hLineWidth'] = function (i) { return .2; };
                            objLayout['vLineWidth'] = function (i) { return .2; };
                            objLayout['hLineColor'] = function (i) { return '#aaa'; };
                            objLayout['vLineColor'] = function (i) { return '#aaa'; };
                            doc.content[0].layout = objLayout;
                            var now = new Date();
                            var jsDate = now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear();
                            doc["header"] = function () {
                                return {
                                    columns: [
                                        {
                                            alignment: 'left',
                                            italics: false,
                                            text: [
                                                { text: '\nCONTROL REGISTRO DE ASISTENCIA', bold: true },
                                                { text: '\n\nRazon Social:\t\t\t\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                                { text: '\nDireccion:\t\t\t\t\t\t\t', bold: false }, { text: '\t' + direccion, bold: false },
                                                { text: '\nNumero de Ruc:\t\t\t\t\t', bold: false }, { text: ruc, bold: false },
                                                { text: '\nFecha:\t\t\t\t\t\t\t\t\t', bold: false }, { text: fechaAsisteDH, bold: false }
                                            ],

                                            fontSize: 10,
                                            margin: [30, 0]
                                        },
                                    ],
                                    margin: 20
                                };
                            };
                        }
                    }],
                    initComplete: function () {
                        setTimeout(function () {
                            $("#tablaReport").DataTable().draw();
                        }, 200);
                    },
                });
                $(window).on("resize", function () {
                    $("#tablaReport").css("width", "100%");
                    table.draw(true);
                });
                if ($("#customSwitDetalles").is(":checked")) {
                    $('[name="tiempoSitHi"]').show();
                    setTimeout(function () {
                        $("#tablaReport").css("width", "100%");
                        $("#tablaReport").DataTable().draw(true);
                    }, 200);
                } else {
                    $('[name="tiempoSitHi"]').hide();
                    setTimeout(function () {
                        $("#tablaReport").css("width", "100%");
                        $("#tablaReport").DataTable().draw(true);
                    }, 200);
                }
            } else {
                $("#MostarDetalles").hide();
                $("#tbodyD").empty();
                $("#tbodyD").append(
                    '<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>'
                );
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
function cambiartabla() {
    if ($("#customSwitDetalles").is(":checked")) {
        $('[name="tiempoSitHi"]').show();
        setTimeout(function () {
            $("#tablaReport").css("width", "100%");
            $("#tablaReport").DataTable().draw(true);
        }, 200);
    } else {
        $('[name="tiempoSitHi"]').hide();
        setTimeout(function () {
            $("#tablaReport").css("width", "100%");
            $("#tablaReport").DataTable().draw(true);
        }, 200);
    }
}


/* --------------INTERCAMBIAR  ENTRADA Y SALIDA--------------------------------- */
function intercambiarMar(id) {

    alertify
        .confirm("¿Desea intercambiar entrada y salida?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    async: false,
                    type: "POST",
                    url: "/intercambiarTareo",
                    data: {
                        id: id,
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

                        $('#btnRecargaTabla').click();
                        $.notifyClose();
                        $.notify({
                            message: data,
                            icon: 'admin/images/checked.svg',
                        }, {
                            icon_type: 'image',
                            allow_dismiss: true,
                            newest_on_top: true,
                            delay: 6000,
                            template: '<div data-notify="container" class="col-xs-8 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                '</div>',
                            spacing: 35
                        });
                    },
                    error: function () { }
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
            oncancel: function (closeEvent) {
            },
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
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idH) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) return;
    $('#idMarcacionIS').val(id);
    $('#i_hora').text(hora);
    $('#idHorarioIS').val(idH);
    $('#insertarSalida').modal();
    horasS = $('#horaSalidaNueva').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",
        time_24hr: true,
        enableSeconds: true,
        static: true
    });
}

// * INSERTAR SALIDA
function insertarSalida() {
    var id = $('#idMarcacionIS').val();
    var salida = $('#horaSalidaNueva').val();
    var horario = $('#idHorarioIS').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoregistrarNSalida",
        data: {
            id: id,
            salida: salida,
            horario: horario
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
                $('#i_validS').empty();
                $('#i_validS').append(data.respuesta);
                $('#i_validS').show();
                $('button[type="submit"]').attr("disabled", false);
            } else {
                $('#i_validS').empty();
                $('#i_validS').hide();
                $('#insertarSalida').modal('toggle');
                $('button[type="submit"]').attr("disabled", false);
                fechaValue.setDate(fechaGlobal);
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
            }
        },
        error: function () {
        },
    });
}
// * VALIDACION
$('#formInsertarSalida').attr('novalidate', true);
$('#formInsertarSalida').submit(function (e) {
    e.preventDefault();
    if ($("#horaSalidaNueva").val() == "00:00:00" || $("#horaSalidaNueva").val() == "00:00:0") {
        $('#i_validS').empty();
        $('#i_validS').append("Ingresar salida.");
        $('#i_validS').show();
        $('button[type="submit"]').attr("disabled", false);
        return;
    }
    $('#i_validS').empty();
    $('#i_validS').hide();
    $('button[type="submit"]').attr("disabled", true);
    this.submit();
});
/* ---------------------------------------- */

/* -------------------FUNCIONES INSERTAR ENTRADA----------- */
function insertarEntradaModal(hora, id, idH) {
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idH) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) return;
    $('#idMarcacionIE').val(id);
    $('#ie_hora').text(hora);
    $('#idHorarioIE').val(idH);
    $('#insertarEntrada').modal();
    horasE = $('#horasEntradaNueva').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",
        time_24hr: true,
        enableSeconds: true,
        static: true
    });
}
// * INSERTAR ENTRADA
function insertarEntrada() {
    var id = $('#idMarcacionIE').val();
    var entrada = $('#horasEntradaNueva').val();
    var horario = $('#idHorarioIE').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/TareoregistrarNEntrada",
        data: {
            id: id,
            entrada: entrada,
            horario: horario
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
                $('#i_validE').empty();
                $('#i_validE').append(data.respuesta);
                $('#i_validE').show();
                $('button[type="submit"]').attr("disabled", false);
            } else {
                $('#i_validE').empty();
                $('#i_validE').hide();
                $('#insertarEntrada').modal('toggle');
                $('button[type="submit"]').attr("disabled", false);
                fechaValue.setDate(fechaGlobal);
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
            }
        },
        error: function () {
        },
    });
}
// * VALIDACION
$('#formInsertarEntrada').attr('novalidate', true);
$('#formInsertarEntrada').submit(function (e) {
    e.preventDefault();
    if ($("#horasEntradaNueva").val() == "00:00:00" || $("#horasEntradaNueva").val() == "00:00:0") {
        $('#i_validE').empty();
        $('#i_validE').append("Ingresar entrada.");
        $('#i_validE').show();
        $('button[type="submit"]').attr("disabled", false);
        return;
    }
    $('#i_validE').empty();
    $('#i_validE').hide();
    $('button[type="submit"]').attr("disabled", true);
    this.submit();
});
/* -------------------------------------------------------- */
// * LIMPIEZA DE CAMPOS
function limpiarAtributos() {
    // ? MODAL DE CAMBIAR ENTRADA
    $('#entradaM').empty();
    $('#e_valid').empty();
    $('#e_valid').hide();
    $('#c_horaE').empty();
    // ? MODAL DE CAMBIAR SALIDA
    $('#salidaM').empty();
    $('#s_valid').empty();
    $('#s_valid').hide();
    $('#c_horaS').empty();
    // ? MODAL DE ASIGNACION A NUEVA MARCACIÓN
    $('#a_valid').empty();
    $('#a_valid').hide();
    $('#horarioM').empty();
    $('#a_hora').empty();
    // ? MODAL DE INSERTAR SALIDA
    $('#i_validS').empty();
    $('#i_validS').hide();
    if (horasS.config != undefined) {
        horasS.setDate("00:00:00");
    }
    // ? MODAL DE INSERTAR ENTRADA
    $('#i_validE').empty();
    $('#i_validE').hide();
    if (horasE.config != undefined) {
        horasE.setDate("00:00:00");
    }

    // ? MODAL DE NUEVA MARCACION
    $('#v_entrada').prop("checked", false);
    $('#v_salida').prop("checked", false);
    $('#nuevaEntrada').prop("disabled", false);
    $('#nuevaSalida').prop("disabled", false);
    if (newSalida.config != undefined) {
        newSalida.setDate("00:00:00");
    }
    if (newEntrada.config != undefined) {
        newEntrada.setDate("00:00:00");
    }
    $('#rowDatosM').hide();
    $('#r_horarioXE').empty();
}
