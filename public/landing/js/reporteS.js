$('#fecha').daterangepicker({
    "locale": {
        "format": "YYYY-MM-DD",
        "separator": " a ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cerrar",
        "customRangeLabel": "Seleccionar Fechas",
        "daysOfWeek": [
            "Do",
            "Lu",
            "Ma",
            "Mi",
            "Ju",
            "Vi",
            "Sa"
        ],
        "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Setiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
    },
    ranges: {
        'Hace 1 día': [moment().subtract(1, 'days'), moment().add('days')],
        'Hace 1 semana': [moment().subtract(6, 'days'), moment()],
        'Hace 1 mes': [moment().subtract(29, 'days'), moment()],
    }
});
$('#fecha').val('');
$(function(){
    $('#fecha').on('cancel.daterangepicker', function(ev, picker) {
        $('#fecha').val('');
    });
    $("#fecha").on('apply.daterangepicker',onSelectFechas);
});

function acumular60(suma,acumulado){
    if(suma > 60){
        suma = suma - 60;
        acumulado += 1;
        acumular60(suma,acumulado);
    }

    if(suma == 60){
        suma = 0;
        acumulado += 1;
        acumular60(suma,acumulado);
    }
    return[suma,acumulado];
}

function sumarHora(a,b){
    if(!a) return b;
    let resultado = [];
    a = a.split(":").reverse();
    b = b.split(":").reverse();
    let acumulado = 0;
    let sumaAcumulado;
    let suma = parseInt(a[0]) + parseInt(b[0]);
    sumaAcumulado = acumular60(suma,acumulado);
    resultado.push(sumaAcumulado[0]);
    suma = parseInt(a[1]) + parseInt(b[1]) + sumaAcumulado[1];
    acumulado = 0;
    sumaAcumulado = acumular60(suma,acumulado);
    resultado.push(sumaAcumulado[0]);
    suma = parseInt(a[2]) + parseInt(b[2]) + sumaAcumulado[1];
    resultado.push(suma);
    resultado.reverse();
    return resultado.join(":");
}

function onSelectFechas(){
    var fecha = $('#fecha').val();
    if($.fn.DataTable.isDataTable("#Reporte")){
        $('#Reporte').DataTable().destroy();
    }
    $('#empleado').empty();
    $('#dias').empty();
    $('#myChart').show();
    $.ajax({
        url:"reporte/empleado",
        method: "GET",
        data:{fecha:fecha},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            var container = $('#empleado');
            var containerD = $('#dias');
            var nombre =[];
            var horas=[];
            var prom=[];
            var color = ['rgb(63,77,113)'];
            var borderColor = ['rgb(63,77,113)'];
            var html_tr = "";
            var html_trD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
            for(var i=0; i<data.length; i++){
                html_tr += '<tr><td>'+ data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                nombre.push(data[i].nombre.split('')[0]+data[i].apPaterno.split('')[0]+data[i].apMaterno.split('')[0]);
                var total = data[i].horas.reduce(function(a,b){
                    return sumarHora(a,b);
                });
                var promedio = data[i].promedio.reduce(function(a,b){
                    return sumarHora(a,b);
                });
                for(let j = 0; j < data[i].horas.length; j++){
                    html_tr += '<td>'+ data[i].horas[j] + '</td>';
                }
                var p1 = promedio.split(":");
                var t1 = total.split(":");
                var sumaT = parseInt(t1[0])*3600 + parseInt(t1[1])*60 + parseInt(t1[2]);
                var sumaP = parseInt(p1[0])*3600 + parseInt(p1[1])*60 + parseInt(p1[2]);
                var sumaTotalP = 0;
                if(sumaT != 0){
                    sumaTotalP = Math.round((sumaP*100)/sumaT);
                    prom.push(sumaTotalP);
                }else{
                    sumaTotalP = 0;
                    prom.push(sumaTotalP);
                }
                html_tr += '<td>'+ total +'</td>';
                html_tr += '<td>'+ sumaTotalP + '%' +'</td>';
                var decimal = parseFloat(total.split(":")[0] + "." + total.split(":")[1] + total.split(":")[2]).toFixed(2);
                horas.push(decimal);
                html_tr += '</tr>';
            }
            for(var m = 0; m < data[0].fechaF.length; m++){
                var momentValue = moment(data[0].fechaF[m]);
                    momentValue.toDate();
                    momentValue.format("ddd DD/MM");
                    html_trD += '<th>'+momentValue.format("ddd DD/MM")+'</th>';
            }
            html_trD += '<th>TOTAL</th>';
            html_trD += '<th>ACTIV.</th></tr>';
            $("#dias").html(html_trD);
            $("#empleado").html(html_tr);
            //container.append(html_tr);
            //containerD.append(html_trD);

            $("#Reporte").DataTable({
                "searching": true,
                "scrollX": true,
                retrieve: true,
                "ordering": false,
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
                dom: 'Bfrtip',
                /*buttons: [{
                    extend: 'excel',
                    text:"<i><img src='admin/images/excel.svg' height='20'></i> Descargar",
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];},
                        sheetName: 'Exported data',
                        autoFilter: false
                }],*/
                buttons: ["copy","excel","pdf","print"],
                paging: true
            });

            var chartdata = {
                labels: nombre,
                datasets: [{
                    label: nombre,
                    backgroundColor: color,
                    borderColor: color,
                    borderWidth: 2,
                    hoverBackgroundColor: color,
                    hoverBorderColor: borderColor,
                    data:horas
                }]
            };
            var mostrar = $("#myChart");
            var grafico = new Chart(mostrar, {
                type: 'bar',
                data: chartdata,
                options: {
                    responsive: true,
                    scales: {
                        xAxes: [{
                            stacked: true,
                            gridLines: {
                                display:false
                            } 
                        }],
                        yAxes: [{
                            stacked: true
                        }]
                    }
                }
            });
            $('#myChartD').hide();
        },
        error:function(data){
            $.notify("Error", {align:"right", verticalAlign:"top",type: "danger", icon:"warning"});
        }
    })
}