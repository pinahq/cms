<ul class="nav">
    {foreach from=$tags item=t name=column}
        <li><a href="/{if $t.url}{$t.url}{else}{$params.url|cat:"?tag_id[]="|cat:$t.tag_id}{/if}">{$t.tag|tag:$params.tag_type}</a></li>
        {if $smarty.foreach.column.iteration%16 eq 0}</ul><ul class="nav">{/if}
    {/foreach}
</ul>