{foreach from=$resources item=r}
    <li>
        <a href="/{$r.url}">{$r.title}</a>
    </li>
{/foreach}