var PinaSkin = {}

PinaSkin.alert = function (elem, message)
{

}

PinaSkin.alertFew = function (elem, a)
{
    var message = a.join("\r\n");
    PinaSkin.alert(elem, message);

}

PinaSkin.hideErrors = function (elem)
{
    $('.alert', elem).remove();
    $('.pina-error', elem).html('');
    $('.has-error', elem).removeClass('has-error');
}

PinaSkin.addError = function (elem, subject, message)
{
    if (!subject) {
        PinaSkin.showErrorPopup(elem, message);
        return;
    }
    var input = $(elem).find("[name=" + subject + "]");
    if (input.length == 0) {
        PinaSkin.showErrorPopup(elem, message);
        return;
    }
    input.parents('.form-group').addClass('has-error');
    var help = input.parents('.form-group').find('.pina-error');
    if (help.length > 0) {
        help.html(message);
    } else {
        input.after('<span class="help-block pina-error">' + message + '</span>');
    }
}

PinaSkin.showErrorPopup = function(elem, message)
{
    if ($('.pina-error-popup').length == 0) {
        $(elem).append('<div class="pina-error-popup" style="position:fixed; right: 0; top: 0;"></div>');
    }
    
    var alert = $('<div class="alert alert-danger" style="cursor: pointer;"><strong>Error!</strong> ' + message + '</div>');
    $('.pina-error-popup').append(alert);
    var deleteAlert = function() { alert.remove(); };
    alert.on('click', deleteAlert);
    setTimeout(deleteAlert, 15000)
}

PinaSkin.showErrors = function (elem)
{

}

PinaSkin.showWaitingOverlay = function (title, message) {
    /*
     $('body').addClass('modal-open')
     .append('<div class="modal fade in pina-overlay" role="dialog" tabindex="-1" style="display: block;">' 
     + '<div class="modal-dialog" role="document"><div class="modal-content">!!!!!!</div></div>' 
     + '</div>')
     .append('<div class="modal-backdrop in pina-overlay" role="dialog" tabindex="-1" style="display: block;"></div>');
     */
}

PinaSkin.hideOverlay = function (callback) {
    //$('.pina-overlay').remove();
}