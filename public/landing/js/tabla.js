$(document).ready(function() {
    $('#tablaEmpleado').DataTable({
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "scrollX" : true ,
        "select" : true,
        "tableTools": {
             "sRowSelect": "single" 
        }
    });
} )