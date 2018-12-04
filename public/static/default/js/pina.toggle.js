$(".action-toggle").on('change', function () {
    var $el = $(this);
    var resource = $el.data('resource');
    var method = $el.data('method');
    var key = $el.data('key');
    if (!resource || !method || !key) {
        return;
    }
    var data = {};
    data[key] = $el.is(":checked") ? 'Y' : 'N';
    $.ajax({
        url: '/' + resource,
        method: method,
        data: data,
        headers: $el.data('csrf-token') ? {'X-CSRF-Token': $el.data('csrf-token')} : {},
        success: function (r) {
            var current = $el.is(":checked") ? 'Y' : 'N';
            if (r[key] != current) {
                $el.prop('checked', r[key] == 'Y' ? true : false);
            }
            $el.trigger("success");
        }
    });
    
});