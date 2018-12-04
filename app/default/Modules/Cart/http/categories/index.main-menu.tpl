{foreach from=$resources item=r}
    {if $r.parent_id eq 0}
    {capture name=submenu}{foreach from=$resources item=rr}{if $rr.parent_id eq $r.id}<li><a href="/{$rr.url}">{$rr.title}</a></li>{/if}{/foreach}{/capture}
    <li class="dropdown">
        <a href="/{$r.url}" {if $smarty.capture.submenu}class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"{/if}>{$r.title}</a>
        {if $smarty.capture.submenu}
            <div class="dropdown-menu">
                <ul class="nav">
                    {$smarty.capture.submenu}
                </ul>
            </div>
        {/if}
    </li>
    {/if}
{/foreach}