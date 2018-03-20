$(document).ready(function () {
    var textareaAutoHeight = function () {
        var diff = parseInt($(this).css('paddingBottom')) + parseInt($(this).css('paddingTop')) +
                parseInt($(this).css('borderTopWidth')) + parseInt($(this).css('borderBottomWidth'))
                || 0;

        var initialHeight = $(this).height();
        $(this).on('input keyup', function () {
            var currentScrollPosition = $(window).scrollTop();
            var newHeight = this.scrollHeight - diff;
            if (newHeight > initialHeight) {
                $(this).height(0).height(this.scrollHeight - diff);
            }
            $(window).scrollTop(currentScrollPosition);
        }).trigger('input');
    };

    $('.auto-height > textarea, .auto-height > div > textarea').each(textareaAutoHeight);
});