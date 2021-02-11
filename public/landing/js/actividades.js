$.fn.select2.defaults.set('language', 'es');
var table;
var sent = false;
function tablaActividades() {
    table = $("#actividades").DataTable({
        scrollX: true,
        responsive: true,
        retrieve: true,
        "searching": true,
        "lengthChange": true,
        scrollCollapse: false,
        "bAutoWidth": true,
        columnDefs: [
            { targets: 3, sortable: false },
            { targets: 4, sortable: false },
            { targets: 5, sortable: false },
            { targets: 6, sortable: false },
            { targets: 7, sortable: false }
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
function actividadesOrganizacion() {
    if ($.fn.DataTable.isDataTable("#actividades")) {
        $('#actividades').DataTable().destroy();
    }
    $('#actividOrga').empty();
    $.ajax({
        async: false,
        url: "/actividadOrg",
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
            var tr = "";
            for (let index = 0; index < data.length; index++) {
                tr += `<tr onclick="return cambiarEstadoActividad(${data[index].Activi_id})"><td>${(index + 1)}</td>`;
                tr += `<td>${data[index].Activi_Nombre}</td>`;
                tr += `<td>${data[index].codigoA}</td>`;
                if (data[index].eliminacion == 0) {
                    if (data[index].controlRemoto == 1) {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvCR${data[index].Activi_id}" checked disabled>
                                        <label class="custom-control-label" for="switchActvCR${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvCR${data[index].Activi_id}" disabled>
                                        <label class="custom-control-label" for="switchActvCR${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }
                    if (data[index].controlRuta == 1) {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvCRT${data[index].Activi_id}" checked disabled>
                                        <label class="custom-control-label" for="switchActvCRT${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvCRT${data[index].Activi_id}" disabled>
                                        <label class="custom-control-label" for="switchActvCRT${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }
                    if (data[index].asistenciaPuerta == 1) {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvAP${data[index].Activi_id}" checked disabled>
                                        <label class="custom-control-label" for="switchActvAP${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvAP${data[index].Activi_id}" disabled>
                                        <label class="custom-control-label" for="switchActvAP${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }
                    if (data[index].modoTareo == 1) {
                        tr += `<td>
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvMT${data[index].Activi_id}" checked disabled>
                                        <label class="custom-control-label" for="switchActvMT${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tr += `<td>
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvMT${data[index].Activi_id}" disabled>
                                        <label class="custom-control-label" for="switchActvMT${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }
                    if (data[index].respuesta === 1) {
                        tr += `<td class="text-center" style="font-size:12px">
                                    <img src="/admin/images/checkH.svg" height="13" class="mr-2">
                                    Si
                                </td>`;
                    } else {
                        tr += `<td class="text-center" style="font-size:12px">
                                    <img src="/admin/images/borrarH.svg" height="11" class="mr-2">
                                    No
                                </td>`;
                    }
                    tr += `<td class="text-center">
                                <a class="badge badge-soft-primary mr-2">Predeterminado</a>
                            </td>`;
                } else {
                    if (data[index].controlRemoto == 1) {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvCR${data[index].Activi_id}" checked>
                                        <label class="custom-control-label" for="switchActvCR${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvCR${data[index].Activi_id}">
                                        <label class="custom-control-label" for="switchActvCR${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }
                    if (data[index].controlRuta == 1) {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvCRT${data[index].Activi_id}" checked>
                                        <label class="custom-control-label" for="switchActvCRT${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvCRT${data[index].Activi_id}">
                                        <label class="custom-control-label" for="switchActvCRT${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }
                    if (data[index].asistenciaPuerta == 1) {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvAP${data[index].Activi_id}" checked>
                                        <label class="custom-control-label" for="switchActvAP${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tr += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvAP${data[index].Activi_id}">
                                        <label class="custom-control-label" for="switchActvAP${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }
                    if (data[index].modoTareo == 1) {

                        if (data[index].padreSubactividad == 1) {
                            tr += `<td>
                                        <div class="custom-control custom-switch mb-2">
                                            <input type="checkbox" class="custom-control-input" id="switchActvMT${data[index].Activi_id}" checked disabled>
                                            <label class="custom-control-label" for="switchActvMT${data[index].Activi_id}" style="font-weight: bold"></label>
                                            <img  data-toggle='tooltip' data-original-title='Tiene asignado subactividades' data-placement='right'
                                                title='Tiene asignado subactividades' style="cursor: pointer" src='landing/images/info.svg' height='14'>
                                        </div> 
                                    </td>`;
                        }
                        else {
                            tr += `<td>
                                        <div class="custom-control custom-switch mb-2">
                                            <input type="checkbox" class="custom-control-input" id="switchActvMT${data[index].Activi_id}" checked>
                                            <label class="custom-control-label" for="switchActvMT${data[index].Activi_id}" style="font-weight: bold"></label>
                                        </div>
                                    </td>`;
                        }

                    } else {
                        tr += `<td>
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchActvMT${data[index].Activi_id}">
                                        <label class="custom-control-label" for="switchActvMT${data[index].Activi_id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }
                    if (data[index].respuesta === 1) {
                        tr += `<td class="text-center" style="font-size:12px">
                                    <img src="/admin/images/checkH.svg" height="13" class="mr-2">
                                    Si
                                </td>`;
                    } else {
                        tr += `<td class="text-center" style="font-size:12px">
                                    <img src="/admin/images/borrarH.svg" height="11" class="mr-2">
                                    No
                                </td>`;
                    }
                    if (data[index].padreSubactividad == 1) {
                        tr += `<td class="text-center">
                                    <a name="aedit" onclick="javascript:editarActividad(${data[index].Activi_id},${data[index].respuesta})" style="cursor: pointer">
                                        <img src="/admin/images/edit.svg" height="15">
                                    </a>
                                    &nbsp;&nbsp;&nbsp;
                                </td>`;
                    } else {
                        tr += `<td class="text-center">
                                <a name="aedit" onclick="javascript:editarActividad(${data[index].Activi_id},${data[index].respuesta})" style="cursor: pointer">
                                    <img src="/admin/images/edit.svg" height="15">
                                </a>
                                &nbsp;&nbsp;&nbsp;
                                <a name="deletePermiso" onclick="javascript:eliminarActividad(${data[index].Activi_id})" style="cursor: pointer">
                                    <img src="/admin/images/delete.svg" height="15">
                                </a>
                            </td>`;
                    }

                }
                tr += `</tr>`;
            }
            $('#actividOrga').html(tr);
            tablaActividades();
            $(window).on('resize', function () {
                $("#actividades").css('width', '100%');
                table.draw(true);
            });
            $('[data-toggle="tooltip"]').tooltip();
        },
        error: function () {

        }
    });
    var valorswitch = $('#modifActI').val();
    var valorBaja = $('#bajaActI').val();
    if (valorswitch == 0) {
        $('input[type=checkbox]').prop('disabled', true);
        $('[name="aedit"]').hide();
    }
    if (valorBaja == 0) {
        $('[name="deletePermiso"]').hide();
    }

}
actividadesOrganizacion();
// ! ****************************************** CAMBIAR ESTADO DE SWICTH EN TABLA PRINCIPAL **********************
function cambiarEstadoParaControles(id, valor, control) {
    $.ajax({
        type: "POST",
        url: "/estadoActividadControl",
        data: {
            id: id,
            valor: valor,
            control: control
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            actividadesOrganizacion();
            $.notifyClose();
            $.notify(
                {
                    message: "\nActividad Modificada.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { },
    });
}
function cambiarEstadoActividad(id) {

    $("#switchActvCR" + id).on("change.bootstrapSwitch", function (event) {
        var control = "CR";
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("¿Desea modificar el estado de la  actividad?", function (
                e
            ) {
                if (e) {
                    cambiarEstadoParaControles(id, valor, control);
                }
            })
            .setting({
                title: "Modificar Actividad",
                labels: {
                    ok: "Aceptar",
                    cancel: "Cancelar",
                },
                modal: true,
                startMaximized: false,
                reverseButtons: true,
                resizable: false,
                closable: false,
                transition: "zoom",
                oncancel: function (closeEvent) {
                    actividadesOrganizacion();
                },
            });
    });

    $("#switchActvCRT" + id).on("change.bootstrapSwitch", function (event) {
        var control = "CRT";
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("¿Desea modificar el estado de la  actividad?", function (
                e
            ) {
                if (e) {
                    cambiarEstadoParaControles(id, valor, control);
                }
            })
            .setting({
                title: "Modificar Actividad",
                labels: {
                    ok: "Aceptar",
                    cancel: "Cancelar",
                },
                modal: true,
                startMaximized: false,
                reverseButtons: true,
                resizable: false,
                closable: false,
                transition: "zoom",
                oncancel: function (closeEvent) {
                    actividadesOrganizacion();
                },
            });
    });

    $("#switchActvAP" + id).on("change.bootstrapSwitch", function (event) {
        var control = "AP";
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("¿Desea modificar el estado de la  actividad?", function (
                e
            ) {
                if (e) {
                    cambiarEstadoParaControles(id, valor, control);
                }
            })
            .setting({
                title: "Modificar Actividad",
                labels: {
                    ok: "Aceptar",
                    cancel: "Cancelar",
                },
                modal: true,
                startMaximized: false,
                reverseButtons: true,
                resizable: false,
                closable: false,
                transition: "zoom",
                oncancel: function (closeEvent) {
                    actividadesOrganizacion();
                },
            });
    });

    /* MODO TAREO SWITCH */
    $("#switchActvMT" + id).on("change.bootstrapSwitch", function (event) {
        var control = "MT";
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("¿Desea modificar el estado de la  actividad?", function (
                e
            ) {
                if (e) {
                    cambiarEstadoParaControles(id, valor, control);
                }
            })
            .setting({
                title: "Modificar Actividad",
                labels: {
                    ok: "Aceptar",
                    cancel: "Cancelar",
                },
                modal: true,
                startMaximized: false,
                reverseButtons: true,
                resizable: false,
                closable: false,
                transition: "zoom",
                oncancel: function (closeEvent) {
                    actividadesOrganizacion();
                },
            });
    });
    /* ------------------ */
}
// ! ***************************************** FINALIZACION *****************************************************
// **********REMOVER CLASES
$("#nombreTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
$("#codigoTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
$("#e_codigoTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
$('#e_nombreTarea').keyup(function () {
    $(this).removeClass("borderColor");
});
// * BUSCAR PERSONALIZADO
function filterGlobal() {
    $("#actividades").DataTable().search(
        $('#global_filter').val(),

    ).draw();
}
$('input.global_filter').on('keyup click change clear', function () {
    filterGlobal();
});
// ! ********************************************************** FORMULARIO EDITAR ***************************************
// :INICIALIZAR PLUGIN DE EMPLEADOS
$("#empleados").select2({
    placeholder: 'Seleccionar Empleados',
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
$("#areaAsignarEditar").select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
//: FUNCIONALIDAD DEL SWIRCH EN CONTROL REMOTO
$('#e_customCR').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $('.rowEmpleadosEditar').show();
        estadoAsignaciones();
    } else {
        if ($('#e_customCRT').is(":checked")) {
            $('.rowEmpleadosEditar').show();
            estadoAsignaciones();
        } else {
            $('.rowEmpleadosEditar').hide();
            limpiarAsignacionPorEmpleado();
            limpiarAsignacionPorArea();
        }
    }
});
//: FUNCIONALIDAD DEL SWITCH EN CONTROL RUTA
$('#e_customCRT').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $('.rowEmpleadosEditar').show();
        estadoAsignaciones();
    } else {
        if ($('#e_customCR').is(":checked")) {
            $('.rowEmpleadosEditar').show();
            estadoAsignaciones();
        } else {
            $('.rowEmpleadosEditar').hide();
            limpiarAsignacionPorEmpleado();
            limpiarAsignacionPorArea();
        }
    }
});
//: FUNCIONALIDAD DEL SWITCH ASISTENCIA EN PUERTA
$('#e_customAP').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        if ($('#e_customCR').is(":checked") || $('#e_customCRT').is(":checked")) {
            $('.rowEmpleadosEditar').show();
            estadoAsignaciones();
        } else {
            $('.rowEmpleadosEditar').hide();
            limpiarAsignacionPorEmpleado();
            limpiarAsignacionPorArea();
        }
    } else {
        if ($('#e_customCR').is(":checked") || $('#e_customCRT').is(":checked")) {
            $('.rowEmpleadosEditar').show();
            estadoAsignaciones();
        } else {
            $('.rowEmpleadosEditar').hide();
            limpiarAsignacionPorEmpleado();
            limpiarAsignacionPorArea();
        }
    }
});
//: FUNCIONALIDAD DEL SWITCH MODO TAREO
$('#e_customMT').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        if ($('#e_customCR').is(":checked") || $('#e_customCRT').is(":checked")) {
            $('.rowEmpleadosEditar').show();
            estadoAsignaciones();
        } else {
            $('.rowEmpleadosEditar').hide();
            limpiarAsignacionPorEmpleado();
            limpiarAsignacionPorArea();
        }
    } else {
        if ($('#e_customCR').is(":checked") || $('#e_customCRT').is(":checked")) {
            $('.rowEmpleadosEditar').show();
            estadoAsignaciones();
        } else {
            $('.rowEmpleadosEditar').hide();
            limpiarAsignacionPorEmpleado();
            limpiarAsignacionPorArea();
        }
    }
});
// : MODAL DE EDITAR
var EmpleadosDeActividadEditar;
var ActividadDeActividadEditar;
function editarActividad(id, estado) {
    $('[name="aedit"]').css('pointer-events', 'none');
    $.ajax({
        async: false,
        type: "POST",
        url: "/editarA",
        data: {
            idA: id
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            $('#idActiv').val(data.Activi_id);
            $('#e_nombreTarea').val(data.Activi_Nombre);
            // ****************** ESTADO DE USO *********
            if (estado == 1) {
                $('#e_nombreTarea').attr("disabled", true);
            } else {
                $('#e_nombreTarea').attr("disabled", false);
            }
            // * *****************************************
            $('#e_codigoTarea').val(data.codigoActividad);
            if (data.codigoActividad === null || estado == 0) {
                $('#e_codigoTarea').attr("disabled", false);
            } else {
                $('#e_codigoTarea').attr("disabled", true);
            }
            if (data.controlRemoto === 1) {
                $('#e_customCR').prop("checked", true);
            } else {
                $('#e_customCR').prop("checked", false);
            }
            if (data.controlRuta === 1) {
                $('#e_customCRT').prop("checked", true);
            } else {
                $('#e_customCRT').prop("checked", false);
            }
            if (data.asistenciaPuerta === 1) {
                $('#e_customAP').prop("checked", true);
            } else {
                $('#e_customAP').prop("checked", false);
            }
            if (data.modoTareo === 1) {
                $('#e_customMT').prop("checked", true);
                if (data.padreSubactividad === 1) {
                    $('#svgInfo').show();
                    $('#e_customMT').prop("disabled", true);
                } else {
                    $('#svgInfo').hide();
                    $('#e_customMT').prop("disabled", false);
                }
            } else {
                $('#svgInfo').hide();
                $('#e_customMT').prop("disabled", false);
                $('#e_customMT').prop("checked", false);
            }
            if (data.controlRemoto === 1 || data.controlRuta === 1) {
                $('.rowEmpleadosEditar').show();
            } else {
                $('.rowEmpleadosEditar').hide();
            }
            if (data.porEmpleados === 1) {
                $('#e_customAE').prop("checked", true);
                $('#porEmpleados').show();
                $('.todosCol').show();
                datosAsignacionPorEmpleado();
                EmpleadosDeActividadEditar = $('#empleados').val();
            } else {
                $('#e_customAE').prop("checked", false);
                $('#porEmpleados').hide();
                $('.todosCol').hide();
            }
            if (data.porAreas === 1) {
                $('#e_customAA').prop("checked", true);
                $('.colAreas').show();
                datosAsignacionPorArea();
                ActividadDeActividadEditar = $('#areaAsignarEditar').val();
            } else {
                $('#e_customAA').prop("checked", false);
                $('.colAreas').hide();
            }
        },
        error: function () { },
    });
    $.notifyClose();
    $('#editactividadTarea').modal({ backdrop: 'static', keyboard: false });
    $('[name="aedit"]').css('pointer-events', 'auto');
    sent = false;
}
// : GUARDAR CAMBIOS DE ACTIVIDAD
function editarActividadTarea() {
    var codigo = $("#e_codigoTarea").val();
    var descripcion = $('#e_nombreTarea').val();
    var idA = $('#idActiv').val();
    var empleados = $('#empleados').val();
    var areas = $('#areaAsignarEditar').val();
    var globalEmpleado;
    var asignacionEmpleado;
    var asignacionArea;
    var globalArea;
    var modoTareo;
    //* CONTROL REMOTO
    if ($('#e_customCR').is(":checked") == true) {
        var controlRemoto = 1;
    } else {
        var controlRemoto = 0;
    }
    //* ASISTENCIA EN PUERTA
    if ($('#e_customAP').is(":checked") == true) {
        var asistenciaPuerta = 1;
    } else {
        var asistenciaPuerta = 0;
    }

    //* MODO TAREO
    if ($('#e_customMT').is(":checked") == true) {
        var modoTareo = 1;
    } else {
        var modoTareo = 0;
    }
    //* CONTROL EN RUTA
    if ($('#e_customCRT').is(":checked") == true) {
        var controlRuta = 1;
    } else {
        var controlRuta = 0;
    }
    //* ASIGNACION DE EMPLEADOS GLOBAL
    if ($('#checkboxEmpleadosEditarTodos').is(":checked") == true) {
        globalEmpleado = 1;
    } else {
        globalEmpleado = 0;
    }
    //* ASIGNACION DE EMPLEADOS
    if ($('#e_customAE').is(":checked")) {
        asignacionEmpleado = 1;
    } else {
        asignacionEmpleado = 0;
    }
    //* ASIGNACION  DE AREAS
    if ($('#e_customAA').is(":checked")) {
        asignacionArea = 1;
    } else {
        asignacionArea = 0;
    }
    //* ASIGNACION DE AREAS GLOBAL
    if ($('#checkboxAreasEditarTodos').is(":checked")) {
        globalArea = 1;
    } else {
        globalArea = 0;
    }
    $.ajax({
        type: "GET",
        url: "/registrarEditar",
        data: {
            idA: idA,
            descripcion: descripcion,
            cr: controlRemoto,
            ap: asistenciaPuerta,
            crt: controlRuta,
            codigo: codigo,
            empleados: empleados,
            globalEmpleado: globalEmpleado,
            asignacionEmpleado: asignacionEmpleado,
            areas: areas,
            asignacionArea: asignacionArea,
            globalArea: globalArea,
            modoTareo: modoTareo
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            if (data.respuesta == undefined) {
                limpiarModo();
                actividadesOrganizacion();
                $.notifyClose();
                $.notify(
                    {
                        message: "\nActividad modificada.",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        position: "fixed",
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 35,
                    }
                );
                $('#editactividadTarea').modal("toggle");
            } else {
                if (data.respuesta == 0) {
                    sent = false;
                    $("#e_codigoTarea").addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message: data.mensaje,
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#editactividadTarea'),
                            position: "fixed",
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "center",
                            },
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 2000,
                            template:
                                '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                } else {
                    sent = false;
                    $('#e_nombreTarea').addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message: data.mensaje,
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#editactividadTarea'),
                            position: "fixed",
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "center",
                            },
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 2000,
                            template:
                                '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                }
            }
        },
        error: function () { },
    });
}
//: VALIDACION DE FORMULARIO
$('#FormEditarActividadTarea').attr('novalidate', true);
$('#FormEditarActividadTarea').submit(function (e) {
    e.preventDefault();
    if ($('#e_customAA').is(":checked")) {
        if ($('#areaAsignarEditar').val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar Actividad',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#editactividadTarea"),
                position: "fixed",
                icon_type: 'image',
                placement: {
                    from: "top",
                    align: "center",
                },
                allow_dismiss: true,
                newest_on_top: true,
                delay: 6000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            sent = false;
            return;
        }
    }
    if ($('#e_customAE').is(":checked")) {
        if ($('#empleados').val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar Empleado',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#editactividadTarea"),
                position: "fixed",
                icon_type: 'image',
                placement: {
                    from: "top",
                    align: "center",
                },
                allow_dismiss: true,
                newest_on_top: true,
                delay: 6000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            sent = false;
            return;
        }
    }
    if (!sent) {
        sent = true;
        this.submit();
    }
});
//: TODOS LOS EMPLEADOS EN EDITAR
$('#checkboxEmpleadosEditar').click(function () {
    if ($(this).is(':checked')) {
        $("#empleados > option").prop("selected", "selected");
        $('#empleados').trigger("change");
    } else {
        $('#empleados').val(EmpleadosDeActividadEditar).trigger('change');
    }
});
//: TODAS LAS AREAS EN EDITAR
$('#checkboxAreasEditar').click(function () {
    if ($(this).is(':checked')) {
        $("#areaAsignarEditar > option").prop("selected", "selected");
        $('#areaAsignarEditar').trigger("change");
    } else {
        $('#areaAsignarEditar').val(ActividadDeActividadEditar).trigger('change');
    }
});
//: FUNCION PARA MOSTRAR EMPLEADOS POR AREAS
$("#areaAsignarEditar").on("change", function () {
    var empleados = EmpleadosDeActividadEditar;
    var areas = $("#areaAsignarEditar").val();
    $("#empleados").empty();
    var container = $("#empleados");
    $.ajax({
        async: false,
        url: "/empleadoConAreas",
        method: "POST",
        data: {
            empleados: empleados,
            areas: areas
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
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
            var option = "";
            data.forEach(element => {
                option += `<option value="${element.emple_id}">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;
            });
            container.append(option);
            $("#empleados").val(EmpleadosDeActividadEditar).trigger('change');
            if ($('#checkboxEmpleadosEditar').is(':checked')) {
                $("#empleados > option").prop("selected", "selected");
                $("#empleados").trigger("change");
            }
        },
        error: function () { },
    });
});
// : SWITCH POR ASIGNACION POR AREA
$('#e_customAA').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        $('#e_customAE').prop("checked", false);
        $('#porEmpleados').hide();
        $('.todosCol').hide();
        $('.colAreas').show();
        limpiarAsignacionPorEmpleado();
        $('.aNuevosE').hide();
        datosAsignacionPorArea();
    } else {
        $('.colAreas').hide();
        limpiarAsignacionPorArea();
    }
});
// : SWITCH POR ASIGNACION POR EMPLEADO
$('#e_customAE').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        $('#e_customAA').prop("checked", false);
        $('.colAreas').hide();
        $('#porEmpleados').show();
        $('.todosCol').show();
        datosAsignacionPorEmpleado();
        limpiarAsignacionPorArea();
    } else {
        $('#porEmpleados').hide();
        $('.todosCol').hide();
        limpiarAsignacionPorEmpleado();
    }
});
//: FUNCTION ESTADOS SWITCH
function estadoAsignaciones() {
    if (!$('#e_customAE').is(":checked")) {
        $('#porEmpleados').hide();
        $('.todosCol').hide();
        limpiarAsignacionPorEmpleado();
    }
    if (!$('#e_customAA').is(":checked")) {
        $('.colAreas').hide();
        limpiarAsignacionPorArea();
    }
}
//: OBTENER DATOS DE ASIGNACION POR EMPLEADO
function datosAsignacionPorEmpleado() {
    var idA = $('#idActiv').val();
    $("#empleados").empty();
    var container = $("#empleados");
    $.ajax({
        url: "/datosPorAsignacionE",
        method: "GET",
        data: {
            id: idA
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            var option = ``;
            if (data[0].select.length != 0) {
                data[0].select.forEach(element => {
                    option += `<option value="${element.idEmpleado}" selected="selected">${element.nombre} ${element.apPaterno} ${element.apMaterno}</option>`;
                });
            }
            if (data[0].noSelect.length != 0) {
                data[0].noSelect.forEach(element => {
                    option += `<option value="${element.emple_id}">${element.nombre} ${element.apPaterno} ${element.apMaterno}</option>`;
                });
            }
            container.append(option);
            if (data[0].global === 1) {
                $('.todosCol').hide();
                $('#checkboxEmpleadosEditarTodos').prop("checked", true);
            } else {
                $('.todosCol').show();
                $('#checkboxEmpleadosEditarTodos').prop("checked", false);
            }
            if (data[0].noSelect.length === 0) {
                $('#checkboxEmpleadosEditar').prop("checked", true);
            }
        },
        error: function () { },
    });
}
//: OBTENER DATOS DE ASIGNACION POR AREA
function datosAsignacionPorArea() {
    var idA = $('#idActiv').val();
    $('#areaAsignarEditar').empty();
    var container = $('#areaAsignarEditar');
    $.ajax({
        url: "/datosPorAsignacionA",
        method: "GET",
        data: {
            id: idA
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            var option = ``;
            if (data[0].select.length != 0) {
                data[0].select.forEach(element => {
                    option += `<option value="${element.area_id}" selected="selected">Área : ${element.area_descripcion}</option>`;
                });
            }
            if (data[0].noSelect.length != 0) {
                data[0].noSelect.forEach(element => {
                    option += `<option value="${element.area_id}">Área : ${element.area_descripcion}</option>`;
                });
            }
            container.append(option);
            if (data[0].global === 1) {
                $('#checkboxAreasEditarTodos').prop("checked", true);
            } else {
                $('#checkboxAreasEditarTodos').prop("checked", false);
            }
            if (data[0].noSelect.length === 0 && data[0].select.length != 0) {
                $('#checkboxAreasEditar').prop("checked", true);
            }
        },
        error: function () { },
    });
}
//: LIMPIAR EN ASIGNACION POR EMPLEADO
function limpiarAsignacionPorEmpleado() {
    $('#checkboxEmpleadosEditarTodos').prop("checked", false);
    $('#checkboxEmpleadosEditar').prop("checked", false);
    $('#empleados').empty();
    $('#e_customAE').prop("checked", false);
}
//: LIMPIAR EN ASIGNACION POR AREAS
function limpiarAsignacionPorArea() {
    $('#areaAsignarEditar').empty();
    $('#checkboxAreasEditarTodos').prop("checked", false);
    $('#e_customAA').prop("checked", false);
}
//: SELECT DE EMPLEADOS EN EDITAR
$("#empleados").on("change", function (e) {
    if ($("#empleados").select2('data').length === $("#empleados >option").length) {
        $('#checkboxEmpleadosEditar').prop("checked", true);
    } else {
        $('#checkboxEmpleadosEditar').prop("checked", false);
    }
});
//: SELECT DE AREAS EN EDITAR
$('#areaAsignarEditar').on("change", function (e) {
    if ($("#areaAsignarEditar").select2('data').length === $("#areaAsignarEditar >option").length) {
        $('#checkboxAreasEditar').prop("checked", true);
    } else {
        $('#checkboxAreasEditar').prop("checked", false);
    }
});
// : SELECCIONAR TODOS LOS EMPLEADOS
$('#checkboxEmpleadosEditarTodos').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        $("#empleados > option").prop("selected", "selected");
        $('#empleados').trigger("change");
        $('.aNuevosE').hide();
    } else {
        $('#empleados').val(EmpleadosDeActividadEditar).trigger('change');
        $('.aNuevosE').show();
    }
});
//! ****************************************** FORMULARIO REGISTRAR ***********************************
// * INICIALIZAR PLUGIN
$("#areaAsignarReg").select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
$("#reg_empleados").select2({
    placeholder: 'Seleccionar Empleados',
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
// * MODAL DE REGISTRAR NUEVA ACTIVIDAD
function modalRegistrar() {
    $('#regactividadTarea').modal({ backdrop: 'static', keyboard: false })
    sent = false;
}
//* FUNCIONALIDAD DEL SWITCH EN CONTROL REMOTO
$('#customCR').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $('.rowEmpleados').show();
        estadoAsignacionesReg();
    } else {
        if ($('#customCRT').is(":checked")) {
            $('.rowEmpleados').show();
            estadoAsignacionesReg();
        } else {
            $('.rowEmpleados').hide();
            limpiarAsignacionPorEmpleadoReg();
            limpiarAsignacionPorAreaReg();
        }
    }
});
//* FUNCIONALIDAD DEL SWITCH EN CONTROL RUTA
$('#customCRT').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $('.rowEmpleados').show();
        estadoAsignacionesReg();
    } else {
        if ($('#customCR').is(":checked")) {
            $('.rowEmpleados').show();
            estadoAsignacionesReg();
        } else {
            $('.rowEmpleados').hide();
            limpiarAsignacionPorEmpleadoReg();
            limpiarAsignacionPorAreaReg();
        }
    }
});
//* FUNCIONALIDAD DEL SWITCH ASISTENCIA EN PUERTA
$('#customAP').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        if ($('#customCR').is(":checked") || $('#customCRT').is(":checked")) {
            $('.rowEmpleados').show();
            estadoAsignacionesReg();
        } else {
            $('.rowEmpleados').hide();
            limpiarAsignacionPorEmpleadoReg();
            limpiarAsignacionPorAreaReg();
        }
    } else {
        if ($('#customCR').is(":checked") || $('#customCRT').is(":checked")) {
            $('.rowEmpleados').show();
            estadoAsignacionesReg();
        } else {
            $('.rowEmpleados').hide();
            limpiarAsignacionPorEmpleadoReg();
            limpiarAsignacionPorAreaReg();
        }
    }
});
//* FUNCIONALIDAD DEL SWITCH MODO TAREO
$('#customMT').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        if ($('#customCR').is(":checked") || $('#customCRT').is(":checked")) {
            $('.rowEmpleados').show();
            estadoAsignacionesReg();
        } else {
            $('.rowEmpleados').hide();
            limpiarAsignacionPorEmpleadoReg();
            limpiarAsignacionPorAreaReg();
        }
    } else {
        if ($('#customCR').is(":checked") || $('#customCRT').is(":checked")) {
            $('.rowEmpleados').show();
            estadoAsignacionesReg();
        } else {
            $('.rowEmpleados').hide();
            limpiarAsignacionPorEmpleadoReg();
            limpiarAsignacionPorAreaReg();
        }
    }
});
//* FUNCTION ESTADOS SWITCH
function estadoAsignacionesReg() {
    if (!$('#customAE').is(":checked")) {
        $('#porEmpleadosReg').hide();
        $('.todosColReg').hide();
        limpiarAsignacionPorEmpleadoReg();
    }
    if (!$('#customAA').is(":checked")) {
        $('.colAreasReg').hide();
        limpiarAsignacionPorAreaReg();
    }
    $.notifyClose();
}
//* LIMPIAR EN ASIGNACION POR EMPLEADO EN REGISTRAR
function limpiarAsignacionPorEmpleadoReg() {
    $('#checkboxEmpleadosTodosReg').prop("checked", false);
    $('#checkboxEmpleadosReg').prop("checked", false);
    $('#reg_empleados').empty();
    $('#customAE').prop("checked", false);
}
//* LIMPIAR EN ASIGNACION POR AREAS EN REGISTRAR
function limpiarAsignacionPorAreaReg() {
    $('#areaAsignarReg').empty();
    $('#checkboxAreasTodosReg').prop("checked", false);
    $('#customAA').prop("checked", false);
}
// * FUNCION PARA OBTENER LAS AREAS
function listaAreas() {
    $("#areaAsignarReg").empty();
    var container = $("#areaAsignarReg");
    $.ajax({
        async: false,
        url: "/listaAreasE",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            var option = ``;
            data.forEach(element => {
                option += `<option value="${element.area_id}"> Área : ${element.area_descripcion} </option>`;
            });
            container.append(option);
        },
        error: function () { },
    });
}
//* SELECT DE EMPLEADOS EN FORMULARIO AGREGAR
function empleadoListaReg() {
    $("#reg_empleados").empty();
    var container = $("#reg_empleados");
    $.ajax({
        async: false,
        url: "/empleadoActivReg",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            var option = ``;
            data.forEach(element => {
                option += `<option value="${element.emple_id}">${element.nombre} ${element.apPaterno} ${element.apMaterno}</option>`;
            });
            container.append(option);
        },
        error: function () { },
    });
}
// * SWITCH POR ASIGNACION POR AREA
$('#customAA').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        $('#customAE').prop("checked", false);
        $('#porEmpleadosReg').hide();
        $('.todosColReg').hide();
        $('.colAreasReg').show();
        limpiarAsignacionPorEmpleadoReg();
        listaAreas();
    } else {
        $('.colAreasReg').hide();
        limpiarAsignacionPorAreaReg();
    }
});
//  * SWITCH POR ASIGNACION POR EMPLEADO
$('#customAE').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        $('#customAA').prop("checked", false);
        $('.colAreasReg').hide();
        limpiarAsignacionPorAreaReg();
        $('#porEmpleadosReg').show();
        $('.todosColReg').show();
        empleadoListaReg();
    } else {
        $('#porEmpleadosReg').hide();
        $('.todosColReg').hide();
        limpiarAsignacionPorAreaReg();
    }
});
//* REGISTRAR NUEVA ACTIVIDAD
function registrarActividadTarea() {
    var nombre = $("#nombreTarea").val();
    var codigo = $("#codigoTarea").val();
    var empleados = $("#reg_empleados").val();
    var areas = $("#areaAsignarReg").val();
    var globalEmpleado;
    var asignacionEmpleado;
    var asignacionArea;
    var globalArea;
    var modoTareo;
    //* CONTROL REMOTO
    if ($('#customCR').is(":checked") == true) {
        var controlRemoto = 1;
    } else {
        var controlRemoto = 0;
    }
    //* ASISTENCIA EN PUERTA
    if ($('#customAP').is(":checked") == true) {
        var asistenciaPuerta = 1;
    } else {
        var asistenciaPuerta = 0;
    }
    //* MODO TAREO
    if ($('#customMT').is(":checked") == true) {
        var modoTareo = 1;
    } else {
        var modoTareo = 0;
    }
    //* CONTROL EN RUTA
    if ($('#customCRT').is(":checked") == true) {
        var controlRuta = 1;
    } else {
        var controlRuta = 0;
    }
    //* ASIGNACION DE EMPLEADOS GLOBAL
    if ($('#checkboxEmpleadosTodosReg').is(":checked") == true) {
        globalEmpleado = 1;
    } else {
        globalEmpleado = 0;
    }
    //* ASIGNACION DE EMPLEADOS
    if ($('#customAE').is(":checked")) {
        asignacionEmpleado = 1;
    } else {
        asignacionEmpleado = 0;
    }
    //* ASIGNACION  DE AREAS
    if ($('#customAA').is(":checked")) {
        asignacionArea = 1;
    } else {
        asignacionArea = 0;
    }
    //* ASIGNACION DE AREAS GLOBAL
    if ($('#checkboxAreasTodosReg').is(":checked")) {
        globalArea = 1;
    } else {
        globalArea = 0;
    }
    $.ajax({
        type: "POST",
        url: "/registrarActvO",
        data: {
            nombre: nombre,
            cr: controlRemoto,
            ap: asistenciaPuerta,
            crt: controlRuta,
            codigo: codigo,
            empleados: empleados,
            globalEmpleado: globalEmpleado,
            asignacionEmpleado: asignacionEmpleado,
            areas: areas,
            asignacionArea: asignacionArea,
            globalArea: globalArea,
            modoTareo: modoTareo
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            if (data.estado === 1) {
                sent = false;
                if (data.actividad.estado == 0) {
                    alertify
                        .confirm("Ya existe una actividad inactiva con este nombre. ¿Desea recuperarla si o no?", function (
                            e
                        ) {
                            if (e) {
                                recuperarActividad(data.actividad.Activi_id);
                            }
                        })
                        .setting({
                            title: "Modificar Actividad",
                            labels: {
                                ok: "Si",
                                cancel: "No",
                            },
                            modal: true,
                            startMaximized: false,
                            reverseButtons: true,
                            resizable: false,
                            closable: false,
                            transition: "zoom",
                            oncancel: function (closeEvent) {
                            },
                        });
                } else {
                    $("#nombreTarea").addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message:
                                "\nYa existe una actividad con este nombre.",
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#regactividadTarea'),
                            position: "fixed",
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "center",
                            },
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 2000,
                            template:
                                '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                }
            } else {
                if (data.estado === 0) {
                    sent = false;
                    if (data.actividad.estado == 0) {
                        alertify
                            .confirm("Ya existe una actividad inactiva con este código. ¿Desea recuperarla si o no?", function (
                                e
                            ) {
                                if (e) {
                                    recuperarActividad(data.actividad.Activi_id);
                                }
                            })
                            .setting({
                                title: "Modificar Actividad",
                                labels: {
                                    ok: "Si",
                                    cancel: "No",
                                },
                                modal: true,
                                startMaximized: false,
                                reverseButtons: true,
                                resizable: false,
                                closable: false,
                                transition: "zoom",
                                oncancel: function (closeEvent) {
                                },
                            });
                    } else {
                        $("#codigoTarea").addClass("borderColor");
                        $.notifyClose();
                        $.notify(
                            {
                                message:
                                    "\nYa existe una actividad con este código.",
                                icon: "admin/images/warning.svg",
                            },
                            {
                                element: $('#regactividadTarea'),
                                position: "fixed",
                                mouse_over: "pause",
                                placement: {
                                    from: "top",
                                    align: "center",
                                },
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 2000,
                                template:
                                    '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 35,
                            }
                        );
                    }
                } else {
                    limpiarModo();
                    actividadesOrganizacion();
                    $('#regactividadTarea').modal('toggle');
                    $.notifyClose();
                    $.notify(
                        {
                            message: "\nActividad registrada.",
                            icon: "admin/images/checked.svg",
                        },
                        {
                            position: "fixed",
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 5000,
                            template:
                                '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                }
            }
        },
        error: function () { },
    });
}
// * VALIDACION DE FORMULARIO
$('#FormRegistrarActividadTarea').attr('novalidate', true);
$('#FormRegistrarActividadTarea').submit(function (e) {
    e.preventDefault();
    if ($('#customAA').is(":checked")) {
        if ($('#areaAsignarReg').val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar Actividad',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#regactividadTarea"),
                position: "fixed",
                icon_type: 'image',
                placement: {
                    from: "top",
                    align: "center",
                },
                allow_dismiss: true,
                newest_on_top: true,
                delay: 6000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            sent = false;
            return;
        }
    }
    if ($('#customAE').is(":checked")) {
        if ($('#reg_empleados').val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar Empleado',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#regactividadTarea"),
                position: "fixed",
                icon_type: 'image',
                placement: {
                    from: "top",
                    align: "center",
                },
                allow_dismiss: true,
                newest_on_top: true,
                delay: 6000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            sent = false;
            return;
        }
    }
    if (!sent) {
        sent = true;
        this.submit();
    }
});
// * RECUPERAR ACTIVIDAD
function recuperarActividad(id) {
    $.ajax({
        type: "GET",
        url: "/recuperarA",
        data: {
            id: id
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            limpiarModo();
            actividadesOrganizacion();
            $('#regactividadTarea').modal('toggle');
            editarActividad(data.Activi_id);
        },
        error: function () { },
    });

}
// * TODO LOS EMPLEADOS EN REGISTRAR
$('#checkboxEmpleadosReg').click(function () {
    if ($(this).is(':checked')) {
        $("#reg_empleados > option").prop("selected", "selected");
        $('#reg_empleados').trigger("change");
    } else {
        $('#reg_empleados').val('').trigger('change');
    }
});
// * TODO LAS AREAS EN REGISTRAR
$('#checkboxAreasReg').click(function () {
    if ($(this).is(':checked')) {
        $("#areaAsignarReg > option").prop("selected", "selected");
        $('#areaAsignarReg').trigger("change");
    } else {
        $('#areaAsignarReg').val('').trigger('change');
    }
});
// * SELECT DE EMPLEADOS EN REGISTRAR
$("#reg_empleados").on("change", function (e) {
    if ($("#reg_empleados").select2('data').length === $("#reg_empleados >option").length) {
        $('#checkboxEmpleadosReg').prop("checked", true);
    } else {
        $('#checkboxEmpleadosReg').prop("checked", false);
    }
});
// * SELECT DE AREAS EN REGISTRAR
$('#areaAsignarReg').on("change", function (e) {
    if ($("#areaAsignarReg").select2('data').length === $("#areaAsignarReg >option").length) {
        $('#checkboxAreasReg').prop("checked", true);
    } else {
        $('#checkboxAreasReg').prop("checked", false);
    }
});
// * SELECT DE TODOS LOS EMPLEADOS
$('#checkboxEmpleadosTodosReg').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        $('#checkboxAreasReg').prop("checked", true);
        $("#reg_empleados > option").prop("selected", "selected");
        $('#reg_empleados').trigger("change");
        $('.aNuevosR').hide();
    } else {
        $('#checkboxAreasReg').prop("checked", false);
        $('#reg_empleados').val('').trigger('change');
        $('.aNuevosR').show();
    }
});
// ! ************************************************ FORMULARIO ASIGNAR ******************************************
//? INICIALIZAR PLUGIN
$("#actividadesAsignar").select2({
    placeholder: 'Seleccionar actividad',
    matcher: matchStart
});
$("#areaAsignar").select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
$("#empleAsignar").select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
//? FUNCIONN PARA LISTAR ACTIVIDADES
function listaActividades() {
    $("#actividadesAsignar").empty();
    var container = $("#actividadesAsignar");
    $.ajax({
        async: false,
        url: "/listActivi",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            var option = `<option value="" disabled selected>Seleccionar</option>`;
            data.forEach(element => {
                option += `<option value="${element.idActividad}"> Actividad : ${element.nombre} </option>`;
            });
            container.append(option);
        },
        error: function () { },
    });
}
// ? ABRIR MODAL DE ASIGNACION
function asignarActividadMasiso() {
    $('#asignarPorArea').modal({ backdrop: 'static', keyboard: false });
    $("#empleAsignar").empty();
    $("#areaAsignar").empty();
    listaActividades();
    sent = false;
}
var EmpleadosDeActividad;
//? FUNCION DE CHANGE DE ACTIVIDADES
$("#actividadesAsignar").on("change", function () {
    //: *********** ACTIVAR FORMULARIO ***********
    $('#a_customAA').attr("disabled", false);
    $('#a_customAE').attr("disabled", false);
    //: ******************************************
    var idA = $(this).val();
    $.ajax({
        async: false,
        url: "/datosActividad",
        method: "GET",
        data: {
            idA: idA
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            if (data.porEmpleados == 1) {
                $('.aEmpleado').show();
                datosAsignacionPorEmpleado_Asignacion(data.Activi_id);
                $('#a_customAE').prop("checked", true);
            } else {
                limpiarAE();
            }
            if (data.porAreas == 1) {
                $('.aArea').show();
                datosAsignacionPorArea_Asignacion(data.Activi_id);
                $('#a_customAA').prop("checked", true);
            } else {
                limpiarAA();
            }
        },
        error: function () { },
    });
});
var EmpleadosAsig;
var AreaAsig;
//? OBTENER DATOS DE ACTIVIDAD SELECCIONADA
function datosAsignacionPorEmpleado_Asignacion(id) {
    $("#empleAsignar").empty();
    var container = $("#empleAsignar");
    $.ajax({
        async: false,
        url: "/datosPorAsignacionE",
        method: "GET",
        data: {
            id: id
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            var option = ``;
            if (data[0].select.length != 0) {
                data[0].select.forEach(element => {
                    option += `<option value="${element.idEmpleado}" selected="selected">${element.nombre} ${element.apPaterno} ${element.apMaterno}</option>`;
                });
            }
            if (data[0].noSelect.length != 0) {
                data[0].noSelect.forEach(element => {
                    option += `<option value="${element.emple_id}">${element.nombre} ${element.apPaterno} ${element.apMaterno}</option>`;
                });
            }
            container.append(option);
            if (data[0].global === 1) {
                $('.aNuevos').hide();
                $('#checkboxEmpleadosTodos').prop("checked", true);
            } else {
                $('.aNuevos').show();
                $('#checkboxEmpleadosTodos').prop("checked", false);
            }
            if (data[0].noSelect.length === 0) {
                $('#checkboxEmpleados').prop("checked", true);
            }
            EmpleadosAsig = $("#empleAsignar").val();
        },
        error: function () { },
    });
}
//? OBTENER DATOS DE ASIGNACION POR AREA
function datosAsignacionPorArea_Asignacion(id) {
    $('#areaAsignar').empty();
    var container = $('#areaAsignar');
    $.ajax({
        async: false,
        url: "/datosPorAsignacionA",
        method: "GET",
        data: {
            id: id
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            var option = ``;
            if (data[0].select.length != 0) {
                data[0].select.forEach(element => {
                    option += `<option value="${element.area_id}" selected="selected">Área : ${element.area_descripcion}</option>`;
                });
            }
            if (data[0].noSelect.length != 0) {
                data[0].noSelect.forEach(element => {
                    option += `<option value="${element.area_id}">Área : ${element.area_descripcion}</option>`;
                });
            }
            container.append(option);
            if (data[0].global === 1) {
                $('#checkboxAreasTodos').prop("checked", true);
            } else {
                $('#checkboxAreasTodos').prop("checked", false);
            }
            if (data[0].noSelect.length === 0) {
                $('#checkboxAreas').prop("checked", true);
            }
            AreaAsig = $('#areaAsignar').val();
        },
        error: function () { },
    });
}
//? LIMPIAR EN ASIGNACION POR EMPLEADO
function limpiarAE() {
    $('#a_customAE').prop("checked", false);
    $('#checkboxEmpleadosTodos').prop("checked", false);
    $('#checkboxEmpleados').prop("checked", false);
    $('#empleAsignar').empty();
    $('.aNuevos').show();
    $('.aEmpleado').hide();
}
//? LIMPIAR EN ASIGNACION POR AREAS
function limpiarAA() {
    $('#a_customAA').prop("checked", false);
    $('#checkboxAreasTodos').prop("checked", false);
    $('#checkboxAreas').prop("checked", false);
    $('#areaAsignar').empty();
    $('.aArea').hide();
}
// ? FUNCION DE LIMPIAR ASIGNACION
function limpiarAsignacion() {
    limpiarAE();
    limpiarAA();
    $('#a_customAE').prop("disabled", true);
    $('#a_customAA').prop("disabled", true);
}
// ? TODO LOS EMPLEADOS EN ASIGNAR
$('#checkboxEmpleados').click(function () {
    if ($(this).is(':checked')) {
        $("#empleAsignar > option").prop("selected", "selected");
        $('#empleAsignar').trigger("change");
    } else {
        $('#empleAsignar').val(EmpleadosAsig).trigger('change');
    }
});
// ? TODO LAS AREAS EN ASIGNAR
$('#checkboxAreas').click(function () {
    if ($(this).is(':checked')) {
        $("#areaAsignar > option").prop("selected", "selected");
        $('#areaAsignar').trigger("change");
    } else {
        $('#areaAsignar').val(AreaAsig).trigger('change');
    }
});
// ? SELECT DE EMPLEADOS
$("#empleAsignar").on("change", function (e) {
    if ($("#empleAsignar").select2('data').length === $("#empleAsignar >option").length) {
        $('#checkboxEmpleados').prop("checked", true);
    } else {
        $('#checkboxEmpleados').prop("checked", false);
    }
});
// ? SELECT DE AREAS
$('#areaAsignar').on("change", function (e) {
    if ($("#areaAsignar").select2('data').length === $("#areaAsignar >option").length) {
        $('#checkboxAreas').prop("checked", true);
    } else {
        $('#checkboxAreas').prop("checked", false);
    }
});
// ? SWITCH DE ASIGNACION POR AREA
$('#a_customAA').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        limpiarAE();
        var idA = $("#actividadesAsignar").val();
        datosAsignacionPorArea_Asignacion(idA);
        $('.aArea').show();
    } else {
        limpiarAA();
    }
});
// ? SWITCH DE ASIGNACION POR EMPLEADO
$('#a_customAE').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        limpiarAA();
        var idA = $("#actividadesAsignar").val();
        datosAsignacionPorEmpleado_Asignacion(idA);
        $('.aEmpleado').show();
    } else {
        limpiarAA();
    }
});
// ?  GUARDAR ASIGNACION
function asignarActividadEmpleado() {
    var empleados = $("#empleAsignar").val();
    var actividad = $("#actividadesAsignar").val();
    var areas = $('#areaAsignar').val();
    var globalEmpleado;
    var globalArea;
    var porEmpleados;
    var porAreas;
    if ($('#checkboxEmpleadosTodos').is(":checked") == true) {
        globalEmpleado = 1;
    } else {
        globalEmpleado = 0;
    }
    if ($('#checkboxAreasTodos').is(":checked") == true) {
        globalArea = 1;
    } else {
        globalArea = 0;
    }
    if ($('#a_customAE').is(":checked") == true) {
        porEmpleados = 1;
    } else {
        porEmpleados = 0;
    }
    if ($('#a_customAA').is(":checked") == true) {
        porAreas = 1;
    } else {
        porAreas = 0;
    }
    $.ajax({
        async: false,
        url: "/asignacionActividadE",
        method: "POST",
        data: {
            empleados: empleados,
            idActividad: actividad,
            areas: areas,
            globalEmpleado: globalEmpleado,
            globalArea: globalArea,
            asignacionArea: porAreas,
            asignacionEmpleado: porEmpleados
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            $('#asignarPorArea').modal('toggle');
            limpiarAsignacion();
            //: ************************************************
            $.notifyClose();
            $.notify(
                {
                    message: "\nAsignación exitosa.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { },
    });
}
//  ? VALIDACION DE FORMULARIO
$('#FormAsignarActividadEmpleado').attr('novalidate', true);
$('#FormAsignarActividadEmpleado').submit(function (e) {
    e.preventDefault();
    if ($('#actividadesAsignar').val() == "" || $('#actividadesAsignar').val() == null) {
        $.notifyClose();
        $.notify({
            message: '\nSeleccionar actividad',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#asignarPorArea"),
            position: "fixed",
            icon_type: 'image',
            placement: {
                from: "top",
                align: "center",
            },
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
        sent = false;
        return;
    }
    if ($('#a_customAA').is(":checked")) {
        if ($('#areaAsignar').val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar área',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#asignarPorArea"),
                position: "fixed",
                icon_type: 'image',
                placement: {
                    from: "top",
                    align: "center",
                },
                allow_dismiss: true,
                newest_on_top: true,
                delay: 6000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            sent = false;
            return;
        }
    }
    if ($('#a_customAE').is(":checked")) {
        if ($("#empleAsignar").val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar empleado',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#asignarPorArea"),
                position: "fixed",
                icon_type: 'image',
                placement: {
                    from: "top",
                    align: "center",
                },
                allow_dismiss: true,
                newest_on_top: true,
                delay: 6000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            sent = false;
            return;
        }
    }
    if (!sent) {
        sent = true;
        this.submit();
    }
});
// ? TODOS LOS EMPLEADOS
$('#checkboxEmpleadosTodos').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        $('#checkboxEmpleados').prop("checked", true);
        $("#empleAsignar > option").prop("selected", "selected");
        $('#empleAsignar').trigger("change");
        $('.aNuevos').hide();
    } else {
        $('#empleAsignar').val(EmpleadosAsig).trigger('change');
        $('.aNuevos').show();
    }
});
$(function () {
    $(window).on('resize', function () {
        $("#actividades").css('width', '100%');
        table.draw(true);
    });
});
// ! ****************************** LIMPIAR DE ACTIVIDADES *************************************
function limpiarModo() {
    //* FORMULARIO REGISTRAR
    $('#nombreTarea').val("");
    $('#codigoTarea').val("");
    $('#customCR').prop("checked", false);
    $('#customCRT').prop("checked", false);
    $('#customMT').prop("checked", false);
    $('#customAP').prop("checked", false);
    $('.rowEmpleados').hide();
    $('#customAE').prop("checked", false);
    $('#customAA').prop("checked", false);
    //* FORMULARIO EDITAR
    $('#e_nombreTarea').val("");
    $('#e_codigoTarea').val("");
    $('#e_customCR').prop("checked", false);
    $('#e_customCRT').prop("checked", false);
    $('#e_customAP').prop("checked", false);
    $('#e_customMT').prop("checked", false);
}
// ! ***************************** ELIMINAR ACTIVIDAD **********************************************
function eliminarActividad(id) {
    alertify
        .confirm("¿Desea eliminar actividad?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    type: "GET",
                    url: "/estadoActividad",
                    data: {
                        id: id
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
                        if (data == 1) {
                            $.notifyClose();
                            $.notify({
                                message: '\nActividad en uso, no se puede eliminar.',
                                icon: '/landing/images/alert1.svg',
                            }, {
                                icon_type: 'image',
                                allow_dismiss: true,
                                newest_on_top: true,
                                delay: 6000,
                                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        } else {
                            actividadesOrganizacion();
                            $.notifyClose();
                            $.notify({
                                message: '\nActividad eliminada',
                                icon: 'landing/images/bell.svg',
                            }, {
                                icon_type: 'image',
                                allow_dismiss: true,
                                newest_on_top: true,
                                delay: 6000,
                                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        }
                    },
                    error: function () { },
                });
            }
        })
        .setting({
            title: "Eliminar actividad",
            labels: {
                ok: "Aceptar",
                cancel: "Cancelar",
            },
            modal: true,
            startMaximized: false,
            reverseButtons: true,
            resizable: false,
            closable: false,
            transition: "zoom",
            oncancel: function (closeEvent) {
                actividadesOrganizacion();
            },
        });
}
// ! ***************************** AGREGANDO TOOLTIP POR PAGINACION DESAPARECE *********************
$('#actividades tbody').on('mouseover', 'tr', function () {
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        html: true
    });
});
// ! ********************************* FINALIZACION ********************************************************
