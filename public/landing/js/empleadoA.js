$('#m_fechaIE').combodate({
    value: new Date(),
    minYear: 2000,
    maxYear: moment().format('YYYY') + 1,
    yearDescending: false
});
$('#m_fechaFE').combodate({
    minYear: 2014,
    maxYear: moment().format('YYYY') + 1,
    yearDescending: false,
});
//SHOW DE BOTON DE ACTUALIZAR
$('#navActualizar').hide();
$('#v_apPaterno').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_apMaterno').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_email').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_celular').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_fechaN').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_nombres').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_telefono').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_direccion').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_dep').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_departamento').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_tipo').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_prov').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_provincia').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_dist').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_distrito').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_codigoEmpleado').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_cargo').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_contrato').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_area').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_nivel').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_centroc').on("focus", function () {
    $('#navActualizar').show();
});
$('#v_local').on("focus",function(){
    $('#navActualizar').show();
});
$('#file2').on("click",function(){
    $('#navActualizar').show();
});
$('input[name=v_disp]').on("click",function(){
    $('#navActualizar').show();
});
//AREA
function agregarAreaA() {
    objArea = datosAreaA("POST");
    enviarAreaA('', objArea);
};

function datosAreaA(method) {
    nuevoArea = {
        area_descripcion: $('#textAreaE').val().toUpperCase(),
        '_method': method
    }
    return (nuevoArea);
}

function enviarAreaA(accion, objArea) {
    $.ajax({
        type: "POST",
        url: "/registrar/area" + accion,
        data: objArea,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            $('#areamodalE').modal('toggle');
            $('#form-ver').modal('show');
            $.notify("Área registrada", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {
            alert("Hay un error");
        }
    });
}

///CARGO
function agregarcargoA() {
    objCargo = datosCargoA("POST");
    enviarCargoA('', objCargo);
};

function datosCargoA(method) {
    nuevoCargo = {
        cargo_descripcion: $('#textCargoE').val().toUpperCase(),
        '_method': method
    }
    return (nuevoCargo);
}

function enviarCargoA(accion, objCargo) {
    $.ajax({
        type: "POST",
        url: "/registrar/cargo" + accion,
        data: objCargo,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            $('#cargomodalE').modal('toggle');
            $('#form-ver').modal('show');
            $.notify("Cargo registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });
        },
        error: function () {
            alert("Hay un error");
        }
    });
}

//centro costo
function agregarcentroA() {
    objCentroC = datosCentroA("POST");
    enviarCentroA('', objCentroC);
};

function datosCentroA(method) {
    nuevoCentro = {
        centroC_descripcion: $('#textCentroE').val().toUpperCase(),
        '_method': method
    }
    return (nuevoCentro);
}

function enviarCentroA(accion, objCentroC) {
    $.ajax({
        type: "POST",
        url: "/registrar/centro" + accion,
        data: objCentroC,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            $('#centrocmodalE').modal('toggle');
            $('#form-ver').modal('show');
            $.notify("Centro de costo registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });
        },
        error: function () {
            alert("Hay un error");
        }
    });
}
//LOCAL
function agregarlocalA() {
    objLocal = datosLocalA("POST");
    enviarLocalA('', objLocal);
};

function datosLocalA(method) {
    nuevoLocal = {
        local_descripcion: $('#textLocalE').val().toUpperCase(),
        '_method': method
    }
    return (nuevoLocal);
}

function enviarLocalA(accion, objLocal) {
    $.ajax({
        type: "POST",
        url: "/registrar/local" + accion,
        data: objLocal,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            $('#localmodalE').modal('toggle');
            $('#form-ver').modal('show');
            $.notify("local registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {
            alert("Hay un error");
        }
    });
}
//NIVEL
function agregarnivelA() {
    objNivel = datosNivelA("POST");
    enviarNivelA('', objNivel);
};

function datosNivelA(method) {
    nuevoNivel = {
        nivel_descripcion: $('#textNivelE').val().toUpperCase(),
        '_method': method
    }
    return (nuevoNivel);
}

function enviarNivelA(accion, objNivel) {
    $.ajax({
        type: "POST",
        url: "/registrar/nivel" + accion,
        data: objNivel,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            $('#nivelmodalE').modal('toggle');
            $('#form-ver').modal('show');
            $.notify("nivel registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {
            alert("Hay un error");
        }
    });
}

//CONTRATO
function agregarContratoA() {
    objContrato = datosContratoA("POST");
    enviarContratoA('', objContrato);
};

function datosContratoA(method) {
    nuevoContrato = {
        contrato_descripcion: $('#textContratoE').val().toUpperCase(),
        '_method': method
    }
    return (nuevoContrato);
}

function enviarContratoA(accion, objContrato) {
    $.ajax({
        type: "POST",
        url: "/registrar/contrato" + accion,
        data: objContrato,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $('#v_contrato').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.contrato_id,
                text: data.contrato_descripcion,
                selected: true
            }));
            $('#contrato').val(data.contrato_id).trigger("change"); //lo selecciona
            $('#v_contrato').val(data.contrato_id).trigger("change");
            $('#textcontrato').val('');
            $('#contratomodalE').modal('toggle');
            $('#form-ver').modal('show');
            $.notify("Contrato registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {
            alert("Hay un error");
        }
    });
}
//FECHAS

function agregarFechasA() {
    $('#fechasmodalE').modal('toggle');
    $('#form-ver').modal('show');
    fechaI = $('#m_fechaIE').val();
    fechaF = $('#m_fechaFE').val();
    $('#v_fechaIC').text(fechaI);
    $('#v_fechaFC').text(fechaF);
    $('#m_fechaIE').combodate("clearValue");
    $('#m_fechaFE').combodate("clearValue");
}

$('#btnCerrar').on("click", function () {
    $('#form-ver').modal('show');
    console.log('ingreso');
})
