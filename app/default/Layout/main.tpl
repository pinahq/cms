<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {if $resource.id}
            {module get="resources/:id/meta" id=$resource.id}
        {else}
            <title>{place name="title"}</title>
        {/if}

        {include file="Skin/resources.tpl"}
        {view get="cp/:cp/block" resource_id=$params.resource_id display="front-menu"}
        <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

        {style src="/static/default/css/main.css"}{/style}

        {styles}
        
        {config namespace="Pina\\Modules\\CMS" key="custom_header_code"}
    </head>
    <body class="{place name="body_class"}">

        <nav class="navbar navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">PINA2</a>

                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        {module get="categories" display="main-menu" parent_id=0 length=2}
                        {config key="company_phone" namespace="Pina\Modules\CMS" assign=phone}
                        {if $phone}
                            <li><a href="tel:{$phone}">{$phone}</a></li>
                        {/if}
                        {view get="submissions/block" display="callback"}
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="search">
                            <a href="{link get="search"}">Поиск</a>
                        </li>
                        {module get="auth" display=navbar}
                        {module get="carts" display="navbar" wrapper="li class=cart"}
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>


        {place name="top"}

        <div class="container theme-showcase" role="main">
            {include file="Skin/message.tpl"}
            {$content}
        </div>

        <footer>
            <ul class="nav">
                {module get="pages" display="list" parent_id=0 length=1}
            </ul>
            <p class="copyrights"><span class="glyphicon glyphicon-copyright-mark"></span> 2017 Alex Yashin</p>
        </footer>

        {place name="bottom"}

        {scripts}
        
        {config namespace="Pina\\Modules\\CMS" key="custom_footer_code"}
    </body>
</html>