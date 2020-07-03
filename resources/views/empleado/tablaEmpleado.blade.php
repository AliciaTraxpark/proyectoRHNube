<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<style>
    div.dataTables_wrapper div.dataTables_filter {
        display: none;
    }

    .table {
        width: 100% !important;
    }

    .dataTables_scrollHeadInner {
        width: 100% !important;
    }
</style>
<div id="modalCorreo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCorreo" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Enviar correo a empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel" style="font-size: 15px">¿Desea enviar correo al empleado
                        empleado?</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                            <button type="button" id="enviarCorreo" name="enviarCorreo"
                                style="background-color: #163552;" class="btn btn-sm ">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="modalCorreoM" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCorreo" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Enviar correo a empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel" style="font-size: 15px">¿Desea enviar correo al empleado
                        empleado?</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                            <button type="button" id="enviarCorreoM" name="enviarCorreo"
                                style="background-color: #163552;" class="btn btn-sm ">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
<div class="row">

    <div class="col-md-6" id="filter_global">

        <td align="center"><input type="text" class="global_filter form-control form-control-sm" id="global_filter">
        </td>
    </div>
    <div class="col-md-6" id="filter_col2" data-column="2" style="display: none">
        <label for="">Nombre:</label>
        <td align="center"><input type="text" class="column_filter form-control form-control-sm" id="col2_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col3" data-column="3" style="display: none">
        <label>Apellidos</label>
        <td align="center"><input type="text" class="column_filter form-control form-control-sm" id="col3_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col4" data-column="4" style="display: none">
        <label for="">Cargo</label>
        <td align="center"><input type="text" class="column_filter form-control form-control-sm" id="col4_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col5" data-column="5" style="display: none">
        <label for="">Área</label>
        <td align="center"><input type="text" class="column_filter form-control form-control-sm" id="col5_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col6" data-column="6" style="display: none">
        <label for="">Costo</label>
        <td align="center"><input type="text" class="column_filter form-control form-control-sm" id="col6_filter"></td>
    </div>

</div>

<table id="tablaEmpleado" class="table dt-responsive nowrap">
    <thead style=" background: #e3eaef;color: #6c757d;">
        <tr style="background: #ffffff">
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="inputR"
                    id="i2"></th>
            <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio"
                    name="inputR" id="i3"></th>
            <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR"
                    id="i4"></th>
            <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR" id="i5"></th>
            <th style="border-top: 1px solid #fdfdfd;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR" id="i6">
            </th>
            <th style="border-top: 1px solid #fdfdfd;" id="enviarCorreosMasivos"> <button type="button"
                    class="btn btn-secondary  btn-sm" onclick="$('#modalCorreoM').modal();"
                    style="background:#f0f4fd; border-color:#f0f4fd; color:#a0add3">Envio Masivo @</button></th>
        </tr>
        <tr>
            <th></th>
            <th class="text-center"></th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Cargo</th>
            <th>Área</th>
            <th>Centro de Costo</th>
            <th class="text-center">Windows</th>
            <th class="text-center">Android</th>
            <th>&nbsp;<input type="checkbox" name="" id="selectT"></th>

        </tr>
    </thead>
    <tbody style="background:#ffffff;color: #2c2c2c;" id="tbodyr">
        @foreach ($tabla_empleado as $tabla_empleados)
        <tr class="" id="{{$tabla_empleados->emple_id}}" value="{{$tabla_empleados->emple_id}}">

            <td><a id="formNuevoEd" onclick="$('#form-ver').modal();" style="cursor: pointer"><img
                        src="{{asset('admin/images/edit.svg')}}" height="15"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a
                    onclick="marcareliminar({{$tabla_empleados->emple_id}})" style="cursor: pointer"><img
                        src="{{asset('admin/images/delete.svg')}}" height="15"></a></td>
            <td class="text-center">&nbsp; <input type="hidden" id="codE" value="{{$tabla_empleados->emple_id}}"><img
                    src="{{ URL::asset('admin/assets/images/users/empleado.png') }}" class=" " alt="" /></td>
            <td>{{$tabla_empleados->perso_nombre}}</td>
            <td>{{$tabla_empleados->perso_apPaterno}} {{$tabla_empleados->perso_apMaterno}}</td>
            <td>{{$tabla_empleados->cargo_descripcion}}</td>
            <td>{{$tabla_empleados->area_descripcion}}</td>
            <td>{{$tabla_empleados->centroC_descripcion}} </td>
            @if(!in_array("1",$tabla_empleados->dispositivos))
            <td></td>
            @else
            @if ($tabla_empleados->envio != null)
            @if($tabla_empleados->reenvio == null)
            <td class="text-center"><button id="reenviar" type="button" class="btn btn-secondary  btn-sm"
                    onclick="$('#modalCorreo').modal();"
                    style="background:#f0f4fd; border-color:#f0f4fd; color:#a0add3">Reenviar @</button></td>
            @else
            <td class="text-center"><button id="reenviar" type="button" class="btn btn-secondary  btn-sm"
                    onclick="$('#modalCorreo').modal();" style="background:#eae7d9; border-color:#eae7d9; color:#6c757d"
                    disabled>Reenviar @</button></td>
            @endif
            @else
            <td class="text-center"><button type="button" class="btn btn-secondary  btn-sm"
                    onclick="$('#modalCorreo').modal();"
                    style="background:#f0f4fd; border-color:#f0f4fd; color:#a0add3">Enviar @</button></td>
            @endif
            @endif
            @if(!in_array("2",$tabla_empleados->dispositivos))
            <td></td>
            @else
            @if ($tabla_empleados->envio != null)
            @if($tabla_empleados->reenvio == null)
            <td class="text-center"><button id="reenviar" type="button" class="btn btn-secondary  btn-sm"
                    onclick="$('#modalCorreo').modal();"
                    style="background:#f0f4fd; border-color:#f0f4fd; color:#a0add3">Reenviar @</button></td>
            @else
            <td class="text-center"><button id="reenviar" type="button" class="btn btn-secondary  btn-sm"
                    onclick="$('#modalCorreo').modal();" style="background:#eae7d9; border-color:#eae7d9; color:#6c757d"
                    disabled>Reenviar @</button></td>
            @endif
            @else
            <td class="text-center"><button type="button" class="btn btn-secondary  btn-sm"
                    onclick="$('#modalCorreo').modal();"
                    style="background:#f0f4fd; border-color:#f0f4fd; color:#a0add3">Enviar @</button></td>
            @endif
            @endif
            <td class="text-center"><input type="checkbox" id="tdC" style="margin-left:5.5px!important"
                    class="form-check-input sub_chk" data-id="{{$tabla_empleados->emple_id}}" $(this)$(this)$(this)>
            </td>
        </tr>

        @endforeach

    </tbody>
</table>
<script src="{{asset('landing/js/checkB.js')}}"></script>
<script>
    $("#tablaEmpleado tbody tr").click(function () {
        $('#smartwizard1').smartWizard("reset");
        //$(this).addClass('selected').siblings().removeClass('selected');
        var value = $(this).find('input[type=hidden]').val();
        $('#formNuevoEl').show();
        $.ajax({
            type: "get",
            url: "empleado/show",
            data: {
                value: value
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {


                $('#v_tipoDoc').val(data[0].tipoDoc_descripcion);
                $('#v_apPaterno').val(data[0].perso_apPaterno);
                $('#v_departamento').val(data[0].depaN);
                onSelectVDepartamento('#v_departamento').then(function () {
                    $('#v_provincia').val(data[0].idproviN);
                    onSelectVProvincia('#v_provincia').then((result) => $('#v_distrito')
                        .val(data[0].iddistN))
                });


                $('#v_dep').val(data[0].deparNo);
                onSelectVDepart('#v_dep').then(function () {
                    $('#v_prov').val(data[0].proviId);
                    onSelectVProv('#v_prov').then((result) => $('#v_dist').val(data[0]
                        .distId))
                });

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
                $('#v_email').val(data[0].emple_Correo);
                $('#v_codigoEmpleado').val(data[0].emple_codigo);
                $('input[name=v_disp]').each(function () {
                    $(this).prop('checked', false);
                });
                if (data[0].dispositivo != null) {
                    for (let i = 0; i < data.length; i++) {
                        $('#v_disp' + data[i].dispositivo).prop('checked', true);
                    }
                }
                if (data[0].foto != "") {
                    urlFoto = data[0].foto;
                    hayFoto = true;
                    $('#file2').fileinput('destroy');
                    cargarFile2();
                    $('#v_foto').attr("src", "{{asset('/fotosEmpleado')}}" + "/" + data[0].foto);
                } else {
                    hayFoto = false;
                    urlFoto = "";
                    $('#file2').fileinput('destroy');
                    cargarFile2();
                }
                console.log(data)

            },
            error: function () {
                alert("Hay un error");
            }
        });
    });

</script>
<script>
    function filterGlobal() {
        $('#tablaEmpleado').DataTable().search(
            $('#global_filter').val(),

        ).draw();
    }

    function filterColumn(i) {
        $("#tablaEmpleado").DataTable({
            retrieve: true,
            "searching": true,
            "scrollX": true,

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
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },


        }).column(i).search(

            $('#col' + i + '_filter').val(),
        ).draw();
        $('#i' + i).prop('checked', true);
    }

    $(document).ready(function () {

        var table = $("#tablaEmpleado").DataTable({
            retrieve: true,
            "searching": true,
            "lengthChange": false,
            "scrollX": true,
            "pageLength": 30,

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
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },


        });
        //$('#verf1').hide();
        //$('#tablaEmpleado tbody #tdC').css('display', 'none');

        $("#tablaEmpleado tbody tr").hover(function () {
            //$('#verf1').css('display', 'block');
            $('#tablaEmpleado tbody #tdC').css('display', 'block');

        }, function () {

        });


        $("#i2").click(function () {
            if ($("#i2").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i2').prop('checked', true);
                $('#filter_global').hide()
                $('#filter_col2').show();
                $('#filter_col3').hide();
                $('#filter_col4').hide();
                $('#filter_col5').hide();
                $('#filter_col6').hide();

            } else {
                alert("No está activado");
            }
        });

        $("#i3").click(function () {
            if ($("#i3").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i3').prop('checked', true);
                $('#filter_global').hide()
                $('#filter_col2').hide();
                $('#filter_col3').show();
                $('#filter_col4').hide();
                $('#filter_col5').hide();
                $('#filter_col6').hide();


            } else {
                alert("No está activado");
            }
        });

        $("#i4").click(function () {
            if ($("#i4").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i4').prop('checked', true);
                $('#filter_global').hide()
                $('#filter_col2').hide();
                $('#filter_col3').hide();
                $('#filter_col4').show();
                $('#filter_col5').hide();
                $('#filter_col6').hide();

            } else {
                alert("No está activado");
            }
        });
        $("#i5").click(function () {
            if ($("#i5").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i5').prop('checked', true);
                $('#filter_global').hide()
                $('#filter_col2').hide();
                $('#filter_col3').hide();
                $('#filter_col4').hide();
                $('#filter_col5').show();
                $('#filter_col6').hide();

            } else {
                alert("No está activado");
            }
        });
        $("#i6").click(function () {
            if ($("#i6").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i6').prop('checked', true);
                table.columns([1, 2, 3, 4, 5]).deselect();
                $('#filter_global').hide()
                $('#filter_col2').hide();
                $('#filter_col3').hide();
                $('#filter_col4').hide();
                $('#filter_col5').hide();
                $('#filter_col6').show();

            } else {
                alert("No está activado");
            }
        });


        $('input.global_filter').on('keyup click', function () {
            filterGlobal();
        });

        $('input.column_filter').on('keyup click', function () {
            filterColumn($(this).parents('div').attr('data-column'));
        });
    });

</script>
{{-- ELIMINAR VARIOS ELEMENTOS --}}
<script>
    $('.delete_all').on('click', function (e) {
        var allVals = [];
        $(".sub_chk:checked").each(function () {
            allVals.push($(this).attr('data-id'));
        });

        if (allVals.length <= 0) {
            alert("Por favor seleccione una fila.");
            return false;
        } else {
            $('#modalEliminar').modal();

            $('#confirmarE').click(function () {

                var join_selected_values = allVals.join(",");
               /*  $.notify(" Empleado eliminado", {
                    align: "right",
                    verticalAlign: "top",
                    type: "danger",
                    icon: "bell",
                    autoHide: true
                }); */
                var table = $('#tablaEmpleado').DataTable();
                 $.ajax({
                    url: "/eliminarEmpleados",
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: 'ids=' + join_selected_values,
                    success: function (data) {

                        $(".sub_chk:checked").each(function () {
                            //$(this).parents("tr").remove();


                        });
                        $('#modalEliminar').modal('hide');
                        leertabla();
                        //$('#tablaEmpleado').DataTable().destroy();
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
            });
        }

    });
    function marcareliminar(data){
    $('input:checkbox').prop('checked', false);

    $('input:checkbox[data-id='+data+']').prop('checked', true);
    $('.delete_all').click();
}
</script>
{{-- CORREO MASIVO--}}
<script>
    function CorreosMasivos(){
        var correoEmpleado = [];
        $(".sub_chk:checked").each(function () {
            correoEmpleado.push($(this).attr('data-id'));
        });
        var join_selected_values = correoEmpleado.join(",");
        $.ajax({
            async: false,
            type:"get",
            url:"envioMasivo",
            data: 'ids=' + join_selected_values,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                for(var i = 0; i < data.length; i++){
                    if(data[i].Correo == true && data[i].Reenvio == true){
                        $.notify({
                            message: "\nCorreo enviado a" + data[i].Persona.perso_nombre +" " + data[i].Persona.perso_apPaterno + " "+ data[i].Persona.perso_apMaterno ,
                            icon: 'admin/images/checked.svg'
                        }, {
                            icon_type: 'image',
                            newest_on_top: true,
                            delay: 5000,
                            template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                '</div>',
                            spacing: 35
                        });
                    }else{
                        if(data[i].Correo != true){
                            $.notify({
                                message: "\nAún no ha registrado correo a" + data[i].Persona.perso_nombre +" " + data[i].Persona.perso_apPaterno + " "+ data[i].Persona.perso_apMaterno ,
                                icon: 'admin/images/warning.svg'
                            }, {
                                icon_type: 'image',
                                newest_on_top: true,
                                delay: 5000,
                                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        }else{
                            if(data[0].Reenvio[i] != true){
                                $.notify({
                                message: data[i].Persona.perso_nombre +" " + data[i].Persona.perso_apPaterno+ " " + data[i].Persona.perso_apMaterno +"\nllego al limite de envio de correo",
                                icon: 'admin/images/warning.svg'
                                }, {
                                    icon_type: 'image',
                                    newest_on_top: true,
                                    delay: 5000,
                                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                        '<span data-notify="title">{1}</span> ' +
                                        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                        '</div>',
                                    spacing: 35
                                });
                            }
                        }
                    }
                }
            $('#modalCorreoM').modal('toggle');
            leertabla();
            }
        });
    }
    $('#enviarCorreoM').on("click",CorreosMasivos);
</script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
<script src="{{asset('landing/js/correoEmpleados.js')}}"></script>