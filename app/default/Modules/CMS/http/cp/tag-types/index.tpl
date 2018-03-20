{content name="page_header"}{t}Select tag type{/t}{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>{t}Tag types{/t}</li>
</ol>
{/content}

<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='tag-types' display="sidebar"}
    </div>
    <div class="col-md-10">
        {view get="cp/:cp/tag-types" display="list"}
    </div>
</div>