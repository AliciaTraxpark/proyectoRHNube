

// FECHA
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
  $(function () {
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
    $( "#fechaInput" ).change();

  });
function cargartabla (fecha) {
    $('#tableZoom').empty();
    $('#tableZoom').html(' <table id="tablaReport" class="table  nowrap" style="font-size: 12.8px;">'+
    '<thead id="datosHtm" style=" background: #edf0f1;color: #6c757d;"></thead>'+
    '<tbody id="tbodyD" style="">'+
    '</tbody></table>');
  /*   $('#datosHtm').empty();
    $('#tbodyD').empty(); */
   /*  if ($.fn.DataTable.isDataTable("#tablaReport")) {
        $('#tablaReport').DataTable().destroy();
    } */



    $.ajax({
        type: "POST",
        url: "/reporteTablaMarca",
        data: {
           fecha
        },
        async:false,
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

            thead='<tr>'+
            '<th>CC &nbsp;</th>'+
            '<th>DNI  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>'+
            '<th>Nombre &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>'+
            '<th>Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>'+
            '<th id="hEntrada">Hora de entrada</th>'+
            '<th id="hSalida">Hora de salida</th>'+
            '<th id="tSitio">Tiempo en sitio</th>'+
        '</tr>'

        $('#datosHtm').html(thead);
        if(data.length> 0  ){
            var cadena=[];
             $.each( JSON.parse(data), function( index, value ){
                nDatos1=value.entrada;
                nEntradas=nDatos1.split(',');
                cadena.push(nEntradas.length);
            })
            nclonar=Math.max(...cadena);
            $('<input type="hidden" id="nfila"></input>').insertAfter($('#tablaReport'));
            $('#nfila').val(nclonar);
             var e = $('#tSitio');

            for (var i = 0; i < nclonar-1; i++) {
                $("<th>Hora de entrada</th><th>Hora de salida</th><th>Tiempo en sitio</th>").insertAfter(e);
             }
        }



        },
        error: function () {}
    });
    $.ajax({
        type: "POST",
        url: "/reporteTablaMarca",
        data: {
           fecha
        },
        async:false,
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
            var tbodyTabla=[];

            dataA=JSON.parse(data)

            for (var i = 0; i < dataA.length; i++) {
        tbody='<tr>'+
        '<td>' + (i + 1) +'</td>'+
        '<td>'+dataA[i].emple_nDoc+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>'+
        '<td>'+dataA[i].perso_nombre+' '+dataA[i].perso_apPaterno+' '+dataA[i].perso_apMaterno+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';

        if(dataA[i].cargo_descripcion!=null){
        tbody+=
            '<td>'+ dataA[i].cargo_descripcion+
        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        }
        else{
            tbody+=
            '<td> ---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        }

        var nfi=$('#nfila').val();
         let cuerpoA='';
         for (var b = 0; b < nfi; b++) {
            //entrada
            if(dataA[i].entrada!=0 || dataA[i].entrada!=null ){
                vectorEntrada=dataA[i].entrada;
                nEntradas=vectorEntrada.split(',');


                cuerpoTDB='';
                cuerpoVacioTd='';
                if(nEntradas.length<2){
                    if(b==0){
                       if( nEntradas[0]!=0){
                        cuerpo = '<td><img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>'+moment(nEntradas[0]).format("HH:mm:ss") +'</td>';
                       } else{
                        cuerpo= '<td><span class="badge badge-soft-warning"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span></td> ';
                    }

                    } else{
                        cuerpo = '<td>----</td>';
                    }

                }else{
                for(var z = 0; z < nEntradas.length; z++){
                    if(nEntradas[b]!=0){
                        cuerpo=  '<td><img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>'+moment(nEntradas[b]).format("HH:mm:ss")+'</td>';
                    }
                    else{
                        cuerpo= '<td><span class="badge badge-soft-warning"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span></td> ';
                    }

                }
            }

           }
           else{
            cuerpo= '<td><span class="badge badge-soft-warning"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span></td> ';

           }
           //salida
           if(dataA[i].final!=0 || dataA[i].final!=null ){
            vectorSalida=dataA[i].final;
            nSalidas=vectorSalida.split(',');

             if(nSalidas.length<2){
                if(b==0){
                    if( nSalidas[0]!=0){
                    cuerpo+= '<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>'+moment(nSalidas[0]).format("HH:mm:ss") +'</td>';}
                    else{
                        cuerpo+= '<td><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span></td> ';
                    }
                } else{
                    cuerpo+= '<td>--</td>';
                }

            }else{

                if(nSalidas[b]!=0){
                    cuerpo+=  '<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>'+moment(nSalidas[b]).format("HH:mm:ss")+'</td>';
                }
                else{
                    cuerpo+= '<td><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span></td> ';
                }

           }
           }
            else{
           cuerpo+= '<td><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span></td> ';
           }

          //resta
           if(dataA[i].final!=0 && dataA[i].final!=null && dataA[i].entrada!=0 &&  dataA[i].entrada!=null){



            if(nSalidas.length<2){
                if(b==0){
                    tfinal=moment(nSalidas[0]);
                    tInicio=moment(nEntradas[0]);
                    if(tfinal>=tInicio){
                        tiempo=tfinal-tInicio;
                    var seconds = moment.duration(tiempo).seconds();
                    var minutes = moment.duration(tiempo).minutes();
                    var hours = Math.trunc(moment.duration(tiempo).asHours());
                    if(hours<10){
                        hours='0'+hours;
                    }
                    if(minutes<10){
                        minutes='0'+minutes;
                    }

                    if(seconds<10){
                        seconds='0'+seconds;
                    }

                    cuerpo+=  '<td><a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">'+hours+':'+minutes+':'+seconds+'</a></td>';
                    }
                    else{
                        cuerpo+= '<td><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>---</span></td> ';
                    }
                } else{
                    cuerpo+= '<td>--</td>';
                }

            }else{

                tfinalV=moment(nSalidas[b]);
                tInicioV=moment(nEntradas[b]);
                rrestaConsole=tfinalV[1]-tInicioV[1];
                console.log('resta'+rrestaConsole);
                if(tfinalV>=tInicioV && nEntradas[b]!=0 ){
                    tiempoV=tfinalV-tInicioV;
                var secondsV = moment.duration(tiempoV).seconds();
                var minutesV = moment.duration(tiempoV).minutes();
                var hoursV = Math.trunc(moment.duration(tiempoV).asHours());
                if(hoursV<10){
                    hoursV='0'+hoursV;
                }
                if(minutesV<10){
                    minutesV='0'+minutesV;
                }

                if(secondsV<10){
                    secondsV='0'+secondsV;
                }
                cuerpo+=  '<td><a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">'+hoursV+':'+minutesV+':'+secondsV+'</a></td>';
                }  else{
                    cuerpo+= '<td><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>---</span></td> ';
                }

           }

           } else{
            cuerpo+='<td>--</td>';
           }

            cuerpoA=cuerpo+cuerpoA;
         }



          tbody+=cuerpoA;
         tbody+='</tr>';

    tbodyTabla.push(tbody);
    }
    $('#tbodyD').html(tbodyTabla);

    table =
    $("#tablaReport").DataTable({

        "searching": false,
        "scrollX": true,

        "ordering": false,
        "autoWidth": true,

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

      /*   ajax: {

            type: "post",
            url: "/reporteTablaMarca", */
           /*  complete: function (data) {
                dataD=data['responseJSON'];
 */

               /*  $('#tablaReport>thead').append(
               ' <tr>'+
                    '<th></th>'+
                    '<th>DNI</th>'+
                    '<th>Nombre</th>'+
                    '<th>Cargo</th>'+
                    '<th>Hora de entrada</th>'+
                    '<th>Hora de salida</th>'+
                    '<th>Tiempo en sitio</th>'+
                '</tr>'
            ) */
               /*  $.each(dataD, function (index, value1) {
                    console.log(value1.emple_id);
                    $('#nombreEmpleado').find('option[value="' + value1.emple_id + '"]').remove();


                $('#nombreEmpleado').select2({});
                 }) */
           /*  }, */
         /*    data:{fecha},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            "dataSrc": ""
        }, */

      /*   "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }
        ],
        "order": [[1, 'asc']], */
       /*  columns: [
            { data: null },
            {
                data: "emple_nDoc",
            },
            { data: "emple_id" ,
           "render": function (data, type, row) {

                return row.perso_nombre+' '+row.perso_apPaterno+' '+row.perso_apMaterno+' ';

            }},
            { data: "cargo_descripcion" },

            {
                data: "entrada",
                "render": function (data, type, row) {
                    if(row.entrada!=0 ){
                        return  '<img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>'+ moment(row.entrada).format("HH:mm:ss")+'';
                   }
                   else{
                       return '<span class="badge badge-soft-warning"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span> ';
                   }
                }
            },

            { data: "final" ,
                "render": function (data, type, row) {
                    tfinal=moment(row.final);
                    tInicio=moment(row.entrada);
                    if(row.entrada!=0 ){
                        if(tfinal>=tInicio){

                            return  '<img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>'+ moment(row.final).format("HH:mm:ss")+'';
                           }
                           else{
                               return  '<span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span>';
                           }
                    } else{
                        return  '<img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>'+ moment(row.final).format("HH:mm:ss")+'';
                    }

                }},
            { data: "final" ,
            "render": function (data, type, row) {
                tfinal=moment(row.final);
                    tInicio=moment(row.entrada);
                    if(tfinal>=tInicio){
                tiempo=tfinal-tInicio;

            var seconds = moment.duration(tiempo).seconds();
            var minutes = moment.duration(tiempo).minutes();
            var hours = Math.trunc(moment.duration(tiempo).asHours());
                        if(hours<10){
                            hours='0'+hours;
                        }
                        if(minutes<10){
                            minutes='0'+minutes;
                        }

                        if(seconds<10){
                            seconds='0'+seconds;
                        }

               return '<a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">'+hours+':'+minutes+':'+seconds+'</a>'   ;
                }
                else{
                    return '---';
                }
            }},
            { data: "cargo_descripcion" },
            { data: "cargo_descripcion" },
            { data: "cargo_descripcion" },
            { data: "cargo_descripcion" },
            { data: "cargo_descripcion" }, { data: "cargo_descripcion" },
            {"render": function (data, type, row) {
               return  {data: "cargo_descripcion"};
            },}
        ], */});

        },
        complete : function(){




        },
        error: function () {}
    });



  /*   table.on('order.dt search.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw(); */

};


function cambiarF(){

    f1 = $("#fechaInput").val();
    f2=moment(f1).format("YYYY-MM-DD");
    if ($.fn.DataTable.isDataTable("#tablaReport")) {

        /* $('#tablaReport').DataTable().destroy(); */
    }
    cargartabla(f2);
}
