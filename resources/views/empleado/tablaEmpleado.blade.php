<table id="tablaEmpleado" class="table nowrap" style="font-size: 12.5px">
    <thead style="background: #566879;color: white;">
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
  /*  $("#tablaEmpleado tr").click(function(){

        $(this).addClass("selected");
    //id.addClass('selected').siblings().removeClass('selected');
    var value=$(this).find('tr:first').html();
    alert(value);
 });*/

 $("#tablaEmpleado tr").click(function(){
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

        $('#v_numDocumento').val(data[0].emple_nDoc);
        $('#v_apMaterno').val(data[0].perso_apMaterno);

        $('#v_fechaN').val(data[0].perso_fechaNacimiento);
        $('#v_nombres').val(data[0].perso_nombre);
        $('#v_direccion').val(data[0].perso_direccion);





        },
        error:function(){ alert("Hay un error");}
    });
 });

</script>
