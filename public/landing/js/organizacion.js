$(document).ready(function () {
    var table = $("#tablaOrgan").DataTable({
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
            url: "/listaoOrganiS",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            "dataSrc": ""
        },

        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }
        ],
        "order": [[1, 'asc']],
        columns: [
            { data: null },
            {
                data: "organi_razonSocial",
                "render": function (data, type, row) {

                    return '<label class="font-weight-bold mb-1">'+row.organi_razonSocial+'</label>' + '<br><a class="badge badge-soft-primary mr-2">'+row.organi_ruc+'</a>';

                }
            },
            { data: "organi_tipo" },
            { data: "created_at",
            "render": function (data, type, row) {

                return moment(row.created_at).format('DD/MM/YYYY');

            } },
            {
                data: "organi_nempleados",
                "render": function (data, type, row) {
                   return row.organi_nempleados+' empleados' ;
                }
            },
            {
                data: "organi_nempleados",
                "render": function (data, type, row) {
                   return row.nemple+' empleados';
                }
            },

            { data: "users" ,
                "render": function (data, type, row) {
                    if(row.organi_estado==1){
                        return '<button class=" btn badge badge-soft-success" onclick="desactivarO('+row.organi_id+')">Activo</button>' ;
                    }
                    else{
                        return '<button class=" btn badge badge-soft-danger" onclick="activarO('+row.organi_id+')">Desactivado</button>' ;
                    }

                }},
            {
                data: "celular",
                "render": function (data, type, row) {
                 /*var cadena=[];
                var nombres=row.nombres;
                idsV=nombres.split(',');
                var celu=row.celular;
                idsC=celu.split(',');
                var correo=row.correo;
                corre=correo.split(',');
                $.each( idsV, function( index, value2 ){
                    variableResult1=value2+'<br><img src="landing/images/telefono-inteligente.svg" height="14">'+idsC[index]+' - '+corre[index]+'<br>';
                    cadena.push(variableResult1);
                })
               return cadena;*/
               return '<button class="btnhora btn  btn-sm btn-rounded" style="color: #548ec7;border-color: #e7edf3; padding-left: 4px; padding-right: 4px;" onclick="verUsuarios('+row.organi_id+')" > <img src="landing/images/ver.svg" height="14" > ver</button>';
                }
            },
            {
                data: "celular",
                "render": function (data, type, row) {
               return '<button class="btnhora btn  btn-sm btn-rounded" style="color: #548ec7;border-color: #e7edf3; padding-left: 4px; padding-right: 4px;"  > <img src="landing/images/ver.svg" height="14" > ver</button>';
                }
            },


        ]


    });

    table.on('order.dt search.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
});
function activarO(id){
    bootbox.confirm({
        message: "¿Desea activar esta organización?",
        buttons: {
            confirm: {
                label: 'Si',
                className: 'btn-primary'
            },
            cancel: {
                label: 'No',
                className: 'btn-light'
            }
        },
        callback: function (result) {
            if (result == true) {
             b=1;
                $.ajax({
                    type: "POST",
                    url: "/activacionOrg",
                    data: {id,b},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    statusCode: {
                        /*401: function () {
                            location.reload();
                        },*/
                        419: function () {
                            location.reload();
                        }
                    },
                    success: function (data) {
                        $('#tablaOrgan').DataTable().ajax.reload();


                    },
                    error: function () {}
                });

            }
        }
    });
}
function desactivarO(id){
    bootbox.confirm({
        message: "¿Desea desactivar esta organización?",
        buttons: {
            confirm: {
                label: 'Si',
                className: 'btn-primary'
            },
            cancel: {
                label: 'No',
                className: 'btn-light'
            }
        },
        callback: function (result) {
            if (result == true) {
             b=0;
                $.ajax({
                    type: "POST",
                    url: "/activacionOrg",
                    data: {id,b},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    statusCode: {
                        /*401: function () {
                            location.reload();
                        },*/
                        419: function () {
                            location.reload();
                        }
                    },
                    success: function (data) {
                        $('#tablaOrgan').DataTable().ajax.reload();


                    },
                    error: function () {}
                });

            }
        }
    });
}
function verUsuarios(idorgani){
    $('#cardsUsuarios').empty();
    $.ajax({
        type: "POST",
        url: "/superAdUsuario",
        data: {idorgani},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
           401: function () {
                location.reload();
            },
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            htmlApe='';
            $.each(data, function (index, usuario) {
                if(usuario.rol_id==1){
                htmlUs='<div class="col-xl-4 col-lg-6">'+
                '<div class="card" style="border: 1px solid #dedede;">'+
                    '<div class="card-body">'+
                        '<div class="badge badge-secondary float-right">'+usuario.rol_nombre+'</div>'+
                        '<p class="text-secondary font-size-12 mb-2">Tipo de usuario:</p>'+
                        '<h5 style="font-size:13px!important"><a href="#" class="text-dark">'+usuario.perso_nombre+' '+usuario.perso_apPaterno+' '+usuario.perso_apMaterno+'</a></h5>'+
                        '<p class="text-muted mb-4">'+usuario.email+'</p>'+
                        '<div class="row">'+
                        '<div class="col-md-3">'+
                            '<a href="javascript: void(0);">'+
                                '<img src="landing/images/usuario.svg" alt="" class="avatar-sm m-1 rounded-circle">'+
                            '</a>'+
                            '</div>'+
                            '<div class="row col-md-9" style=" padding-right: 0px;padding-left: 16px;">'+
                           '<label style="font-weight:600">Fecha de nac: &nbsp; </label>'+''+ moment(usuario.perso_fechaNacimiento).format('DD/MM/YYYY')+
                           '<label style="font-weight:600">Género: </label>'+'&nbsp; '+ usuario.perso_sexo+'&nbsp;&nbsp; '+
                           '<label style="font-weight:600">Celular: <i class="uil  uil-mobile-android-alt mr-1"></i> </label>'+' '+ usuario.perso_celular+
                           '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="card-body border-top">'+
                        '<div class="row align-items-center">'+
                            '<div class="col-sm-auto">'+
                                '<ul class="list-inline mb-0">'+
                                    '<li class="list-inline-item pr-2">'+
                                        '<a href="#" class="text-muted d-inline-block"'+
                                            'data-toggle="tooltip" data-placement="top" title=""'+
                                            'data-original-title="Due date">'+
                                            '<i class="uil uil-calender mr-1"></i>Registrado: '+ moment(usuario.updated_at).format('DD/MM/YYYY')+
                                        '</a>'+
                                    '</li>'+

                               '</ul>'+
                            '</div>'+
                            '<div class="col offset-sm-1">'+
                                '<div class="progress mt-4 mt-sm-0" style="height: 5px;"'+
                                    'data-toggle="tooltip" data-placement="top" title=""'+
                                    'data-original-title="100% completed">'+
                                    '<div class="progress-bar bg-secondary" role="progressbar" style="width: 100%;"'+
                                        'aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>';}
            else{
                htmlUs='<div class="col-xl-4 col-lg-6">'+
                '<div class="card" style="border: 1px solid #dedede;">'+
                    '<div class="card-body">'+
                        '<div class="badge badge-warning float-right">'+usuario.rol_nombre+'</div>'+
                        '<p class="text-warning font-size-12 mb-2">Tipo de usuario:</p>'+
                        '<h5 style="font-size:13px!important"><a href="#" class="text-dark">'+usuario.perso_nombre+' '+usuario.perso_apPaterno+' '+usuario.perso_apMaterno+'</a></h5>'+
                        '<p class="text-muted mb-4">'+usuario.email+'</p>'+
                        '<div class="row">'+
                        '<div class="col-md-3">'+
                            '<a href="javascript: void(0);">'+
                                '<img src="landing/images/usuario.svg" alt="" class="avatar-sm m-1 rounded-circle">'+
                            '</a>'+
                            '</div>'+
                            '<div class="row col-md-9" style=" padding-right: 0px;padding-left: 16px;">'+
                           '<label style="font-weight:600">Fecha de nac: &nbsp; </label>'+''+ moment(usuario.perso_fechaNacimiento).format('DD/MM/YYYY')+
                           '<label style="font-weight:600">Género: </label>'+'&nbsp; '+ usuario.perso_sexo+'&nbsp;&nbsp; '+
                           '<label style="font-weight:600">Celular: <i class="uil  uil-mobile-android-alt mr-1"></i> </label>'+' '+ usuario.perso_celular+
                           '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="card-body border-top">'+
                        '<div class="row align-items-center">'+
                            '<div class="col-sm-auto">'+
                                '<ul class="list-inline mb-0">'+
                                    '<li class="list-inline-item pr-2">'+
                                        '<a href="#" class="text-muted d-inline-block"'+
                                            'data-toggle="tooltip" data-placement="top" title=""'+
                                            'data-original-title="Due date">'+
                                            '<i class="uil uil-calender mr-1"></i>Registrado: '+ moment(usuario.updated_at).format('DD/MM/YYYY')+
                                        '</a>'+
                                    '</li>'+

                               '</ul>'+
                            '</div>'+
                            '<div class="col offset-sm-1">'+
                                '<div class="progress mt-4 mt-sm-0" style="height: 5px;"'+
                                    'data-toggle="tooltip" data-placement="top" title=""'+
                                    'data-original-title="100% completed">'+
                                    '<div class="progress-bar bg-warning" role="progressbar" style="width: 100%;"'+
                                        'aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>';
            }
            htmlApe=htmlApe+htmlUs;
            });

            $('#cardsUsuarios').append(htmlApe);

        },
        error: function () {}
    });
    $('#modalUsuario').modal('show');
}
