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
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/settings" import_id=$params.import_id}">{t}Settings{/t}</a></li>
</ul>

<div class="panel">
	<div class="panel-body">
		{form action="cp/:cp/offer-imports/:import_id/keys" import_id=$params.import_id method="put" class="form-horizontal pina-form form-keys"}
        <table class="table table-hover table-responsive table-condensed table-flat table-with-controls">
            <col  />
            <thead>
                <tr>
                    <th>{t}#{/t}</th>
                    <th>{t}Column{/t}</th>
                    <th>{t}Property{/t}</th>
                    <th>{t}Product key{/t}</th>
                    <th>{t}Offer key{/t}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$schema key=index item=field}
                    <tr>
                        <td>{$index+1}</td>
                        <td>{$header.$index}</td>
                        <td>{$field}</td>
                        <td>
                            {if $key_schema.$index eq 'resource'}
                        	<div class="togglebutton">
                                <label>
                                    <input type="checkbox" name="resource_keys[{$index}]" value="Y" 
                                        {foreach from=$keys.resource item=key}
                                            {if $key eq $index} checked="checked"{/if}
                                        {/foreach}
                                    />
                                </label>
                            </div>
                            {else}
                                <input type="hidden" name="resource_keys[{$index}]" value="" />
                            {/if}
                        </td>
                        <td>
                            {if $key_schema.$index eq 'offer'}
                        	<div class="togglebutton">
                                <label>
                                    <input type="checkbox" name="offer_keys[{$index}]" value="Y" 
                                        {foreach from=$keys.offer item=key}
                                            {if $key eq $index} checked="checked"{/if}
                                        {/foreach}
                                    />
                                </label>
                            </div>
                            {else}
                                <input type="hidden" name="offer_keys[{$index}]" value="" />
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>

        <fieldset class="operations">
            <div class="button-bar row">
                <div class="col-sm-6">
                    <a class="btn btn-danger btn-raised" href="{link get="cp/:cp/offer-imports/:import_id/schema" import_id=$params.import_id}">{t}Return to schema{/t}</a>
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
            document.location = document.location.origin + path + '/settings?changed=' + Math.random();
        });
    </script>
{/literal}
{/script}