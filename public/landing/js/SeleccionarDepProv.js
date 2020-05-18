$(function(){
    $('#departamento').on('change', onSelectDepartamento);
    $('#provincia').on('change', onSelectProvincia);
  });

  function onSelectDepartamento(){
    var depar_id = $(this).val();
    
    $.get('/api/departamento/'+depar_id+'/niveles', function(data){
      var html_select = '<option value="">PROVINCIA</option>';
      var html_dist = '<option value="">DISTRITO</option>';
      for(var i=0; i<data.length; i++)
          html_select += '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
          $('#provincia').html(html_select);
          $('#distrito').html(html_dist);
    });
  }
  function onSelectProvincia(){
    var prov_id = $(this).val();

    $.get('/api/provincia/'+prov_id+'/niveles', function(data){
      var html_select = '<option value="">DISTRITO</option>';
      for(var i=0; i<data.length; i++)
          html_select += '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
          $('#distrito').html(html_select);
    });
  }
  $(function(){
    $('#dep').on('change', onSelectDepart);
    $('#prov').on('change', onSelectProv);
  });

  function onSelectDepart(){
    var depar_id = $(this).val();
    
    $.get('/api/departamento/'+depar_id+'/niveles', function(data){
      var html_select = '<option value="">PROVINCIA</option>';
      var html_dist = '<option value="">DISTRITO</option>';
      for(var i=0; i<data.length; i++)
          html_select += '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
          $('#prov').html(html_select);
          $('#dist').html(html_dist);
    });
  }
  function onSelectProv(){
    var prov_id = $(this).val();

    $.get('/api/provincia/'+prov_id+'/niveles', function(data){
      var html_select = '<option value="">DISTRITO</option>';
      for(var i=0; i<data.length; i++)
          html_select += '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
          $('#dist').html(html_select);
    });
  }