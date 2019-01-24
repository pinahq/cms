{view get="banner-content/:id" content=$content}

<div class="content-form form" {action_attributes put="cp/:cp/banner-content/:content_id" content_id=$content.id}>

    <div class="content-params" style="width:60%;">
        <fieldset class="image-control" {action_attributes post="cp/:cp/images"}>
            <div class="images row">
                {if $content.params.images}
                    {foreach from=$content.params.images key=key item=i}
                        {view get="cp/:cp/banner-content/:id/images/:id" image=$i key=$key}
                    {/foreach}             
                {/if}
            </div>
            <div class="template" style="display:none">
                {view get="cp/:cp/banner-content/:id/images/:id"}
            </div>
        </fieldset>

        <div class="form-group">
            <div class="col-sm-12">
                <button class="btn btn-default btn-raised action-upload-image">Загрузить изображение</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>

    </div>
</div>

{script src="/vendor/jquery-file-upload/js/jquery.fileupload.js"}{/script}
{script}
{literal}
    <script>
        Pina.slotEditor.on('banner-content', 'edit', function (currentBlock) {
            var elem = $(".image-control");

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

                    var headers = $(elem).data('csrf-token') ? {'X-CSRF-Token': $(elem).data('csrf-token')} : {};

                    $('#' + uploadInputId).fileupload({
                        url: url,
                        dataType: 'json',
                        headers: headers,
                        add: function (e, data) {
                            $('.progress', elem).removeClass('hidden');
                            $('.progress-bar', elem).css('width', 0 + '%');
                            data.submit();
                        },
                        done: function (e, data) {
                            if (!data || !data.result || !data.result['id']) {
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

            Pina.slotEditor.showCurrentContentParams();

            $(".action-upload-image", currentBlock).on('click', function () {
                var elem = $(currentBlock).find('.image-control');
                uploadImage(elem, function (data) {
                    console.log(elem, data);
                    var newItem = $(elem).find('.template > div').clone();
                    newItem.find('.image').css('background-image', 'url(' + data.result['url'] + ')');
                    newItem.find('input.media_id').val(data.result['id']);
                    newItem.find('.sizes').text(data.result['width'] + 'x' + data.result['height']);
                    $(elem).find('.images').append(newItem);

                });
                return false;
            });


            $(currentBlock).on('click', ".content-editable .action-remove", function () {
                $(this).parents('.col').remove();
                return false;
            });

            $('.images', elem).sortable({
                placeholder: "image image-placeholder ui-corner-all"
            });

        });
    </script>
{/literal}
{/script}
