function detalle() {
    var idEmpleado = $('#totalPC').val();
}
$('.detalle').on('click', function () {
    detalle();
    $('#detallesWindows').modal();
});
