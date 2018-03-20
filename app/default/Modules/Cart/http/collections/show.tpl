{content name=title}{$resource.title}{/content}

{content name="breadcrumb"}
    {module get="resources/:resource_id/parents" resource_id=$resource.id display=breadcrumbs class=collection title=$resource.title}
{/content}

{img id=$resource.image_id}

<h1 class="page-header">{$resource.title}</h1>

{$resource.text|format_description}
    
{view get="products" 
    category_title=$resource.title
    page=$params.page
    sort=$params.sort
    tag_id=$params.tag_id}
