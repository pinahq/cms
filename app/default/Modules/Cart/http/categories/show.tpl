{content name=title}{$resource.title}{/content}

{content name="breadcrumb"}
{module get="resources/:resource_id/parents" resource_id=$resource.id display=breadcrumbs title=$resource.title}
{/content}

{module get="categories/:id/images" id=$resource.id}

<h1 class="page-header">{$resource.title}</h1>

{$resource.text|format_description}

{module get="products" 
    parent_id=$resource.id 
    page=$params.page
    sort=$params.sort
    tag_id=$params.tag_id}