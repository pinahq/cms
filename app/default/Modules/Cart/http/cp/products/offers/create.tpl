{*content name="page_header"}Редактировать товарное предложение{/content*}
{module get="cp/:cp/resources/:resource_id" resource_id=$params.resource_id display=header}

{place name=tabs}
<div class="panel">
    <div class="tab-content panel-body">

        {form action="cp/:cp/products/:resource_id/offers" method="post" resource_id=$params.resource_id class="form form-horizontal pina-form form-offer"}

            {view get="cp/:cp/products/:product_id/offers/block" display="form"}

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <button class="btn btn-primary btn-raised">{t}Create offer{/t}</button>
                </div>
            </div>

        {/form}
    </div>
</div>


{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-offer").on("success", function () {
            var parts = document.location.pathname.split('/');
            var path = parts.slice(0, parts.length - 1).join('/');
            document.location = document.location.origin + path + '?changed=' + Math.random();
        });
    </script>
{/literal}
{/script}