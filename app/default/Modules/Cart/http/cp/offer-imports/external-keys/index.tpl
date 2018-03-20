{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/offers"}">Прайс-лист</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/create"}">Импорт</a></li>
    <li class="active">{t}Key fields{/t}</li>
</ol>
{/content}

{content name="title"}
{t}Key fields{/t}
{/content}

{content name="page_header"}{t}Offer import{/t}{/content}

<ul class="nav nav-tabs">
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/offers" import_id=$params.import_id}">{t}Preview{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/schema" import_id=$params.import_id}">{t}Schema{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/replaces" import_id=$params.import_id}" class="action">{t}Replaces{/t}</a></li>
    <li class="active"><a href="{link get="cp/:cp/offer-imports/:import_id/keys" import_id=$params.import_id}">{t}Key fields{/t}</a></li>
</ul>

<div class="panel">
    <div class="panel-body">
        {form action="cp/:cp/offer-imports/:import_id/external-keys" import_id=$params.import_id method="put" class="form-horizontal pina-form form-keys"}

        <div class="row">
            <div class="col-sm-6">
                <h4>{t}Product key{/t}:</h4>
                <div class="labels">
                    {foreach from=$keys.resource item=index}<span data-id="{$index}" class="label" style="display: inline-block;margin-right:5px;">{$header.$index}</span>{/foreach}
                </div>
                <input type="hidden" name="resource_keys" value="{foreach from=$keys.resource name="keys" item=index}{$index}{if !$smarty.foreach.keys.last},{/if}{/foreach}" />
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="togglebutton">
                            <label>
                                <input type="checkbox" name="external_keys[resource]" value="Y" 
                                {foreach from=$external_keys name="keys" item=item}{if $item eq 'resource'}checked="checked"{/if}{/foreach}
                                />
                            </label>
                            {t}Use as external key{/t}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <h4>{t}Offer key{/t}:</h4>
                <div class="labels">
                    {foreach from=$keys.offer item=index}<span class="label" style="display: inline-block;margin-right:5px;">{$header.$index}</span>{/foreach}
                </div>
                <input type="hidden" name="offer_keys" value="{foreach from=$keys.offer name="keys" item=index}{$index}{if !$smarty.foreach.keys.last},{/if}{/foreach}" />

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="togglebutton">
                            <label>
                                <input type="checkbox" name="external_keys[offer]" value="Y" 
                                {foreach from=$external_keys name="keys" item=item}{if $item eq 'offer'}checked="checked"{/if}{/foreach}
                                />
                            </label>
                            {t}Use as external key{/t}
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <fieldset class="operations">
            <div class="button-bar row">
                <div class="col-sm-6">
                    <a class="btn btn-default btn-raised" href="{link get="cp/:cp/offer-imports/:import_id/keys" import_id=$params.import_id}">{t}Return to key selection{/t}</a>
                </div>
                <div class="col-sm-6" style="text-align: right;">
                    <button class="btn btn-primary btn-raised">{t}Save and proceed{/t}</button>
                </div>
            </div>        
        </fieldset>
        {/form}
	</div>
</div>

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-keys").on("success", function (event, packet, status, xhr) {
            var parts = document.location.pathname.split('/');
            var path = parts.slice(0, parts.length - 1).join('/');
            var headers = $("[name=csrf_token]").val() ? {'X-CSRF-Token': $("[name=csrf_token]").val()} : {};
            $.ajax({
                type: 'post',
                url: document.location.origin + path + '/offers',
                headers: headers,
                success: function () {
                    document.location = document.location.origin + path + '/results?changed=' + Math.random();
                }
            });
        });
    </script>
{/literal}
{/script}


{script}
{literal}
    <script>
        $('.labels').sortable({
            placeholder: '',
            start: function (event, ui) {
                ui.placeholder.html('<span class="" style="display:inline-block;margin-right:5px;width:' + event.toElement.style.width + ';height:1px"></span>');
            },
            stop: function (event, ui) {
                var data = [];
                var order = 0;
                $('.label', this).each(function () {
                    data.push($(this).data('id'));
                });
                $(this).next().val(data.join(','));
            }
        }).disableSelection();
    </script>
{/literal}
{/script}