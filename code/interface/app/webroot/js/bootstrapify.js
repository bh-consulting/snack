/*
* Twitter Bootstrappifier for CakePHP
*
* Author: Julien Guepin
*
* CakePHP Twitter Bootstrappifier
*
* Selects all CakePHP incompatible forms and buttons,
* and converts them into pretty Twitter Bootstrap style.
*
*/

var Boostrapify = {
    load    : function(){

        // FORMS
        $('form').addClass('form-horizontal');
        $('div.input').wrap('<div class="control-group"></div>');
        $('label').addClass('control-label');
        $('input').wrap('<div class="controls"></div>');
        //All submit forms converted to primary button
        $('input[type="submit"]').addClass('btn btn-primary');

        // MESSAGES
        $('.message').addClass('alert alert-info');
        $('.flash_success').addClass('alert alert-success');
        $('.flash_warning').addClass('alert');
        $('.error-message').addClass('alert alert-error');
        $('.form-error').addClass('error');

        // ELEMENTS
        $('table').addClass('table table-hover table-bordered');
    }
}

// styling starts when document loads
$(document).ready(function(){
    Boostrapify.load();
});