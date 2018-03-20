<div class="row sitemap">
    {foreach from=$resources item=r}
        <ul class="nav">
            <li>
                <a href="/{$r.url}">{$r.title}</a>
            </li>
        </ul>
    {/foreach}
</div>