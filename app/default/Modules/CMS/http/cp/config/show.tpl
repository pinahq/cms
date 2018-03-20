{if $group_params}
    <link rel="stylesheet" href="/static/default/css/image-control.css">

    {form action="cp/:cp/config" method="put" class="form form-horizontal pina-form form-config"}
        {foreach from=$group_params key=group item=ps}
        <input type="hidden" name="namespace" value="{$params.namespace}" />
        <div class="panel">
            <div class="panel-body">
                <h4>{$group}</h4>

                {foreach from=$ps item=param}
                    {if $param.type eq "select"}
                        {include file="Skin/form-line-select.tpl" list=$param.variants title=$param.title id=$param.key name="params[`$param.key`]" value=$param.value}
                    {elseif $param.type eq "checkbox"}
                        <div class="form-group">
                            <label class="control-label col-sm-2">{$param.title}</label>
                            <div class="col-sm-10">
                                <div class="togglebutton" style="margin: 1rem 0;">
                                    <label>
                                        <input type="hidden" id="{$param.key}" name="params[{$param.key}]" class="action-toggle-value" value="{$param.value}" />
                                        <input type="checkbox" class="action-toggle {$param.key}" value="Y" {if $param.value eq 'Y'}checked="checked"{/if} />
                                    </label>
                                </div>
                            </div>
                        </div>
                    {elseif $param.type eq "image"}
                        <div class="form-group">
                            <label class="control-label col-sm-2">Изображение</label>
                            <div class="image-control col-sm-10" {action_attributes post="cp/:cp/images" content_id=0}>
                                <div class="images select-images action-upload-image" data-value="{$param.key}">
                                    {if $param.value}
                                        {module get="cp/:cp/config/:namespace/images/:image_id" namespace=$param.namespace image_id=$param.value key=$param.key}
                                    {else}
                                        <div class="image thumbnail" style="background-color:#fff;cursor: pointer;">
                                            <input type="hidden" name="params[{$param.key}]" value="0" />
                                            <a class="image-center" href="#" target="blank"><span>{t}Click to upload{/t}</span></a>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {elseif $param.type eq 'textarea'}
                        {include file="Skin/form-line-textarea.tpl" rows=10 title=$param.title id=$param.key name="params[`$param.key`]" value=$param.value}
                    {else}
                        {include file="Skin/form-line-input.tpl" title=$param.title id=$param.key name="params[`$param.key`]" value=$param.value}
                    {/if}
                    
                    {assign var="resource" value=$param.resource}
                {/foreach}

                {if $resource}
                    {module get=$resource}
                {/if}
            </div>
        </div>
        {/foreach}

        <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
                <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
            </div>
        </div>
    {/form}

    {script src="/vendor/jquery.form.js"}{/script}
    {script src="/static/default/js/pina.skin.js"}{/script}
    {script src="/static/default/js/pina.request.js"}{/script}
    {script src="/static/default/js/pina.form.js"}{/script}
    {script src="/vendor/jquery-file-upload/js/jquery.fileupload.js"}{/script}

    {script}
    {literal}
    <script>
        $(".form-config").on("success", function () {
            document.location.reload();
        });

        $(".action-toggle").on('change', function () {
            var $el = $(this);
            var value = $el.is(":checked") ? 'Y' : 'N';
            $el.prev('.action-toggle-value').val(value);
        });

        var uploadImage = function(elem, callback) {
            $('.progress-bar', elem).css('width', 0 + '%');
            $('.progress', elem).addClass('hidden');
            
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
                        if (!data || !data.result || !data.result['image'] || !data.result['image']['id']) {
                            PinaSkin.alert($('.field', elem), 'File can not been uploaded');
                            return false
                        }
                        
                        if (callback) {
                            callback(data);
                        }
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('.progress-bar', elem).css('width', progress + '%');
                    }
                }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
                
            }

            $("#" + uploadInputId).trigger('click');
        }
        
        $(".action-upload-image").on('click', function () {
            var elem = $(this).parents('.image-control');
            var configKey = $(this).attr('data-value');

            uploadImage(elem, function(data) {
                var url = '/' + $(elem).data('resource');
                
                $.ajax({
                    type: 'get',
                    url: url.replace('/images', '/') +'config/{/literal}{$params.namespace}{literal}/images/'+ data.result['image']["id"] +'?key='+ configKey,
                    success: function (html) {
                        $('.images', elem).html(html);
                    },
                    error: function () {
                        alert('error');
                    },
                    dataType: 'html'
                });
            });

            return false;
        });
    </script>
    {/literal}
    {/script}
{/if}