{content name="page_header"}
{t}Modules{/t}
{/content}


{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>{t}Modules{/t}</li>
</ol>
{/content}



<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='modules' display="sidebar"}
    </div>
    <div class="col-md-10">
        <div class="panel panel-default">
            <div class="panel-body">
                {if $modules || $new_modules}
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{"Module"|t}</th>
                                <th>{"Status"|t}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$modules item=module}
                                <tr>
                                    <td {if $module.removed eq 'Y'}style="text-decoration:line-through;color:#999;"{/if}>
                                        {$module.title}
                                    </td>
                                    <td>
                                        {if $module.system ne 'Y' && $module.removed ne 'Y' && $module.installed eq 'Y'}
                                            <div class="togglebutton">
                                                <label>
                                                    <input type="checkbox" class="action-toggle" data-key="enabled"
                                                           {action_attributes put="cp/:cp/modules/:id/status" id=$module.id}
                                                           {if $module.enabled eq 'Y'} checked=""{/if}>
                                                </label>
                                            </div>
                                        {/if}
                                    </td>
                                    <td>
                                        {if $module.system ne 'Y'}
                                            {if $module.installed eq 'Y'}
                                                <button class="btn btn-warning pina-action action-remove" {action_attributes delete="cp/:cp/modules/:id" id=$module.id}>{t}Remove{/t}</button>
                                            {else}
                                                <button class="btn btn-raised btn-primary pina-action action-install" {action_attributes post="cp/:cp/modules" namespace=$module.namespace}>{t}Install{/t}</button>
                                            {/if}
                                        {else}
                                            <button class="btn btn-default" disabled="disabled">{t}Default{/t}</button>
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                {else}
                    <p>{t}Modules do not exist{/t}.</p>
                {/if}

                <hr />

            </div>
        </div>

    </div>
</div>

{script src="/static/default/js/pina.toggle.js"}{/script}

{script}
{literal}
    <script>
        $(".action-toggle,.action-install,.action-remove").on('success', function () {
            document.location.reload();
        });
    </script>
{/literal}
{/script}