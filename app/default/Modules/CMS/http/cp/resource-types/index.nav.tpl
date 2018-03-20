<ul class="nav nav-pills">
    <li {if !$params.id} class="active"{/if}><a href="{link}">{t}All{/t}</a></li>
    {foreach from=$resource_types item=type}
        <li {if $params.id eq $type.id} class="active"{/if}>
            <a href="{link resource_type_id=$type.id}">{$type.title}</a>
    	</li>
    {/foreach}
</ul>