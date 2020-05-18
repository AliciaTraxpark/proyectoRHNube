$(document).ready(function() {
    $('#tablaEmpleado').DataTable({
        "language" :{
            "search" : "Buscar",
            "paginate" : {
                "next" : "Siguiente",
                "previous" : "Anterior"
            },
            "lengthMenu" :'Mostrar <select class="form-control">'+
                        '<option value="10">10</option>'+
                        '<option value="15">15</option>'+
                        '<option value="-1">Todos</option>'+
                        '</select> Empleados'
        },
        "scrollX" : true 
    });
} )