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
    <tr >
            <td >{{$loop->index+1}}</td>
            <td>{{$tabla_empleados->perso_nombre}}</td>
            <td>{{$tabla_empleados->perso_apPaterno}} {{$tabla_empleados->perso_apMaterno}}</td>
            <td>{{$tabla_empleados->cargo_descripcion}}</td>
            <td>{{$tabla_empleados->area_descripcion}}</td>
            <td>{{$tabla_empleados->centroC_descripcion}}</td>

        </tr>

        @endforeach

    </tbody>
</table>
