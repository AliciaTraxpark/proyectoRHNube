$('#cargaMasivaF').click('change',function(){
    $('#modalInformacionF').modal();
});
$('#cerrarIF').click('change',function(){
    $('#modalInformacionF').modal('toggle');
    $('#modalMasivaFoto').modal();
});
$(document).ready(function() {
    $("#fileMasiva").fileinput({
        browseLabel: 'Seleccionar Carpeta...',
        allowedFileExtensions: ['jpg','jpeg','png'],
        uploadUrl:'/subirfoto',
        uploadAsync: true,
        overwriteInitial: false,
        validateInitialCount: true,
        showUpload:true,
        minFileCount:6,
        initialPreviewAsData: true ,// identify if you are sending preview data only and not the markup
        language: 'es',
        showBrowse: true,
        browseOnZoneClick: true,
        theme: "fa"
    });
    $("#fileMasiva").on('fileuploaded',function(event, data, previewId, index){
        var files = [];
        for (var i = 0; i < $(this)[0].files.length; i++) {
            files.Push($(this)[0].files[i].name);
        }

        for(var i = 0; i< files.length;i++){
            for(var j = 0; j < data[0].length; j++){
                if(files[i] == data[j].emple_nroDoc){
                    var foto = files[i];
                    $.ajax({
                        url:"/subirfoto",
                        method: "post",
                        data:{foto:foto},
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function(data){},
                        error:function(){ alert("Hay un error");}
                    })
                }else{
                    $errors[i] = $files[i].name;
                }
            }
        }
        if (!empty($errors)) {
            $img = count($errors) === 1 ? 'file "' + $error[0]  + '" ' : 'files: "' + implode('", "', $errors) + '" ';
            $out['error'] = 'Oh snap! We could not upload the ' + $img + 'now. Please try again later.';
        }
        return $out;
    })
});

