$(document).ready(function() {
    $('#form-ver').hide();
    leertabla();
});
function leertabla() {
    $.get("tablaempleado/ver", {}, function (data, status) {
        $('#tabladiv').html(data);
        $("#tablaEmpleado").DataTable({    
            responsive: true,
            language :
            {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ ",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     ">",
                    "sPrevious": "<"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    var input = $('<input type="radio" id="1">')
                        .appendTo($(column.header()))
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                                column
                                .search(this.value)
                                .draw();
                        });
                        //Este codigo sirve para que no se active el ordenamiento junto con el filtro
                    $(input).click(function(e) {
                        e.stopPropagation();
                    });
                });
            },
            "aoColumnDefs": [
             { "bSearchable": false, "aTargets": [ 1 ] }
           ] 
        });
    });
}
$("#tablaEmpleado tr").click(function(){
    $(this).addClass('selected').siblings().removeClass('selected');
    var value=$(this).find('td:first').html();
    alert(value);
 });

 $('.ok').on('click', function(e){
     alert($("#tablaEmpleado tr.selected td:first").html());
 });