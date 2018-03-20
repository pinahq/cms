{content name="page_header"}
{t}Configuration{/t}
{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>{t}Configuration{/t}</li>
</ol>
{/content}


<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace=$params.namespace display="sidebar"}
    </div>
    <div class="col-md-10">
        {if $params.namespace}
            {module get="cp/:cp/config/:namespace" namespace=$params.namespace}
        {/if}
    </div>
</div>