{content name=title}{$resource.title}{/content}

{content name="breadcrumb"}
    {module get="resources/:resource_id/parents" resource_id=$resource.id display=breadcrumbs title=$resource.title}
{/content}

{if $resource.title}
    <h1 class="page-header">{$resource.title}</h1>
{/if}

{content name="body_class"}page{/content}

{content name="top"}
    {img id=$resource.image_id style="width:100%;"}
{/content}

{form action="submissions" method="post"}
<div class="text">
    {$resource.text|format_description}
</div>

{slot name="page"}
{/form}