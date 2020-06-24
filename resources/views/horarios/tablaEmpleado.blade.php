<style>
    div.dataTables_wrapper div.dataTables_filter{
        display: none;
    }
    .btnhora{
    font-size: 12px;
    padding-top: 1px;
    padding-bottom: 1px;
    }
</style>
<input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
   <div class="row">

    <div class="col-md-6" id="filter_global">

        <td align="center"><input type="text" class="global_filter form-control" id="global_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col1" data-column="1">
         <label for="">Nombre:</label>
         <td align="center"><input type="text" class="column_filter form-control" id="col1_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col2" data-column="2">
        <label>Apellidos</label>
        <td align="center"><input type="text" class="column_filter form-control" id="col2_filter"></td>
    </div>
<div class="col-md-6" id="filter_col3" data-column="3">
        <label for="">Cargo</label>
        <td align="center"><input type="text" class="column_filter form-control" id="col3_filter"></td>
</div>
<div class="col-md-6" id="filter_col4" data-column="4">
        <label for="">Área</label>
        <td align="center"><input type="text" class="column_filter form-control" id="col4_filter"></td>
</div>
<div class="col-md-6" id="filter_col5" data-column="5">
        <label for="">Costo</label>
        <td align="center"><input type="text" class="column_filter form-control" id="col5_filter"></td>
</div>

  </div>

<table id="tablaEmpleado" class="table nowrap" style="font-size: 12.5px; width: 100%">
    <thead style=" background: #5a6f82;color: white;">
        <tr style="background: #fdfdfd">
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="inputR" id="i1"></th>
             <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR" id="i2"></th>
             <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR" id="i3"></th>
             <th style="border-top: 1px solid #fdfdfd;" >&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR" id="i4"></th>
             <th style="border-top: 1px solid #fdfdfd;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR" id="i5"></th>
             <th style="border-top: 1px solid #fdfdfd;"></th>
             <th style="border-top: 1px solid #fdfdfd;"> </th>
         </tr>
        <tr>
            <th>#</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Cargo</th>
            <th>Área</th>
            <th>Centro de Costo</th>
            <th>Horario</th>
           <th>&nbsp;<input type="checkbox" name="" id="selectT"></th>

        </tr>
    </thead>
    <tbody style="background:#fdfdfd;color: #2c2c2c;">
        @foreach ($tabla_empleado as  $tabla_empleados)
    <tr class="" id="{{$tabla_empleados->emple_id}}" value= "{{$tabla_empleados->emple_id}}">

            <td> <input type="hidden" value="{{$tabla_empleados->emple_id}}"><img src="{{ URL::asset('admin/assets/images/users/empleado.png') }}" class=" mr-2" alt="" /></td>
            <td>{{$tabla_empleados->perso_nombre}}</td>
            <td>{{$tabla_empleados->perso_apPaterno}} {{$tabla_empleados->perso_apMaterno}}</td>
            <td>{{$tabla_empleados->cargo_descripcion}}</td>
            <td>{{$tabla_empleados->area_descripcion}}</td>
            <td>{{$tabla_empleados->centroC_descripcion}} </td>
            <td>@if ($tabla_empleados->horario_horario_id==null)
                 no tiene horario
                 @else
                <button class="btnhora btn btn-soft-dark btn-sm" id="verDataHorario" onclick="verhorarioEmpleado({{$tabla_empleados->emple_id}})">Ver horario</button>
                 @endif </td>
            <td > @if ($tabla_empleados->horario_horario_id==null)
                <input type="checkbox" style="margin-left:5.5px!important" id="tdC" class="form-check-input sub_chk" data-id="{{$tabla_empleados->emple_id}}" >
                @endif
             </td>
        </tr>

        @endforeach

    </tbody>
</table>

<script>
    $(document).ready(function() {
  var $selecTodo = $('#selectT');
  var $table = $('#tablaEmpleado');
  var $tdCheckbox = $table.find('tbody input:checkbox');
  var tdCheckboxChecked = 0;

  $selecTodo.on('click', function () {
    $tdCheckbox.prop('checked', this.checked);
  });


  $tdCheckbox.on('change', function(e){
    tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
    $selecTodo.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
  })
});
</script>
<script>
 function filterGlobal () {
    $('#tablaEmpleado').DataTable().search(
        $('#global_filter').val(),

    ).draw();
}
    function filterColumn ( i ) {
       $("#tablaEmpleado").DataTable({
            "searching": true,
            "scrollX": true,
            retrieve: true,

            language :
            {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ ",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     ">",
                    "sPrevious": "<"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },


        }).column( i ).search(
            $('#col'+i+'_filter').val(),

        ).draw();
        $('#i'+i).prop('checked',true);
    }

    $(document).ready(function() {
        var table =  $("#tablaEmpleado").DataTable({
            "searching": true,
            "lengthChange": false,
            "scrollX": true,
            language :
            {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ ",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     ">",
                    "sPrevious": "<"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },


        });
        //$('#verf1').hide();
        //$('#tablaEmpleado tbody #tdC').css('display', 'none');

        $("#tablaEmpleado tbody tr").hover(function(){
           //$('#verf1').css('display', 'block');
            $('#tablaEmpleado tbody #tdC').css('display', 'block');

		}, function(){

		});


        $("#i1").click(function() {
            if($("#i1").is(':checked')) {

                table
                .search( '' )
                .columns().search( '' )
                .draw();
                $('#i1').prop('checked',true);
                $('#filter_global').hide()
                $('#filter_col1').show();
                $('#filter_col2').hide();
                $('#filter_col3').hide();
                $('#filter_col4').hide();
                $('#filter_col5').hide();

            } else {
                alert("No está activado");
            }
        });

        $("#i2").click(function() {
            if($("#i2").is(':checked')) {
                table
                .search( '' )
                .columns().search( '' )
                .draw();
                $('#i2').prop('checked',true);
                $('#filter_global').hide()
                $('#filter_col1').hide();
                $('#filter_col2').show();
                $('#filter_col3').hide();
                $('#filter_col4').hide();
                $('#filter_col5').hide();


            } else {
                alert("No está activado");
            }
        });

        $("#i3").click(function() {
            if($("#i3").is(':checked')) {
                table
                .search( '' )
                .columns().search( '' )
                .draw();
                $('#i3').prop('checked',true);
                $('#filter_global').hide()
                $('#filter_col1').hide();
                $('#filter_col2').hide();
                $('#filter_col3').show();
                $('#filter_col4').hide();
                $('#filter_col5').hide();

            } else {
                alert("No está activado");
            }
        });
        $("#i4").click(function() {
            if($("#i4").is(':checked')) {
                table
                .search( '' )
                .columns().search( '' )
                .draw();
                $('#i4').prop('checked',true);
                $('#filter_global').hide()
                $('#filter_col1').hide();
                $('#filter_col2').hide();
                $('#filter_col3').hide();
                $('#filter_col4').show();
                $('#filter_col5').hide();

            } else {
                alert("No está activado");
            }
        });
        $("#i5").click(function() {
            if($("#i5").is(':checked')) {
                table
                .search( '' )
                .columns().search( '' )
                .draw();
                $('#i5').prop('checked',true);
                table.columns([1,2,3,4]).deselect();
                $('#filter_global').hide()
                $('#filter_col1').hide();
                $('#filter_col2').hide();
                $('#filter_col3').hide();
                $('#filter_col4').hide();
                $('#filter_col5').show();

            } else {
                alert("No está activado");
            }
        });
        $('#filter_col1').hide();
        $('#filter_col2').hide();
        $('#filter_col3').hide();
        $('#filter_col4').hide();
        $('#filter_col5').hide();

        $('input.global_filter').on( 'keyup click', function () {
            filterGlobal();
        } );

        $('input.column_filter').on( 'keyup click', function () {
            filterColumn( $(this).parents('div').attr('data-column') );
        } );
    } );
    </script>
    {{-- ELIMINAR VARIOS ELEMENTOS --}}
   <script>



   </script>
