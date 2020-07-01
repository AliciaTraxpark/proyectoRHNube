$(document).ready(function() {
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
