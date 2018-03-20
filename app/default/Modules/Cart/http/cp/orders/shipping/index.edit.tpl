{content name="page_header"}Заказ {$order.number} (#{$order.id}) от {$order.created|format_date}{/content}
{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/orders"}">Заказы</a></li>
    <li><a href="{link get="cp/:cp/orders/:id" id=$order.id}">Заказ {$order.number} (#{$order.id})</a></li>
    <li>Редактировать</li>
</ol>
{/content}


<div class="panel panel-default">
    <div class="panel-heading">
        <h2>Доставка</h2>
    </div>
    <div class="panel-body">
        {form action="cp/:cp/orders/:id/shipping" id=$order.id method="put" class="form form-horizontal form-shipping pina-form"}

        <div class="form-group">
            <label class="control-label col-sm-2" for="shipping_method_id">Метод доставки</label>
            <div class="col-sm-10">
                {foreach from=$shipping_methods item=item name=shipping_methods}
                    <div class="radio">
                        <label>
                            <input type="radio" name="shipping_method_id" value="{$item.id}" {if $item.id == $order.shipping_method_id}checked{/if}>
                            {$item.title} ({if $item.fee}{$item.fee|format_price}{else}N/A{/if})
                        </label>
                    </div>
                {/foreach}

            </div>
        </div>

        {include file="Skin/form-line-input.tpl" name="shipping_subtotal" title="Стоимость" value=$order.shipping_subtotal}
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
        $(".form-shipping").on("success", function (event, packet, status, xhr) {
            if (!PinaRequest.handleRedirect(xhr)) {
                document.location = document.location.origin + document.location.pathname.replace('/shipping', '') + '?changed=' + Math.random();
            }
        });
    </script>
{/literal}
{/script}