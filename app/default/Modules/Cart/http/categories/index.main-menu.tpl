{foreach from=$resources item=r}
    {if $r.parent_id eq 0}
        <li class="dropdown">
            <a href="/{$r.url}" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">{$r.title}</a>
            <div class="dropdown-menu">
                <div class="menu-division menu-division-categories">
                    <ul class="nav">
                        {foreach from=$resources item=rr}
                            {if $rr.parent_id eq $r.id}
                                <li>
                                    <a href="/{$rr.url}">{$rr.title}</a>
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                </div>
            </div>
        </li>
    {/if}
{/foreach}