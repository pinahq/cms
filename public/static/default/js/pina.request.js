var PinaRequest = {}

PinaRequest.handle = function (elem, data)
{
    PinaSkin.hideErrors(elem);

    if (data && data.errors && data.errors.length > 0) {
        var errors = [];

        for (i = 0; i < data.errors.length; i++)
        {
            var m = data.errors[i];
            errors.push(m[0]);
            PinaSkin.addError(elem, m[1]?m[1]:'', m[0]);
        }

        PinaSkin.showErrors(elem);

        if (errors.length > 0) {
            PinaSkin.alertFew(elem, errors);
        }
        return false;
    }
    
    return true;
}

PinaRequest.handleRedirect = function (xhr) {
    var loc = '';
    if (loc = xhr.getResponseHeader('Location'))
    {
        location.href = loc;
        return true;
    }

    if (loc = xhr.getResponseHeader('Content-Location'))
    {
        location.href = loc;
        return true;
    }
    return false;
}