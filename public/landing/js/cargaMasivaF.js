$('#cargaMasivaF').click('change',function(){
    $('#modalInformacionF').modal();
});
$('#cerrarIF').click('change',function(){
    $('#modalInformacionF').modal('toggle');
    $('#modalMasivaFoto').modal();
});
$(document).ready(function() {
    $("#fileMasiva").fileinput({
        browseLabel: 'Seleccionar Carpeta...',
        allowedFileExtensions: ['jpg','jpeg','png'],
        uploadUrl:'/empleadoFoto',
        uploadAsync: false,
        overwriteInitial: false,
        validateInitialCount: true,
        showUpload:true,
        minFileCount:6,
        initialPreviewAsData: true ,// identify if you are sending preview data only and not the markup
        language: 'es',
        showBrowse: true,
        browseOnZoneClick: true,
        theme: "fa"
    });
    $("#fileMasiva").on('fileuploaded',function(event, data, previewId, index){
        var files = [];
        for (var i = 0; i < $(this)[0].files.length; i++) {
            files.Push($(this)[0].files[i].name);
        }
    })
});

