$(function(){
    $('#contrato').on('change', onSelectContrato);
  });

  function onSelectContrato(){
    var contrato_id = $(this).val();
    
    $.get('/api/contrato/'+contrato_id+'/contrato', function(data){
      var html_select = '<label></label>';
      for(var i=0; i<data.length; i++)
          html_select += '<label value="'+ data[i].id +'">'+ data[i].contrato_fechaI +'</label>';
          $('#c_fechaI').html(html_select);
    });
    $.get('/api/contrato/'+contrato_id+'/contrato', function(data){
      var html_selectF = '<label></label>';
    for(var i=0; i<data.length; i++)
          html_selectF += '<label value="'+ data[i].id +'">'+ data[i].contrato_fechaF +'</label>';
          $('#c_fechaF').html(html_selectF);
    });
  }