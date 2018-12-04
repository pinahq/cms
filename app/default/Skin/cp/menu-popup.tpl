<div class="menu-popup">
    <div class="page-header container-fluid">
        <h1>Menu</h1>
    </div>
    <div class="row">
        {composer position="menu.list"}
        {composer position="menu.news"}
    </div>
    <div class="row">
        <div class="col-sm-12">
            <center><a href="/" class="btn btn-primary btn-raised">Go to Web-site</a></center>
        </div>
    </div>
</div>

{script}
{literal}
    <script>
        $('.menu-popup .has-child > a').on('click', function () {
            $(this).parent().toggleClass('open');
            return false;
        });
    </script>
{/literal}
{/script}