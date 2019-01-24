{view get="catalog-matrix-content/:content_id" content=$content wrapper="div class=slot-content-container"}

<div class="content-form form form-horizontal" {action_attributes put="cp/:cp/catalog-matrix-content/:content_id" content_id=$content.id}>
    <div class="content-params" style="min-width:60%;">
        {style src="/static/default/css/bootstrap-tagsinput.modified.css"}{/style}
        {style src="/vendor/bootstrap-tagsinput/bootstrap-tagsinput-typeahead.css"}{/style}
        {style src="/static/default/css/image-control.css"}{/style}

        <div class="row">

            <div class="col col-sm-6">
                {view get="cp/:cp/catalog-matrix-content/block" display=form-item item=$content.params.catalog.0 title="Верхняя строчка"}
            </div>

            <div class="col col-sm-6">
                {view get="cp/:cp/catalog-matrix-content/block" display=form-item item=$content.params.catalog.1 title="Нижняя строчка"}
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary">{t}Save{/t}</button>
            </div>
        </div>

    </div>
</div>

{script src="/vendor/jquery-file-upload/js/jquery.fileupload.js"}{/script}
{script src="/static/default/js/typeahead.bundle.modified.js"}{/script}
{script src="/static/default/js/bootstrap-tagsinput.modified.js"}{/script}

{script}
{literal}
    <script>
        Pina.slotEditor.on('catalog-matrix-content', 'edit', function (currentBlock) {

            var uploadImage = function (elem, callback) {

                $('.progress-bar', elem).css('width', 0 + '%');
                $('.progress', elem).addClass('hidden');

                var url = '/' + $(elem).data('resource');

                if (!url) {
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
                            if (!data || !data.result || !data.result['id']) {
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

            };

            $(".action-upload-image").on('click', function () {
                var elem = $(this).parents('.image-control');
                uploadImage(elem, function (data) {
                    var url = '/' + $(elem).data('resource');

                    $.ajax({
                        type: 'get',
                        url: url.replace('/images', '/') + 'catalog-matrix-content/0/images/' + data.result["id"],
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

            $('.image-control').on('click', ".image .action-remove", function () {
                $(this).parents('.col.image').remove();
                return false;
            });

            $('.image-control').on('click', ".image .action-remove", function () {
                $(this).parents('.col.image').remove();
                return false;
            });

            Pina.slotEditor.showCurrentContentParams();


            var tags = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tag'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '/cp/ru/tags.json',
                    prepare: function (query, settings) {
                        var url = settings.url;
                        var q = '?q=' + query;

                        settings.url = url + q;

                        return settings;
                    },
                    cache: false
                }
            });
            tags.initialize();

            $('.tag-selector input').each(function (index) {
                var elt = $(this);
                $(elt).tagsinput({
                    confirmKeys: [13, 14, 44],
                    freeInput: true,
                    itemValue: 'id',
                    itemText: 'tag',
                    typeaheadjs: {
                        name: 'tags',
                        displayKey: 'tag',
                        source: tags.ttAdapter()
                    }
                });
                $(elt).on('beforeItemAdd', function (event) {
                    var tag = event.item;
                    if (!event.options || !event.options.preventPost) {
                        var headers = $(".content-form").attr('data-csrf-token') ? {'X-CSRF-Token': $(".content-form").attr('data-csrf-token')} : {};
                        $.ajax('{/literal}{link get="cp/:cp/tags"}{literal}', {method: 'post', data: {tag: tag}, headers: headers, success: function (response) {
                                if (response.tag && response.id) {
                                    elt.tagsinput('add', response, {preventPost: true});
                                } else {
                                    // Remove the tag since there was a failure
                                    // "preventPost" here will stop this ajax call from running when the tag is removed
                                    elt.tagsinput('remove', tag, {preventPost: true});
                                }
                            }});
                    }
                });

                if (elt.val()) {
                    $.ajax(
                        '{/literal}{link get="cp/:cp/tags"}{literal}' + '/' + elt.val(),
                        {method: 'get', dataType: 'json', success: function (response) {
                                if (response.tags) {
                                    response.tags.forEach(function (item, i, arr) {
                                        $(elt).tagsinput('add', item, {preventPost: true});
                                    })
                                }
                            }});
                }
            });
        });
    </script>
{/literal}
{/script}

{styles}