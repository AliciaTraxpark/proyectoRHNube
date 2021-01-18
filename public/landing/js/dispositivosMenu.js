$(document).ready(function () {
    var ipv4_address = $('#ipv4');
    ipv4_address.inputmask({
        alias: "ip",
        greedy: false //The initial mask shown will be "" instead of "-____".
    });
    var ipv4_address_ed = $('#ipv4_ed');
    ipv4_address_ed.inputmask({
        alias: "ip",
        greedy: false //The initial mask shown will be "" instead of "-____".
    });

    var table =  $("#tablaDips").DataTable({
        "searching": true,
      /* "lengthChange": false,
       "scrollX": true, */
       "processing": true,

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
   url: "/tablaDisposito",
    headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   },

   "dataSrc": ""
  },

   "columnDefs": [ {
               "searchable": false,
               "orderable": false,
               "targets": 0
           }
    ],
           "order": [[ 1, 'asc' ]],
  columns: [

    { data: "dispo_codigoNombre",
    "render": function (data, type, row) {
            var variablePermiso=$('#modifDisPer').val();
            if(variablePermiso==1){

               return '<a onclick="editarDispo('+row.idDispositivos+')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>';
            }
            else{
                return '';
            }

     } },

     { data: null },
     { data: "dispo_descripUbicacion" ,
     "render": function (data, type, row) {
        if (row.tipoDispositivo ==2) {
            return '<span class="badge badge-soft-secondary">ANDROID</span>';
        }
         else{
            return '<span class="badge badge-soft-secondary">BIOMETRICO</span>';
         }


     } },
     { data: "dispo_descripUbicacion" /* ,
     "render": function (data, type, row) {
        if (row.tipoDispositivo ==2) {
            return row.dispo_descripUbicacion;
        }
         else{
            return '---';
         }


     } */ },
     { data: "dispo_movil"},
     { data: "dispo_estado",
     "render": function (data, type, row) {
        if (row.tipoDispositivo ==2) {
        if (row.dispo_estado ==0) {
            return '&nbsp; <button class="btn btn-sm  botonsms" onclick="enviarSMS('+row.idDispositivos+')" >Enviar <img src="landing/images/note.svg" height="20"  ></button>';
        }
         else{
            return '&nbsp; <button class="btn btn-sm botonsms" onclick="reenviarSMS('+row.idDispositivos+')">Reenviar <img src="landing/images/note.svg" height="20"  ></button>';
         }
        }
        else {
            return '---';
        }

     } },
     { data: "dispo_codigoNombre",
     "render": function (data, type, row) {
        if(row.dispo_codigoNombre==null){
            return '----';
        }
        else{
            return row.dispo_codigoNombre;
        }
      }
          },
     { data: "dispo_estado",
     "render": function (data, type, row) {
        if (row.dispo_estado ==0) {
             return '<span class="badge badge-soft-primary">Creado</span>';
        }
        if (row.dispo_estado ==1) {
            return '<span class="badge badge-soft-info">Enviado</span>';
       }
       if (row.dispo_estado ==2) {
        return '<span class="badge badge-soft-success">Confirmado</span>';
   }

     } },
    { data: "dispo_tMarca",
    "render": function (data, type, row) {
        if (row.tipoDispositivo ==2) {
        return row.dispo_tMarca+'&nbsp; minutos';
        }
        else{
            return '---';
        }

      }},
     { data: "dispo_tSincro",
     "render": function (data, type, row) {
         if (row.tipoDispositivo ==2) {
        return row.dispo_tSincro+'&nbsp; minutos';
         }
         else{
             return '---';
         }
      }},
      { data: "dispo_tSincro",
     "render": function (data, type, row) {
        var variablePermiso2=$('#modifDisPer').val();
        if(variablePermiso2==1){
            if(row.dispo_estadoActivo==1){
                return '<div class="custom-control custom-switch">'+
                '<input type="checkbox" class="custom-control-input" id="customSwitDetalles'+row.idDispositivos+'" checked>'+
                '<label class="custom-control-label" for="customSwitDetalles'+row.idDispositivos+'" onclick="switchEleg('+row.idDispositivos+')" style="font-weight: bold"></label>'+
            '</div>';
            } else{
                return '<div class="custom-control custom-switch">'+
                '<input type="checkbox" class="custom-control-input" id="customSwitDetalles'+row.idDispositivos+'" >'+
                '<label class="custom-control-label" for="customSwitDetalles'+row.idDispositivos+'" onclick="switchEleg('+row.idDispositivos+')" style="font-weight: bold"></label>'+
            '</div>';
            }
           }
           else{
            if(row.dispo_estadoActivo==1){
                return '<div class="custom-control custom-switch">'+
                '<input type="checkbox" class="custom-control-input" id="customSwitDetalles'+row.idDispositivos+'" checked disabled>'+
                '<label class="custom-control-label" for="customSwitDetalles'+row.idDispositivos+'"  style="font-weight: bold"></label>'+
            '</div>';
            } else{
                return '<div class="custom-control custom-switch">'+
                '<input type="checkbox" class="custom-control-input" id="customSwitDetalles'+row.idDispositivos+'" disabled>'+
                '<label class="custom-control-label" for="customSwitDetalles'+row.idDispositivos+'"  style="font-weight: bold"></label>'+
            '</div>';
            }
           }



      }},
  ]


   });
   //$('#verf1').hide();
   //$('#tablaEmpleado tbody #tdC').css('display', 'none');
    table.on( 'order.dt search.dt', function () {
   table.column(1, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
       cell.innerHTML = i+1;
   } );
} ).draw();

});
function maxLengthCheck(object) {
    if (object.value.length > object.maxLength)
        object.value = object.value.slice(0, object.maxLength)
}

function isNumeric(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}
function switchEleg(id){
    if( $('#customSwitDetalles'+id+'').is(':checked')) {
        $('#customSwitDetalles'+id+'').prop('checked',false);
        desactivarDispo(id);
    }
    else{
        $('#customSwitDetalles'+id+'').prop('checked',true);
        activarDispo(id);
    }
}
$(function() {
	$(document).on('keyup', '#smarcacion', function(event) {
    	let min= parseInt(this.min);
        let valor = parseInt(this.value);
    	if(valor<min){
    		$('#errorMarca').show();
    		this.value = min;
        }
        else{
            $('#errorMarca').hide();
        }

	});
});

$(function() {
	$(document).on('keyup', '#tiempoSin', function(event) {
    	let minS= parseInt(this.min);
        let valorS = parseInt(this.value);
    	if(valorS<minS){
    		$('#errorSincro').show();
    		this.value = min;
        }
        else{
            $('#errorSincro').hide();
        }

	});
});

$(function() {
	$(document).on('keyup', '#tiempoData', function(event) {
    	let minD= parseInt(this.min);
        let valorD = parseInt(this.value);
    	if(valorD<minD){
    		$('#errorData').show();
    		this.value = min;
        }
        else{
            $('#errorData').hide();
        }

	});
});

function NuevoDispo(){
    $("#errorMovil").hide();
    $("#errorMarca").hide();
    $("#errorMovil").hide();
    $("#frmHorNuevo")[0].reset();
    $('#selectLectura').val('').trigger("change");
    $('#selectControlador').val('').trigger("change");
$('#nuevoDispositivo').modal('show');
}
function RegistraDispo(){

    var descripccionUb=$('#descripcionDis').val();
    var numeroM='51'+$('#numeroMovil').val();
    var tSincron=$('#tiempoSin').val();
    var tMarcac=$('#smarcacion').val();
    var tData=$('#tiempoData').val();
    var lectura=$('#selectLectura').val();
    var idContro= $('#selectControlador').val();
    var smsCh;

   if($('#smsCheck').is(':checked') ){
    smsCh=1;
   } else{
       smsCh=0;
   }
   $.ajax({
    type: "post",
    url: "/comprobarMovil",
    data: {
        numeroM
    },
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (data) {
        if (data == 1) {

            $("#errorMovil").show();
            return false;

        } else {
            $("#errorMovil").hide();
            $.ajax({
                type: "post",
                url: "/dispoStore",
                data: {
                    descripccionUb,numeroM,tSincron,tMarcac,smsCh,tData,lectura,idContro
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data) {
                    $('#tablaDips').DataTable().ajax.reload();
                    $.notifyClose();
                    $.notify(
                        {
                            message: "\nDispositivo registrado.",
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
                            spacing: 35,
                        }
                    );
                    $('#nuevoDispositivo').modal('hide');
                },
                error: function (data) {
                    alert("Ocurrio un error");
                },
            });
        }
    },
});



}

function enviarSMS(idDis){
    bootbox.confirm({
        message: "¿Enviar código al dispositivo?",
        buttons: {
            confirm: {
                label: 'Aceptar',
                className: 'btn-success'
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
                    url: "/enviarMensajePru",
                    data: {
                        idDis
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (data) {
                        $.notifyClose();
                    $.notify(
                        {
                            message: "\nMensaje enviado.",
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
                            spacing: 35,
                        }
                    );
                        $('#tablaDips').DataTable().ajax.reload();

                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        }
    });


}
function reenviarSMS(idDis){
    bootbox.confirm({
        message: "¿Reenviar código al dispositivo?",
        buttons: {
            confirm: {
                label: 'Aceptar',
                className: 'btn-success'
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
                    url: "/reenviarmensajeDis",
                    data: {
                        idDis
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (data) {
                    $.notifyClose();
                    $.notify(
                        {
                            message: "\nMensaje enviado.",
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
                            spacing: 35,
                        }
                    );
                        $('#tablaDips').DataTable().ajax.reload();

                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        }
    });
}
function comprobarMovil() {

    var numeroM='51'+$('#numeroMovil').val();

    $.ajax({
        type: "post",
        url: "/comprobarMovil",
        data: {
            numeroM
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data == 1) {

                $("#errorMovil").show();
                $('#numeroMovil').val('');

            } else {
                $("#errorMovil").hide();
            }
        },
    });
}
function editarDispo(id){
    $('#selectLectura_ed').val('').trigger("change");
    $('#selectControlador_ed').val('').trigger("change");
    $.ajax({
        type: "post",
        url: "/datosDispoEditar",
        data: {
            id
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $('#idDisposi').val(data[0].idDispositivos)
            $('#descripcionDis_ed').val(data[0].dispo_descripUbicacion);
            $('#numeroMovil_ed').val(data[0].dispo_movil.substr(2));
            $('#tiempoSin_ed').val(data[0].dispo_tSincro);
            $('#smarcacion_ed').val(data[0].dispo_tMarca);
            $('#tiempoData_ed').val(data[0].dispo_Data);
            var seleccionadosLe=[];
            if(data[0].dispo_Manu==1){
                seleccionadosLe.push('1');
            }
            if(data[0].dispo_Scan==1){
                seleccionadosLe.push('2');
            }
            if(data[0].dispo_Cam==1){
                seleccionadosLe.push('3');
            }
            $.each( seleccionadosLe, function( index, value ){
             $("#selectLectura_ed > option[value='"+value+"']").prop("selected","selected");
            $("#selectLectura_ed").trigger("change");
            });
            $.each( data, function( index, value ){
                $("#selectControlador_ed > option[value='"+value.idControladores+"']").prop("selected","selected");
               $("#selectControlador_ed").trigger("change");
               });

               if (data[0].tipoDispositivo==2){
                $('#editarDispositivo').modal('show');
               } else{
                $('#idDisposiBio').val(data[0].idDispositivos)
                $('#descripcionDisBio_ed').val(data[0].dispo_descripUbicacion);
                $('#descripcionBiome_ed').val(data[0].dispo_codigo);

                $('#descripcionBiome_ed').prop("disabled",true);
                $('#versionFi_ed').prop("disabled",true);

                splitE =data[0].dispo_movil.split(":");
                $('#ipv4_ed').val(splitE[0]);
                $('#nPuerto_ed').val(splitE[1]);
                $('#versionFi_ed').val(data[0].version_firmware);
                $('#editarBiometrico').modal('show');
               }

        },
    });

}
function reditarDispo(){
    var descripccionUb_ed=$('#descripcionDis_ed').val();
    var numeroM_ed='51'+$('#numeroMovil_ed').val();
    var tSincron_ed=$('#tiempoSin_ed').val();
    var tMarca_ed=$('#smarcacion_ed').val();
    var tData_ed=$('#tiempoData_ed').val();
    var lectura_ed=$('#selectLectura_ed').val();
    var idcont_id=$('#selectControlador_ed').val();
    var idDisposEd_ed= $('#idDisposi').val();
    $.ajax({
        type: "post",
        url: "/actualizarDispos",
        data: {
            descripccionUb_ed,numeroM_ed,tSincron_ed,tMarca_ed,tData_ed,lectura_ed,
            idDisposEd_ed,idcont_id
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $('#tablaDips').DataTable().ajax.reload();
            $('#editarDispositivo').modal('hide');
            $.notifyClose();
                    $.notify(
                        {
                            message: "\nDispositivo editado correctamente.",
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
                            spacing: 35,
                        }
                    );
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });

}
function desactivarDispo(idDisDesac){
    bootbox.confirm({
        message: "¿Desea desactivar dispositivo?",
        buttons: {
            confirm: {
                label: 'Aceptar',
                className: 'btn-success'
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
                url: "/desactivarDisposi",
                data: {
                    idDisDesac
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data) {
                    $('#tablaDips').DataTable().ajax.reload();

                },
                error: function (data) {
                    alert("Ocurrio un error");
                },
            });

        } }
    });



}
function activarDispo(idDisAct){
    bootbox.confirm({
        message: "¿Desea volver activar dispositivo?",
        buttons: {
            confirm: {
                label: 'Aceptar',
                className: 'btn-success'
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
                    url: "/activarDisposi",
                    data: {
                        idDisAct
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (data) {
                        $('#tablaDips').DataTable().ajax.reload();

                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        }
    });


}
function NuevoBiome(){

    /* RESETERAR FORMULARIO */
    $("#frmHorNuevoBi")[0].reset();

    /* ESCONDER AVISO DE SELECCIOAN */
    $('#spanChEmple').hide();

    /* ESCONDER DIV DE SELECCIION EMPLEADO */
    $("#divEmpleado").hide();
    $("#selectTodoCheck").prop("checked", false);

    /* ESCONDER OPCION DE TODOS EMPLEADOS */
    $("#divTodoECheck").hide();

    /* ESCONDER DIV DE SELECCIION AREA */
    $("#divArea").hide();
    $("#selectArea > option").prop("selected", false);
    $("#selectArea").trigger("change");
    $("#selectAreaCheck").prop("checked", false);
    $('#nuevoBiometrico').modal('show');
}

function RegistraBiome(){

    var ip=$('#ipv4').val();
    ppp=':';
    var puerto=$('#nPuerto').val();
    var ippuerto=ip.concat(ppp, puerto);
    var descripcionBio=$('#descripcionDisBio').val();
    var checkTodoEmp;
    var switchEmp;
    var switchArea;
    var selectEmp=$("#nombreEmpleado").val();
    var selectArea=$("#selectArea").val();
   
    /* ASIGNANDO VALORES A SWITCHS */
    if ($("#TodoECheck").is(":checked")) {
        checkTodoEmp=1;
    }
    else{
        checkTodoEmp=0;
    }

    if ($("#switchEmpS").is(":checked")) {
        switchEmp=1;
    }
    else{
        switchEmp=0;
    }

    if ($("#switchAreaS").is(":checked")) {
        switchArea=1;
    }
    else{
        switchArea=0;
    }
    /* ----------------------------------- */

    /* VALIDAR QUE DEBO SELECCIONAR UN MODO DE SELECCION DE EMPLEADO */
    if (!$("#switchEmpS").is(":checked") && !$("#switchAreaS").is(":checked") &&  !$("#adminCheck").is(":checked") ) {
        $('#spanChEmple').show();
        return false;
    } else{
        $('#spanChEmple').hide();
    }
    /* -------------------------------------------------------------------------- */

    $.ajax({
        type: "post",
        url: "/dispoStoreBiometrico",
        data: {
           ippuerto,descripcionBio,checkTodoEmp,switchEmp,switchArea,selectEmp,selectArea
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $('#tablaDips').DataTable().ajax.reload();
            $('#nuevoBiometrico').modal('hide');
            $.notifyClose();
                    $.notify(
                        {
                            message: "\nDispositivo biometrico registrado.",
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
                            spacing: 35,
                        }
                    );
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}
function EditaBiome(){
    var descripccionUb_ed=$('#descripcionDisBio_ed').val();
    var nserie_ed=$('#descripcionBiome_ed').val();
    var IP_ed=$('#ipv4_ed').val();
    var puerto_ed=$('#nPuerto_ed').val();
    ppp_ed=':';
    var ippuerto_ed=IP_ed.concat(ppp_ed, puerto_ed);
    var version_ed=$('#versionFi_ed').val();
    var idDisposEd_ed= $('#idDisposiBio').val();
    $.ajax({
        type: "post",
        url: "/actualizarBiometrico",
        data: {
            descripccionUb_ed, nserie_ed, ippuerto_ed,version_ed,
            idDisposEd_ed
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $('#tablaDips').DataTable().ajax.reload();
            $('#editarBiometrico').modal('hide');
            $.notifyClose();
                    $.notify(
                        {
                            message: "\nDispositivo editado correctamente.",
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
                            spacing: 35,
                        }
                    );
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });

}

/* EVENTOS PARA SWITCHS DE EEMOLEADOS O AREAS */
$("#switchEmpS").change(function (event) {
    if ($("#switchEmpS").prop("checked")) {
        $("#switchAreaS").prop("checked", false);
        $("#selectArea").prop("disabled", true);
        $("#nombreEmpleado").prop("disabled", false);
        $("#selectArea > option").prop("selected", false);
        $("#selectArea").trigger("change");
        $("#divArea").hide();
        $("#divEmpleado").show();
        $("#nombreEmpleado > option").prop("selected", false);
        $("#nombreEmpleado").trigger("change");
        $("#selectTodoCheck").prop("checked", false);
        $("#TodoECheck").prop("checked", false);
        $("#nombreEmpleado").prop("required", true);
        $("#divTodoECheck").show();
    } else {
        $("#selectArea").prop("disabled", false);
        $("#selectArea").prop("required", false);
        $("#divEmpleado").hide();
        $("#nombreEmpleado").prop("required", false);
        $("#divTodoECheck").hide();
    }
});

$("#switchAreaS").change(function (event) {
    if ($("#switchAreaS").prop("checked")) {
        $("#switchEmpS").prop("checked", false);
        $("#nombreEmpleado").prop("disabled", true);
        $("#selectArea").prop("disabled", false);
        $("#nombreEmpleado > option").prop("selected", false);
        $("#nombreEmpleado").trigger("change");
        $("#divEmpleado").hide();
        $("#divArea").show();
        $("#selectAreaCheck").prop("checked", false);
        $("#nombreEmpleado").prop("required", false);
        $("#selectArea").prop("required", true);
        $("#divTodoECheck").hide();
        $("#TodoECheck").prop("checked", false);
    } else {
        $("#nombreEmpleado").prop("disabled", false);
        $("#selectArea").prop("required", false);
        $("#divArea").hide();
    }
});
/* ---------------------------------------------------- */

/* CUADNO ACTIVO O DESACRIVO SELECCIONAR TODOS, INCLUYENDO NUEVOS */
$("#TodoECheck").click(function () {
    if ($("#TodoECheck").is(":checked")) {
        $("#nombreEmpleado").prop("required", false);
        $("#divEmpleado").hide();
    } else {
        $("#nombreEmpleado").prop("required", true);
        $("#divEmpleado").show();
    }
});
/* ------------------------------------------------------------------ */
//select all empleados
$("#selectTodoCheck").click(function () {
    if ($("#selectTodoCheck").is(":checked")) {
        $("#nombreEmpleado > option").prop("selected", "selected");
        $("#nombreEmpleado").trigger("change");
    } else {
        $("#nombreEmpleado > option").prop("selected", false);
        $("#nombreEmpleado").trigger("change");
    }
});

//selct all area
$("#selectAreaCheck").click(function () {
    if ($("#selectAreaCheck").is(":checked")) {
        $("#selectArea > option").prop("selected", "selected");
        $("#selectArea").trigger("change");
    } else {
        $("#selectArea > option").prop("selected", false);
        $("#selectArea").trigger("change");
    }
});
