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

      /*  ajax: {
   type: "post",
   url: "/horario/listar",
    headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   },

   "dataSrc": ""
  },
 */
  /*  "columnDefs": [ {
               "searchable": false,
               "orderable": false,
               "targets": 0
           }
    ],
           "order": [[ 1, 'asc' ]],
  columns: [
     { data: null },
     { data: "horario_descripcion" },
     { data: "horario_tolerancia",
     "render": function (data, type, row) {

       return row.horario_tolerancia+'&nbsp;&nbsp; minutos';

     } },
     { data: "horaI" },
     { data: "horaF" },
     { data: "horario_horario_id",
     "render": function (data, type, row) {
       if (row.horario_horario_id ==null) {
           return '<img src="admin/images/borrarH.svg" height="11" />&nbsp;&nbsp;No';}
           else {
       return '<img src="admin/images/checkH.svg" height="13" />&nbsp;&nbsp;Si';
              }
     } },
     { data: "horario_id",
     "render": function (data, type, row) {

       return '<a onclick=" editarHorarioLista('+row.horario_id+')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="" style="cursor: pointer">'+
           '<img src="/admin/images/delete.svg" onclick="eliminarHorario('+row.horario_id+')" height="15"></a>';

     } },

  ] */


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
    $.ajax({
        type: "post",
        url: "/enviarMensajePru",
        data: {

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

            console.log(data);
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
$('#nuevoDispositivo').modal('show');
}


