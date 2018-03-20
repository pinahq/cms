{if $content.params.columns gt 0}
    {math equation="12/x" x=$content.params.columns assign=column_width}
    {assign var=columns value=$content.params.columns}
{else}
    {assign var=column_width value=3}
    {assign var=columns value=4}
{/if}

<div class="row">
    <div class="col-sm-{$column_width} margin-top">
        <ul>
            {assign var=index value=1}
            {foreach name=items from=$content.params.items item=item}

                {assign var=per_column value=$smarty.foreach.items.total/$columns|ceil}
                {if $smarty.foreach.items.index > 0 && ($smarty.foreach.items.index%$per_column)==0}
                </ul>
            </div>
            <div class="col-sm-{$column_width} margin-top">
                <ul>
                {/if}
                <li>
                    <h2>{$item.title}</h2><p>{$item.text}</p>
                </li>
                {assign var=index value=$index+1}
            {/foreach}
        </ul>
    </div>
</div>