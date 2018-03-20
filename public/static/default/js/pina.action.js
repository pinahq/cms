var Pina = Pina || {};

Pina.action = function (options)
{
    if (options['modal'] != 'no-modal') {
        PinaSkin.showWaitingOverlay();
    }
    $.ajax({
        type: options['method'],
        url: '/' + options['resource'],
        data: options['params'],
        headers: options['headers'],
        success: function (result, status, xhr) {
            PinaSkin.hideOverlay();
            if (PinaRequest.handle($(options['wrapper']), result)) {
                if (!PinaRequest.handleRedirect(xhr)) {
                    var fn = options['success'];
                    fn(result, status, xhr);
                }
            };
        },
        error: function (xhr, status, errorThrown) {
            PinaSkin.hideOverlay();
            PinaRequest.handle($(options['wrapper']), $.parseJSON(xhr.responseText));
        },
        dataType: 'json'
    });

    return false;

}

Pina.actionOptions = function (elem) {
    var options = {};
    options['method'] = $(elem).data('method') ? $(elem).data('method') : '';
    options['resource'] = $(elem).data('resource') ? $(elem).data('resource') : '';
    options['params'] = $(elem).data('params') ? $(elem).data('params') : '';
    options['redirect'] = $(elem).data('redirect') ? $(elem).data('redirect') : '';
    options['wrapper'] = $(elem).data('wrapper') ? $(elem).data('wrapper') : 'body';
    options['modal'] = $(elem).data('modal-mode') ? $(elem).data('modal-mode') : '';
    options['headers'] = $(elem).data('csrf-token') ? {'X-CSRF-Token': $(elem).data('csrf-token')} : {};

    options['success'] = function (packet, status, xhr) {
        $(elem).trigger('success', [packet, status, xhr]);
    };
    return options;
}

Pina.actionHandler = function (elem) {
    var options = Pina.actionOptions(elem);
    Pina.action(options);
}

$(document).ready(function () {
    $('.pina-action').on('click', function () {
        Pina.actionHandler(this);
        return false;
    });
});