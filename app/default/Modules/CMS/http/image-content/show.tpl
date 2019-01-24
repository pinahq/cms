<div class="image-content {if $content.params.width or $content.params.offset_left}row{/if}">
    {if $content.params.url}
        <a href="{$content.params.url}">
    {/if}
        {if $content.params.image.id}
            <img src="{img class=image media=$content.params.image return=src}" 
                 class="image {$content.params.width} {$content.params.offset_left}"
                 title="{$content.params.title}" />
        {else}
            <img class="" src="/static/default/images/empty.gif" title="{$content.params.title}" style="{if $content.params.width}width: 100%;{else}width:150px;{/if}">
        {/if}
    {if $content.params.url}
        </a>
    {/if}
</div>