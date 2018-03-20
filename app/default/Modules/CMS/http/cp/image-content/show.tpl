{view get="image-content/:content_id" content=$content wrapper="div class=slot-content-container"}

<div class="content-form form form-horizontal" {action_attributes put="cp/:cp/image-content/:content_id" content_id=$content.id}>

    <div class="content-params">
        <h2 class="params-header">Параметры<br>изображения</h2>
        <div class="form-group">
            <label class="control-label col-sm-12">Изображение</label>
            {capture name=src}{img id=$content.params.image.id return=src}{/capture}
            {strip}
                <div class="image-control col-sm-12" {action_attributes post="cp/:cp/images"} style="text-align:right;">
                    <div class="thumbnail action-upload-image">
                        <img src="{$smarty.capture.src}" style="max-width:200px;max-height:200px" />
                        <input type="hidden" name="image_id" value="{$content.params.image.id|default:0}" />
                        <center>{if $content.params.image.id}{$content.params.image.width}x{$content.params.image.height}{else}{t}Click to upload{/t}{/if}</center>
                        <div class="message" style="text-align: center;"></div>
                    </div>
                    <a href="#" class="btn btn-sm btn-default action-upload-image">Upload new</a>
                </div>
            {/strip}
        </div>

        {include file="Skin/form-line-input.tpl" labelColumn=12 title="Ссылка" name="url" value=$content.params.url|default:''}
        {include file="Skin/form-line-input.tpl" labelColumn=12 title="Заголовок" name="title" value=$content.params.title|default:''}

        {view get="cp/:cp/content-params/block" display=width param=$content.params.width}
        {view get="cp/:cp/content-params/block" display=offset-left param=$content.params.offset_left}

        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</div>

{script src="/vendor/jquery-file-upload/js/jquery.fileupload.js"}{/script}
{script}
{literal}
    <script>

        Pina.slotEditor.on('image-content', 'edit', function (currentBlock) {

            var uploadImage = function (elem, callback) {

                $('.progress-bar', elem).css('width', 0 + '%');
                $('.progress', elem).addClass('hidden');

                $('.message', elem).html('');

                var url = '/' + $(elem).data('resource');

                if (!url) {
                    console.log('target resource has been not found', elem, url);
                    return;
                }

                var configured = true;
                var id = $(elem).data('upload-id');

                if (!id) {
                    id = Math.floor(Math.random() * 1000000) + 1;
                    configured = false;
                }

                var formId = 'upload-form' + id;
                var uploadInputId = 'upload-input' + id;

                if (!configured) {

                    $('body').append(
                        '<form action="' + url + '" method="POST" enctype="multipart/form-data" id="' + formId + '" style="display:none;">'
                        + '<input name="Filedata" id="' + uploadInputId + '" type="file" multiple />'
                        + '</form>'
                        );

                    $('#' + uploadInputId).fileupload({
                        url: url,
                        dataType: 'json',
                        headers: $(elem).data('csrf-token') ? {'X-CSRF-Token': $(elem).data('csrf-token')} : {},
                        add: function (e, data) {
                            $('.progress', elem).removeClass('hidden');
                            $('.progress-bar', elem).css('width', 0 + '%');
                            data.submit();
                        },
                        done: function (e, data) {
                            if (!data || !data.result || !data.result['image'] || !data.result['image']['id']) {
                                $('.message', elem).html('File can not been uploaded');
                                return false
                            }

                            if (callback) {
                                callback(data);
                            }

                        },
                        fail: function (e, data) {
                            $('.message', elem).html('<div class="alert alert-danger">File can not been uploaded:<br />' + data.jqXHR.status + ' ' + data.jqXHR.statusText + '</div>');
                        },
                        progressall: function (e, data) {
                            var progress = parseInt(data.loaded / data.total * 100, 10);
                            $('.progress-bar', elem).css('width', progress + '%');
                        }
                    }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');

                }

                $("#" + uploadInputId).trigger('click');

            };

            $(".action-upload-image", currentBlock).on('click', function () {
                var elem = $(this).parents('.image-control');
                uploadImage(elem, function (data) {
                    $(elem).find('img').attr('src', data.result['image']['url']);
                    $(elem).find('input[name=image_id]').val(data.result['image']['id']).trigger('change');
                });
                return false;
            });

            Pina.slotEditor.showCurrentContentParams();

            $('.content-editable form input[name=url]', currentBlock).focus();
            $(currentBlock).on('keydown', '.content-editable form', function (e) {
                if ((e.key && e.key == 'Enter' && e.ctrlKey) || (e.keyCode && e.keyCode == 13 && e.ctrlKey)) {
                    e.preventDefault();

                    Pina.slotEditor.saveCurrentContent();
                }

                if ((e.key && e.key == 'Escape') || (e.keyCode && e.keyCode == 27)) {
                    e.preventDefault();

                    Pina.slotEditor.cancelCurrentContentChanges();
                }
            });


            currentBlock.find('.content-params')
                .find('select,input')
                .on('change', function () {
                    var name = $(this).attr('name');
                    var value = $(this).val();

                    var tag = $(currentBlock).find('.content-editable .slot-content-container img');

                    if (name == 'width') {
                        tag.removeClass(
                            'col-sm-1 col-sm-2 col-sm-3 col-sm-4 col-sm-5 col-sm-6 ' +
                            'col-sm-7 col-sm-8 col-sm-9 col-sm-10 col-sm-11 col-sm-12 '
                            );
                        tag.addClass(value);
                    } else if (name == 'offset_left') {
                        tag.removeClass(
                            'col-sm-offset-1 col-sm-offset-2 col-sm-offset-3 col-sm-offset-4 ' +
                            'col-sm-offset-5 col-sm-offset-6 col-sm-offset-7 col-sm-offset-8 col-sm-offset-9 ' +
                            'col-sm-offset-10 col-sm-offset-11 '
                            );
                        tag.addClass(value);
                    } else if (name == 'image_id') {
                        var src = currentBlock.find('.content-params .image-control > .thumbnail > img').attr('src');
                        tag.attr('src', src);
                    }

                    var wrapper = tag.parents('.image-content');
                    if (tag.attr('class') == 'image') {
                        wrapper.removeClass('row');
                    } else {
                        wrapper.addClass('row');
                    }
                });
        });

    </script>
{/literal}
{/script}