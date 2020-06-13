    $("#Reporte").DataTable({
              "searching": true,
              "responsive":true,
              "scrollX": true,
                retrieve: true,
                "ordering": false,
            language :
            {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ ",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     ">",
                    "sPrevious": "<"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                text:"<i><img src='admin/images/excel.svg' height='20'></i> Descargar",
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];},
                    sheetName: 'Exported data',
                    autoFilter: true
            }],
            paging: true,
            fixedHeader: {
                header: true,
                headerOffset: 45,
                },
        });