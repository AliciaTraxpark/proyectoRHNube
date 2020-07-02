var allVals = [];
$(".sub_chk:checked").each(function () {
    allVals.push($(this).attr('data-id'));
    $('#masivoC').show();
    if (allVals.length > 0) {
        $('#masivoC').show();
    } else {
        $('#masivoC').hide();
    }
});
