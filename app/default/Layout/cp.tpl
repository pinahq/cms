<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {meta}

        {place name=title assign="title"}
        <title>{if $title}{$title} - {/if} PinaCMS</title>

        {include file="Skin/cp/resources.tpl"}

        <!-- Material Design fonts -->
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">

        <!-- Bootstrap Material Design -->
        <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-material-design/css/bootstrap-material-design.css">
        <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-material-design/css/ripples.min.css">

        {module get="favicon-link" image_id=$smarty.capture.image_id}

        {script src="/vendor/bootstrap-material-design/js/material.min.js"}{/script}
        {script src="/vendor/bootstrap-material-design/js/ripples.min.js"}{/script}

        {style src="/static/default/css/cp.css"}{/style}
        {style src="/static/default/css/cp-navbar.css"}{/style}
        {style src="/static/default/css/cp-overlay.css"}{/style}
        {style src="/static/default/css/cp-colors.css"}{/style}
        {styles}

    </head>
    {include file="Skin/cp/navbar.tpl"}


    <div class="page-header container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1>{place name="page_header"}</h1>
            </div>
            <div class="col-sm-6">
                {place name="breadcrumb"}
            </div>
        </div>
    </div>

    <div class="container-fluid" role="main">
        {include file="Skin/message.tpl"}

        {$content}
    </div>

    {include file="Skin/cp/menu-popup.tpl"}

    {place name=footer}

    {script}
    {literal}
        <script>
            $('.nav-menu > li.logo > a').on('click', function () {
                $('body').toggleClass('disable-scroll');
                $('.menu-popup').toggleClass('activating');
                $('.menu-popup').toggleClass('active');
                $('.overlay').toggleClass('active');
                $b = $('.logo > a > i');
                $b.text($b.text()==='menu'?'close':'menu');
                return false;
            });
        </script>
    {/literal}
    {/script}

    {script}
    <script>
        $.material.init();
    </script>
    {/script}
    {scripts}
</body>
</html>