{foreach from=$params.statuses item=status}
    {if $params.order_status_id ne $status.id}
        <button class="btn btn-primary btn-block btn-raised pina-action"
                style="{if $status.color}background-color: #{$status.color};color:black;{/if}text-align:center;padding-left:0;padding-right:0;"
                {action_attributes put="cp/:cp/orders/:id" id=$order.id status=$status.status}>
            {$status.title}
        </button>
    {/if}
{/foreach}