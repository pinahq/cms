{assign var=index value=0}
<div class="row sitemap">
{foreach from=$resources item=r}
    {if $r.parent_id eq $params.parent_id}
        <div class="col-sm-4">
            <h2>{$r.title}</h2>
            <ul class="nav">
                {assign var=found value=false}
                {foreach from=$resources item=rr}
                    {if $rr.parent_id eq $r.id}
                        <li><a href="/{$rr.url}">{$rr.title}</a></li>
                        {assign var=found value=true}
                    {/if}
                {/foreach}
                {if !$found}
                    <li><a href="/{$r.url}">{$r.title}</a></li>
                {/if}
            </ul>
        </div>
        {assign var=index value=$index+1}
        {if $index%3 eq 0}</div><div class="row sitemap">{/if}
    {/if}
{/foreach}
</div>