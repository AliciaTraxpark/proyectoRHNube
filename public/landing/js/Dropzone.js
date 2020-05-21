Dropzone.options.Dropzone={
    autoProcessQueue:false
};
$("div#Dropzone").dropzone({
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 2, // MB
    url: "/empleado/store",
    headers: {
        'X-CSRF-Token': $('input[name="authenticity_token"]').val()
    },
    sending: function(file, xhr, formData) {
        formData.append("_token", $('[name=_token').val());
        formData.append("nombres",$('#nombres').val());
        formData.append("apPaterno",$('#apPaterno').val());
        formData.append("apMaterno",$('#apMaterno').val());
        formData.append("fechaN",$('#fechaN').val());
        formData.append("tipo",$('#tipo').val());
        formData.append("documento",$('#documento').val());
        formData.append("numDocumento",$('#numDocumento').val());
        formData.append("departamento",$('#departamento').val());
        formData.append("provincia",$('#provincia').val());
        formData.append("distrito",$('#distrito').val());
        formData.append("cargo",$('#cargo').val());
        formData.append("area",$('#area').val());
        formData.append("centroc",$('#centroc').val());
        formData.append("dep",$('#dep').val());
        formData.append("prov",$('#prov').val());
        formData.append("dist",$('#dist').val());
        formData.append("contrato",$('#contrato').val());
        formData.append("direccion",$('#direccion').val());
        formData.append("nivel",$('#nivel').val());
        formData.append("local",$('#local').val());
    },
    success:function(msg){
        leertabla();



    },
    acceptedFiles: ".jpeg,.jpg,.png"
});
Dropzone.options.Dropzone = false;
function leertabla() {
    $.get("tablaempleado/ver", {}, function (data, status) {
        $('#tabladiv').html(data);



        $("#tablaEmpleado").DataTable(
            {
                responsive: true,
                   "language" :    {
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
            }
            }
        );



    });;
}
