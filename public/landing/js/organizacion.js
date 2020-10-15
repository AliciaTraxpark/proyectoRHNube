$(document).ready(function () {
    var table = $("#tablaOrgan").DataTable({
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
            url: "/listaoOrganiS",
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
                data: "organi_razonSocial",
                "render": function (data, type, row) {

                    return '<label class="font-weight-bold mb-1">'+row.organi_razonSocial+'</label>' + '<br><a class="badge badge-soft-primary mr-2">'+row.organi_ruc+'</a>';

                }
            },
            { data: "organi_tipo" },
            { data: "created_at",
            "render": function (data, type, row) {

                return moment(row.created_at).format('DD/MM/YYYY');

            } },
            {
                data: "users",
                "render": function (data, type, row) {
                   return row.nombres;
                }
            },
            {
                data: "users",
                "render": function (data, type, row) {
                   return '<label style="font-style:oblique">Empleados de org.</label>'+row.organi_nempleados+'<br>'+
                   '<label style="font-style:oblique">Empleados regist.</label>'+row.nemple ;
                }
            },
            { data: "users" ,
                "render": function (data, type, row) {
                   return 'Activo' ;
                }},
        ]


    });

    table.on('order.dt search.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
});