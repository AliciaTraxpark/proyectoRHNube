//select all empleados
$("#selectTodoCheck").click(function(){
    if($("#selectTodoCheck").is(':checked') ){
        $("#nombreEmpleado > option").prop("selected","selected");
        $("#nombreEmpleado").trigger("change");
    }else{
        $("#nombreEmpleado > option").prop("selected",false);
         $("#nombreEmpleado").trigger("change");
     }
});

//selct all area
$("#selectAreaCheck").click(function(){
    if($("#selectAreaCheck").is(':checked') ){
        $("#selectArea > option").prop("selected","selected");
        $("#selectArea").trigger("change");
    }else{
        $("#selectArea > option").prop("selected",false);
         $("#selectArea").trigger("change");
     }
});

///////////////seleccionar empleado por area
$('#selectArea').change(function(e){
    var idempresarial=[];
 idempresarial=$('#selectArea').val();
 textSelec=$('select[name="selectArea"] option:selected:last').text();
 textSelec2=$('select[name="selectArea"] option:selected:last').text();

 palabraEmpresarial=textSelec.split(' ')[0];
 if(palabraEmpresarial=='Area'){
    $.ajax({
        type: "post",
        url: "/horario/empleArea",
        data: {
            idarea: idempresarial
        },
        statusCode: {

            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $("#nombreEmpleado > option").prop("selected",false);
            $("#nombreEmpleado").trigger("change");
            $.each( data, function( index, value ){
                 $("#nombreEmpleado > option[value='"+value.emple_id+"']").prop("selected","selected");
                 $("#nombreEmpleado").trigger("change");
            });
        console.log(data);
        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
 }
})

/* jQuery.fn.filterByText = function(textbox) {
    return this.each(function() {
      var select = this;
      var options = [];
      $(select).find('option').each(function() {
        options.Push({
          value: $(this).val(),
          text: $(this).text()
        });
      });
      $(select).data('options', options);

      $(textbox).bind('change keyup', function() {
        var options = $(select).empty().data('options');
        var search = $.trim($(this).val());
        var regex = new RegExp(search, "gi");

        $.each(options, function(i) {
          var option = options[i];
          if (option.text.match(regex) !== null) {
            $(select).append(
              $('<option>').text(option.text).val(option.value)
            );
          }
        });
      });
    });
  };

  // You could use it like this:

  $(function() {
    $('select').filterByText($('input'));
  });
 */
