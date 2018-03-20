
<nav class="menu">
    <div class="container">
        <ul class="nav">
            {foreach from=$resources item=r}
                <li>
                    <a href="/{$r.url}">{$r.title} ({$r.child_count})</a>
                </li>
            {/foreach}
        </ul>
    </div>
</nav>