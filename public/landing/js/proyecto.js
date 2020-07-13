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
function editarproyecto(id){
    $('#editarPro').hide();
    $('#myModal2').modal('toggle');
    $("#tablaProyectoE>tbody>tr").remove();
    $.ajax({
        type: "POST",
        url: "/proyecto/tablaEmpleados",
        data: {
            id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            $('#nombre1_e').val(data[0][0].Proye_Nombre);
            $('#detalleProyecto_e').val(data[0][0].Proye_Detalle);
            $('#id1_e').val(data[0][0].Proye_id);
            $.each(data[1], function (key, item) {
                $("#tablaProyectoE>tbody").append(
                    "<tr  id='r"+item.proye_empleado_id+"'><td style='padding: 4px;'>"+item.perso_nombre+" "+item.perso_apPaterno+" "+item.perso_apMaterno+
                    "</td> <td style='padding: 4px;'><a style='cursor: pointer' onclick='eliminarsoloEmp("+item.proye_empleado_id+")' ><img src='admin/images/delete.svg' height='15'></a> </td></tr>"
                    );
               });
               $("#nombre1_e").bind("keyup change", function(){
                tarea=$('#nombre1_e').val();
                if(tarea!=data[0][0].Proye_Nombre){
                    $('#editarPro').show();

                } else{
                    if($('#detalleProyecto_e').val()==data[0][0].Proye_Detalle){
                     $('#editarPro').hide();
                    }
                  }
               });
               $("#detalleProyecto_e").bind("keyup change", function(){
                tarea=$('#detalleProyecto_e').val();
                if(tarea!=data[0][0].Proye_Detalle){
                    $('#editarPro').show();

                } else{
                    if($('#nombre1_e').val()==data[0][0].Proye_Nombre){
                        $('#editarPro').hide();
                       }


                  }
               });

        },
        error: function () {
            bootbox.alert({
                message: "Ocurrio un error",

            })
        }
    });

}

function eliminarsoloEmp(id){
    bootbox.confirm({
        message: "¿Desea eliminar el empleado de este proyecto?",
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
                    type: "POST",
                    url: "/proyecto/eliminarEmpleado",
                    data: {
                        id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        }
                    },
                    success: function (data) {
                        $('#r'+id).remove();
                        $('#tablaProyecto').load(location.href + " #tablaProyecto>*");
                        $.notify(" empleado eliminado", {
                            align: "right",
                            verticalAlign: "top",
                            type: "success",
                            icon: "check"
                        });

                    },
                    error: function () {
                        bootbox.alert({
                            message: "Ocurrio un error",

                        })
                }});
            }
        }
    });

  }

function guardarEdicion(){
    var nombreP=$('#nombre1_e').val();
    var detalleP=$('#detalleProyecto_e').val();
    var idPr=$('#id1_e').val();

     $.ajax({
        type: "POST",
        url: "/proyecto/editarPro",
        data: {
            nombreP,detalleP,idPr
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            $('#tablaProyecto').load(location.href + " #tablaProyecto>*");
            $('#myModal2').modal('hide');
            $.notify(" Proyecto editado", {
                align: "right",
                verticalAlign: "top",
                type: "success",
                icon: "check"
            });

        },
        error: function () {
            bootbox.alert({
                message: "Ocurrio un error",

            })
    }});
}
