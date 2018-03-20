<h1 class="page-header">Разделы</h1>
<div class="row sitemap">
    {foreach from=$resources item=r}
        {if ($params.parent_id && $r.parent_id eq $params.parent_id) or (!$params.parent_id && $r.parent_id eq 0)}
            <div class="col-sm-4 col-md-3">
                <h2>{$r.title}</h2>
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
        {/if}
    {/foreach}
</div>