// MOSTRAR DATOS EN TABLA
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
            if (data != 0) {
                $('#tablaBodyTarea').empty();
                var container = $('#tablaBodyTarea');
                var td = '';
                for (var $i = 0; $i < data.length; $i++) {
                    td += `<tr><td>${data[$i].Proye_Nombre}</td>`;
                    if (data[$i].Proye_estado == 1) {
                        td += `<td>Activo</td></tr>`;
                    } else {
                        td += `<td>Inactivo</td></tr>`;
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
$('#customSwitch1').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#bodyModoTarea').show();
        actividadEmp();
    } else {
        $('#bodyModoTarea').hide();
    }
});
$('#bodyModoProyecto').hide();
$('#customSwitch2').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#bodyModoProyecto').show();
    } else {
        $('#bodyModoProyecto').hide();
    }
});
