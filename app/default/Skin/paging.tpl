{if $paging.total gt 1}
    <ul class="pagination">
        {section name=pages loop=$paging.total+1 start=1}
            {if $smarty.section.pages.first}
                {if $paging.current gt 1}
                    {*<li><a class="pref" href="{link get=$get page=$paging.current-1}" data-value="{$paging.current-1}">&laquo;</a></li>*}
                    {else}
                        {*<li><a class="prev disabled">&laquo;</a></li>*}
                    {/if}
                {/if}

            {if ($smarty.section.pages.index le $paging.current
                    and $smarty.section.pages.index ge $paging.current - 1)
                or ($smarty.section.pages.index ge $paging.current 
                    and $smarty.section.pages.index le $paging.current + 1)
                or ($smarty.section.pages.index le 5
                    and $paging.current le 4)
                or ($smarty.section.pages.index ge $paging.total - 4
                    and $paging.current ge $paging.total - 3)
                or $smarty.section.pages.index eq 1 
                or $smarty.section.pages.index eq $paging.total}
                <li {if $paging.current eq $smarty.section.pages.index}class="active"{/if}>
                    <a href="{link get=$paging.resource page=$smarty.section.pages.index}">{$smarty.section.pages.index}</a>
                </li>
            {elseif $smarty.section.pages.index lt $paging.current}
                {if $smarty.section.pages.index eq 2}
                    <li><a>...</a></li>
                    {/if}
                {elseif $smarty.section.pages.index gt $paging.current}
                    {if $smarty.section.pages.index eq $paging.total - 1}
                    <li><a>...</a></li>
                    {/if}
                {/if}

            {if $smarty.section.pages.last}
                {if $paging.current lt $paging.total}
                    {*<li><a class="next" href="{link get=$get page=$paging.current+1}">&raquo;</a></li>*}
                    {else}
                        {*<li><a class="next disabled">&raquo;</a></li>*}
                    {/if}
                {/if}
            {/section}
    </ul>
{/if}