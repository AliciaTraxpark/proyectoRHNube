$('#horaI').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
$('#horaF').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
$('#btnasignar').on('click', function(e) {
    var allVals = [];
    $(".sub_chk:checked").each(function() {
        allVals.push($(this).attr('data-id'));
    });

    if(allVals.length<=0)
    {
        alert("Selecciona al menos un empleado.");
        return false;
    }  else {
        $('#asignarHorario').modal();

            $('#confirmarE').click(function(){

            var join_selected_values = allVals.join(",");
            $.notify(" Empleado eliminado", {align:"right", verticalAlign:"top",type: "danger", icon:"bell",autoHide: true});
            $.ajax({
                url: "/eliminarEmpleados",
                type: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: 'ids='+join_selected_values,
                success: function (data) {

                    $(".sub_chk:checked").each(function() {
                            $(this).parents("tr").remove();
                        });
                        $('#modalEliminar').modal('hide');
                },
                error: function (data) {
                    alert(data.responseText);
                }
            });
        });
    }

});
