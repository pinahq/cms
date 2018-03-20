$(function() {

    var elem = $('fieldset.operations');

    if (elem.length <= 0) {
        return;
    }

    var html = elem.html();
    var obj = $(document.createElement('div'))
            .attr('id', 'operations-fixed')
            .css('display', 'none')
            .addClass('operations operations-fixed-inactive');

    var form = elem.parents('form');
    var wrapper = $(document.createElement('div')).addClass('wrapper');
    var content = $(document.createElement('div')).addClass('content');

    content.html(elem.html());
    obj.append(wrapper).append(content).insertAfter(elem);

    obj.width(elem.width() + 20);

    var showFixed = function() {

        $('#operations-fixed').removeClass('operations-fixed-inactive');
        if ($(window).scrollTop() < elem.position().top - $(window).height()) {
            $('#operations-fixed').fadeIn('slow');
        }
    }

    $('input:text, input:password, textarea', form).bind('keydown', showFixed);
    $('input:radio, input:checkbox, select', form).bind('change', showFixed);

    $(window).scroll(function() {

        if (obj.length <= 0 || obj.hasClass('operations-fixed-inactive')) {
            return false;
        }

        if ($(window).scrollTop() > elem.position().top - $(window).height() && obj.not(':hidden')) {
            obj.fadeOut('slow');
        } else {
            if (obj.is(':hidden')) {
                obj.fadeIn('slow');
            }
        }
    });

});