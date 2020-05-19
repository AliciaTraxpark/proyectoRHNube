$(document).ready(function() {
    $('#tablaEmpleado').DataTable({
        "language" :{
            "search" : "Buscar",
            "info" : "_TOTAL_ empleados",
            "infoEmpty": "Mostrando 0.",
            "infoFiltered": "(filtrados de un total de _MAX_ empleados)",
            "zeroRecords": "No se han encontrado coincidencias.",
            "paginate" : {
                "next" : "Siguiente",
                "previous" : "Anterior"
            },
            "lengthMenu" :'Mostrar <select class="form-control">'+
                        '<option value="10">10</option>'+
                        '<option value="15">15</option>'+
                        '<option value="-1">Todos</option>'+
                        '</select> Empleados',
            "select": {
            "rows": ""
        }
        },
        "scrollX" : true ,
        "select" : true,
        "tableTools": {
             "sRowSelect": "single" 
        }
    });
} )