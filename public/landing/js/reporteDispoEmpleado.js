//* FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "D, j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
    onChange:function(selectedDates){
        var _this=this;
        var dateArr=selectedDates.map(function(date){return _this.formatDate(date,'Y-m-d');});
        $('#ID_START').val(dateArr[0]);
        $('#ID_END').val(dateArr[1]);
    },
});
$(function () {
    $('#idempleado').select2({
        placeholder: 'Seleccionar',
        language: {
            inputTooShort: function (e) {
                return "Escribir nombre o apellido";
            },
            loadingMore: function () { return "Cargando más resultados…" },
            noResults: function () { return "No se encontraron resultados" }
        },
        minimumInputLength: 2
    });
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
    $("#fechaInput").change();
    $('#ID_START').val(f);
    $('#ID_END').val(f);
   /*  cambiarF(); */
});


function cargartabla(fecha1,fecha2) {

    var idemp = $('#idempleado').val();
    if(idemp==0){
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
        url: "/reporteTablaEmp",
        data: {
            fecha1,fecha2, idemp
        },
        async: false,
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
                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                $('#btnsDescarga').show();
                // ! *********** CABEZERA DE TABLA**********
                $('#theadD').empty();
                //* CANTIDAD MININO VALOR DE COLUMNAS PARA HORAS
                var cantidadColumnasHoras = 0;
                for (let i = 0; i < data.length; i++) {
                    //* OBTENER CANTIDAD TOTAL DE COLUMNAS
                    if (cantidadColumnasHoras < data[i].marcaciones.length) {
                        cantidadColumnasHoras = data[i].marcaciones.length;
                    }
                }
                //* ARMAR CABEZERA
                var theadTabla = `<tr>
                                    <th>CC&nbsp;</th>
                                    <th>Fecha&nbsp;</th>
                                   `;
                for (let j = 0; j < cantidadColumnasHoras; j++) {
                    theadTabla += `<th style="border-left-color: #c8d4de!important;
                    border-left: 2px solid;">Horario</th>
                                     <th>Hora de entrada</th>
                                    <th>Hora de salida</th>
                                    <th id="tSitio" name="tiempoSitHi">Tiempo en sitio</th><th  name="tiempoSitHi">Tardanza</th>
                                    <th  name="tiempoSitHi">Faltas</th><th  name="tiempoSitHi">Incidencias</th>`;
                }
                theadTabla += `<th>Tiempo total</th><th >Tardanza T.</th>
                <th >Faltas T.</th>
                <th >Incidencias T.</th>  </tr>`;
                //* DIBUJAMOS CABEZERA
                $('#theadD').html(theadTabla);
                // ! *********** BODY DE TABLA**********
                $('#tbodyD').empty();
                var tbody = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {
                    tbody += `<tr>
                    <td>${(index + 1)}&nbsp;</td>
                    <td>${moment(data[index].entradaModif).format('DD/MM/YYYY') }&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;

                    //* ARMAR Y ORDENAR MARCACIONES
                    var tbodyEntradaySalida = "";
                    var sumaTiempos = moment("00:00:00", "HH:mm:ss");
                    //: HORA
                    for (let h = 0; h < 24; h++) {
                        for (let j = 0; j < data[index].marcaciones.length; j++) {
                            var marcacionData = data[index].marcaciones[j];
                            if (marcacionData.entrada != 0) {
                                if (h == moment(marcacionData.entrada).format("HH")) {
                                    var permisoModificarCS=$('#modifReporte').val();
                                    if (data[index].horario != 0) {
                                        tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                                        border-left: 2px solid;">${data[index].horario}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                                        border-left: 2px solid;">---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>>`;
                                    }
                                    if(permisoModificarCS==1){
                                        tbodyEntradaySalida += `<td><div class="dropdown" id="">
                                                                <a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                                style="cursor: pointer">
                                                                    <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                                    ${moment(marcacionData.entrada).format("HH:mm:ss")}
                                                                </a>
                                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                    <div class="dropdown-item" onclick="cambiarEntrada(${marcacionData.idMarcacion})">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12" />
                                                                        Cambiar a salida
                                                                    </div>
                                                                </ul>
                                                            </div></td>`;
                                    }
                                    else{
                                        tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>${moment(marcacionData.entrada).format("HH:mm:ss")}</td>`;
                                    }

                                    if (marcacionData.salida != 0) {
                                        var permisoModificarCE1=$('#modifReporte').val();
                                        if(permisoModificarCE1==1){
                                            tbodyEntradaySalida += `<td><div class="dropdown" id="">
                                                                <a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false
                                                                style="cursor: pointer">
                                                                <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                    ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                </a>
                                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                    <div class="dropdown-item" onclick="cambiarSalida(${marcacionData.idMarcacion})">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12" />
                                                                        Cambiar a entrada
                                                                    </div>
                                                                </ul>
                                                            </div></td>`;
                                        } else{
                                            tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> ${moment(marcacionData.salida).format("HH:mm:ss")}</td>`;

                                        }

                                        var horaFinal = moment(marcacionData.salida);
                                        var horaInicial = moment(marcacionData.entrada);
                                        if (horaFinal.isSameOrAfter(horaInicial)) {
                                            var tiempoRestante = horaFinal - horaInicial;
                                            var segundosTiempo = moment.duration(tiempoRestante).seconds();
                                            var minutosTiempo = moment.duration(tiempoRestante).minutes();
                                            var horasTiempo = Math.trunc(moment.duration(tiempoRestante).asHours());
                                            if (horasTiempo < 10) {
                                                horasTiempo = '0' + horasTiempo;
                                            }
                                            if (minutosTiempo < 10) {
                                                minutosTiempo = '0' + minutosTiempo;
                                            }
                                            if (segundosTiempo < 10) {
                                                segundosTiempo = '0' + segundosTiempo;
                                            }
                                            tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                                    <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                                                                    <a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                        ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                    </a>
                                                                </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                                            sumaTiempos = moment(sumaTiempos).add(segundosTiempo, 'seconds');
                                            sumaTiempos = moment(sumaTiempos).add(minutosTiempo, 'minutes');
                                            sumaTiempos = moment(sumaTiempos).add(horasTiempo, 'hours');
                                        }
                                    } else {
                                        var permisoModificarS=$('#modifReporte').val();
                                        if(permisoModificarS==1){
                                            tbodyEntradaySalida += `<td><div class="dropdown" id="">
                                                                <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false"
                                                                    style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                                    <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                        No tiene salida
                                                                    </span>
                                                                </button>
                                                                <form class="dropdown-menu dropdown p-3" id="UlS${marcacionData.idMarcacion}" style="padding-left: 8px!important;padding-right: 32px!important;padding-bottom: 4px!important;">
                                                                    <div class="form-group">
                                                                        <input type="text" id="horaSalidaN${marcacionData.idMarcacion}" class="form-control form-control-sm horasSalida" >
                                                                        &nbsp; <a onclick="insertarSalida(${marcacionData.idMarcacion}) " style="cursor: pointer"><img src="admin/images/checkH.svg" height="15"></a>
                                                                    </div>
                                                                </form>
                                                            </div></td>`;
                                        }
                                        else{
                                            tbodyEntradaySalida +=`<td><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span></td>`;
                                        }

                                        tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                            <span class="badge badge-soft-secondary">
                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                --:--:--
                                                            </span>
                                                        </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                                    }
                                }
                            } else {
                                if (marcacionData.salida != 0) {
                                    if (h == moment(marcacionData.salida).format("HH")) {
                                        //* COLUMNA DE ENTRADA
                                        if (data[index].horario != 0) {
                                            tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                                            border-left: 2px solid;">${data[index].horario}</td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                                            border-left: 2px solid;">---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>`;
                                        }
                                        var permisoModificarE=$('#modifReporte').val();
                                        if(permisoModificarE==1){
                                            tbodyEntradaySalida += `<td>
                                                                <div class=" dropdown">
                                                                    <button class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                                        style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                                        <span class="badge badge-soft-warning" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                                            No tiene entrada
                                                                        </span>
                                                                    </button>
                                                                    <form class="dropdown-menu dropdown p-3"  id="UlE${marcacionData.idMarcacion}" style="padding-left: 8px!important;padding-right: 32px!important;padding-bottom: 4px!important;">
                                                                        <div class="form-group">
                                                                            <input type="text" id="horaEntradaN${marcacionData.idMarcacion}" class="form-control form-control-sm horasEntrada">
                                                                            &nbsp;
                                                                            <a onclick="insertarEntrada(${marcacionData.idMarcacion})" style="cursor: pointer">
                                                                                <img src="admin/images/checkH.svg" height="15">
                                                                            </a>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </td>`;
                                        }
                                        else{
                                            tbodyEntradaySalida += `<td><span class="badge badge-soft-warning"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span></td>`;
                                        }

                                        //* COLUMNA DE SALIDA
                                        var permisoModificarCE2=$('#modifReporte').val();
                                        if(permisoModificarCE2==1){
                                            tbodyEntradaySalida += `<td>
                                                                <div class="dropdown" id="">
                                                                    <a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                                        style="cursor: pointer">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                        ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                    </a>
                                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                        <div class="dropdown-item" onclick="cambiarSalida(${marcacionData.idMarcacion})">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12" />
                                                                            Cambiar a entrada
                                                                        </div>
                                                                    </ul>
                                                                </div>
                                                            </td>`;
                                        } else{
                                            tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> ${moment(marcacionData.salida).format("HH:mm:ss")}</td>`;
                                        }

                                        tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                            <span class="badge badge-soft-secondary">
                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                --:--:--
                                                            </span>
                                                        </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                                    }
                                }
                            }
                        }
                    }
                    for (let m = data[index].marcaciones.length; m < cantidadColumnasHoras; m++) {
                        tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                        border-left: 2px solid;">---</td><td>---</td><td>---</td><td name="tiempoSitHi">---</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                    }
                    tbody += tbodyEntradaySalida;
                    tbody += `<td id="TiempoTotal${data[index].emple_id}">
                                <a class="badge badge-soft-primary mr-2">
                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                    ${sumaTiempos.format("HH:mm:ss")}
                                </a>
                            </td> <td >--</td>
                            <td >--</td>
                            <td >--</td> </tr>`;
                }
                $('#tbodyD').html(tbody);

                table = $("#tablaReport").DataTable({
                    "bLengthChange" : false,
                    "searching": false,
                    "scrollX": true,
                    "ordering": false,
                    "autoWidth": false,
                    fixedHeader: true,
                    language: {
                        sProcessing: "Procesando...",
                        sLengthMenu: "Mostrar _MENU_ registros",
                        sZeroRecords: "No se encontraron resultados",
                        sEmptyTable: "Ningún dato disponible en esta tabla",
                        sInfo: "Mostrando registros del _START_ al _END_ ",
                        sInfoEmpty:
                            "Mostrando registros del 0 al 0 de un total de 0 registros",
                        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
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
                        setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
                    },


                });
                $(window).on('resize', function () {
                    $("#tablaReport").css('width', '100%');
                    table.draw(true);
                });
                if ($('#customSwitDetalles').is(':checked')) {
                    $('[name="tiempoSitHi"]').show();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
                else {
                    $('[name="tiempoSitHi"]').hide();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
            } else {
                $('#btnsDescarga').hide();
                $('#tbodyD').empty();
                $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
            }

            /* CREANDO TABLAS PARA IMPORTAR */
            if (data.length != 0) {

                // ! *********** CABEZERA DE TABLA**********
                $('#theadDI').empty();
                //* CANTIDAD MININO VALOR DE COLUMNAS PARA HORAS
                var cantidadColumnasHorasI = 0;
                for (let i = 0; i < data.length; i++) {
                    //* OBTENER CANTIDAD TOTAL DE COLUMNAS
                    if (cantidadColumnasHorasI < data[i].marcaciones.length) {
                        cantidadColumnasHorasI = data[i].marcaciones.length;
                    }
                }
                //* ARMAR CABEZERA
                var theadTablaI = `<tr class="">
                                    <th class="">CC&nbsp;</th>
                                    <th class="">Fecha&nbsp;</th>
                                   `;
                for (let j = 0; j < cantidadColumnasHorasI; j++) {
                    theadTablaI += `<th class="" >horario</th>
                                     <th class="">Hora de entrada</th>
                                    <th class="" >Hora de salida</th>
                                    <th  class="" id="" name="tiempoSitHi">Tiempo en sitio</th><th  name="tiempoSitHi">Tardanza</th>
                                    <th  name="tiempoSitHi">Faltas</th><th  name="tiempoSitHi">Incidencias</th>`;
                }
                theadTablaI += `<th class="">Tiempo total</th><th >Tardanza T.</th>
                <th >Faltas T.</th>
                <th >Incidencias T.</th> </tr>`;


                 var inicioR=moment($('#ID_START').val()).format('DD/MM/YYYY');
                 var finR=moment($('#ID_END').val()).format('DD/MM/YYYY');
                $('#RangoFechas').html(' De '+inicioR+' '+' a '+' '+finR)



                //* DIBUJAMOS CABEZERA
                $('#theadDI').html(theadTablaI);
                // ! *********** BODY DE TABLA**********
                $('#tbodyIDI').empty();
                var tbodyI = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {
                    $('#ponerDNI').html(data[index].emple_nDoc);
                    $('#ponerApe').html(data[index].perso_apPaterno+' '+data[index].perso_apMaterno+' '+data[index].perso_nombre);
                    if (data[index].area_descripcion != null) {
                         $('#ponerArea').html(data[index].area_descripcion);
                    }
                    else{
                        $('#ponerArea').html('--');
                    }

                    if (data[index].cargo_descripcion != null) {
                        $('#ponerCarg').html(data[index].cargo_descripcion);
                   }
                   else{
                    $('#ponerCarg').html('--');
                   }
                    tbodyI += `<tr>
                    <td>${(index + 1)}&nbsp;</td>
                    <td>${moment(data[index].entradaModif).format('DD/MM/YYYY') }&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;

                    //* ARMAR Y ORDENAR MARCACIONES
                    var tbodyIEntradaySalida = "";
                    var sumaTiemposI = moment("00:00:00", "HH:mm:ss");
                    //: HORA
                    for (let h = 0; h < 24; h++) {
                        for (let j = 0; j < data[index].marcaciones.length; j++) {
                            var marcacionDataI = data[index].marcaciones[j];
                            if (marcacionDataI.entrada != 0) {
                                if (h == moment(marcacionDataI.entrada).format("HH")) {
                                    var permisoModificarCS=$('#modifReporte').val();
                                    if (marcacionDataI.horario != 0) {
                                        tbodyIEntradaySalida += `<td  >${marcacionDataI.horario}</td>`;
                                    } else {
                                        tbodyIEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }
                                    if(permisoModificarCS==1){
                                        tbodyIEntradaySalida += `<td >
                                                                    ${moment(marcacionDataI.entrada).format("HH:mm:ss")}
                                                               </td>`;
                                    }
                                    else{
                                        tbodyIEntradaySalida += `<td >${moment(marcacionDataI.entrada).format("HH:mm:ss")}</td>`;
                                    }

                                    if (marcacionDataI.salida != 0) {
                                        var permisoModificarCE1=$('#modifReporte').val();
                                        if(permisoModificarCE1==1){
                                            tbodyIEntradaySalida += `<td >
                                                                    ${moment(marcacionDataI.salida).format("HH:mm:ss")}
                                                                </td>`;
                                        } else{
                                            tbodyIEntradaySalida += `<td> ${moment(marcacionDataI.salida).format("HH:mm:ss")}</td>`;

                                        }

                                        var horaFinal = moment(marcacionDataI.salida);
                                        var horaInicial = moment(marcacionDataI.entrada);
                                        if (horaFinal.isSameOrAfter(horaInicial)) {
                                            var tiempoRestante = horaFinal - horaInicial;
                                            var segundosTiempo = moment.duration(tiempoRestante).seconds();
                                            var minutosTiempo = moment.duration(tiempoRestante).minutes();
                                            var horasTiempo = Math.trunc(moment.duration(tiempoRestante).asHours());
                                            if (horasTiempo < 10) {
                                                horasTiempo = '0' + horasTiempo;
                                            }
                                            if (minutosTiempo < 10) {
                                                minutosTiempo = '0' + minutosTiempo;
                                            }
                                            if (segundosTiempo < 10) {
                                                segundosTiempo = '0' + segundosTiempo;
                                            }
                                            tbodyIEntradaySalida += `<td  name="tiempoSitHi">

                                                                        ${horasTiempo}:${minutosTiempo}:${segundosTiempo}

                                                                </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                                            sumaTiemposI = moment(sumaTiemposI).add(segundosTiempo, 'seconds');
                                            sumaTiemposI = moment(sumaTiemposI).add(minutosTiempo, 'minutes');
                                            sumaTiemposI = moment(sumaTiemposI).add(horasTiempo, 'hours');
                                        }
                                    } else {
                                        var permisoModificarS=$('#modifReporte').val();
                                        if(permisoModificarS==1){
                                            tbodyIEntradaySalida += `<td >
                                                                        No tiene salida
                                                                  </td>`;
                                        }
                                        else{
                                            tbodyIEntradaySalida +=`<td >No tiene salida</td>`;
                                        }

                                        tbodyIEntradaySalida += `<td  name="tiempoSitHi">

                                                                --:--:--

                                                        </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                                    }
                                }
                            } else {
                                if (marcacionDataI.salida != 0) {
                                    if (h == moment(marcacionDataI.salida).format("HH")) {
                                        //* COLUMNA DE ENTRADA
                                        if (marcacionDataI.horario != 0) {
                                            tbodyIEntradaySalida += `<td  >${marcacionDataI.horario}</td>`;
                                        } else {
                                            tbodyIEntradaySalida += `<td  >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }
                                        var permisoModificarE=$('#modifReporte').val();
                                        if(permisoModificarE==1){
                                            tbodyIEntradaySalida += `<td  >

                                                                            No tiene entrada


                                                            </td>`;
                                        }
                                        else{
                                            tbodyIEntradaySalida += `<td  >No tiene entrada</td>`;
                                        }

                                        //* COLUMNA DE SALIDA
                                        var permisoModificarCE2=$('#modifReporte').val();
                                        if(permisoModificarCE2==1){
                                            tbodyIEntradaySalida += `<td  >

                                                                        ${moment(marcacionDataI.salida).format("HH:mm:ss")}

                                                            </td>`;
                                        } else{
                                            tbodyIEntradaySalida += `<td  > ${moment(marcacionDataI.salida).format("HH:mm:ss")}</td>`;
                                        }

                                        tbodyIEntradaySalida += `<td   name="tiempoSitHi">

                                                                --:--:--

                                                        </td ><td class="tableHi" name="tiempoSitHi">---</td><td class="tableHi" name="tiempoSitHi">---</td><td class="tableHi" name="tiempoSitHi">---</td>`;
                                    }
                                }
                            }
                        }
                    }
                    for (let m = data[index].marcaciones.length; m < cantidadColumnasHorasI; m++) {
                        tbodyIEntradaySalida += `<td  >---</td><td  >---</td><td  >---</td><td   name="tiempoSitHi">---</td> <td class="tableHi" name="tiempoSitHi">---</td><td class="tableHi" name="tiempoSitHi">---</td><td class="tableHi" name="tiempoSitHi">---</td>`;
                    }
                    tbodyI += tbodyIEntradaySalida;
                    tbodyI += `<td id="TiempoTotal${data[index].emple_id}">

                                    ${sumaTiemposI.format("HH:mm:ss")}

                            </td> <td >--</td>
                            <td >--</td>
                            <td >--</td></tr>`;
                }
                $('#tbodyIDI').html(tbodyI);


                if ($('#customSwitDetalles').is(':checked')) {
                    $('[name="tiempoSitHi"]').show();

                }
                else {
                    $('[name="tiempoSitHi"]').hide();

                }
            } else {
                $('#tbodyIDI').empty();
                $('#tbodyIDI').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
            }
            /*  */

        },
        error: function () { }
    });
    $('.horasEntrada').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true
    });

    $('.horasSalida').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true
    });
}

function cambiarF() {

    f1 = $("#ID_START").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $('#pasandoV').val(f2);
    f3 = $("#ID_END").val();
    if ($.fn.DataTable.isDataTable("#tablaReport")) {

        /* $('#tablaReport').DataTable().destroy(); */
    }
    console.log(f2);
    cargartabla(f2,f3);

}
function cambiartabla() {
    if ($('#customSwitDetalles').is(':checked')) {
        $('[name="tiempoSitHi"]').show();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    }
    else {
        $('[name="tiempoSitHi"]').hide();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    }
}



/////////GENERAR EXCEL////////////
function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
  }
  function doexcel(){
      var wb = XLSX.utils.table_to_book(document.getElementById("tableZoomI"),{sheet:"Sheet 1"})	//my html table

       wb["Sheets"]["Sheet 1"]["!cols"] = [{ wpx : 149 },{ wpx : 130 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },
        { wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },
        { wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },
        { wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },{ wpx : 100 },];

     console.log(wb);

      var wbout = XLSX.write(wb, {bookType:'xlsx',  bookSST:true, type: 'binary'});
      saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'Asistencia.xlsx');
  }
function generatePDF() {

var doc = new jsPDF('l', 'pt', 'legal');
    var htmlstring = '';
    var tempVarToCheckPageHeight = 0;
    var pageHeight = 0;
    pageHeight = doc.internal.pageSize.height;
    specialElementHandlers = {
        // element with id of "bypass" - jQuery style selector
        '#bypassme': function(element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    };
  
    var y = 20;
    doc.setLineWidth(2);

    doc.autoTable({
        html: '#Encabezado',
        startY: 40,
        theme:'plain'

    })
    doc.autoTable({
        html: '#tablaReportI',
        startY: 250

    })
    doc.save('Asistencia.pdf');

  }

