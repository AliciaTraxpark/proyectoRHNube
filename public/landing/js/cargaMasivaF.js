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
        browseLabel: 'Seleccionar',
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
    }).on('fileuploaded', function (event, index, data, msg) {
        $.notify({
            message: "\nFoto registrada\n" + data,
            icon: 'admin/images/checked.svg'
        }, {
            element: $('#modalMasivaFoto'),
            position: 'fixed',
            icon_type: 'image',
            allow_dismiss: true,
            newest_on_top: true,
            delay: 13000,
            template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
    }).on('fileuploaderror', function (event, index, data, msg) {
        console.log(data);
        $.notify({
            message: 'Error:\n' + data,
            icon: 'landing/images/warning (1).svg',
        }, {
            element: $('#modalMasivaFoto'),
            position: 'fixed',
            icon_type: 'image',
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
    });
});
