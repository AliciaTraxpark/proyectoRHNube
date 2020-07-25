<style>
    div.dataTables_wrapper div.dataTables_filter{
        display: none;
    }
    .btnhora{
    font-size: 12px;
    padding-top: 1px;
    padding-bottom: 1px;
    }
    .table{
        width: 100%!important;
    }
    .dataTables_scrollHeadInner{
        width: 100%!important;
    }
    .table th, .table td{
        padding: 0.4rem;
    }
</style>
<input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
<br>
<table id="tablaEmpleado" class="table dt-responsive nowrap" style="font-size: 12.8px;">
    <thead style=" background: #edf0f1;color: #6c757d;">

        <tr>
            <th>#</th>
            <th>Descripcion</th>
            <th>Tolerancia</th>
            <th>Hora inicio</th>
            <th>Hora fin</th>
           <th>&nbsp;<input type="checkbox" name="" id="selectT"></th>

        </tr>
    </thead>
    <tbody style="background:#ffffff;color: #585858;font-size: 12.5px">
        @foreach ($horario as  $horarios)
        <tr class="" >
            <td>{{$loop->index+1}}</td>
            <td>{{$horarios->horario_descripcion}}</td>
            <td>{{$horarios->horario_tolerancia}} min</td>
            <td>{{$horarios->horaI}}</td>
            <td>{{$horarios->horaF}} </td>

            <td >{{--  @if ($tabla_empleados->horario_horario_id==null) --}}
                <input type="checkbox" style="margin-left:5.5px!important" id="tdC" name="tdC" class="form-check-input sub_chk" data-id="{{$horarios->horario_id}}" >
             {{--    @endif --}}
             </td>
        </tr>

        @endforeach

    </tbody>
</table>


<script>



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


    } );
    </script>
    {{-- ELIMINAR VARIOS ELEMENTOS --}}
   <script>



   </script>
