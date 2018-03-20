{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>{$title}</li>
</ol>
{/content}

{content name="page_header"}
{$title}
<a href="{link get="cp/:cp/resources/create" resource_type_id=$id}"
   class="btn btn-fab btn-fab-mini">
    <i class="material-icons">plus_one</i>
    <div class="ripple-container"></div>
</a>
{/content}