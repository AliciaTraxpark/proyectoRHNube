//************* */
$("#checkboxFechaI").on("click", function () {
    if ($("#checkboxFechaI").is(':checked')) {
        $('#ocultarFecha > .combodate').hide();
        $('#labelfechaF').hide();
        $('#m_fechaF').combodate("clearValue");
    } else {
        $('#labelfechaF').show();
        $('#ocultarFecha > .combodate').show();
    }
});
////////////////
$("#file").fileinput({
    allowedFileExtensions: ['jpg', 'jpeg', 'png'],
    uploadAsync: false,
    showRemove: true,
    minFileCount: 0,
    maxFileCount: 1,
    initialPreviewAsData: true, // identify if you are sending preview data only and not the markup
    language: 'es',
    browseOnZoneClick: true,
    theme: "fa",
    showUpload: false,
    showBrowse: false
});
$('#fechaN').combodate({
    minYear: 1960,
    yearDescending: false,
});
$('#m_fechaI').combodate({
    value: new Date(),
    minYear: 2000,
    maxYear: moment().format('YYYY') + 1,
    yearDescending: false
});
$('#m_fechaF').combodate({
    minYear: 2014,
    maxYear: moment().format('YYYY') + 1,
    yearDescending: false,
});
$('#v_fechaN').combodate({
    minYear: 1900,
    yearDescending: false,
});
//AREA
function agregarArea() {
    objArea = datosArea("POST");
    enviarArea('', objArea);
};

function datosArea(method) {
    nuevoArea = {
        area_descripcion: $('#textArea').val().toUpperCase(),
        '_method': method
    }
    return (nuevoArea);
}

function enviarArea(accion, objArea) {
    var id = $('#editarA').val();
    if (id == '') {
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
                    text: data.area_descripcion,
                    selected: true
                }));
                $('#v_area').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.area_id,
                    text: data.area_descripcion,
                    selected: true
                }));
                $('#area').val(data.area_id).trigger("change"); //lo selecciona
                $('#v_area').val(data.area_id).trigger("change");
                $('#textArea').val('');
                $('#editarArea').hide();
                $('#areamodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nÁrea Registrada\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
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
            },
            error: function () {}
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
                console.log(data);
                $('#area').append($(`<option>`, { //agrego los valores que obtengo de una base de datos
                    value: data.area_id,
                    text: data.area_descripcion,
                    selected: true
                }, `</option>`));
                $('#area').val(data.area_id).trigger("change"); //lo selecciona
                $('#v_area').val(data.area_id).trigger("change");
                $('#textArea').val('');
                $('#editarArea').hide();
                $('#areamodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nÁrea Registradaa\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
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
///CARGO
function agregarcargo() {
    objCargo = datosCargo("POST");
    enviarCargo('', objCargo);
};

function datosCargo(method) {
    nuevoCargo = {
        cargo_descripcion: $('#textCargo').val().toUpperCase(),
        '_method': method
    }
    return (nuevoCargo);
}

function enviarCargo(accion, objCargo) {
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
                text: data.cargo_descripcion,
                selected: true
            }));
            $('#v_cargo').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.cargo_id,
                text: data.cargo_descripcion,
                selected: true
            }));
            $('#cargo').val(data.cargo_id).trigger("change"); //lo selecciona
            $('#v_cargo').val(data.cargo_id).trigger("change"); //lo selecciona
            $('#textCargo').val('');
            $('#cargomodal').modal('toggle');
            $('#form-registrar').modal('show');
            $.notify("Cargo registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });
        },
        error: function () {}
    });
}

//centro costo
function agregarcentro() {
    objCentroC = datosCentro("POST");
    enviarCentro('', objCentroC);
};

function datosCentro(method) {
    nuevoCentro = {
        centroC_descripcion: $('#textCentro').val().toUpperCase(),
        '_method': method
    }
    return (nuevoCentro);
}

function enviarCentro(accion, objCentroC) {
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
            $('#centroc').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.centroC_id,
                text: data.centroC_descripcion,
                selected: true
            }));
            $('#v_centroc').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.centroC_id,
                text: data.centroC_descripcion,
                selected: true
            }));
            $('#centroc').val(data.centroC_id).trigger("change"); //lo selecciona
            $('#v_centroc').val(data.centroC_id).trigger("change"); //lo selecciona
            $('#textCentro').val('');
            $('#centrocmodal').modal('toggle');
            $('#form-registrar').modal('show');
            $.notify("Centro de costo registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });
        },
        error: function () {}
    });
}
//LOCAL
function agregarlocal() {
    objLocal = datosLocal("POST");
    enviarLocal('', objLocal);
};

function datosLocal(method) {
    nuevoLocal = {
        local_descripcion: $('#textLocal').val().toUpperCase(),
        '_method': method
    }
    return (nuevoLocal);
}

function enviarLocal(accion, objLocal) {
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
                text: data.local_descripcion,
                selected: true
            }));
            $('#v_local').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.local_id,
                text: data.local_descripcion,
                selected: true
            }));
            $('#local').val(data.local_id).trigger("change"); //lo selecciona
            $('#v_local').val(data.local_id).trigger("change"); //lo selecciona
            $('#textLocal').val('');
            $('#localmodal').modal('toggle');
            $('#form-registrar').modal('show');
            $.notify("local registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {}
    });
}
//NIVEL
function agregarnivel() {
    objNivel = datosNivel("POST");
    enviarNivel('', objNivel);
};

function datosNivel(method) {
    nuevoNivel = {
        nivel_descripcion: $('#textNivel').val().toUpperCase(),
        '_method': method
    }
    return (nuevoNivel);
}

function enviarNivel(accion, objNivel) {
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
                text: data.nivel_descripcion,
                selected: true
            }));
            $('#v_nivel').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.nivel_id,
                text: data.nivel_descripcion,
                selected: true
            }));
            $('#nivel').val(data.nivel_id).trigger("change"); //lo selecciona
            $('#v_nivel').val(data.nivel_id).trigger("change"); //lo selecciona
            $('#textNivel').val('');
            $('#nivelmodal').modal('toggle');
            $('#form-registrar').modal('show');
            $.notify("nivel registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {}
    });
}

//CONTRATO
function agregarContrato() {
    objContrato = datosContrato("POST");
    enviarContrato('', objContrato);
};

function datosContrato(method) {
    nuevoContrato = {
        contrato_descripcion: $('#textContrato').val().toUpperCase(),
        '_method': method
    }
    return (nuevoContrato);
}

function enviarContrato(accion, objContrato) {
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
                text: data.contrato_descripcion,
                selected: true
            }));
            $('#contrato').val(data.contrato_id).trigger("change"); //lo selecciona
            $('#textcontrato').val('');
            $('#contratomodal').modal('toggle');
            $('#form-registrar').modal('show');
            $.notify("Contrato registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {}
    });
}
//FECHAS
function agregarFechas() {
    $('#form-registrar').modal('show');
    fechaI = $('#m_fechaI').val();
    fechaF = $('#m_fechaF').val();
    $('#c_fechaI').text(fechaI);
    $('#c_fechaF').text(fechaF);
    $('#fechasmodal').modal('toggle');
}
//EMPLEADO
$('#guardarEmpleado').click(function () {
    objEmpleado = datosPersona("POST");
    enviarEmpleado('', objEmpleado);
});


function datosPersona(method) {
    nuevoEmpleado = {
        nombres: $('#nombres').val(),
        apPaterno: $('#apPaterno').val(),
        apMaterno: $('#apMaterno').val(),
        fechaN: $('#fechaN').val(),
        tipo: $('input:radio[name=tipo]:checked').val(),
        documento: $('#documento').val(),
        numDocumento: $('#numDocumento').val(),
        departamento: $('#departamento').val(),
        provincia: $('#provincia').val(),
        distrito: $('#distrito').val(),
        cargo: $('#cargo').val(),
        area: $('#area').val(),
        centroc: $('#centroc').val(),
        dep: $('#dep').val(),
        prov: $('#prov').val(),
        dist: $('#dist').val(),
        contrato: $('#contrato').val(),
        direccion: $('#direccion').val(),
        nivel: $('#nivel').val(),
        local: $('#local').val(),
        celular: $('#celular').val(),
        telefono: $('#telefono').val(),
        fechaI: $('#m_fechaI').val(),
        fechaF: $('#m_fechaF').val(),
        correo: $('#email').val(),
        codigoEmpleado: $('#codigoEmpleado').val(),
        '_method': method
    }
    return (nuevoEmpleado);
}

function enviarEmpleado(accion, objEmpleado) {

    var formData = new FormData();
    formData.append('file', $('#file').prop('files')[0]);
    formData.append('objEmpleado', JSON.stringify(objEmpleado));
    $.ajax({

        type: "POST",
        url: "/empleado/store" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
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
        success: function (msg) {
            leertabla();
            $('#smartwizard').smartWizard("reset");
            $('#formNuevoEd').hide();
            $('#formNuevoEl').hide();
            $('input[type="text"]').val("");
            $('input:radio[name=tipo]:checked').prop('checked', false);
            $('input[type="date"]').val("");
            $('input[type="file"]').val("");
            $('input[type="email"]').val("");
            $('select').val("");
            $("#form-registrar :input").prop('disabled', true);
            $('#documento').attr('disabled', false);
            $('#cerrarMoadalEmpleado').attr('disabled', false);
            $('#m_fechaI').combodate("clearValue");
            $('#m_fechaF').combodate("clearValue");
            $('#detalleContrato').hide();
            $('#checkboxFechaI').prop('checked', false);
            $('#form-registrar').modal('toggle');
            $.notify({
                message: "\nEmpleado Registrado.",
                icon: 'admin/images/checked.svg'
            }, {
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
        },
        error: function (data, errorThrown) {}
    });
}

//EMPLEADO ACTUALIZAR
$("#checkboxFechaIE").on("click", function () {
    if ($("#checkboxFechaIE").is(':checked')) {
        $('#m_fechaFE').combodate("clearValue");
        $('#ocultarFechaE > .combodate').hide();
        $('#ocultarFechaE').hide();
    } else {
        $('#ocultarFechaE').show();
        $('#ocultarFechaE > .combodate').show();
    }
});
$('#actualizarEmpleado').click(function () {
    idE = $('#v_id').val();
    objEmpleadoA = datosPersonaA("PUT");
    actualizarEmpleado('/' + idE, objEmpleadoA);
});


function datosPersonaA(method) {
    console.log($('#m_fechaFE').val());
    nuevoEmpleadoA = {
        nombres_v: $('#v_nombres').val(),
        apPaterno_v: $('#v_apPaterno').val(),
        apMaterno_v: $('#v_apMaterno').val(),
        fechaN_v: $('#v_fechaN').val(),
        tipo_v: $('input:radio[name=v_tipo]:checked').val(),
        departamento_v: $('#v_departamento').val(),
        provincia_v: $('#v_provincia').val(),
        distrito_v: $('#v_distrito').val(),
        cargo_v: $('#v_cargo').val(),
        area_v: $('#v_area').val(),
        centroc_v: $('#v_centroc').val(),
        dep_v: $('#v_dep').val(),
        prov_v: $('#v_prov').val(),
        dist_v: $('#v_dist').val(),
        contrato_v: $('#v_contrato').val(),
        direccion_v: $('#v_direccion').val(),
        nivel_v: $('#v_nivel').val(),
        local_v: $('#v_local').val(),
        celular_v: $('#v_celular').val(),
        telefono_v: $('#v_telefono').val(),
        correo_v: $('#v_email').val(),
        fechaI_v: $('#m_fechaIE').val(),
        fechaF_v: $('#m_fechaFE').val(),
        codigoEmpleado_v: $('#v_codigoEmpleado').val(),
        '_method': method
    }
    return (nuevoEmpleadoA);
}

function actualizarEmpleado(accion, objEmpleadoA) {

    var formDataA = new FormData();
    formDataA.append('file', $('#file2').prop('files')[0]);
    formDataA.append('objEmpleadoA', JSON.stringify(objEmpleadoA));
    console.log(objEmpleadoA);
    $.ajax({

        type: "POST",
        url: "/empleadoA" + accion,
        data: formDataA,
        contentType: false,
        processData: false,
        dataType: "json",
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (msg) {
            leertabla();
            $('#smartwizard').smartWizard("reset");
            $('#navActualizar').hide();
            $('input[type="file"]').val("");
            $('input[typt="checkbox"]').val("");
            $('#formNuevoEd').hide();
            $('#formNuevoEl').hide();
            $('#checkboxFechaIE').prop('checked', false);
            $('#form-ver').modal('toggle');
            $.notify({
                message: "\nEmpleado Actualizado.",
                icon: 'admin/images/checked.svg'
            }, {
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
        },
        error: function (data, errorThrown) {
            alert("Hay un error");
            console.log(formDataA.get('objEmpleadoA'));
        }
    });
}
///ELIMINAR EMPLEADO

//abrir nuevo form
function abrirnuevo() {
    $('#form-ver').hide();
    $('#tablaEmpleado tbody tr').removeClass('selected');
    $('#form-registrar').smartWizard("reset");
    $('input[type="text"]').val("");
    $('input:radio[name=tipo]:checked').prop('checked', false);
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('select').val("");
    $('#form-registrar').show();
}

//eliminar foto
function cargarFile2() {
    $("#file2").fileinput({
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        uploadAsync: false,
        overwriteInitial: false,
        showUpload: false,
        validateInitialCount: true,
        showRemove: true,
        minFileCount: 0,
        maxFileCount: 1,
        ...(hayFoto && {
            initialPreview: [
                "<img  id=v_foto src='{{asset('/fotosEmpleado')}}/'" +
                urlFoto + " style='max-width:200px; max-height:200px; height:auto; width:auto'>"
            ],
            initialPreviewConfig: [{
                width: "200px",
                height: "200px",
                url: "/eliminarFoto/" + id_empleado,
                showDelete: true,
                key: id_empleado
            }]
        }),
        language: 'es',
        deleteExtraData: {
            _token: $("#csrf_token").val()
        },
        showBrowse: false,
        browseOnZoneClick: true,
        theme: "fa",
        fileActionSettings: {
            "showDrag": false,
            'showZoom': false
        },
    })
}
//********************** */
$('#documento').on('change', function () {
    $("#form-registrar :input").attr('disabled', false);
});
$("#form-registrar :input").prop('disabled', true);
$('#documento').attr('disabled', false);
$('#cerrarModalEmpleado').attr('disabled', false);
$('#cerrarE').attr('disabled', false);
$('#cerrarEd').attr('disabled', false);
$('#documento').on('change', function () {
    $("#form-registrar :input").attr('disabled', false);
});
$('#formNuevoE').click(function () {
    $('#form-registrar').modal();
    $('#cerrarModalEmpleado').attr('disabled', false);
});
$('#formNuevoEd').click(function () {
    $('#form-ver').modal();
});

$('#formNuevoEd').hide();
$('#formNuevoEl').hide();
$('#cerrarE').click(function () {
    //leertabla();
    $('#smartwizard1').smartWizard("reset");
    $('#formNuevoEd').hide();
    $('#formNuevoEl').hide();
});
$('#cerrarEd').click(function () {
    //leertabla();
    $('#smartwizard1').smartWizard("reset");
    $('#formNuevoEd').hide();
    $('#formNuevoEl').hide();
    $('#navActualizar').hide();
    $('#m_fechaIE').combodate("clearValue");
    $('#m_fechaFE').combodate("clearValue");
    $('#checkboxFechaIE').prop('checked', false);
    //************* */
    $('#v_validApPaterno').hide();
    $('#v_validNumDocumento').hide();
    $('#v_validApMaterno').hide();
    $('#v_validNombres').hide();
    $('#v_validFechaN').hide();

});
$('#cerrarModalEmpleado').click(function () {
    //leertabla();

    //************ */
    $('#formNuevoEd').hide();
    $('#formNuevoEl').hide();
    $('#smartwizard').smartWizard("reset");
    $('input[type="text"]').val("");
    $('input:radio[name=tipo]:checked').prop('checked', false);
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('input[type="email"]').val("");
    $('select').val("");
    $("#form-registrar :input").prop('disabled', true);
    $('#documento').attr('disabled', false);
    $('#cerrarMoadalEmpleado').attr('disabled', false);
    $('#checkboxFechaI').prop('checked', false);
    //********** */
    $('#v_emailR').hide();
    $('#validDocumento').hide();
    $('#validApPaterno').hide();
    $('#validNumDocumento').hide();
    $('#validApMaterno').hide();
    $('#validFechaN').hide();
    $('#validNombres').hide();
    $('#validGenero').hide();
    $('#detalleContrato').hide();
    $('#editarArea').hide();
    $('#form-registrar').modal('toggle');
});
//*********************/
$('#numR').hide();
$('#emailR').hide();
$('#v_emailR').hide();
$('#validDocumento').hide();
$('#validApPaterno').hide();
$('#validNumDocumento').hide();
$('#validApMaterno').hide();
$('#validFechaN').hide();
$('#validNombres').hide();
$('#validGenero').hide();
//************* */
$('#v_validApPaterno').hide();
$('#v_validNumDocumento').hide();
$('#v_validApMaterno').hide();
$('#v_validNombres').hide();
$('#v_validFechaN').hide();
$('#detalleContrato').hide();
$('#editarArea').hide();
//************************Editar en los modal de agregar */
$('#buscarArea').on("click", function () {
    $('#editarArea').empty();
    var container = $('#editarArea');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/area",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
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
                        $('#textArea').val(data);
                    },
                    error: function () {
                        $('#textArea').val("");
                    }
                })
            });
        },
        error: function () {}
    });
    $('#editarArea').show();
});
