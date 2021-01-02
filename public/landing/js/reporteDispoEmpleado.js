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
    cambiarF();
});
// function cargartabla(fecha) {

//     idemp = $('#idempleado').val();
//     $('#tableZoom').empty();
//     $('#tableZoom').html(' <table id="tablaReport" class="table  nowrap" style="font-size: 12.8px;">' +
//         '<thead id="datosHtm" style=" background: #edf0f1;color: #6c757d;"></thead>' +
//         '<tbody id="tbodyD" style="">' +
//         '</tbody></table>');
//     $.ajax({
//         type: "GET",
//         url: "/reporteTablaMarca",
//         data: {
//             fecha, idemp
//         },
//         async: false,
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         statusCode: {
//             /*401: function () {
//                 location.reload();
//             },*/
//             419: function () {
//                 location.reload();
//             }
//         },
//         success: function (data) {
//             console.log(data);
//             //* CABEZERAS
//             thead = `<tr>
//                         <th>CC &nbsp;</th>
//                         <th>DNI  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
//                         <th>Nombre &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
//                         <th>Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
//                         <th id="hEntrada">Hora de entrada</th>
//                         <th id="hSalida">Hora de salida</th>
//                         <th id="tSitio" name="tiempoSitHi">Tiempo en sitio</th>
//                         <th >Tiempo total</th>
//                     </tr>`;
//             $('#datosHtm').html(thead);
//             if (data.length > 0) {
//                 var cadena = [];
//                 data.forEach((value) => {
//                     nDatos1 = value.entrada;
//                     nEntradas = nDatos1.split(',');
//                     cadena.push(nEntradas.length);
//                 });
//                 nclonar = Math.max(...cadena);
//                 $('<input type="hidden" id="nfila"></input>').insertAfter($('#tablaReport'));
//                 $('#nfila').val(nclonar);
//                 var e = $('#tSitio');
//                 for (var i = 0; i < nclonar - 1; i++) {
//                     $("<th>Hora de entrada</th><th>Hora de salida</th><th name='tiempoSitHi'>Tiempo en sitio</th>").insertAfter(e);
//                 }
//                 //* BODY
//                 var tbodyTabla = [];
//                 dataA = data;
//                 for (var i = 0; i < dataA.length; i++) {
//                     tbody = `<tr>
//                         <td>${(i + 1)}</td>
//                         <td>${dataA[i].emple_nDoc}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
//                         <td>${dataA[i].perso_nombre} ${dataA[i].perso_apPaterno} ${dataA[i].perso_apMaterno}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;

//                     if (dataA[i].cargo_descripcion != null) {
//                         tbody += `<td>${dataA[i].cargo_descripcion}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
//                     }
//                     else {
//                         tbody += `<td> ---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
//                     }

//                     var nfi = $('#nfila').val();
//                     let cuerpoA = '';
//                     for (var b = nfi - 1; b >= 0; b--) {
//                         //entrada
//                         if (dataA[i].entrada != 0 || dataA[i].entrada != null) {
//                             vectorEntrada = dataA[i].entrada;
//                             nEntradas = vectorEntrada.split(',');
//                             vectorIDSEntrada = dataA[i].idMarcacion;
//                             nIDSEnt = vectorIDSEntrada.split(',');
//                             cuerpoTDB = '';
//                             cuerpoVacioTd = '';
//                             if (nEntradas.length < 2) {
//                                 if (b == 0) {
//                                     if (nEntradas[0] != 0) {
//                                         cuerpo = '<td>' +
//                                             '<div class="dropdown" id="" ' +
//                                             '<a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' +
//                                             'style="cursor: pointer">' +
//                                             '<img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>' + moment(nEntradas[0]).format("HH:mm:ss") +
//                                             '</a>' +
//                                             '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">' +
//                                             '<div class="dropdown-item" onclick="cambiarEntrada(' + nIDSEnt[0] + ')">' +
//                                             '<img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12" />Cambiar a salida' +
//                                             '</div>' +
//                                             '</ul>' +
//                                             '</div>' +
//                                             '</td>';
//                                     } else {
//                                         cuerpo = '<td>' +
//                                             '<div class=" dropdown " >' +
//                                             '<button class="btn  dropdown-toggle" type="button"' +
//                                             'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' +
//                                             'style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">' +
//                                             '<span class="badge badge-soft-warning" data-toggle="tooltip" data-placement="left" title="Agregar hora"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span>' +
//                                             '</button>' +
//                                             ' <form class="dropdown-menu dropdown p-3"  id="UlE' + nIDSEnt[0] + '" style="padding-left: 8px!important;padding-right: 32px!important;padding-bottom: 4px!important;">' +
//                                             '<div class="form-group"  >' +
//                                             '<input type="text" id="horaEntradaN' + nIDSEnt[0] + '" class="form-control form-control-sm horasEntrada" >' +
//                                             ' &nbsp; <a onclick="insertarEntrada(' + nIDSEnt[0] + ') " style="cursor: pointer"><img src="admin/images/checkH.svg" height="15">  </a>  </div>' +
//                                             '</form>' +
//                                             '</div>' +
//                                             '</td> ';
//                                     }

//                                 } else {
//                                     cuerpo = '<td>----</td>';
//                                 }

//                             } else {
//                                 for (var z = 0; z < nEntradas.length; z++) {
//                                     if (nEntradas[b] === undefined) {
//                                         cuerpo = '<td>--</td> ';
//                                     }
//                                     else {
//                                         if (nEntradas[b] != 0) {
//                                             cuerpo = '<td>' +
//                                                 '<div class="dropdown" id="" ' +
//                                                 '<a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' +
//                                                 'style="cursor: pointer">' +
//                                                 '<img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>' + moment(nEntradas[b]).format("HH:mm:ss") +
//                                                 '</a>' +
//                                                 '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">' +
//                                                 '<div class="dropdown-item" onclick="cambiarEntrada(' + nIDSEnt[b] + ')">' +
//                                                 '<img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12" />Cambiar a salida' +
//                                                 '</div>' +
//                                                 '</ul>' +
//                                                 '</div>' +
//                                                 '</td>';
//                                         }
//                                         else {
//                                             cuerpo = '<td>' +
//                                                 '<div class=" dropdown " >' +
//                                                 '<button class="btn  dropdown-toggle" type="button"' +
//                                                 'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' +
//                                                 'style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">' +
//                                                 '<span class="badge badge-soft-warning" data-toggle="tooltip" data-placement="left" title="Agregar hora"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span>' +
//                                                 '</button>' +
//                                                 ' <form class="dropdown-menu dropdown p-3"  id="UlE' + nIDSEnt[b] + '" style="padding-left: 8px!important;padding-right: 32px!important;padding-bottom: 4px!important;">' +
//                                                 '<div class="form-group"  >' +
//                                                 '<input type="text" id="horaEntradaN' + nIDSEnt[b] + '" class="form-control form-control-sm horasEntrada" >' +
//                                                 ' &nbsp; <a onclick="insertarEntrada(' + nIDSEnt[b] + ') " style="cursor: pointer"><img src="admin/images/checkH.svg" height="15">  </a>  </div>' +
//                                                 '</form>' +
//                                                 '</div>' +
//                                                 '</td> ';

//                                         }
//                                     }


//                                 }
//                             }

//                         }
//                         else {
//                             cuerpo = '<td><span class="badge badge-soft-warning"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span></td> ';

//                         }
//                         //salida
//                         if (dataA[i].final != 0 || dataA[i].final != null) {
//                             vectorSalida = dataA[i].final;
//                             nSalidas = vectorSalida.split(',');
//                             //id's
//                             vectorIDSSalida = dataA[i].idMarcacion;
//                             nIDSSalid = vectorIDSSalida.split(',');

//                             if (nSalidas.length < 2) {
//                                 if (b == 0) {
//                                     if (nSalidas[0] != 0) {
//                                         cuerpo += '<td>' +
//                                             '<div class="dropdown" id="" ' +
//                                             '<a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' +
//                                             'style="cursor: pointer">' +
//                                             '<img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>' + moment(nSalidas[0]).format("HH:mm:ss") +
//                                             '</a>' +
//                                             '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">' +
//                                             '<div class="dropdown-item" onclick="cambiarSalida(' + nIDSSalid[0] + ')">' +
//                                             '<img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12" />Cambiar a entrada' +
//                                             '</div>' +
//                                             '</ul>' +
//                                             '</div>' +
//                                             '</td>';

//                                     }
//                                     else {

//                                         cuerpo += '<td>' +
//                                             '<div class=" dropdown " >' +
//                                             '<button class="btn  dropdown-toggle" type="button"' +
//                                             'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' +
//                                             'style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">' +
//                                             '<span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span>' +
//                                             '</button>' +
//                                             ' <form class="dropdown-menu dropdown p-3"  id="UlS' + nIDSSalid[0] + '" style="padding-left: 8px!important;padding-right: 32px!important;padding-bottom: 4px!important;">' +
//                                             '<div class="form-group"  >' +
//                                             '<input type="text" id="horaSalidaN' + nIDSSalid[0] + '" class="form-control form-control-sm horasSalida" >' +
//                                             ' &nbsp; <a onclick="insertarSalida(' + nIDSSalid[0] + ') " style="cursor: pointer"><img src="admin/images/checkH.svg" height="15">  </a>  </div>' +
//                                             '</form>' +
//                                             '</div>' +
//                                             '</td> ';
//                                     }
//                                 } else {
//                                     cuerpo += '<td>--</td>';
//                                 }

//                             } else {
//                                 if (nSalidas[b] === undefined) {
//                                     cuerpo += '<td>--</td> ';
//                                 } else {
//                                     if (nSalidas[b] != 0) {
//                                         cuerpo += '<td>' +
//                                             '<div class="dropdown" id="" ' +
//                                             '<a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' +
//                                             'style="cursor: pointer">' +
//                                             '<img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>' + moment(nSalidas[b]).format("HH:mm:ss") +
//                                             '</a>' +
//                                             '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">' +
//                                             '<div class="dropdown-item" onclick="cambiarSalida(' + nIDSSalid[b] + ')">' +
//                                             '<img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12" />Cambiar a entrada' +
//                                             '</div>' +
//                                             '</ul>' +
//                                             '</div>' +
//                                             '</td>';
//                                     }
//                                     else {
//                                         cuerpo += '<td>' +
//                                             '<div class=" dropdown " >' +
//                                             '<button class="btn  dropdown-toggle" type="button"' +
//                                             'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' +
//                                             'style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">' +
//                                             '<span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span>' +
//                                             '</button>' +
//                                             ' <form class="dropdown-menu dropdown p-3"  id="UlS' + nIDSSalid[b] + '" style="padding-left: 8px!important;padding-right: 32px!important;padding-bottom: 4px!important;">' +
//                                             '<div class="form-group"  >' +
//                                             '<input type="text" id="horaSalidaN' + nIDSSalid[b] + '" class="form-control form-control-sm horasSalida" >' +
//                                             ' &nbsp; <a onclick="insertarSalida(' + nIDSSalid[b] + ') " style="cursor: pointer"><img src="admin/images/checkH.svg" height="15">  </a>  </div>' +
//                                             '</form>' +
//                                             '</div>' +
//                                             '</td> ';
//                                     }
//                                 }


//                             }
//                         }
//                         else {
//                             cuerpo += '<td><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span></td> ';
//                         }

//                         //resta

//                         if (dataA[i].final != 0 && dataA[i].final != null && dataA[i].entrada != 0 && dataA[i].entrada != null) {



//                             if (nSalidas.length < 2) {
//                                 if (b == 0) {
//                                     tfinal = moment(nSalidas[0]);
//                                     tInicio = moment(nEntradas[0]);
//                                     if (tfinal >= tInicio) {
//                                         tiempo = tfinal - tInicio;
//                                         var seconds = moment.duration(tiempo).seconds();
//                                         var minutes = moment.duration(tiempo).minutes();
//                                         var hours = Math.trunc(moment.duration(tiempo).asHours());
//                                         if (hours < 10) {
//                                             hours = '0' + hours;
//                                         }
//                                         if (minutes < 10) {
//                                             minutes = '0' + minutes;
//                                         }

//                                         if (seconds < 10) {
//                                             seconds = '0' + seconds;
//                                         }

//                                         cuerpo += '<td name="tiempoSitHi" ><input type="hidden" value= "' + hours + ':' + minutes + ':' + seconds + '" name="tiempoSit' + dataA[i].emple_id + '[]" id="tiempoSit' + dataA[i].emple_id + '"><a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">' + hours + ':' + minutes + ':' + seconds + '</a></td>';
//                                         var idemp = dataA[i].emple_id;

//                                         $.when($('input[name="tiempoSit' + idemp + '[]"]') != null || $('input[name="tiempoSit' + idemp + '[]"]') != ' ').then(function (x) {
//                                             var tiempoto = [];
//                                             $('input[name="tiempoSit' + idemp + '[]"]').each(function () {
//                                                 tiempoto.push(($(this).val()));
//                                             });

//                                         });
//                                     }
//                                     else {
//                                         cuerpo += '<td name="tiempoSitHi"><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>---</span></td> ';
//                                     }
//                                 } else {
//                                     cuerpo += '<td name="tiempoSitHi">--</td>';
//                                 }

//                             } else {

//                                 tfinalV = moment(nSalidas[b]);
//                                 tInicioV = moment(nEntradas[b]);
//                                 rrestaConsole = tfinalV[1] - tInicioV[1];
//                                 /* console.log('resta'+rrestaConsole); */
//                                 if (tfinalV >= tInicioV && nEntradas[b] != 0) {
//                                     tiempoV = tfinalV - tInicioV;
//                                     var secondsV = moment.duration(tiempoV).seconds();
//                                     var minutesV = moment.duration(tiempoV).minutes();
//                                     var hoursV = Math.trunc(moment.duration(tiempoV).asHours());
//                                     if (hoursV < 10) {
//                                         hoursV = '0' + hoursV;
//                                     }
//                                     if (minutesV < 10) {
//                                         minutesV = '0' + minutesV;
//                                     }

//                                     if (secondsV < 10) {
//                                         secondsV = '0' + secondsV;
//                                     }
//                                     cuerpo += ' <td name="tiempoSitHi" ><input type="hidden" value= "' + hoursV + ':' + minutesV + ':' + secondsV + '" name="tiempoSit' + dataA[i].emple_id + '[]" id="tiempoSit' + dataA[i].emple_id + '"><a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">' + hoursV + ':' + minutesV + ':' + secondsV + '</a></td>';

//                                 } else {
//                                     cuerpo += '<td name="tiempoSitHi"><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>---</span></td> ';
//                                 }

//                             }

//                         } else {
//                             cuerpo += '<td name="tiempoSitHi">--</td>';
//                         }

//                         cuerpoA = cuerpo + cuerpoA;
//                     }
//                     /*  $('#pasandoV').val(dataA[i].emple_id);
//                      var tiempoto1=[];
//                      $.when($('input[name="tiempoSit'+dataA[i].emple_id+'[]"]')!=null || $('input[name="tiempoSit'+dataA[i].emple_id+'[]"]')!=' ').then(function( x ) {
//                          var valorrec=$('#pasandoV').val();
//                          console.log('valorando'+valorrec);
//                         $('input[name="tiempoSit'+valorrec+'[]"]').each(function () {
//                            tiempoto1.push(($(this).val()));

//                         });
//                         cuerpoC='<td>'+tiempoto1+
//                         '</td>';
//                         console.log('nopo'+cuerpoC);
//                       }); */


//                     cuerpoA += '<td id="TiempoTotal' + dataA[i].emple_id + '">' + dataA[i].emple_id + ' </td>';

//                     tbody += cuerpoA;


//                     /*        var tbodyAña1;
//                                      cambianteVal=dataA[i].emple_id;
//                                  $.when($('input[name="tiempoSit'+dataA[i].emple_id+'[]"]')!=null || $('input[name="tiempoSit'+dataA[i].emple_id+'[]"]')!=' ' ).then(function( x ) {
//                                      console.log('canb'+cambianteVal);
//                                      var tiempoto=[];

//                                      $('input[name="tiempoSit'+dataA[i].emple_id+'[]"]').each(function () {
//                                          tiempoto.push(($(this).val()));
//                                       });
//                                       tbodyAña= '<td>sdfghjhgfdsdfg'+tiempoto+'</td> ';
//                                       tbodyAña1=tbodyAña+tbodyAña1;
//                                      console.log('u'+tbodyAña);
//                                    });

//                                    tbody+=tbodyAña1; */
//                     tbody += '</tr>';

//                     tbodyTabla.push(tbody);
//                     /*   if(tbodyTabla!=){
//                           $('#TiempoTotal'+dataA[i].emple_id+'').html('derodillas');
//                       } */

//                 }

//                 $('#tbodyD').html(tbodyTabla);
//                 var valoresArray = [];
//                 $.each(dataA, function (i, item) {

//                     valoresArray.push(item.emple_id);

//                     var tiempoto = [];
//                     a = 0;
//                     $('input[name="tiempoSit' + item.emple_id + '[]"]').each(function () {

//                         tiempoto.push(($(this).val()));
//                         b = moment($(this).val());
//                         a = a + moment.duration(b._i).asSeconds();



//                         /* horaIndi= moment($(this).val()).format("HH:mm:ss"); */

//                     });
//                     var segundos = (Math.round(a % 0x3C)).toString();
//                     var horas = (Math.floor(a / 0xE10)).toString();
//                     var minutos = (Math.floor(a / 0x3C) % 0x3C).toString();
//                     if (horas < 10) {
//                         horas = '0' + horas;
//                     }
//                     if (minutos < 10) {
//                         minutos = '0' + minutos;
//                     }

//                     if (segundos < 10) {
//                         segundos = '0' + segundos;
//                     }

//                     $('#TiempoTotal' + item.emple_id + '').html('<a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">' + horas + ':' + minutos + ':' + segundos + '</a>');
//                 });

//                 /*  var valorrec=$('#pasandoV').val(); */
//                 if ($('#customSwitDetalles').is(':checked')) {
//                     $('[name="tiempoSitHi"]').show();
//                 }
//                 else {
//                     $('[name="tiempoSitHi"]').hide();
//                 }

//                 table =
//                     $("#tablaReport").DataTable({

//                         "searching": false,
//                         "scrollX": true,

//                         "ordering": false,
//                         "autoWidth": true,

//                         language: {
//                             sProcessing: "Procesando...",
//                             sLengthMenu: "Mostrar _MENU_ registros",
//                             sZeroRecords: "No se encontraron resultados",
//                             sEmptyTable: "Ningún dato disponible en esta tabla",
//                             sInfo: "Mostrando registros del _START_ al _END_ ",
//                             sInfoEmpty:
//                                 "Mostrando registros del 0 al 0 de un total de 0 registros",
//                             sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
//                             sInfoPostFix: "",
//                             sSearch: "Buscar:",
//                             sUrl: "",
//                             sInfoThousands: ",",
//                             sLoadingRecords: "Cargando...",
//                             oPaginate: {
//                                 sFirst: "Primero",
//                                 sLast: "Último",
//                                 sNext: ">",
//                                 sPrevious: "<",
//                             },
//                             oAria: {
//                                 sSortAscending:
//                                     ": Activar para ordenar la columna de manera ascendente",
//                                 sSortDescending:
//                                     ": Activar para ordenar la columna de manera descendente",
//                             },
//                             buttons: {
//                                 copy: "Copiar",
//                                 colvis: "Visibilidad",
//                             },
//                         },

//                         dom: 'Bfrtip',
//                         buttons: [{
//                             extend: 'excel',
//                             className: 'btn btn-sm mt-1',
//                             text: "<i><img src='admin/images/excel.svg' height='20'></i> Descargar",
//                             customize: function (xlsx) {
//                                 var sheet = xlsx.xl.worksheets['sheet1.xml'];
//                             },
//                             sheetName: 'Exported data',
//                             autoFilter: false
//                         }, {
//                             extend: "pdfHtml5",
//                             className: 'btn btn-sm mt-1',
//                             text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
//                             orientation: 'landscape',
//                             pageSize: 'LEGAL',
//                             title: 'REPORTE ASISTENCIA',
//                             customize: function (doc) {
//                                 doc['styles'] = {
//                                     userTable: {
//                                         margin: [0, 15, 0, 15]
//                                     },
//                                     title: {
//                                         color: '#163552',
//                                         fontSize: '20',
//                                         alignment: 'center'
//                                     },
//                                     tableHeader: {
//                                         bold: !0,
//                                         fontSize: 11,
//                                         color: '#FFFFFF',
//                                         fillColor: '#163552',
//                                         alignment: 'center'
//                                     }
//                                 };
//                             }
//                         }],
//                         paging: true

//                     });

//             }

//         },
//         error: function () { }
//     });

//     $('.horasEntrada').flatpickr({
//         enableTime: true,
//         noCalendar: true,
//         dateFormat: "H:i:s",
//         defaultDate: "00:00:00",

//         time_24hr: true,
//         enableSeconds: true,
//         /*  inline:true, */
//         static: true
//     });

//     $('.horasSalida').flatpickr({
//         enableTime: true,
//         noCalendar: true,
//         dateFormat: "H:i:s",
//         defaultDate: "00:00:00",

//         time_24hr: true,
//         enableSeconds: true,
//         /*  inline:true, */
//         static: true
//     });


// }

function cargartabla(fecha) {
    var idemp = $('#idempleado').val();
    $.ajax({
        type: "GET",
        url: "/reporteTablaMarca",
        data: {
            fecha, idemp
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
                                    <th>DNI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>Nombre&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>`;
                for (let j = 0; j < cantidadColumnasHoras; j++) {
                    theadTabla += `<th>Hora de entrada</th>
                                    <th>Hora de salida</th>
                                    <th id="tSitio" name="tiempoSitHi">Tiempo en sitio</th>`;
                }
                theadTabla += `<th>Tiempo total</th></tr>`;
                //* DIBUJAMOS CABEZERA
                $('#theadD').html(theadTabla);
                // ! *********** BODY DE TABLA**********
                $('#tbodyD').empty();
                var tbody = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {
                    tbody += `<tr>
                    <td>${(index + 1)}&nbsp;</td>
                    <td>${data[index].emple_nDoc}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>${data[index].perso_nombre} ${data[index].perso_apPaterno} ${data[index].perso_apMaterno}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    if (data[index].cargo_descripcion != null) {
                        tbody += `<td>${data[index].cargo_descripcion}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    } else {
                        tbody += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    }
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
                                                                </td>`;
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
                                                        </td>`;
                                    }
                                }
                            } else {
                                if (marcacionData.salida != 0) {
                                    if (h == moment(marcacionData.salida).format("HH")) {
                                        //* COLUMNA DE ENTRADA

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
                                                        </td>`;
                                    }
                                }
                            }
                        }
                    }
                    for (let m = data[index].marcaciones.length; m < cantidadColumnasHoras; m++) {
                        tbodyEntradaySalida += `<td>---</td><td>---</td><td name="tiempoSitHi">---</td>`;
                    }
                    tbody += tbodyEntradaySalida;
                    tbody += `<td id="TiempoTotal${data[index].emple_id}">
                                <a class="badge badge-soft-primary mr-2">
                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                    ${sumaTiempos.format("HH:mm:ss")}
                                </a>
                            </td></tr>`;
                }
                $('#tbodyD').html(tbody);

                table = $("#tablaReport").DataTable({
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
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm mt-1',
                        text: "<i><img src='admin/images/excel.svg' height='20'></i> Descargar",
                        customize: function (xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        },
                        sheetName: 'Exported data',
                        autoFilter: false
                    }, {
                        extend: "pdfHtml5",
                        className: 'btn btn-sm mt-1',
                        text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: 'REPORTE ASISTENCIA',
                        customize: function (doc) {
                            doc['styles'] = {
                                userTable: {
                                    margin: [0, 15, 0, 15]
                                },
                                title: {
                                    color: '#163552',
                                    fontSize: '20',
                                    alignment: 'center'
                                },
                                tableHeader: {
                                    bold: !0,
                                    fontSize: 11,
                                    color: '#FFFFFF',
                                    fillColor: '#163552',
                                    alignment: 'center'
                                }
                            };
                        }
                    }],
                    paging: true

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
                $('#tbodyD').empty();
            }
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

    f1 = $("#fechaInput").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $('#pasandoV').val(f2);
    if ($.fn.DataTable.isDataTable("#tablaReport")) {

        /* $('#tablaReport').DataTable().destroy(); */
    }

    cargartabla(f2);

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
function cambiarEntrada(idMarca) {
    $('#tableZoom').hide();
    $('#espera').show();
    $.ajax({
        type: "post",
        url: "/cambiarEntrada",
        data: {
            idMarca
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

            $('#btnRecargaTabla').click();
            $('#espera').hide();
            $('#tableZoom').show();

        },
        error: function () {
            alert("Hay un error");
        },
    });
}
function cambiarSalida(idMarca) {
    $('#tableZoom').hide();
    $('#espera').show();

    $.ajax({
        type: "post",
        url: "/cambiarSalida",
        data: {
            idMarca
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

            $('#btnRecargaTabla').click();
            $('#espera').hide();
            $('#tableZoom').show();
        },

        error: function () {
            alert("Hay un error");
        },
    });
}

function insertarEntrada(idMarca) {
    let hora = $('#horaEntradaN' + idMarca + '').val();
    let fecha = $('#pasandoV').val() + ' ' + hora;

    $.ajax({
        type: "post",
        url: "/registrarNEntrada",
        data: {
            idMarca, hora, fecha
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
            if (data == 1) {
                $('#tableZoom').hide();
                $('#espera').show();
                $('#btnRecargaTabla').click();
                $('#espera').hide();
                $('#tableZoom').show();
            } else {

                $.notifyClose();
                $.notify({
                    message: '\nHora de entrada debe ser menor que hora de salida.',
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
            }


        },
        error: function () {
            alert("Hay un error");
        },
    });
}
/////////////////////
function insertarSalida(idMarca) {
    let hora = $('#horaSalidaN' + idMarca + '').val();
    let fecha = $('#pasandoV').val() + ' ' + hora;

    $.ajax({
        type: "post",
        url: "/registrarNSalida",
        data: {
            idMarca, hora, fecha
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
            if (data == 1) {
                $('#tableZoom').hide();
                $('#espera').show();
                $('#btnRecargaTabla').click();
                $('#espera').hide();
                $('#tableZoom').show();
            } else {

                $.notifyClose();
                $.notify({
                    message: '\nHora de salida debe ser mayor a que hora de entrada.',
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
            }


        },
        error: function () {
            alert("Hay un error");
        },
    });
}