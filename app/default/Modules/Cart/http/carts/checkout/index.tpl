{content name="body_class"}checkout{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="/"><span class="glyphicon glyphicon-home"></span></a></li>
    <li class="active">{t}Checkout{/t}</li>
</ol>
{/content}
<h1 class="page-header">{t}Checkout{/t}</h1>

<div class="row">
    <div class="col-md-6 col-sm-8">
        {if !$user}
            <ul class="nav nav-pills nav-checkout">
                <li><a href="javascript: void(0);" class="collapsed" data-toggle="collapse" data-target="#registration-collapse" data-hide="#auth-collapse" aria-expanded="false">Зарегистируйтесь</a></li>
                <li><a href="javascript: void(0);" class="collapsed" data-toggle="collapse" data-target="#auth-collapse" data-hide="#registration-collapse" aria-expanded="false">Войдите на сайт</a></li>
            </ul>

            {module get="registration" display=checkout wrapper="div class='checkout-registration collapse' id=registration-collapse"}
            {module get="auth" display=checkout wrapper="div class='checkout-auth collapse' id=auth-collapse"}

            {script}
            {literal}
                <script>
                    $(".nav-checkout a.collapsed").on('click', function () {
                        $($(this).attr("data-hide")).removeClass('in');
                        return true;
                    });
                </script>
            {/literal}
            {/script}
        {/if}
        {form action="carts/:cart_id/orders" method="post" cart_id=$params.cart_id class="form-dialog form-checkout form-horizontal pina-form form-utm"}
        <fieldset class="checkout-address">

            {include file="Skin/form-line-input.tpl" required=1 name="firstname" title="Имя" value=$user.user_firstname|default:$prefilled.firstname labelColumn=4}

            {include file="Skin/form-line-input.tpl" required=1 name="lastname" title="Фамилия" value=$user.user_lastname|default:$prefilled.lastname labelColumn=4}

            <input type="hidden" name="country_key" value="ru" />
            <div class="form-group">
                <label class="control-label col-sm-4" for="region_key">Регион</label>
                <div class="col-sm-8">
                    {module get="regions" display="selector" name="region_key" country_key="ru" value=$prefilled.region_key}
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="region_key">Город</label>
                <div class="col-sm-8">
                    {module get="cities" display="selector" country_key=ru region_key=$prefilled.region_key name="city_id" value=$prefilled.city_id name_other="city" value_other=$prefilled.city wrapper='div class="cities-wrapper"'}
                </div>
            </div>
            {script}
            {literal}
                <script>
                    $("select[name=region_key]").on("change", function () {
                        var currentCityId = $("select[name=city_id]").val();
                        $(".cities-wrapper").load("/cities?display=selector&name=city_id&country_key=ru&region_key=" + $(this).val() + '&value=' + currentCityId, function () {
                            $("select[name=city_id]").on("change", function () {
                                var regionKey = $("[name=region_key]").val();
                                var cityId = $("select[name=city_id]").val();
                                $(".shipping-wrapper").load("/shipping-methods?display=selector&country_key=ru&region_key=" + regionKey + '&city_id=' + cityId, function () {
                                    $("[name=shipping_method_id]").on("change", function () {
                                        var shippingMethodId = $("[name=shipping_method_id]:checked").val();
                                        $(".checkout-cart-wrapper").load('/carts/{/literal}{$params.cart_id}{literal}/products?display=checkout&shipping_method_id=' + shippingMethodId + "&country_key=ru&region_key=" + regionKey + '&city_id=' + cityId);
                                    }).trigger("change");
                                });
                            }).trigger("change");
                        });
                    }).trigger("change");
                </script>
            {/literal}
            {/script}

            {script}
            {literal}
                <script>

                </script>
            {/literal}
            {/script}

            {include file="Skin/form-line-input.tpl" required=1 name="street" value=$prefilled.street title="Улица, дом,<br />квартира, офис" labelColumn=4}

            {include file="Skin/form-line-input.tpl" required=1 name="zip" value=$prefilled.zip title="Почтовый индекс" labelColumn=4}
            {include file="Skin/form-line-input.tpl" required=1 name="phone" value=$prefilled.phone title="Телефон" labelColumn=4}

            {include file="Skin/form-line-input.tpl" required=1 name="email" title="E-mail" value=$prefilled.email labelColumn=4}

            {*module action="user-field.list"*}
        </fieldset>

        {if $shipping_enabled}
        <fieldset>
            <div class="form-group">
                <label class="control-label col-sm-4">Доставка</label>
                <div class="col-sm-8 shipping-wrapper"></div>
            </div>
        </fieldset>
        {/if}

        {if $payment_enabled}
        <fieldset>
            <div class="form-group">
                <label class="control-label col-sm-4">Оплата</label>
                <div class="col-sm-8 payment-wrapper">{module get="payment-methods"}</div>
            </div>
        </fieldset>
        {/if}

        <fieldset>
            <div class="form-group operations">
                <div class="col-sm-8 col-sm-offset-4">
                    <button class="btn btn-primary">Оформить заказ</button>
                </div>
            </div>
        </fieldset>
        {/form}
    </div>

    <div class="col-md-3 col-sm-4 checkout-cart-wrapper">
        {module get="carts/:cart_id/products" cart_id=$params.cart_id display="checkout"}
    </div>
</div>

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-checkout").on("success", function (event, packet, status, xhr) {
            if (!PinaRequest.handleRedirect(xhr)) {
                //document.location.reload();
            }
        });
    </script>
{/literal}
{/script}