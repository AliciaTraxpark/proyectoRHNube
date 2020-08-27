// MOSTRAR DATOS EN TABLA DEL FORMULARIO GUARDAR
function actividad_empleado() {
    var id = $('#idEmpleado').val();
    $.ajax({
        type: "GET",
        url: "/actividadEmpleado",
        data: {
            id: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            $('#regtablaBodyTarea').empty();
            if (data != 0) {
                var container = $('#regtablaBodyTarea');
                var td = '';
                for (var $i = 0; $i < data.length; $i++) {
                    td += `<tr><td>${data[$i].Activi_Nombre}</td>`;
                    if (data[$i].estado == 1) {
                        td += `<td>Activo</td><td></td></tr>`;
                    } else {
                        td += `<td>Inactivo</td><td></td></tr>`;
                    }
                }
                container.append(td);
            }
        },
        error: function () {

        }
    });
}
// MOSTRAR DATOS EN TABLA DEL FORMULARIO EDITAR
function actividadEmp() {
    var id = $('#v_id').val();
    $.ajax({
        type: "GET",
        url: "/actividadEmpleado",
        data: {
            id: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            $('#tablaBodyTarea').empty();
            if (data != 0) {
                var container = $('#tablaBodyTarea');
                var td = '';
                for (var $i = 0; $i < data.length; $i++) {
                    td += `<tr><td>${data[$i].Activi_Nombre}</td>`;
                    if (data[$i].estado == 1) {
                        td += `<td>Activo</td><td></td></tr>`;
                    } else {
                        td += `<td>Inactivo</td><td></td></tr>`;
                    }
                }
                container.append(td);
            }
        },
        error: function () {

        }
    });
}
// ***********************************
$('#customSwitch1').prop('checked', true);
$('#bodyModoTarea').show();
$('#customSwitch3').prop('checked', true);
$('#regbodyModoTarea').show();
$('#customSwitch1').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#bodyModoTarea').show();
        actividadEmp();
    } else {
        $('#bodyModoTarea').hide();
    }
});
$('#customSwitch3').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#regbodyModoTarea').show();
        actividadEmp();
    } else {
        $('#regbodyModoTarea').hide();
    }
});
$('#bodyModoProyecto').hide();
$('#regbodyModoProyecto').hide();
$('#customSwitch2').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#bodyModoProyecto').show();
    } else {
        $('#bodyModoProyecto').hide();
    }
});

$('#customSwitch4').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#regbodyModoProyecto').show();
    } else {
        $('#regbodyModoProyecto').hide();
    }
});
// **************************************
function registrarActividadTarea() {
    var idE = $('#v_id').val();
    var nombre = $('#nombreTarea').val();
    $.ajax({
        type: "GET",
        url: "/registrarActvE",
        data: {
            idE: idE,
            nombre: nombre
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            limpiarModo();
            actividadEmp();
            $.notifyClose();
            $.notify({
                message: "\nActividad registrada.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-ver'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            $('#actividadTarea').modal('toggle')
        },
        error: function () {

        }
    });
}

function registrarNuevaActividadTarea() {
    var idE = $('#idEmpleado').val();
    var nombre = $('#regnombreTarea').val();
    $.ajax({
        type: "GET",
        url: "/registrarActvE",
        data: {
            idE: idE,
            nombre: nombre
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            limpiarModo();
            actividad_empleado();
            $.notifyClose();
            $.notify({
                message: "\nActividad registrada.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-ver'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            $('#regactividadTarea').modal('toggle')
        },
        error: function () {

        }
    });
}

 function limpiarModo(){
    $('#nombreTarea').val("");
    $('#regnombreTarea').val("");
 }