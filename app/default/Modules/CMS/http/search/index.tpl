{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="/"><span class="glyphicon glyphicon-home"></span></a></li>
    <li class="active">{t}Search{/t}</li>
</ol>
{/content}

<h1 class="page-header">Поиск</h1>

{form action="search" method="get" class="form form-inline"}
<fieldset style="margin-bottom:15px;">
    <div class="form-group">
        <input id="token" name="token" type="text" class="form-control " value="{$params.token}">
    </div>
        <button type="submit" class="btn btn-default">{t}Search{/t}</button>
</fieldset>
{/form}


<div class="search-results">
{if !$params.token}
    <p>Введите поисковую строку</p>
{else}
    {module get="products" 
    parent_id=$params.parent_id
    page=$params.page
    sort=$params.sort
    token=$params.token
    tag_id=$params.tag_id}
{/if}
</div>