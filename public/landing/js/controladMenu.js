$(document).ready(function () {
    var table =  $("#tablaContr").DataTable({
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
   url: "/listaControladores",
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
     { data: "cont_codigo" },
     { data: "cont_nombres" },
     { data: "cont_ApPaterno" },

     { data:"ids" ,
        "render": function (data, type, row) {
        var valores=row.ids;
        idsV=valores.split(',');
        var variableResult=[];
        $.each( idsV, function( index, value ){
            variableResult1=  '<img src="landing/images/telefono-inteligente.svg" height="14">'+value;

            variableResult.push(variableResult1);

        })
       return variableResult;

     } },
     { data: "cont_correo" },
     { data: "cont_estado",
     "render": function (data, type, row) {
        if (row.cont_estado ==0) {
             return '<span class="badge badge-soft-danger">Inactivo</span>';
        }
        if (row.cont_estado ==1) {
            return '<span class="badge badge-soft-info">Activo</span>';
       }
     

     } },


  ]



   });

    table.on( 'order.dt search.dt', function () {
   table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
       cell.innerHTML = i+1;
   } );
} ).draw();



});

function NuevoContr(){
$('#frmConNuevo')[0].reset();
$("#selectDispo").select2({
    placeholder: "Seleccione dispositivo"
});
$('#selectDispo').val('').trigger("change");
$('#nuevoControlador').modal('show');
}

function RegistraContro(){
    var codigoCon=$('#codContro').val();
    var correoCon=$('#codCorreo').val();
    var nombresCon=$('#codNombres').val();
    var paternoCon=$('#codPaterno').val();
    var maternoCon=$('#codMaterno').val();
    var dispoCon=$('#selectDispo').val();
    $.ajax({
        type: "post",
        url: "/controladStore",
        data: {
            codigoCon,correoCon,nombresCon,paternoCon,maternoCon,dispoCon
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
             $('#tablaContr').DataTable().ajax.reload();
            $('#nuevoControlador').modal('hide');
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}
