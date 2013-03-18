$(document).ready(function(){
    $('input.form-error').first().each(function(){
        var pos = $(this).parents('div.tab-pane').attr('id');
        $('#rootwizard').bootstrapWizard('show', pos.substr(-1));
        $(this).focus();
    });
});
