var table;
function tablaCentroCosto() {
    table = $("#centroC").DataTable({
        scrollX: true,
        responsive: true,
        retrieve: true,
        "searching": true,
        "lengthChange": true,
        scrollCollapse: false,
        "bAutoWidth": true,
        columnDefs: [
            { targets: 2, sortable: false },
            { targets: 3, sortable: false }
        ],
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
function centroCostoOrganizacion() {
    if ($.fn.DataTable.isDataTable("#centroC")) {
        $('#centroC').DataTable().destroy();
    }
    $('#centroOrg').empty();
    $.ajax({
        async: false,
        url: "/centroCOrga",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            console.log(data);
            var tr = "";
            for (let index = 0; index < data.length; index++) {
                tr += `<tr>
                        <td>${(index + 1)}</td>
                        <td>${data[index].descripcion}</td>`;
                tr += `<td><a class="badge badge-soft-primary"><i class="uil-users-alt"></i>&nbsp;${data[index].contar} emp.</a></td>`;
                if (data[index].respuesta == "Si") {
                    tr += `<td><img src="/admin/images/checkH.svg" height="13" class="mr-2">${data[index].respuesta}</td>`;
                } else {
                    tr += `<td><img src="/admin/images/borrarH.svg" height="11" class="mr-2">${data[index].respuesta}</td>`;
                }
                tr += `<td>
                        <a onclick="javascript:editarCentro(${data[index].id})" style="cursor: pointer">
                            <img src="/admin/images/edit.svg" height="15">
                        </a>
                        &nbsp;&nbsp;&nbsp;
                        <a onclick="javascript:eliminarCentro(${data[index]})" style="cursor: pointer">
                            <img src="/admin/images/delete.svg" height="15">
                        </a>
                    </td>
                </tr>`;
            }
            $('#centroOrg').html(tr);
            tablaCentroCosto();
        },
        error: function () { }
    });
}
centroCostoOrganizacion();
$(function () {
    $(window).on('resize', function () {
        $("#centroC").css('width', '100%');
        table.draw(true);
    });
});