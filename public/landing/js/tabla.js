$(document).ready(function () {
    //$('#form-ver').hide();
    leertabla();
});

function leertabla() {
    $('#tabladiv').hide();
    $('#espera').show();
    $.get("tablaempleado/ver", {}, function (data, status) {
        $('#tabladiv').html(data);
        $('#espera').hide();
        $('#tabladiv').show();

    });
}

function RefreshTablaEmpleado() {
    if ($.fn.DataTable.isDataTable("#tablaEmpleado")) {
        $('#tablaEmpleado').DataTable().destroy();
    }
    $('#tbodyr').empty();
    $.ajax({
        async: false,
        type: "get",
        url: "tablaempleado/refresh",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var tbody = "";
            console.log(data);
            console.log(data.length);
            for (var i = 0; i < data.length; i++) {
                tbody += "<tr id=" + data[i].emple_id + " value=" + data[i].emple_id + ">\
                            <td>\
                                <a id=\"formNuevoEd\" onclick=\"javascript:editarEmpleado(" + data[i].emple_id + ")\" style=\"cursor: pointer\">\
                                <img src=\"/admin/images/edit.svg\" height=\"15\">\
                                </a>\
                                &nbsp;&nbsp;&nbsp;\
                                <a onclick=\"javascript:marcareliminar(" + data[i].emple_id + ")\" style=\"cursor: pointer\">\
                                    <img src=\"/admin/images/delete.svg\" height=\"15\">\
                                </a>\
                                &nbsp;&nbsp;\
                                <a class=\"verEmpleado\" onclick=\"javascript:verDEmpleado(" + data[i].emple_id + ")\" data-toggle=\"tooltip\"\
                                    data-placement=\"right\" title=\"Ver Detalles\" data-original-title=\"Ver Detalles\" style=\"cursor:pointer\">\
                                    <img src=\"/landing/images/see.svg\" height=\"18\">\
                                </a>\
                            </td>\
                            <td class=\"text-center\">&nbsp; <input type=\"hidden\" id=\"codE\" value=\"" + data[i].emple_id + "\">\
                                <img src=\"/admin/assets/images/users/empleado.png\" alt=\"\" />\
                            </td>\
                            <td>" + data[i].perso_nombre + "</td>\
                            <td>" + data[i].perso_apPaterno + " " + data[i].perso_apMaterno + "</td>";
                if (data[i].cargo_descripcion == null) {
                    tbody += "<td></td>";
                } else {
                    tbody += "<td>" + data[i].cargo_descripcion + "</td>";
                }
                if (data[i].area_descripcion == null) {
                    tbody += "<td></td>";
                } else {
                    tbody += "<td> " + data[i].area_descripcion + "</td>";
                }
                if (data[i].centroC_descripcion == null) {
                    tbody += "<td></td>";
                } else {
                    tbody += "<td>" + data[i].centroC_descripcion + "</td>";
                }
                console.log(data[i].dispositivos.includes(1));
                if (data[i].dispositivos.includes(1) == false) {
                    tbody += "<td></td>";
                } else {
                    tbody += "<td class=\"text-center\">\
                                    <div class=\"dropdown\" id=\"w" + data[i].emple_id + ">\
                                    <a class=\"dropdown\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\"\
                                        style=\"cursor: pointer\">\
                                        <img src=\"/landing/images/note.svg\" height=\"20\">\
                                    </a>\
                                    <ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">";
                    for (var j = 0; j < data[i].vinculacion.length; j++) {
                        if (data[i].vinculacion[j].dispositivoD == "WINDOWS") {
                            tbody += "<a class=\"dropdown-item\"\
                                            onclick=\"javascript:enviarWindowsTabla(" + data[i].emple_id + "," + data[i].vinculacion[j].idVinculacion + ")\">PC " + j + 1 + "\
                                        </a>";
                        }
                    }
                    tbody += "</ul>\
                    </div>\
                     </td>";
                }
                if (data[i].dispositivos.includes(2) == false) {
                    tbody += "<td></td>";
                } else {
                    tbody += "<td class=\"text-center\">\
                                    <div class=\"dropdown\" id=\"a" + data[i].emple_id + ">\
                                    <a class=\"dropdown\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\"\
                                        style=\"cursor: pointer\">\
                                        <img src=\"/landing/images/note.svg\" height=\"20\">\
                                    </a>\
                                    <ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">";
                    for (var j = 0; j < data[i].vinculacion.length; j++) {
                        if (data[i].vinculacion[j].dispositivoD == "ANDROID") {
                            tbody += "<a class=\"dropdown-item\" \
                                            onclick=\"javascript:enviarAndroidTabla(" + data[i].emple_id + "," + data[i].vinculacion[j].idVinculacion + ")\">PC " + j + 1 + "\
                                        </a>";
                        }
                    }
                    tbody += "</ul>\
                    </div>\
                     </td>";
                }
                tbody += "<td class=\"text-center\">\
                            <input type=\"checkbox\" id=\"tdC\" style=\"margin-right:5px!important\"\
                            class=\"form-check-input sub_chk\" data-id=" + data[i].emple_id + " " + this + "" + this + "" + this + ">\
                        </td></tr>";
            }
            console.log(tbody);
            $('#tbodyr').html(tbody);
            $("#tablaEmpleado").DataTable({
                retrieve: true,
                "searching": true,
                "lengthChange": false,
                "scrollX": true,
                "pageLength": 30,
                fixedHeader: true,
                "processing": true,
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
                },
                initComplete: function () {
                    this.api().columns().every(function () {
                        var that = this;
                        var i;
                        var val1;
                        $('#select').on("keyup change", function () {
                            i = $.fn.dataTable.util.escapeRegex(this.value);
                            console.log(i);
                            var val = $('#global_filter').val();
                            if (that.column(i).search() !== this.value) {
                                that.column(this.value).search(val).draw();
                            }
                            val1 = $.fn.dataTable.util.escapeRegex(this.value);
                            $('#global_filter').on("keyup change clear", function () {
                                var val = $(this).val();
                                if (that.column(i).search() !== val1) {
                                    that.column(val1).search(val).draw();
                                }
                            });
                        });
                    });
                }
            });
            var seleccionarTodos = $('#selectT');
            var table = $('#tablaEmpleado');
            var CheckBoxs = table.find('tbody input:checkbox');
            var CheckBoxMarcados = 0;

            seleccionarTodos.on('click', function () {
                if (seleccionarTodos.is(":checked")) {
                    CheckBoxs.prop('checked', true);
                    $('#enviarCorreosMasivos').show();
                    $('#enviarAndroidMasivos').show();
                    $('#enviarMasivo').show();
                } else {
                    CheckBoxs.prop('checked', false);
                    $('#enviarCorreosMasivos').hide();
                    $('#enviarAndroidMasivos').hide();
                    $('#enviarMasivo').hide();
                };

            });


            CheckBoxs.on('change', function (e) {
                CheckBoxMarcados = table.find('tbody input:checkbox:checked').length;
                if (CheckBoxMarcados > 0) {
                    $('#enviarCorreosMasivos').show();
                    $('#enviarAndroidMasivos').show();
                    $('#enviarMasivo').show();
                } else {
                    $('#enviarCorreosMasivos').hide();
                    $('#enviarAndroidMasivos').hide();
                    $('#enviarMasivo').hide();
                }
                seleccionarTodos.prop('checked', (CheckBoxMarcados === CheckBoxs.length));
            });
            $(".sub_chk").click(function () {
                if ($(this).prop('checked')) {
                    $('#w' + $(this).attr('data-id')).hide();
                    $('#a' + $(this).attr('data-id')).hide();
                } else {
                    $('#w' + $(this).attr('data-id')).show();
                    $('#a' + $(this).attr('data-id')).show();
                }
            });
            seleccionarTodos.click(function () {
                $(".sub_chk").each(function () {
                    if ($(this).prop('checked')) {
                        $('#w' + $(this).attr('data-id')).hide();
                        $('#a' + $(this).attr('data-id')).hide();
                    } else {
                        $('#w' + $(this).attr('data-id')).show();
                        $('#a' + $(this).attr('data-id')).show();
                    }
                });
            });
        }
    });
}
