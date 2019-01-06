<link rel="stylesheet" href="/static/default/css/image-control.css">

<fieldset class="image-control" {action_attributes post="cp/:cp/media"}>
    <div class="field">
        <div class="images select-images">
            {if $items}
                {foreach from=$items item=i}
                    {strip}
                        <div class="image thumbnail" style="background-image:url('{$i.url}');">
                            <a class="remove action-remove" href="#"><i class="glyphicon glyphicon-remove"></i></a>

                            <input type="hidden" name="media_ids[]" value="{$i.id}" />
                            <a class="image-center" href="{$i.url}" target="blank"><span>{$i.width}x{$i.height}</span></a>
                        </div>
                    {/strip}
                {/foreach}                
            {/if}
            <div class="image action-upload-image thumbnail" style="background-color:#fff;cursor: pointer;">
                <a class="image-center" href="#" target="blank"><span>click to add</span></a>
            </div>
        </div>
    </div>
</fieldset>

<script id="template-uploaded-media" type="html/template">
    {strip}
        <div class="image thumbnail">
            <a class="remove action-remove" href="#"><i class="glyphicon glyphicon-remove"></i></a>

            <input type="hidden" name="media_ids[]" value="" />
            <a class="image-center" href="" target="blank"><span></span></a>
        </div>
    {/strip}
</script>

{script src="/vendor/jquery-file-upload/js/jquery.fileupload.js"}{/script}

{script}
{literal}
    <script>


        $(document).ready(function () {

            var Uploader = function (url, headers, options) {

                var id = Math.floor(Math.random() * 1000000) + 1;

                var createForm = function (url) {
                    var formId = 'upload-form' + id;
                    var inputId = 'upload-input' + id;

                    if (document.getElementById(formId) && document.getElementById(inputId)) {
                        console.log('existed!!');
                        return $("#" + inputId);
                    }

                    $('body').append('<form action="' + url + '" method="POST" enctype="multipart/form-data" id="' + formId + '" style="display:none;">'
                            + '<input name="Filedata" id="' + inputId + '" type="file" multiple />'
                            + '</form>'
                            );
                    return $("#" + inputId);
                };

                return {
                    upload: function () {
                        if (options.onProgressBar) {
                            options.onProgressBar(-1);
                        }
                        var uploadInput = createForm(url);

                        $(uploadInput).fileupload({
                            url: url,
                            dataType: 'json',
                            headers: headers,
                            add: function (e, data) {
                                if (options.onProgressBar) {
                                    options.onProgressBar(0);
                                }
                                data.submit();
                            },
                            done: function (e, data) {
                                if (!data || !data.result || !data.result.id) {
                                    if (options.onError) {
                                        options.onError(data);
                                    }
                                    return false
                                }

                                console.log(options);

                                console.log('data', data);

                                if (options.onUpload) {
                                    options.onUpload(data.result);
                                }
                            },
                            progressall: function (e, data) {
                                if (options.onProgressBar) {
                                    var progress = parseInt(data.loaded / data.total * 100, 10);
                                    options.onProgressBar(progress);
                                }
                            }
                        }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');

                        $(uploadInput).trigger('click');
                    }
                };



                $('.progress', elem).addClass('hidden');
                $('.progress-bar', elem).css('width', 0 + '%');


                if (!url) {
                    console.log('target resource has been not found', elem, url);
                    return;
                }

            }

            var container = $("fieldset.image-control");
            var url = '/' + $(container).data('resource');
            var headers = $(container).data('csrf-token') ? {'X-CSRF-Token': $(container).data('csrf-token')} : {};
            var uploader = new Uploader(url, headers, {
                onUpload: function (data) {
                    var html = $("#template-uploaded-media").html();
                    $item = $(html);
                    $item.css('background-image', "url('" + data.url + "')");
                    $item.find('a.image-center').attr('href', data.url);
                    $item.find('input').val(data.id);
                    $item.find('span').html(data.width + 'x' + data.height);

                    $('.images > .action-upload-image', container).before($item);
                }
            });

            $(".action-upload-image", container).on('click', function () {
                uploader.upload();
                return false;
            });

            $(container).on('click', ".image .action-remove", function () {
                $(this).parent().remove();
                return false;
            });

            $('.images', container).sortable({
                placeholder: "image image-placeholder ui-corner-all"
            });

            return false;
        });

    </script>
{/literal}
{/script}