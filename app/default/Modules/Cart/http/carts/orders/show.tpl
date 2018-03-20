<h1 class="page-header">Заказ #{$order.number|default:$order.id}<br /><small>{$order.created|format_date}</small></h1>
<div class="row">
    <div class="col-md-4">
        <table class="table">
            <thead>
                <tr>
                    <th colspan="2">О заказе</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Статус:</td>
                    <td>
                        {$order.order_status_title}
                    </td>
                </tr>
                <tr>
                    <td>К оплате:</td>
                    <td>
                        {$order.total|format_price}
                    </td>
                </tr>
                <tr>
                    <td>
                        Метод доставки:
                    </td>
                    <td>
                        {$order.shipping_method_title}
                    </td>
                </tr>
                <tr>
                    <td>
                        Метод оплаты:
                    </td>
                    <td>
                        {$order.payment_title|default:"Наличными&nbsp;курьеру"}
                    </td>
                </tr>
            </tbody>
        </table> 

    </div>
    <div class="col-md-8">
        <table class="table">
            <thead>
                <tr>
                    <th colspan="2">О покупателе</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>ФИО</td>
                    <td>
                        {$order.lastname} {$order.firstname} {$order.middlename}
                    </td>
                </tr>
                <tr>
                    <td>
                        Адрес: 
                    </td>
                    <td>
                        {$order.zip} {$order.country}, {$order.region}, {$order.city}, {$order.street}
                    </td>
                </tr>
                <tr>
                    <td>
                        Телефон: 
                    </td>
                    <td>
                        {$order.phone}
                    </td>
                </tr>
                <tr>
                    <td>
                        Email: 
                    </td>
                    <td>
                        {$order.email}
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</div>





<h1 class="whereAmI cart-title">Содержимое заказа</h1>
<table class="table">
    <thead>
        <tr>
            <th>Товар</th>
            <th>Количество</th>
            <th>Цена</th>
            <th>Итого</th>
        </tr>
    </thead>
    {foreach from=$offers item=offer}
        <tr>
            <td>
                {img id=$offer.image_id style="width:100px;"}
                {$offer.title}
                {$offer.tags}
            </td>
            <td>
                {$offer.amount}
            </td>
            <td>
                {$offer.actual_price|format_price}
            </td>
            <td>
                {$offer.amount*$offer.actual_price|format_price}
            </td>
        </tr>
    {/foreach}
    {if $order.coupon_discount gt 0}
        <tr>
            <td colspan="3" style="text-align: right;"><strong>Скидка{if $order.coupon} ({$order.coupon}){/if}</strong></td>
            <td>{$order.coupon_discount|format_price}</td>
        </tr>
    {/if}
    {if $order.shipping_method_id || $order.shipping_subtotal gt 0.00}
    <tr>
        <td colspan="3" style="text-align: right;"><strong>Стоимость доставки</strong></td>
        <td>{$order.shipping_subtotal|format_price}</td>
    </tr>
    {/if}
    {if $order.payed gt 0.00}
    <tr>
        <td colspan="3" style="text-align: right;"><strong>Оплачено</strong></td>
        <td>{$order.payed|format_price}</td>
    </tr>
    {/if}
    <tr>
        <td colspan="3" style="text-align: right;"><strong>К оплате</strong></td>
        <td>{$order.total-$order.payed|format_price}</td>
    </tr>
</table>

