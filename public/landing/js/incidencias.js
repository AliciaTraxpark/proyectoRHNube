var idIncidencia;
$.fn.dataTable.ext.errMode = "throw";
$(document).ready(function () {
    var table = $("#tablaIncidencias").DataTable({
        searching: true,
        /* "lengthChange": false,
       "scrollX": true, */
        processing: true,

        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ ",
            sInfoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sInfoPostFix: "",
            sSearch: "Buscar:",
            sUrl: "",
            sInfoThousands: ",",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: ">",
                sPrevious: "<",
            },
            oAria: {
                sSortAscending:
                    ": Activar para ordenar la columna de manera ascendente",
                sSortDescending:
                    ": Activar para ordenar la columna de manera descendente",
            },
            buttons: {
                copy: "Copiar",
                colvis: "Visibilidad",
            },
        },

        ajax: {
            type: "post",
            url: "/tablaIncidencias",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
            },
            statusCode: {
                401: function () {
                    location.reload();
                },
                402: function () {
                    location.reload();
                },
                419: function () {
                    location.reload();
                },
                403: function () {
                    location.reload();
                },
                302: function () {
                    location.reload();
                },
            },
            error: function () {
                console.log("se recarga en 401");
            },

            dataSrc: "",
        },

        columnDefs: [
            {
                searchable: false,
                orderable: false,
                targets: 0,
            },
        ],
        order: [[1, "asc"]],
        columns: [


            { data: null },
            { data: "tipoInc_descripcion" },
            { data: "inciden_codigo" ,
                render: function (data, type, row) {
                    if(row.inciden_codigo){
                        return row.inciden_codigo;
                    } else{
                        return 'No definido';
                    }

                },
            },
            {
                data: "inciden_descripcion",
                render: function (data, type, row) {
                    return row.inciden_descripcion;
                },
            },

            {
                data: "inciden_pagado",
                render: function (data, type, row) {
                    if(row.inciden_pagado==1){
                        return '<a class="badge badge-soft-primary mr-2">Si</a>';
                    } else{
                        return '<a class="badge badge-soft-warning mr-2">No</a>';
                    }

                },
            },
            {
                data: "uso",
                render: function (data, type, row) {
                    if (row.uso == 1) {
                        return '<img src="admin/images/checkH.svg" height="13" />&nbsp;&nbsp;Si';
                    }
                    else {
                        return '<img src="admin/images/borrarH.svg" height="9" />&nbsp;&nbsp;No';
                    }
                },
            },
            {
                data: "estado",
                render: function (data, type, row) {
                   return row.estado;
                },
            },
            {
                data: "sistema",
                render: function (data, type, row) {
                    if(row.sistema == 1){
                        return '<a onclick="IncidenEditar(' + row.inciden_id + ')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>';
                    } else{
                        if (row.uso == 1) {
                            return '<a onclick="IncidenEditar(' + row.inciden_id + ')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>';
                        } else{
                            return '<a onclick="IncidenEditar(' + row.inciden_id + ')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="" style="cursor: pointer">' +
                            '<img src="/admin/images/delete.svg" onclick="eliminarHorario(' + row.inciden_id + ')" height="15"></a>';
                        }

                    }

                },
            }

        ],
    });

    table
        .on("order.dt search.dt", function () {
            table
                .column(0, { search: "applied", order: "applied" })
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
        })
        .draw();
});

//*ABRIR MODAL REG INCIDENCIA***********************************
function nuevaIncidencia() {
    //*LIMPIANDO SELECT DE TIPO
    $("#selectTipoIncide > option").prop("selected", false);
    $("#selectTipoIncide").trigger("change");

    //*LIMPIANDO FORMULARIO
    $("#frmHorNuevo")[0].reset();

    //*DESHABILITANDO
    $("#descripcionIncid").prop("disabled", true);
    $("#codigoIncid").prop("disabled", true);
    $("#pagadoCheck").prop("disabled", true);

    $("#registroIncidencia").modal("show");
}
//**************************************************************

//*CAMBIO  SELECT TIPO INCIDENCIA*******************************
$("#selectTipoIncide").change(function (e) {
    let selectTipo = $("#selectTipoIncide").val();
    let textSelect = $("#selectTipoIncide").select2("data");

    //*SI NO ES VACIO
    if (selectTipo) {
        $("#descripcionIncid").prop("disabled", false);
        $("#codigoIncid").prop("disabled", false);

        //*SI ES FERIADO SI HAY PAGO
        if (textSelect[0].text.trim() == "Feriado") {
            $("#pagadoCheck").prop("disabled", true);
            $("#pagadoCheck").prop("checked", true);
        } else {
            $("#pagadoCheck").prop("disabled", false);
        }
    }
});
//**************************************************************

//*************** */ REGISTRAR INCIDENCIA***********************
function registrarIncidencia(){

    //*OBTENEMOS VALORE
    let tipoIncidencia=$('#selectTipoIncide').val();
    let descripIncidencia=$('#descripcionIncid').val();
    let codigoIncidencia=$('#codigoIncid').val();
    let pagoIncidencia;

    if($('#pagadoCheck').is(':checked')){
        pagoIncidencia=1;
    } else{
        pagoIncidencia=0;
    }

    //*VERIFICAMOS CODIGO QUE NO SE REPITA(SOLO SE PUEDE REPETIR EN FERIADO)
    $.ajax({
        type: "post",
        url: "/verificaCodIncidencia",
        data: {
            tipoIncidencia,
            codigoIncidencia
        },
        statusCode: {

            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {

            //*verificamos si ya existe codigo, 1 existe
            if(data==1){
                $.notifyClose();
                $.notify(
                    {
                        message:
                            "\nYa existe una incidencia con este código.",
                        icon: "admin/images/warning.svg",
                    },
                    {
                        element: $("#registroIncidencia"),
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
            } else{
                //*SI NO EXISTE ENTONCES REGISTRAMOS
                $.ajax({
                    type: "post",
                    url: "/registIncidencia",
                    data: {
                        tipoIncidencia,descripIncidencia,
                        codigoIncidencia, pagoIncidencia

                    },
                    statusCode: {

                        419: function () {
                            location.reload();
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {

                        $('#tablaIncidencias').DataTable().ajax.reload(null, false);
                        $("#registroIncidencia").modal("hide");
                        $.notifyClose();
                        $.notify(
                            {
                                message: "\nIncidencia registrada.",
                                icon: "admin/images/checked.svg",
                            },
                            {
                                position: "fixed",
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 5000,
                                template:
                                    '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 50,
                            }
                        );


                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }


                });
            }

        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });
}
//* ************************************************************

//* EDITAR INCIDENCIA, SI ES DE SISTEMA SOLO SE PUEDE AGREGAR CODIGO SI NO TIENE,NO EDIT
//*SI NO ES DE SISTEMA Y SI NO ESTA EN USO PUEDO CAMBIAR DESCRIPCION Y CODIGO, SI ESTA EN USO SOLO
//*PUEDO AGREGARLE CODIGO */
function IncidenEditar(idIncidencia){
    //* TODO DISAABLED FALSE
    $('#descripcionIncid_ed').prop('disabled',false);
    $('#codigoIncid_ed').prop('disabled',false);
    $('#pagadoCheck_ed').prop('disabled',false);

    //*tipoInci
      $('#selectTipo_ed').show();
    $('#selectTipoIncide_edSis').hide(); 

    $.ajax({
        type: "post",
        url: "/dataIncidencia",
        async:false,
        data: {
            idIncidencia

        },
        statusCode: {

            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {

            //*seteando datos
            $('#idIncidencia_ed').val(data.inciden_id);
            $('#selectTipoIncide_ed').val(data.idtipo_incidencia);
            $("#selectTipoIncide_ed").trigger("change");
            $('#descripcionIncid_ed').val(data.inciden_descripcion);
            $('#codigoIncid_ed').val(data.inciden_codigo);
            $('#selectTipoIncide_ed').prop('required',true);
            if(data.inciden_pagado == 1){
                $('#pagadoCheck_ed').prop('checked',true);
            } else{
                $('#pagadoCheck_ed').prop('checked',false);
            }
            $('#selectTipo_ed').show();
            //*si esta en uso
            if(data.uso==1){
                $('#descripcionIncid_ed').prop('disabled',true);
                $('#pagadoCheck_ed').prop('disabled',true);
                $('#selectTipoIncide_ed').prop('disabled',true);
            } else{
                $('#pagadoCheck_ed').prop('disabled',false);
            }
            //**************SI ES DE SISTEMA******************
            if(data.sistema==1){
                $('#descripcionIncid_ed').prop('disabled',true);
                //*tipo incidencia de sistema
                if(data.tipoInc_descripcion=='De sistema'){
                    $('#selectTipoIncide_edSis').show();
                    $('#selectTipoIncide_edSis').val('De sistema');
                    $('#selectTipoIncide_edSis').prop('disabled',true);
                    $('#selectTipoIncide_ed').prop('required',false);
                    $('#idTipoInci').val(data.idtipo_incidencia);
                    $('#selectTipo_ed').hide();
                } else{
                    $('#selectTipo_ed').show();
                    $('#selectTipoIncide_edSis').hide();
                }
                

                //si tiene codigo
                if(data.inciden_codigo){
                    $('#codigoIncid_ed').prop('disabled',true);
                } else{
                    $('#codigoIncid_ed').prop('disabled',false);
                }

                
            }
            //**************************************************
            else{
                //*tipo incidencia
                $('#selectTipo_ed').show();
                $('#selectTipoIncide_edSis').hide();
                //*SI NO ES DE SISTEMA, PRIMERO SE VERIFICA SI TIENE USO
                if(data.uso==1){
                    $('#selectTipoIncide_ed').prop('disabled',true);
                    $('#descripcionIncid_ed').prop('disabled',true);
                    $('#pagadoCheck_ed').prop('disabled',true);

                    //VERIFICO SI TIENE CODIGO
                    if(data.inciden_codigo){
                        $('#codigoIncid_ed').prop('disabled',true);
                    } else{
                        $('#codigoIncid_ed').prop('disabled',false);
                    }
                } else{
                    $('#selectTipoIncide_ed').prop('disabled',false);
                    //SI NO ESTA EN USO PUEDO MODIFICAR TODO
                    $('#descripcionIncid_ed').prop('disabled',false);
                    $('#codigoIncid_ed').prop('disabled',false);
                    $('#pagadoCheck_ed').prop('disabled',false);

                }
            }
            $('#editarIncidencia').modal('show');


        },
        error: function (data) {

        }


    });
}
//********************************************************************************** */

//* FUNCION ACTUALIZAR INCIDENCIA**********************************************
function UpdateIncidencia(){
let tipoInUpdate;
    if($('#selectTipo_ed').is(':visible')){
        tipoInUpdate=$('#selectTipoIncide_ed').val();
    } else{
        tipoInUpdate=$('#idTipoInci').val();
    }
let descripUp=$('#descripcionIncid_ed').val();
let codigoUp=$('#codigoIncid_ed').val();
let pagadoUp;

if($('#pagadoCheck_ed').is(':checked')){
    pagadoUp=1;
} else{
    pagadoUp=0;
}
let idInc= $('#idIncidencia_ed').val();
$.ajax({
    type: "post",
        url: "/updateIncidencia",
        async:false,
        data: {
            tipoInUpdate, idInc,descripUp,codigoUp,pagadoUp

        },
        statusCode: {

            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            //*si datos no son validos
            if(data.estado==0){
                $.notifyClose();
                $.notify(
                    {
                        message:
                            "\nYa existe una incidencia con este código.",
                        icon: "admin/images/warning.svg",
                    },
                    {
                        element: $("#editarIncidencia"),
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
            } else{
                $('#tablaIncidencias').DataTable().ajax.reload(null, false);
                $('#editarIncidencia').modal('hide');
                $.notifyClose();
                $.notify(
                    {
                        message: "\nIncidencia actualizada.",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        position: "fixed",
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 50,
                    }
                );
            }
            
        },
        error: function (data) {

        }


})
}
