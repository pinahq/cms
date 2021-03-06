{content name="page_header"}Купон {$coupon.coupon}{/content}

{form method="put" action="cp/:cp/coupons/:coupon" coupon=$coupon.coupon class="form form-horizontal pina-form form-coupon"}
<fieldset class="well">

    {view get="cp/:cp/coupons/block" display="form"}

    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
        </div>
    </div>

</fieldset>
{/form}

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-coupon").on("success", function () {
            var parts = document.location.pathname.split('/');
            var path = parts.slice(0, parts.length - 1).join('/');
            document.location = document.location.origin + path + '?changed=' + Math.random();
        });
    </script>
{/literal}
{/script}