$(function() {
    'use strict';

    $.fn.manageImages = function() {
        var elem = this;
        var url = '/' + $(elem).data('resource');

        if (!$("#upload-form").is("#upload-form")) {
            $('body').append(
                '<form action="' + url + '" method="POST" enctype="multipart/form-data" id="upload-form" style="display:none;">'
                + '<input name="Filedata" id="upload-input" type="file" multiple />'
                + '</form>'
            );
        }
        
        $('#upload-input').fileupload({
            url: url,
            dataType: 'json',
            headers: $(elem).data('csrf-token') ? {'X-CSRF-Token': $(elem).data('csrf-token')} : {},
            add: function(e, data) {
                $('.progress', elem).removeClass('hidden');
                $('.progress-bar', elem).css('width', 0 + '%');
                data.submit();
            },
            done: function(e, data) {
                if (!data || !data.result || !data.result['image'] || !data.result['image']['id']) {
                    PinaSkin.alert($('.field', elem), 'File can not been uploaded');
                    return false
                }

                $.ajax({
                    type: 'get',
                    url: url + '/' + data.result['image']["id"],
                    success: function(html) {
                        $('.images > .action-upload-image', elem).before(html);
                    },
                    error: function() {
                        alert('error');
                    },
                    dataType: 'html'
                });
            },
            progressall: function(e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('.progress-bar', elem).css('width', progress + '%');
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

        $(".action-upload-image", elem).on('click', function() {
            $('.progress-bar', elem).css('width', 0 + '%');
            $('.progress', elem).addClass('hidden');
            $("#upload-input").trigger('click');
            return false;
        });
        
        $(elem).on('click', ".image .action-remove", function() {
            $(this).parent().remove();
            return false;
        });

        $('.images', elem).sortable({
            placeholder: "image image-placeholder ui-corner-all"
        });
    }
});