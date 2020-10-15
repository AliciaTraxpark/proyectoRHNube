function tablaActividades() {
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
}
function actividadesOrganizacion() {
    $('#actividOrga').empty();
    $.ajax({
        url: "/actividadOrg",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            var tr = "";
            for (let index = 0; index < data.length; index++) {
                tr += "<tr class=\"text-center\"><td>" + (index + 1) + "</td>";
                tr += "<td>" + data[index].Activi_Nombre + "</td>";
                if (data[index].eliminacion == 0) {
                    if (data[index].controlRemoto == 1) {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                        <input type=\"checkbox\" class=\"custom-control-input\"\
                            id=\"switchActvCR"+ data[index].Activi_id + "\" checked disabled>\
                        <label class=\"custom-control-label\" for=\"switchActvCR"+ data[index].Activi_id + "\"\
                            style=\"font-weight: bold\"></label>\
                        </div></td>";
                    } else {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                        <input type=\"checkbox\" class=\"custom-control-input\"\
                            id=\"switchActvCR"+ data[index].Activi_id + "\" disabled>\
                        <label class=\"custom-control-label\" for=\"switchActvCR"+ data[index].Activi_id + "\"\
                            style=\"font-weight: bold\"></label>\
                        </div></td>";
                    }
                    if (data[index].asistenciaPuerta == 1) {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                            <input type=\"checkbox\" class=\"custom-control-input\"\
                                id=\"switchActvAP"+ data[index].Activi_id + "\" checked disabled>\
                            <label class=\"custom-control-label\" for=\"switchActvAP"+ data[index].Activi_id + "\"\
                                style=\"font-weight: bold\"></label>\
                            </div></td>";
                    } else {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                            <input type=\"checkbox\" class=\"custom-control-input\"\
                                id=\"switchActvAP"+ data[index].Activi_id + "\" disabled>\
                            <label class=\"custom-control-label\" for=\"switchActvAP"+ data[index].Activi_id + "\"\
                                style=\"font-weight: bold\"></label>\
                            </div></td>";
                    }
                    tr += "<td><a class=\"badge badge-soft-primary mr-2\">Predeterminado</a></td>";
                }
                tr += "</tr>"
            }
            $('#actividOrga').html(tr);
            tablaActividades();
        },
        error: function () {

        }
    });
}
actividadesOrganizacion();