{content name="page_header"}Заказ {$order.number} ({$order.id}) от {$order.created|format_date}{/content}
{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/orders"}">Заказы</a></li>
    <li>Заказ {$order.number} (#{$order.id})</li>
</ol>
{/content}

{script src="/static/default/js/pina.cookie.js"}{/script}
{script}
{literal}
    <script>
        Pina.cookie.drop('cp_order_id');
    </script>
{/literal}
{/script}

{module get="cp/:cp/orders/:order_id/statuses" order_id=$order.id order_status_id=$order.order_status_id}

<div class="panel panel-default">
    <div class="panel-heading">
        <h2>Покупатель 
            {if $order.user_id}<a href="{link get="cp/:cp/users/:user_id" user_id=$order.user_id}">{/if}{$order.firstname} {$order.lastname}{if $order.user_id}</a>{/if}.
            <a href="tel:{$order.phone}">{$order.phone}</a> / <a href="mailto: {$order.email}">{$order.email}</a></h2>
    </div>
    <div class="panel-body">
        <p><strong>Адрес доставки: </strong>{$order.zip} {$order.region} {$order.city} {$order.street}</p>
        <p><strong>Дата доставки: </strong>{$order.delivery_date|format_date}</p>
        <p><strong>Время доставки: </strong>
        {if $order.delivery_time_from && $order.delivery_time_from ne '00:00:00'}{$order.delivery_time_from|default:"?"}{/if}
        - 
        {if $order.delivery_time_to && $order.delivery_time_to ne '00:00:00'}
            {$order.delivery_time_to|default:"?"}
        {else}
            *
        {/if}</p>
    <p><strong>Комментарий покупателя:</strong> {$order.customer_comment|strip_tags}</p>
    <p><strong>Комментарий менеджера:</strong> {$order.manager_comment|strip_tags}</p>
</div>
<div class="panel-footer">
    <a class="btn btn-default" href="{link get="cp/:cp/orders/:order_id" order_id=$order.id display="edit"}">Редактировать</a>
</div>
</div>

{module get="cp/:cp/orders/:order_id/offers" order_id=$order.id}

<div class="panel panel-default">
    <div class="panel-heading">
        <h2>Итоги</h2>
    </div>
    <div class="panel-body" style="text-align: right;">
        <table style="display:inline-block;">
            <tr>
                <td>Сумма:</td><td>{$order.subtotal|format_price}</td>
            </tr>
            {if $order.coupon_discount}
                <tr>
                    <td>Скидка{if $order.coupon} ({$order.coupon}){/if}:</td><td>{$order.coupon_discount|format_price}</td>
                </tr>
            {/if}
            <tr>
                <td>Доставка ({$order.shipping_method_title}):</td><td>{$order.shipping_subtotal|format_price}</td>
            </tr>
            {if $order.payed ge 0.00}
                <tr>
                    <td>Оплачено:</td><td>{$order.payed|format_price}</td>
                </tr>
            {/if}
            <tr>
                <td>К оплате:</td><td>{$order.total-$order.payed|format_price}</td>
            </tr>
        </table>
    </div>
    <div class="panel-footer">
        <a class="btn btn-default" href="{link get="cp/:cp/orders/:order_id/shipping" order_id=$order.id display="edit"}">Редактировать</a>
    </div>
</div>

{module get="cp/:cp/payments" order_id=$order.id display="order"}
