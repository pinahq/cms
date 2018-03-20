<div class="panel panel-default">
    <div class="panel-body">
        {if $resources}
            <div class="list-group resources"
                 {if $params.reorder}
                     {action_attributes post="cp/:cp/resources/:reorder_resource_id/:reorder" 
                            reorder_resource_id=$params.reorder_resource_id reorder=$params.reorder}
                 {/if}
                 >
                {foreach from=$resources item=r}
                    <div class="draggable resource" data-id="{$r.id|default:0}">
                        {module get="cp/:cp/:resource_type/:id/row" resource_type=$r.resource_type id=$r.id resource=$r fallback="cp/:cp/resources/:id/row"}
                        <div class="list-group-separator"></div>
                    </div>
                {/foreach}
            </div>
        {else}
            <span>{t}Not Found{/t}</span>
        {/if}
    </div>
</div>

{script src="/static/default/js/pina.toggle.js"}{/script}
