<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        {meta}
        
        {module get="favicon-link"}

        {if $resource.id}
            {module get="resources/:id/meta" id=$resource.id}
        {else}
            {place name=title assign="title"}
            {config namespace="Pina\Modules\CMS" key="meta_title" assign="meta_title"}
            {config namespace="Pina\Modules\CMS" key="company_title" assign="company_title"}
            <title>{$title|default:$meta_title|default:$company_title}</title>
        {/if}

        {include file="Skin/resources.tpl"}
        {view get="cp/:cp/block" resource_id=$params.resource_id display="front-menu"}
        <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

        {style src="/static/default/css/main.css"}{/style}

        {styles}

        {config namespace="Pina\\Modules\\CMS" key="custom_header_code"}
    </head>
    <body class="{place name="body_class"}">

        {include file="Skin/page-header.tpl"}


        <div class="container-breadcrumb">
            <div class="container">
                {place name="breadcrumb"}
            </div>
        </div>


        {place name="top"}

        <div class="container theme-showcase" role="main">
            {$content}
        </div>

        {include file="Skin/page-footer.tpl"}

        {place name="bottom"}

        {scripts}

        {config namespace="Pina\\Modules\\CMS" key="custom_footer_code"}
    </body>
</html>