$(function(){
    $('#pais').on('change', onSelectPais);
  });

  function onSelectPais(){
    var pais_id = $(this).val();
    if (pais_id != 173) {
        document.getElementById('departamento').style.display="none";
    }else{
        document.getElementById('departamento').style.display="flex";
    }
  }