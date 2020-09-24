$(document).ready(function () {
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
     { data: null },
     { data: "dispo_descripUbicacion" },
     { data: "dispo_movil"},
     { data: "dispo_estado",
     "render": function (data, type, row) {
        if (row.dispo_estado ==0) {
            return '&nbsp; <button class="btn btn-sm  botonsms" onclick="enviarSMS('+row.idDispositivos+')" >Enviar <img src="landing/images/note.svg" height="20"  ></button>';
        }
         else{
            return '&nbsp; <button class="btn btn-sm botonsms" onclick="reenviarSMS('+row.idDispositivos+')">Reenviar <img src="landing/images/note.svg" height="20"  ></button>';
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

        return row.dispo_tMarca+'&nbsp; minutos';

      }},
     { data: "dispo_tSincro",
     "render": function (data, type, row) {

        return row.dispo_tSincro+'&nbsp; minutos';

      }},
  ]


   });
   //$('#verf1').hide();
   //$('#tablaEmpleado tbody #tdC').css('display', 'none');
    table.on( 'order.dt search.dt', function () {
   table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
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
$(function() {
	$(document).on('keyup', '#smarcacion', function(event) {
    	let min= parseInt(this.min);
        let valor = parseInt(this.value);
    	if(valor<min){
    		$('#errorMarca').show();
    		this.value = min;
    	}

	});
});
function NuevoDispo(){

    $("#frmHorNuevo")[0].reset();
$('#nuevoDispositivo').modal('show');
}
function RegistraDispo(){
    var descripccionUb=$('#descripcionDis').val();
    var numeroM='51'+$('#numeroMovil').val();
    var tSincron=$('#tiempoSin').val();
    var tMarcac=$('#smarcacion').val();
    var smsCh
   if($('#smsCheck').is(':checked') ){
    smsCh=1;
   } else{
       smsCh=0;
   }
    $.ajax({
        type: "post",
        url: "/dispoStore",
        data: {
            descripccionUb,numeroM,tSincron,tMarcac,smsCh
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
            $('#nuevoDispositivo').modal('hide');
        },
        error: function (data) {
            alert("Ocurrio un error");
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
