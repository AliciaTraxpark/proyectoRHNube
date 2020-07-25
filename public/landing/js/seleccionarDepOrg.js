$(function () {
    $('#depE').on('change', function () {
        onSelectDepartamentoOrgani('#depE');
    });
    $('#provE').on('change', function () {
        onSelectProvinciaOrgani('#provE');
    });
});
async function onSelectDepartamentoOrgani(dep) {
    var depar_id;
    if (dep) depar_id = $(dep).val();
    await $.get('/api/departamento/' + depar_id + '/niveles', function (data) {
        var html_select = '<option value="">PROVINCIA</option>';
        var html_dist = '<option value="">DISTRITO</option>';
        for (var i = 0; i < data.length; i++) {
            html_select += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
        }
        $('#provE').html(html_select);
        $('#distE').html(html_dist);
    });
}

async function onSelectProvinciaOrgani(prov) {
    var prov_id;
    if (prov) prov_id = $(prov).val();
    await $.get('/api/provincia/' + prov_id + '/niveles', function (data) {
        var html_select = '<option value="">DISTRITO</option>';
        for (var i = 0; i < data.length; i++) {
            html_select += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
        }
        $('#distE').html(html_select);
    });
}
