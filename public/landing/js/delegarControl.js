$("#selectTodoCheck").click(function(){
    if($("#selectTodoCheck").is(':checked') ){
        $("#nombreEmpleado > option").prop("selected","selected");
        $("#nombreEmpleado").trigger("change");
    }else{
        $("#nombreEmpleado > option").prop("selected",false);
         $("#nombreEmpleado").trigger("change");
     }
});
