$("#summernote").summernote({
    lang: "es-ES",
    height: 230, // set editor height
    minHeight: null, // set minimum height of editor
    maxHeight: null, // set maximum height of editor
    focus: true, // set focus to editable area after initializing summernote
    disableDragAndDrop: false,
    codemirror: {
        // codemirror options
        theme: "monokai",
    },
    toolbar: [
        ["font", ["bold", "underline", "clear"]],
        ["fontname", ["fontname"]],
        ["color", ["color"]],
        ["para", ["ul", "ol", "paragraph"]],
        ["table", ["table"]],
        ["insert", ["link"]],
    ],
    popover: {
        table: [
            ["add", ["addRowDown", "addRowUp", "addColLeft", "addColRight"]],
            ["delete", ["deleteRow", "deleteCol", "deleteTable"]],
            ["custom", ["tableStyles"]],
        ],
    },
});
$('#summernote').summernote('fontName', 'Arial');
function validarMensaje() {
    var contenido = $("#summernote").summernote("code");
    console.log($(contenido).text());
    if ($("#summernote").summernote("isEmpty") || $(contenido).text() === null) {
        $.notifyClose();
        $.notify(
            {
                message: "\nFalta redactar mensaje.",
                icon: "admin/images/warning.svg",
            },
            {
                icon_type: "image",
                newest_on_top: true,
                delay: 5000,
                template:
                    '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                    "</div>",
                spacing: 35,
            }
        );
        return false;
    }
}
function disabledS() {
    var contenido = $("#summernote").summernote("code");
    $.ajax({
        async: false,
        type: "post",
        url: "/envioTicketCorreo",
        data: {
            contenido: contenido,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log(data);
            $("#summernote").summernote("destroy");
            $.notifyClose();
            $.notify(
                {
                    message: "\nCorreo enviado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {
            $.notify(
                {
                    message: "\nAún no ha registrado correo a empleado.",
                    icon: "admin/images/warning.svg",
                },
                {
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
    });
}
