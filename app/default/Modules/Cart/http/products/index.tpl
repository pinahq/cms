{content name="meta"}
{strip}
    {assign var=current_page value=$paging.current}
    {if $current_page eq 1}
        {assign var=current_page value=null}
    {/if}
    {link_context tag_id=$params.tag_id}
        <link rel="canonical" href="{link_context page=$current_page}{link}{/link_context}">
        {if $paging.current lt $paging.total}
            <link rel="next" href="{link_context page=$paging.current+1}{link}{/link_context}">
        {/if}
        {if $paging.current gt 1}
            {assign var=prev_page value=$paging.current-1}
            {if $prev_page eq 1}
                {assign var=prev_page value=null}
            {/if}
            <link rel="prev" href="{link_context page=$prev_page}{link}{/link_context}">
        {/if}
    {/link_context}
{/strip}
{/content}

{array_column from=$selected_tags column=id assign=selected_tag_ids}

<div class="row">
    <div class="col-md-10">
        <ul class="nav nav-pills">
            {foreach from=$tag_types key=tag_type item=tags}

                <li role="presentation" class="dropdown">

                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        {$tag_type}<span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu">
                        {foreach from=$tags item=tag}
                            <li>
                                <a 
                                    {if $tag.selected}
                                        href="{link tag_id=$selected_tag_ids|@array_diff:$tag.id}"
                                    {else}
                                        href="{link tag_id=$selected_tag_ids|@array_merge:$tag.id}"
                                    {/if}
                                >
                                    {if $tag.selected}
                                    <span class="glyphicon glyphicon-check"></span>
                                    {else}
                                    <span class="glyphicon glyphicon-unchecked"></span>
                                    {/if}
                                    {$tag.tag}
                                </a>
                            </li>
                        {/foreach}
                    </ul>
                </li>

            {/foreach}
        </ul>


        {if $selected_tags}
            <ul class="nav nav-pills nav-selected-tags">
                {foreach from=$selected_tags item=tag}
                    <li>
                        <a href="{link tag_id=$selected_tag_ids|@array_diff:$tag.id}">{$tag.tag|truncate} <span class="glyphicon glyphicon-remove"></span></a>
                    </li>
                {/foreach}
            </ul>
        {/if}

    </div>
    <div class="col-md-2" style="text-align: right;">
        <div>
            <noindex>
            <span class="dropdown">
                <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display:inline-block;">
                    {if $params.sort eq ''}
                        По умолчанию
                    {elseif $params.sort eq 'price'}
                        По цене ▲
                    {elseif $params.sort eq '-price'}
                        По цене ▼
                    {elseif $params.sort eq 'title'}
                        По названию ▲
                    {elseif $params.sort eq '-title'}
                        По названию ▼
                    {/if}
                </a>
                <ul class="dropdown-menu">
                    {link_context token=$params.token tag_id=$params.tag_id}
                    <li><a href="{link sort=price}" rel="nofollow">По цене ▲</a></li>
                    <li><a href="{link sort=-price}" rel="nofollow">По цене ▼</a></li>
                    <li><a href="{link sort=title}" rel="nofollow">По названию ▲</a></li>
                    <li><a href="{link sort=-title}" rel="nofollow">По названию ▼</a></li>
                    <li><a href="{link}">По умолчанию</a></li>
                    {/link_context}
                </ul>
            </span>
            </noindex>
        </div>

    </div>

</div>

{if $paging.total gt 1}
    {link_context sort=$params.sort tag_id=$params.tag_id token=$params.token}
    {include file="Skin/paging.tpl"}
    {/link_context}
{/if}

{view get="products/block" display="items" resources=$resources}

{if $paging.total gt 1}
    {link_context sort=$params.sort tag_id=$params.tag_id token=$params.token}
    {include file="Skin/paging.tpl"}
    {/link_context}
{/if}