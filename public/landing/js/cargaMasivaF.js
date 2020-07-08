$('#cargaMasivaF').click('change', function () {
    $('#modalInformacionF').modal();
});
$('#cerrarIF').click('change', function () {
    $('#modalInformacionF').modal('toggle');
    $('#modalMasivaFoto').modal();
    $("#fileMasiva").val('');
    $("#fileMasiva").fileinput('refresh');
});
$(document).ready(function () {
    $("#fileMasiva").fileinput({
        browseLabel: 'Seleccionar Carpeta...',
        allowedFileExtensions: ['jpg', 'jpeg', 'png'],
        uploadUrl: '/subirfoto',
        uploadAsync: true,
        overwriteInitial: false,
        validateInitialCount: true,
        showUpload: true,
        minFileCount: 1,
        initialPreviewAsData: true, // identify if you are sending preview data only and not the markup
        language: 'es',
        showBrowse: true,
        browseOnZoneClick: true,
        theme: "fa",
        uploadExtraData: function () {
            return {
                _token: $("input[name='_token']").val(),
            };
        }
    }).on('fileuploaded', function (event, previewId, index, fileId) {
        console.log('File Uploaded', 'ID: ' + fileId + ', Thumb ID: ' + previewId);
    }).on('fileuploaderror', function (event, data, msg) {
        console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
        $.notify({
            message: 'File Upload Error' +
                'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId,
            icon: 'admin/images/warning.svg',
        }, {
            element: 'modal',
            icon_type: 'image',
            newest_on_top: true,
            delay: 5000,
            template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
    }).on('filebatchuploadcomplete', function (event, preview, config, tags, extraData) {
        console.log('File Batch Uploaded', preview, config, tags, extraData);
    });
});
