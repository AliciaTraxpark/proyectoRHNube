function agregarProyecto() {

    var nombre = $('#nombreProyecto').val();
    var descripcion = $('#detalleProyecto').val();
    $.ajax({
        type: "POST",
        url: "/proyecto/registrar",
        data: {
            nombre,
            descripcion
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
            $('#nombreProyecto').val('');
            $('#detalleProyecto').val('');
            $('#myModal').modal('hide');
            $('#tablaProyecto').load(location.href + " #tablaProyecto>*");
            $.notify("Tarea registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {}
    });
}


function abrirM(id) {
    $('#idempleado').load(location.href + " #idempleado>*");
    $('#myModal1').modal('toggle');
    $.ajax({
        type: "POST",
        url: "/proyecto/proyectoV",
        data: {
            id
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



            $('#nombre1').val(data.Proye_Nombre);
            $('#id1').val(data.Proye_id);


        },
        error: function () {}
    });
    var $select = $('#idempleado').select2();
    $.ajax({

        type: "POST",
        url: "/proyecto/selectValidar",
        data: {
            id
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
            //$('#prue').val(null).trigger('change');
            var array = [];
            /*  $.each(data, function (i, json) {
                 $select.append('<option value="' + json.emple_id + '">' + json.perso_nombre + '</option>');
               }); */
            $.each(data, function (i, json) {
                array[json.empleado_emple_id] = (parseInt(json.empleado_emple_id));

                $('#idempleado').find('option[value="' + json.empleado_emple_id + '"]').remove();

            });
            $('#idempleado').select2({});;
            //alert(array);
            /*    for(i=0;i<array.length;i++){
                 if(array[i]!=undefined){
                     $('#prue').val(null).trigger('change');
                    //$("#prue option[value=" + i+  "]").hide();
                    $('#prue').find('option[value="'+array[i]+'"]').hide();
                  }
                  if(array[i]==undefined){
                     $('#prue').val(null).trigger('change');
                     $("#prue option[value=" + i + "]").show();
                  }
                } */



        },
        error: function () {}
    });
    $('#idempleado').change(function () {
        var selections = (JSON.stringify($('#idempleado').select2('data')));
        //console.log('Selected IDs: ' + ids);
        console.log('Selected options: ' + selections);
        //$('#selectedIDs').text(ids);
        //$('#selectedText').text(selections);
    });
};

function registrarPE() {

    var proyecto = $('#id1').val();
    var empleado = $('#idempleado').val();


    $.ajax({
        type: "POST",
        url: "/proyecto/registrarPrEm",
        data: {
            proyecto,
            empleado
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
            $('#myModal1').modal('toggle');
            $('#tablaProyecto').load(location.href + " #tablaProyecto>*");
            $.notify("empleado registrado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {}
    });



};

function eliminarp(idproyecto) {
    bootbox.confirm({
        message: "¿Desea eliminar esta tarea con todos sus integrantes?",
        buttons: {
            confirm: {
                label: 'Aceptar',
                className: 'btn-primary'
            },
            cancel: {
                label: 'Cancelar',
                className: 'btn-light'
            }
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/proyecto/eliminar",
                    data: {
                        idproyecto
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
                        $('#tablaProyecto').load(location.href + " #tablaProyecto>*");
                    },
                    error: function (data) {
                        bootbox.alert({
                            message: "No se puede eliminar, tarea pertenece a una actividad.",

                        })
                    }


                });
            }
        }
    });

};
$(document).ready(function () {
    $("#tablaProyecto").DataTable({
        "searching": true,
        responsive: true,
        retrieve: true,

        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ ",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": ">",
                "sPrevious": "<"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },

        },


    });
});
