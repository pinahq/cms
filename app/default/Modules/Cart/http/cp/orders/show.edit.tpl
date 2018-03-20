{content name="page_header"}Заказ {$order.number} (#{$order.order_id}) от {$order.created|format_date}{/content}
{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/orders"}">Заказы</a></li>
    <li><a href="{link get="cp/:cp/orders/:order_id" order_id=$order.order_id}">Заказ {$order.number} (#{$order.order_id})</a></li>
    <li>Покупатель</li>
</ol>
{/content}


<div class="panel panel-default">
    <div class="panel-heading">
        <h2>Покупатель</h2>
    </div>
    <div class="panel-body">
        {form action="cp/:cp/orders/:order_id" order_id=$order.order_id method="put" class="form form-horizontal form-order pina-form"}

        {include file="Skin/form-line-input.tpl" name="firstname" title="Имя" value=$order.firstname}

        {include file="Skin/form-line-input.tpl" name="lastname" title="Фамилия" value=$order.lastname}

        <input type="hidden" name="country_key" value="{$order.country_key}" />
        <div class="form-group">
            <label class="control-label col-sm-2" for="region_key">Регион</label>
            <div class="col-sm-10">
                {module get="regions" display="selector" name="region_key" country_key=$order.country_key value=$order.region_key}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="city_id">Город</label>
            <div class="col-sm-10">
                {module get="cities" display="selector" name="city_id" wrapper='div class="cities-wrapper"' country_key=$order.country_key region_key=$order.region_key value=$order.city_id}
            </div>
        </div>
        {script}
        {literal}
            <script>
                $("select[name=region_key]").on("change", function () {
                    var currentCityId = $("select[name=city_id]").val();
                    var countryKey = $("[name=country_key]").val();
                    $(".cities-wrapper").load("/cities?display=selector&name=city_id&country_key=" + countryKey + "&region_key=" + $(this).val() + "&value=" + currentCityId, function () {
                        $("select[name=city_id]").on("change", function () {
                            var regionKey = $("[name=region_key]").val();
                            var cityId = $("select[name=city_id]").val();
                            $(".shipping-wrapper").load("/shipping-methods?display=selector&country_key=ru&region_key=" + regionKey + '&city_id=' + cityId);
                        }).trigger("change");
                    });
                });
            </script>
        {/literal}
        {/script}

        {script}
        {literal}
            <script>

            </script>
        {/literal}
        {/script}

        {include file="Skin/form-line-input.tpl" name="street" title="Улица, дом,<br />квартира, офис" value=$order.street}

        {include file="Skin/form-line-input.tpl" name="zip" title="Почтовый индекс" value=$order.zip}
        {include file="Skin/form-line-input.tpl" name="phone" title="Телефон" value=$order.phone}

        {include file="Skin/form-line-input.tpl" name="email" title="E-mail" value=$order.email}
        
        <hr />
        
        {include file="Skin/form-line-input.tpl" name="delivery_date" title="Дата доставки" value=$order.delivery_date|format_date}
        {include file="Skin/form-line-input.tpl" name="delivery_time_from" title="Время доставки с" value=$order.delivery_time_from}
        {include file="Skin/form-line-input.tpl" name="delivery_time_to" title="Время доставки по" value=$order.delivery_time_to}
        
        <hr />
        
        {include file="Skin/form-line-textarea.tpl" name="customer_comment" title="Комментарий покупателя" value=$order.customer_comment}
        {include file="Skin/form-line-textarea.tpl" name="manager_comment" title="Комментарий менеджера" value=$order.manager_comment}


        <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
                <button class="btn btn-primary btn-raised">Сохранить</button>
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
        $(".form-order").on("success", function (event, packet, status, xhr) {
            if (!PinaRequest.handleRedirect(xhr)) {
                document.location = document.location.origin + document.location.pathname + '?changed=' + Math.random();
            }
        });
    </script>
{/literal}
{/script}

{script}
{literal}
    <script>
        $(function () {
            $("input[name=delivery_date]").datepicker($.datepicker.regional[ "ru" ]);
        });
    </script>
{/literal}
{/script}