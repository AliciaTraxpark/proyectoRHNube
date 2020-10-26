$('[data-fancybox]').fancybox({
    callbackOnStart: function () { $("#fancy_overlay").bind("click", "null"); },
});
// VIDEO DE REGISTRAR DATOS
function registroDatos() {
    $.fancybox.open({
        src: 'https://player.vimeo.com/video/471447985?title=0&byline=0&portrait=0'
    });
}

// REGISTRAR ORGANIZACION
function registroOrgani() {
    $.fancybox.open({
        src: 'https://player.vimeo.com/video/471448607?title=0&byline=0&portrait=0',
    });
}

// VALIDAR CUENTA
function registroValidaC() {
    $.fancybox.open({
        src: 'https://player.vimeo.com/video/471449241?title=0&byline=0&portrait=0'
    });
}

// PRIMERA CONFIGURACION 
function primeraConfig() {
    $.fancybox.open({
        src: 'https://player.vimeo.com/video/471518323?title=0&byline=0&portrait=0'
    });
}