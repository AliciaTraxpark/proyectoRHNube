var estado = true;
$("#summernote").summernote({
    lang: "es-ES",
    height: 230, // set editor height
    minHeight: null, // set minimum height of editor
    maxHeight: null, // set maximum height of editor
    focus: true, // set focus to editable area after initializing summernote
    disableDragAndDrop: false,
    fontNamesIgnoreCheck: ["Arial"],
    codeviewFilter: false,
    codeviewIframeFilter: true,
    toolbar: [
        ["font", ["bold", "italic", "underline", "strikethrough", "clear"]],
        ["fontsize", ["fontsize"]],
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
    callbacks: {
        onChange: function (contents) {
            obteniendoValor = $("#summernote").summernote("isEmpty");
            if (obteniendoValor === false) {
                $("#mostrarBoton").show();
            } else {
                $("#mostrarBoton").hide();
            }
        },
    },
});
function disabledS() {
    var contenido = $("#summernote").summernote("code");
    var asunto = $("#asunto").val();
    $.ajax({
        async: false,
        type: "post",
        url: "/envioTicketCorreo",
        data: {
            contenido: contenido,
            asunto: asunto,
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
