{module get="cp/:cp/resources/:resource_id" resource_id=$params.resource_id display=header}
{place name=tabs}


<div class="panel">
    <div class="panel-body">
        <table class="table">
            <thead>
            <col width="200">
            </thead>
            <tbody>
                {foreach from=$menus item=menu}
                    <tr>
                        <td>
                            {$menu.title}
                        </td>
                        <td>
                            <div class="least-content">
                                <div class="togglebutton">
                                    <label>
                                        <input type="checkbox" class="action-toggle" data-key="enabled"
                                               {action_attributes put="cp/:cp/resources/:id/menus/:key" id=$params.resource_id key=$menu.key}
                                               {if $menu.resource_id} checked=""{/if} />
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <a href="{link get="cp/:cp/menus"}" class="btn btn-raised">{t}Go to menu management{/t}</a>
    </div>
</div>

{script src="/static/default/js/pina.toggle.js"}{/script}