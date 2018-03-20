<!DOCTYPE html>
<html lang="en" style="height:50px;">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {meta}

        {include file="Skin/cp/resources.tpl"}

        <!-- Material Design fonts -->
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">

        <!-- Bootstrap Material Design -->
        <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-material-design/css/bootstrap-material-design.css">
        <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-material-design/css/ripples.min.css">

        {script src="/vendor/bootstrap-material-design/js/material.min.js"}{/script}
        {script src="/vendor/bootstrap-material-design/js/ripples.min.js"}{/script}

        {literal}
            <style>
                .navbar {
                    margin: 0 !important;
                    height: 50px !important;
                }
                
                .navbar .navbar-brand {
                    padding-top: 10px !important;
                    padding-bottom: 10px !important;
                }
                
                .navbar .navbar-nav > li > a {
                    padding-top: 15px !important;
                    padding-bottom: 15px !important;
                }
                
                .navbar .navbar-form {
                    margin-top: 10px !important;
                }
                
                /* navbar mobile */
                .navbar .navbar-brand {
                    float: left !important;
                }
                
                .navbar .navbar-header {
                    float: left !important;
                }
                
                .navbar .navbar-nav {
                    float: left;
                    margin: 0;
                }
                
                .navbar .navbar-nav.nav>li {
                    display: inline-block !important;
                }
                
            </style>
        {/literal}

    </head>
    <body>
        {include file="Skin/cp/navbar.tpl"}
        {script}
        {literal}
            <script>
                $.material.init();
                $('a').each(function () {
                    $(this).attr('target', '_top');
                });
            </script>
        {/literal}
        {/script}
        {scripts}
    </body>
</html>