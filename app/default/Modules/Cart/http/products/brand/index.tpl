{if $tag.image_id}
    <a href="{link get=$tag.url}">
        {img image=$tag width=80 height=60 trim=4 style="float:left;margin-right: 10px;"}
    </a>
{/if}
<p>
    {if $tag.url}<a href="{link get=$tag.url}" style="color:inherit;">{/if}
        <strong>{$tag.tag|tag:"Бренд"}</strong>
    {if $tag.url}</a>{/if}
</p>