// : ******************************************** AGREGAR ÁREA EN EDITAR **************************************
function agregarAreaA() {
    objArea = datosAreaA("POST");
    enviarAreaA('', objArea);
};

function datosAreaA(method) {
    nuevoArea = {
        area_descripcion: $('#textAreaE').val(),
        '_method': method
    }
    return (nuevoArea);
}

function enviarAreaA(accion, objArea) {
    var id = $('#editarA').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/area" + accion,
            data: objArea,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#area').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.area_id,
                    text: data.area_descripcion
                }));
                $('#v_area').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.area_id,
                    text: data.area_descripcion,
                    selected: true
                }));
                $('#v_area').val(data.area_id).trigger("change");//lo selecciona
                $('#textAreaE').val('');
                $('#editarAreaA').hide();
                limpiarEditar();
                $('#areamodalE').modal('toggle');
                $('#form-ver').modal('show');
                $.notify({
                    message: "\nÁrea Registrada\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2  text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            },
            error: function () { }
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarArea" + accion,
            data: {
                id: id,
                objArea
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#area').empty();
                $('#v_area').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/area",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].area_id}">${data[i].area_descripcion}</option>`;
                        }
                        $('#area').append(select);
                        $('#v_area').append(select);
                    },
                    error: function () { }
                });
                $('#v_area').val(data.area_id).trigger("change");//lo selecciona
                $('#textAreaE').val('');
                $('#editarAreaA').hide();
                limpiarEditar();
                $('#areamodalE').modal('toggle');
                $('#form-ver').modal('show');
                $.notify({
                    message: "\nÁrea Modificada\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
// : ******************************************** AGREGAR CARGO EN EDITAR **************************************
function agregarcargoA() {
    objCargo = datosCargoA("POST");
    enviarCargoA('', objCargo);
};

function datosCargoA(method) {
    nuevoCargo = {
        cargo_descripcion: $('#textCargoE').val(),
        '_method': method
    }
    return (nuevoCargo);
}

function enviarCargoA(accion, objCargo) {
    var id = $('#editarC').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/cargo" + accion,
            data: objCargo,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#cargo').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.cargo_id,
                    text: data.cargo_descripcion
                }));
                $('#v_cargo').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.cargo_id,
                    text: data.cargo_descripcion,
                    selected: true
                }));
                $('#v_cargo').val(data.cargo_id).trigger("change"); //lo selecciona
                $('#textCargoE').val('');
                $('#editarCargoA').hide();
                limpiarEditar();
                $('#cargomodalE').modal('toggle');
                $('#form-ver').modal('show');
                $.notify({
                    message: "\nCargo Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            },
            error: function () { }
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarCargo" + accion,
            data: {
                id: id,
                objCargo
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#cargo').empty();
                $('#v_cargo').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/cargo",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {

                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].cargo_id}">${data[i].cargo_descripcion}</option>`;
                        }
                        $('#cargo').append(select);
                        $('#v_cargo').append(select);
                    },
                    error: function () { }
                });
                $('#v_cargo').val(data.cargo_id).trigger("change"); //lo selecciona
                $('#textCargoE').val('');
                $('#editarCargoA').hide();
                limpiarEditar();
                $('#cargomodalE').modal('toggle');
                $('#form-ver').modal('show');
                $.notify({
                    message: "\nCargo Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}

// : ******************************************** AGREGAR CENTRO DE COSTO EN EDITAR **************************************
function agregarcentroA() {
    objCentroC = datosCentroA("POST");
    enviarCentroA('', objCentroC);
};

function datosCentroA(method) {
    nuevoCentro = {
        centroC_descripcion: $('#textCentroE').val(),
        '_method': method
    }
    return (nuevoCentro);
}

function enviarCentroA(accion, objCentroC) {
    var id = $('#editarCC').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/centro" + accion,
            data: objCentroC,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                if (data.respuesta == undefined) {
                    $('#centroc').append($('<option>', { //agrego los valores que obtengo de una base de datos
                        value: data.centroC_id,
                        text: data.centroC_descripcion
                    }));
                    $('#v_centroc').append($('<option>', { //agrego los valores que obtengo de una base de datos
                        value: data.centroC_id,
                        text: data.centroC_descripcion,
                        selected: true
                    }));
                    var idCC = [];
                    idCC = $('#v_centroc').val();
                    idCC.push(data.centroC_id);
                    $('#v_centroc').val(idCC).trigger("change"); //lo selecciona
                    $('#textCentroE').val('');
                    $('#editarCentroA').hide();
                    limpiarEditar();
                    $('#centrocmodalE').modal('toggle');
                    $('#form-ver').modal('show');
                    $.notifyClose();
                    $.notify({
                        message: "\nCentro Costo Registrado\n",
                        icon: 'admin/images/checked.svg'
                    }, {
                        element: $('#form-ver'),
                        position: 'fixed',
                        icon_type: 'image',
                        newest_on_top: true,
                        delay: 5000,
                        template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            '</div>',
                        spacing: 35
                    });
                } else {
                    $.notifyClose();
                    $.notify(
                        {
                            message: data.mensaje,
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#centrocmodalE'),
                            position: "fixed",
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "center",
                            },
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 2000,
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
                }
            },
            error: function () { }
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarCentro" + accion,
            data: {
                id: id,
                objCentroC
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                if (data.respuesta == undefined) {
                    var idCC = [];
                    idCC = $('#v_centroc').val();
                    if (!idCC.includes(data.centroC_id)) {
                        idCC.push(data.centroC_id);
                    }
                    $('#centroc').empty();
                    $('#v_centroc').empty();
                    var select = "";
                    $.ajax({
                        async: false,
                        type: "GET",
                        url: "/centro",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            select += `<option value="">Seleccionar</option>`;
                            for (var i = 0; i < data.length; i++) {
                                select += `<option class="" value="${data[i].centroC_id}">${data[i].centroC_descripcion}</option>`;
                            }
                            $('#centroc').append(select);
                            $('#v_centroc').append(select);
                        },
                        error: function () { }
                    });
                    $('#v_centroc').val(idCC).trigger("change"); //lo selecciona
                    $('#textCentroE').val('');
                    $('#editarCentroA').hide();
                    limpiarEditar();
                    $('#centrocmodalE').modal('toggle');
                    $('#form-ver').modal('show');
                    $.notifyClose();
                    $.notify({
                        message: "\nCentro Costo Modificado\n",
                        icon: 'admin/images/checked.svg'
                    }, {
                        element: $('#form-ver'),
                        position: 'fixed',
                        icon_type: 'image',
                        newest_on_top: true,
                        delay: 5000,
                        template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            '</div>',
                        spacing: 35
                    });
                } else {
                    $.notifyClose();
                    $.notify(
                        {
                            message: data.mensaje,
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#centrocmodalE'),
                            position: "fixed",
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "center",
                            },
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 2000,
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
                }
            }
        });
    }
}
// : ******************************************** AGREGAR LOCAL EN EDITAR **************************************
function agregarlocalA() {
    objLocal = datosLocalA("POST");
    enviarLocalA('', objLocal);
};

function datosLocalA(method) {
    nuevoLocal = {
        local_descripcion: $('#textLocalE').val(),
        '_method': method
    }
    return (nuevoLocal);
}

function enviarLocalA(accion, objLocal) {
    var id = $('#editarL').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/local" + accion,
            data: objLocal,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#local').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.local_id,
                    text: data.local_descripcion
                }));
                $('#v_local').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.local_id,
                    text: data.local_descripcion,
                    selected: true
                }));
                $('#v_local').val(data.local_id).trigger("change"); //lo selecciona
                $('#textLocalE').val('');
                $('#editarLocalA').hide();
                limpiarEditar();
                $('#localmodalE').modal('toggle');
                $('#form-ver').modal('show');
                $.notify({
                    message: "\nLocal Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });

            },
            error: function () { }
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarLocal" + accion,
            data: {
                id: id,
                objLocal
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#local').empty();
                $('#v_local').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/local",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].local_id}">${data[i].local_descripcion}</option>`;
                        }
                        $('#local').append(select);
                        $('#v_local').append(select);
                    },
                    error: function () { }
                });
                $('#v_local').val(data.local_id).trigger("change"); //lo selecciona
                $('#textLocalE').val('');
                $('#editarLocalA').hide();
                limpiarEditar();
                $('#localmodalE').modal('toggle');
                $('#form-ver').modal('show');
                $.notify({
                    message: "\nLocal Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
// : ******************************************** AGREGAR NIVEL EN EDITAR **************************************
function agregarnivelA() {
    objNivel = datosNivelA("POST");
    enviarNivelA('', objNivel);
};

function datosNivelA(method) {
    nuevoNivel = {
        nivel_descripcion: $('#textNivelE').val(),
        '_method': method
    }
    return (nuevoNivel);
}

function enviarNivelA(accion, objNivel) {
    var id = $('#editarN').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/nivel" + accion,
            data: objNivel,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#nivel').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.nivel_id,
                    text: data.nivel_descripcion
                }));
                $('#v_nivel').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.nivel_id,
                    text: data.nivel_descripcion,
                    selected: true
                }));
                $('#v_nivel').val(data.nivel_id).trigger("change"); //lo selecciona
                $('#textNivelE').val('');
                $('#editarNivelA').hide();
                limpiarEditar();
                $('#nivelmodalE').modal('toggle');
                $('#form-ver').modal('show');
                $.notify({
                    message: "\nNivel Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });

            },
            error: function () { }
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarNivel" + accion,
            data: {
                id: id,
                objNivel
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#nivel').empty();
                $('#v_nivel').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/nivel",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].nivel_id}">${data[i].nivel_descripcion}</option>`;
                        }
                        $('#nivel').append(select);
                        $('#v_nivel').append(select);
                    },
                    error: function () { }
                });
                $('#v_nivel').val(data.nivel_id).trigger("change"); //lo selecciona
                $('#textNivelE').val('');
                $('#editarNivelA').hide();
                limpiarEditar();
                $('#nivelmodalE').modal('toggle');
                $('#form-ver').modal('show');
                $.notify({
                    message: "\nNivel Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
// : ******************************************** AGREGAR CONTRATO EN EDITAR **************************************
function agregarContratoA() {
    objContrato = datosContratoA("POST");
    enviarContratoA('', objContrato);
}
function datosContratoA(method) {
    nuevoContrato = {
        contrato_descripcion: $('#textContratoE').val(),
        '_method': method
    }
    return (nuevoContrato);
}
function enviarContratoA(accion, objContrato) {
    var id = $('#editarCO').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/contrato" + accion,
            data: objContrato,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#contrato').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.contrato_id,
                    text: data.contrato_descripcion
                }));
                $('#v_contratoN').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.contrato_id,
                    text: data.contrato_descripcion,
                    selected: true
                }));
                $('#v_contratoND').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.contrato_id,
                    text: data.contrato_descripcion,
                    selected: true
                }));
                $('#v_contratoN').val(data.contrato_id).trigger("change"); //lo selecciona
                $('#v_contratoND').val(data.contrato_id).trigger("change"); //lo selecciona
                $('#textContratoE').val('');
                $('#editarContratoA').hide();
                limpiarEditar();
                $('#contratomodalE').modal('toggle');
                ModalCerrarCondicion();
                $.notify({
                    message: "\nContrato Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: modalCPEdit(),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            },
            error: function () { }
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarContrato" + accion,
            data: {
                id: id,
                objContrato
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#contrato').empty();
                $('#v_contratoN').empty();
                $('#v_contratoND').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/contrato",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].contrato_id}">${data[i].contrato_descripcion}</option>`;
                        }
                        $('#contrato').append(select);
                        $('#v_contratoN').append(select);
                        $('#v_contratoND').append(select);
                    },
                    error: function () { }
                });
                $('#v_contratoN').val(data.contrato_id).trigger("change");//lo selecciona
                $('#v_contratoND').val(data.contrato_id).trigger("change");//lo selecciona
                $('#textContratoE').val('');
                $('#editarContratoA').hide();
                limpiarEditar();
                $('#contratomodalE').modal('toggle');
                ModalCerrarCondicion();
                $.notify({
                    message: "\nContrato Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: modalCPEdit(),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
// : ******************************************** AGREGAR CONDICION DE PAGO EN EDITAR **************************************
function agregarCondicionA() {
    objCondicion = datosCondicionA("POST");
    enviarCondicionA('', objCondicion);
};

function datosCondicionA(method) {
    nuevoCondicion = {
        condicion: $('#textCondicionE').val(),
        '_method': method
    }
    return (nuevoCondicion);
}
function enviarCondicionA(accion, objCondicion) {
    var id = $('#editarCO').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/condicion" + accion,
            data: objCondicion,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#condicion').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.id,
                    text: data.condicion
                }));
                $('#v_condicion').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.id,
                    text: data.condicion,
                    selected: true
                }));
                $('#v_condicionN').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.id,
                    text: data.condicion,
                    selected: true
                }));
                $('#v_condicionND').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.id,
                    text: data.condicion,
                    selected: true
                }));
                $('#v_condicion').val(data.id).trigger("change"); //lo selecciona
                $('#v_condicionN').val(data.id).trigger("change"); //lo selecciona
                $('#v_condicionND').val(data.id).trigger("change"); //lo selecciona
                $('#textContratoE').val('');
                $('#editarCondicionA').hide();
                limpiarEditar();
                $('#condicionmodalE').modal('toggle');
                ModalCerrarCondicion();
                $.notify({
                    message: "\nCondición de Pago Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: modalCPEdit(),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            },
            error: function () { }
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarCondicion" + accion,
            data: {
                id: id,
                objCondicion
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $('#condicion').empty();
                $('#v_condicion').empty();
                $('#v_condicionN').empty();
                $('#v_condicionND').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/contrato",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].id}">${data[i].condicion}</option>`;
                        }
                        $('#condicion').append(select);
                        $('#v_condicion').append(select);
                        $('#v_condicionN').append(select);
                        $('#v_condicionND').append(select);
                    },
                    error: function () { }
                });
                $('#v_condicion').val(data.id).trigger("change"); //lo selecciona
                $('#v_condicionN').val(data.id).trigger("change"); //lo selecciona
                $('#v_condicionND').val(data.id).trigger("change"); //lo selecciona
                $('#textCondicionE').val('');
                $('#editarCondicionA').hide();
                limpiarEditar();
                $('#condicionmodalE').modal('toggle');
                ModalCerrarCondicion();
                $.notify({
                    message: "\nContrato Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: modalCPEdit(),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
$('#btnCerrar').on("click", function () {
    $('#form-ver').modal('show');
});
// : ******************************************** BOTON DE BUSCAR EN EDITAR **************************************
// ? ÁREA
$('#buscarAreaA').on("click", function () {
    $('#editarArea').empty();
    var container = $('#editarAreaA');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/area",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="area" id="editarA">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].area_id}">${data[i].area_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarA').on("change", function () {
                var id = $('#editarA').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarArea",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textAreaE').val(data);
                    },
                    error: function () {
                        $('#textAreaE').val("");
                    }
                })
            });
        },
        error: function () { }
    });
    $('#editarAreaA').show();
});
// ? CARGO
$('#buscarCargoA').on("click", function () {
    $('#editarCargoA').empty();
    var container = $('#editarCargoA');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/cargo",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="cargo" id="editarC">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].cargo_id}">${data[i].cargo_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarC').on("change", function () {
                var id = $('#editarC').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarCargo",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textCargoE').val(data);
                    },
                    error: function () {
                        $('#textCargoE').val("");
                    }
                })
            });
        },
        error: function () { }
    });
    $('#editarCargoA').show();
});
// ? CENTRO DE COSTO
$('#buscarCentroA').on("click", function () {
    $('#editarCentroA').empty();
    var container = $('#editarCentroA');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/centroPEditar",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="centro" id="editarCC">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].centroC_id}">${data[i].centroC_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarCC').on("change", function () {
                var id = $('#editarCC').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarCentro",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textCentroE').val(data);
                    },
                    error: function () {
                        $('#textCentroE').val("");
                    }
                })
            });
        },
        error: function () { }
    });
    $('#editarCentroA').show();
});
// ? LOCAL
$('#buscarLocalA').on("click", function () {
    $('#editarLocalA').empty();
    var container = $('#editarLocalA');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/local",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="Local" id="editarL">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].local_id}">${data[i].local_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarL').on("change", function () {
                var id = $('#editarL').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarLocal",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textLocalE').val(data);
                    },
                    error: function () {
                        $('#textLocalE').val("");
                    }
                })
            });
        },
        error: function () { }
    });
    $('#editarLocalA').show();
});
// ? NIVEL
$('#buscarNivelA').on("click", function () {
    $('#editarNivelA').empty();
    var container = $('#editarNivelA');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/nivel",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="nivel" id="editarN">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].nivel_id}">${data[i].nivel_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarN').on("change", function () {
                var id = $('#editarN').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarNivel",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textNivelE').val(data);
                    },
                    error: function () {
                        $('#textNivelE').val("");
                    }
                })
            });
        },
        error: function () { }
    });
    $('#editarNivelA').show();
});
// ? CONTRATO
$('#buscarContratoA').on("click", function () {
    $('#editarContratoA').empty();
    var container = $('#editarContratoA');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/contrato",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="contrato" id="editarCO">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].contrato_id}">${data[i].contrato_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarCO').on("change", function () {
                var id = $('#editarCO').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarContrato",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textContratoE').val(data);
                    },
                    error: function () {
                        $('#textContratoE').val("");
                    }
                })
            });
        },
        error: function () { }
    });
    $('#editarContratoA').show();
});
// ? CONDICIÓN
$('#buscarCondicionA').on("click", function () {
    $('#editarCondicionA').empty();
    var container = $('#editarCondicionA');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/condicion",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="contrato" id="editarCP">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].id}">${data[i].condicion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarCP').on("change", function () {
                var id = $('#editarCP').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarCondicion",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textCondicionE').val(data);
                    },
                    error: function () {
                        $('#textCondicionE').val("");
                    }
                })
            });
        },
        error: function () { }
    });
    $('#editarContratoC').show();
});
// ? LIMPIAR
function limpiarEditar() {
    $('#editarAreaA').hide();
    $('#editarCargoA').hide();
    $('#editarCentroA').hide();
    $('#editarLocalA').hide();
    $('#editarNivelA').hide();
    $('#editarContratoA').hide();
    $('#editarCondicionA').hide();
    $('#textAreaE').val("");
    $('#textCargoE').val("");
    $('#textCentroE').val("");
    $('#textLocalE').val("");
    $('#textNivelE').val("");
    $('#textContratoE').val("");
    $('#textCondicionE').val("");
    $('#editarA').val("");
    $('#editarC').val("");
    $('#editarCC').val("");
    $('#editarL').val("");
    $('#editarN').val("");
    $('#editarCO').val("");
    $('#editarCP').val("");
}
