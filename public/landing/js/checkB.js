$('#enviarAndroidMasivos').hide();
$('#enviarCorreosMasivos').hide();
$('#filter_col2').hide();
$('#filter_col3').hide();
$('#filter_col4').hide();
$('#filter_col5').hide();
$('#filter_col6').hide();
var seleccionarTodos = $('#selectT');
var table = $('#tablaEmpleado');
var CheckBoxs = table.find('tbody input:checkbox');
var CheckBoxMarcados = 0;

seleccionarTodos.on('click', function () {
    CheckBoxs.prop('checked', true);
});


CheckBoxs.on('change', function (e) {
    CheckBoxMarcados = table.find('tbody input:checkbox:checked').length;
    if (CheckBoxMarcados > 0) {
        $('#enviarCorreosMasivos').show();
        $('#enviarAndroidMasivos').show();
    } else {
        $('#enviarCorreosMasivos').hide();
        $('#enviarAndroidMasivos').hide();
    }
    seleccionarTodos.prop('checked', (CheckBoxMarcados === CheckBoxs.length));
});
