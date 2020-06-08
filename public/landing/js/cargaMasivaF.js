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
        uploadUrl:'/subirfoto',
        uploadAsync: true,
        overwriteInitial: false,
        validateInitialCount: true,
        showUpload:true,
        minFileCount:1,
        initialPreviewAsData: true ,// identify if you are sending preview data only and not the markup
        language: 'es',
        showBrowse: true,
        browseOnZoneClick: true,
        theme: "fa",
        uploadExtraData: function() {
            return {
                _token: $("input[name='_token']").val(),
            };
        },
    });
});

