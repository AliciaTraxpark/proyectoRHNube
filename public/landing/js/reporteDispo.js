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
 console.log(fecha);
    var table =


    $("#tablaReport").DataTable({

        "destroy": true,
        "searching": true,
        /* "lengthChange": false,
         "scrollX": true, */
        "processing": true,

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

        ajax: {

            type: "post",
            url: "/reporteTablaMarca",
            data:{fecha},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            "dataSrc": ""
        },

        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }
        ],
        "order": [[1, 'asc']],
        columns: [
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
                data: "marcaMov_fecha",
                "render": function (data, type, row) {
                    if(row.marcaMov_fecha!=null || row.marcaMov_fecha!=null ){
                        return  '<span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>'+ moment(row.marcaMov_fecha).format("HH:mm:ss")+'</span>';
                   }
                   else{
                       return '<span class="badge badge-soft-warning"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span> ';
                   }
                }
            },
           /*  {
                data: "users",
                "render": function (data, type, row) {
                   return '<label style="font-style:oblique">Empleados de org.</label>'+row.organi_nempleados+'<br>'+
                   '<label style="font-style:oblique">Empleados regist.</label>'+row.nemple ;
                }
            },*/
            { data: "final" ,
                "render": function (data, type, row) {
                    tfinal=moment(row.final);
                    tInicio=moment(row.marcaMov_fecha);
                    if(row.marcaMov_fecha!=null || row.marcaMov_fecha!=null ){
                        if(tfinal>=tInicio){

                            return  '<span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>'+ moment(row.final).format("HH:mm:ss")+'</span>';
                           }
                           else{
                               return  '<span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span>';
                           }
                    } else{
                        return  '<span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>'+ moment(row.final).format("HH:mm:ss")+'</span>';
                    }

                }},
            { data: "final" ,
            "render": function (data, type, row) {
                tfinal=moment(row.final);
                    tInicio=moment(row.marcaMov_fecha);
                    if(tfinal>=tInicio){
                tiempo=tfinal-tInicio;
               /*  return moment(tiempo,"HH:mm:ss");  */

            /*    resta = moment.utc(tiempo*1).format('HH:mm:ss'); */
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
        ]
    });

    table.on('order.dt search.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
};


function cambiarF(){
    f1 = $("#fechaInput").val();
    f2=moment(f1).format("YYYY-MM-DD");
    console.log(f2);
    cargartabla(f2);
}
