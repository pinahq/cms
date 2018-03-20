<div class="menu-popup">
    <div class="page-header container-fluid">
        <h1>Menu</h1>
    </div>
    <div class="row">
        <div class="col-lg-2 col-md-4 col-sm-6">
            {module get="cp/:cp/resource-types" display="menu"}
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            {view get="cp/:cp/orders/block" display="menu"}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            {module get="cp/:cp/orders" display="menu" status=placed date=today}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            {module get="cp/:cp/submissions" display="menu" date=today}
        </div>
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