{foreach from=$menu_items item=menu_item}
    <li>
        <a href="{if $menu_item.resource_url}{link get=$menu_item.resource_url}{else}{$menu_item.link}{/if}">
            {$menu_item.title}
        </a>
    </li>
{/foreach}