
{content name="page_header"}{if $tag_type.type}{$tag_type.type}{else}{t}Empty tag type{/t}{/if}{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/tag-types"}">{t}Tag types{/t}</a></li>
    <li>{if $tag_type.type}{$tag_type.type}{else}{t}Empty tag type{/t}{/if}</li>
</ol>
{/content}

<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='tag-types' display="sidebar"}
    </div>
    <div class="col-md-10">
        {module get="cp/:cp/tag-types/:id/tags" id=$tag_type.id page=$params.page}
    </div>
</div>