<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    {if $parents}
    {foreach from=$parents item=resource name=resources}
        {if $smarty.foreach.resources.first}
            <li><a href="{link get="cp/:cp/resources" resource_type_id=$resource.resource_type_id}">{module get="cp/:cp/resource-types/:resource_type_id" resource_type_id=$resource.resource_type_id display=title}</a></li>
        {/if}
    <li><a href="{link get="cp/:cp/resources/:resource_id" resource_id=$resource.id}">{$resource.title}</a></li>
    {/foreach}
    {else}
        <li><a href="{link get="cp/:cp/resources" resource_type_id=$params.resource_type_id}">{module get="cp/:cp/resource-types/:resource_type_id" display=title resource_type_id=$params.resource_type_id}</a></li>
    {/if}
    {if $params.title}
    <li class="active">{$params.title}</li>
    {/if}
</ol>
