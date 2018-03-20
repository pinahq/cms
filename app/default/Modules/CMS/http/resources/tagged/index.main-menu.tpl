{foreach from=$tags item=t name=column}
    <li><a href="{link get=$t.url}">{$t.title}</a></li>
    {if $smarty.foreach.column.iteration%16 eq 0}</ul></li><li class="col-centered menu-column"><ul>{/if}
{/foreach}