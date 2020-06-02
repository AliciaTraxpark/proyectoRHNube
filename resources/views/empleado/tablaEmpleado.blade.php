<style>
    div.dataTables_wrapper div.dataTables_filter{
        display: none;
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
    <thead style="background: #566879;color: white;">
        <tr style="background: #f8f8f8">
            <th style="border-top: 1px solid #f8f8f8;"></th>
            <th style="border-top: 1px solid #f8f8f8;"><input type="radio" name="inputR" id="i1"></th>
             <th style="border-top: 1px solid #f8f8f8;"><input type="radio" name="inputR" id="i2"></th>
             <th style="border-top: 1px solid #f8f8f8;"><input type="radio" name="inputR" id="i3"></th>
             <th style="border-top: 1px solid #f8f8f8;"><input type="radio" name="inputR" id="i4"></th>
             <th style="border-top: 1px solid #f8f8f8;"><input type="radio" name="inputR" id="i5"></th>
         </tr>
        <tr>
            <th>#</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Cargo</th>
            <th>Área</th>
            <th>Centro de Costo</th>
        </tr>
    </thead>
    <tbody style="background:#f8f8f8;color: #2c2c2c;">
        @foreach ($tabla_empleado as  $tabla_empleados)
    <tr id="{{$tabla_empleados->emple_id}}" value= "{{$tabla_empleados->emple_id}}">

            <td   > <input type="hidden" value="{{$tabla_empleados->emple_id}}">   {{$loop->index+1}}</td>
            <td>{{$tabla_empleados->perso_nombre}}</td>
            <td>{{$tabla_empleados->perso_apPaterno}} {{$tabla_empleados->perso_apMaterno}}</td>
            <td>{{$tabla_empleados->cargo_descripcion}}</td>
            <td>{{$tabla_empleados->area_descripcion}}</td>
            <td>{{$tabla_empleados->centroC_descripcion}}</td>

        </tr>

        @endforeach

    </tbody>
</table>


<script>
 $("#tablaEmpleado tbody tr").click(function(){
    $('#smartwizard1').smartWizard("reset");
    $(this).addClass('selected').siblings().removeClass('selected');
    var value=$(this).find('input[type=hidden]').val();
    $('#form-registrar').hide();
    $('#form-ver').show();
    $.ajax({
        type:"get",
        url:"empleado/show",
        data:{value:value},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){


        $('#v_tipoDoc').val(data[0].tipoDoc_descripcion);
        $('#v_apPaterno').val(data[0].perso_apPaterno);
        $('#v_departamento').val(data[0].depaN);
        $('#v_provincia').val(data[0].idproviN);
        $('#v_distrito').val(data[0].iddistN);

        $('#v_dep').val(data[0].deparNo);
        $('#v_prov').val(data[0].proviId);
        $('#v_dist').val(data[0].distId);


        $('#v_numDocumento').val(data[0].emple_nDoc);
        $('#v_apMaterno').val(data[0].perso_apMaterno);
        $("[name=v_tipo]").val([data[0].perso_sexo]);
        $('#v_fechaN').combodate('setValue', data[0].perso_fechaNacimiento);
        $('#v_nombres').val(data[0].perso_nombre);
        $('#v_direccion').val(data[0].perso_direccion);


        $('#v_cargo').val(data[0].cargo_id);
        $('#v_area').val(data[0].area_id);
        $('#v_centroc').val(data[0].centroC_id);
        id_empleado = data[0].emple_id;
        $('#v_id').val(data[0].emple_id);
        $('#v_contrato').val(data[0].emple_tipoContrato);
        $('#v_nivel').val(data[0].emple_nivel);
        $('#v_local').val(data[0].emple_local);
        $('#v_celular').val(data[0].emple_celular);
        $('#v_telefono').val(data[0].emple_telefono);
        $('#v_fechaIC').text(data[0].emple_fechaIC);
        $('#v_fechaFC').text(data[0].emple_fechaFC);
        if(data[0].foto!=""){
            urlFoto = data[0].foto;
            hayFoto= true;
            $('#file2').fileinput('destroy');
            cargarFile2();
            $('#v_foto').attr("src","{{asset('/fotosEmpleado')}}"+"/"+data[0].foto);
        }else{
            hayFoto= false;
            urlFoto = "";
            $('#file2').fileinput('destroy');
            cargarFile2();
        }
        console.log(data)

        },
        error:function(){ alert("Hay un error");}
    });
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


        $("#i1").click(function() {
            if($("#i1").is(':checked')) {

                table
                .search( '' )
                .columns().search( '' )
                .draw();
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
