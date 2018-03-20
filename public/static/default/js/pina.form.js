$(document).ready(function () {
    $(".pina-form").each(function () {
        var self = this;
        $(self).ajaxForm({
            dataType: 'json',
            beforeSubmit: function () {
                PinaSkin.showWaitingOverlay();
            },
            success: function (result, status, jqXHR) {
                PinaSkin.hideOverlay();
                if (PinaRequest.handle(self, result)) {
                    $(self).trigger('success', [result, status, jqXHR]);
                } else {
                    $(self).trigger('error', [result, status, jqXHR]);
                };
            },
            error: function (jqXHR, status) {
                PinaSkin.hideOverlay();
                PinaRequest.handle(self, jqXHR.responseJSON);
                $(self).trigger('error', [jqXHR.responseJSON, status, jqXHR]);
            },
        });
    });
});