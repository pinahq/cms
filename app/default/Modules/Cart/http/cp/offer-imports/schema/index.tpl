{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/offers"}">Прайс-лист</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/create"}">Импорт</a></li>
    <li class="active">{$import.file_name}</li>
</ol>
{/content}

{content name="title"}
Связи между столбцами и данными
{/content}

{content name="page_header"}Offer import{/content}

<ul class="nav nav-tabs">
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/offers" import_id=$params.import_id}">{t}Preview{/t}</a></li>
    <li class="active"><a href="{link get="cp/:cp/offer-imports/:import_id/schema" import_id=$params.import_id}">{t}Schema{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/replaces" import_id=$params.import_id}" class="action">{t}Replaces{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/keys" import_id=$params.import_id}">{t}Key fields{/t}</a></li>
</ul>

<div class="panel">
    <div class="panel-body">

        {form action="cp/:cp/offer-imports/:import_id/schema" import_id=$params.import_id method="put" class="form-horizontal pina-form form-schema"}
        <table class="table table-hover table-responsive table-condensed table-flat table-with-controls">
            <col  />
            <thead>
                <tr>
                    <th>{t}#{/t}</th>
                    <th>{t}Column{/t}</th>
                    <th>{t}Property{/t}</th>
                    <th>{t}Tag Title{/t}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$schema key=index item=field}
                    <tr>
                        <td>{$index+1}</td>
                        <td>{$header.$index}</td>
                        <td>
                            <select name="schema[]" class="js-schema form-control">
                                {foreach from=$list item=item key=key}
                                    {if $key == $field}
                                        <option selected="selected" value="{$key}">{$item}</option>
                                    {else}
                                        <option value="{$key}">{$item}</option>
                                    {/if}
                                {/foreach}
                            </select>
                        </td>
                        <td>
                            <input name="names[]" class="form-control" value="{$names.$index}"  />
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>

        <fieldset class="operations">
            <div class="button-bar row">
                <div class="col-sm-6">
                    <button class="btn btn-primary btn-raised">{t}Save and reload data{/t}</button>
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
        $(".form-schema").on("success", function (event, packet, status, xhr) {
            var parts = document.location.pathname.split('/');
            var path = parts.slice(0, parts.length - 1).join('/');
            document.location = document.location.origin + path + '?changed=' + Math.random();
        });
    </script>
{/literal}
{/script}

{script}
{literal}
    <script>
        $(document).ready(function () {
            $("select.js-schema").on('change', function () {
                if ($(this).val() == 'tag' || $(this).val() == 'offer_tag') {
                    $(this).parents('td').next().find('.form-control').show();
                } else {
                    $(this).parents('td').next().find('.form-control').hide();
                }
            }).trigger('change');
        });
    </script>
{/literal}
{/script}