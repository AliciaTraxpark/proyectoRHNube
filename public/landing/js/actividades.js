$(function () {
    $("#actividades").DataTable({
        scrollX: true,
        responsive: true,
        retrieve: true,
        "searching": false,
        "lengthChange": false,
        scrollCollapse: false,
        "pageLength": 30,
        "bAutoWidth": true,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ ",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": ">",
                "sPrevious": "<"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad"
            }
        }
    });
});

function actividadesOrganizacion() {
    $('#actividOrga').empty();
    $.ajax({
        url: "/actividadOrg",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var tr = "";
            for (let index = 0; index < data.length; index++) {
                tr += "<tr class=\"text-center\"><td>" + (index + 1) + "</td>";
                tr += "<td>" + data[index].Activi_Nombre + "</td>";
                tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                <input type=\"checkbox\" class=\"custom-control-input\"\
                    id=\"customSwitch4\">\
                <label class=\"custom-control-label\" for=\"customSwitch4\"\
                    style=\"font-weight: bold\"></label>\
                </div></td>";
                tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                <input type=\"checkbox\" class=\"custom-control-input\"\
                    id=\"customSwitch5\">\
                <label class=\"custom-control-label\" for=\"customSwitch5\"\
                    style=\"font-weight: bold\"></label>\
                </div></td>";
                tr += "</tr>"
            }
            $('#actividOrga').html(tr);
        },
        error: function () {

        }
    });
}
actividadesOrganizacion();