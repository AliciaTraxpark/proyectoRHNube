$(document).ready(function() {
    //$('#form-ver').hide();
    leertabla();
});
function leertabla() {

    $.get("tablaempleado/ver", {}, function (data, status) {
        $('#tabladiv').html(data);
        $('#tabladiv').show();

    });
}
