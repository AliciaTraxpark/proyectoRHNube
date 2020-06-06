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
        uploadAsync: false,
        overwriteInitial: false,
        validateInitialCount: true,
        showUpload:false,
        minFileCount:6,
        initialPreviewAsData: true ,// identify if you are sending preview data only and not the markup
        language: 'es',
        showBrowse: true,
        browseOnZoneClick: true,
        theme: "fa"
    });
});