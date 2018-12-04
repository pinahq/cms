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
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/keys" import_id=$params.import_id}">{t}Key fields{/t}</a></li>
    <li class="active"><a href="{link get="cp/:cp/offer-imports/:import_id/settings" import_id=$params.import_id}">{t}Settings{/t}</a></li>
</ul>

<div class="panel">
    <div class="panel-body">
        {form action="cp/:cp/offer-imports/:import_id/settings" import_id=$params.import_id method="put" class="form-horizontal pina-form form-settings"}
        <div class="form-group">
            <label class="control-label col-sm-4">{t}Тип ресурса товара{/t}</label>
            <div class="col-sm-8">
                {module get="cp/:cp/resource-types" display=select tree='N' name="item_resource_type_id" id=$import.settings.item_resource_type_id}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">{t}Тип ресурса категории{/t}</label>
            <div class="col-sm-8">
                {module get="cp/:cp/resource-types" display=select tree='Y' name="parent_resource_type_id" id=$import.settings.parent_resource_type_id}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">{t}Режим загрузки товаров{/t}</label>
            <div class="col-sm-8">
                <select class="form-control" name="resource_mode">
                    <option value="" {if $import.settings.resource_mode eq ''}selected="selected"{/if}>Создавать и обновлять</option>
                    <option value="create" {if $import.settings.resource_mode eq 'create'}selected="selected"{/if}>Только создавать</option>
                    <option value="update" {if $import.settings.resource_mode eq 'update'}selected="selected"{/if}>Только обновлять</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">{t}Режим загрузки товарных предложений{/t}</label>
            <div class="col-sm-8">
                <select class="form-control" name="offer_mode">
                    <option value=""  {if $import.settings.offer_mode eq ''}selected="selected"{/if}>Создавать и обновлять</option>
                    <option value="create" {if $import.settings.offer_mode eq 'create'}selected="selected"{/if}>Только создавать</option>
                    <option value="update" {if $import.settings.offer_mode eq 'update'}selected="selected"{/if}>Только обновлять</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">{t}Отсутствующие товары{/t}</label>
            <div class="col-sm-8">
                <select class="form-control" name="resource_missing_status">
                    <option value="" {if $import.settings.resource_missing_status eq ''}selected="selected"{/if}>Оставлять без изменений</option>
                    <option value="hidden" {if $import.settings.resource_missing_status eq 'hidden'}selected="selected"{/if}>Выключить</option>
                    <option value="deleted" {if $import.settings.resource_missing_status eq 'deleted'}selected="selected"{/if}>Удалить</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">{t}Отсутствующие товарные предолжения{/t}</label>
            <div class="col-sm-8">
                <select class="form-control" name="offer_missing_status">
                    <option value="" {if $import.settings.offer_missing_status eq ''}selected="selected"{/if}>Оставлять без изменений</option>
                    <option value="hidden" {if $import.settings.offer_missing_status eq 'hidden'}selected="selected"{/if}>Выключить</option>
                    <option value="deleted" {if $import.settings.offer_missing_status eq 'deleted'}selected="selected"{/if}>Удалить</option>
                </select>
            </div>
        </div>
        <fieldset class="operations">
            <div class="button-bar row">
                <div class="col-sm-6">
                    <a class="btn btn-danger btn-raised" href="{link get="cp/:cp/offer-imports/:import_id/keys" import_id=$params.import_id}">{t}Return to keys{/t}</a>
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
        $(".form-settings").on("success", function (event, packet, status, xhr) {
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