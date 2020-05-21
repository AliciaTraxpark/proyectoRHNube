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
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                },
            }
        );



    });;
}
