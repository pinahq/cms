{extends layout=block}

<table class="table table-cart-content">
    {foreach from=$cart_offers item=offer name="offers"}
        <tr>
            <td {if $smarty.foreach.offers.first}style="border:none;"{/if}>
                {img image=$offer|@mine:"image" width=220 height=220 trim=4 style="width:96px;"}

            </td>
            <td {if $smarty.foreach.offers.first}style="border:none;"{/if}>
                {$offer.title|tag_pattern:$offer.resource_type_pattern:$offer.resource_tags}
                <div class="details">
                    {$offer.tags|substract_tag_pattern:$offer.resource_type_pattern|nl2br}
                </div>
            </td>
        </tr>
    {/foreach}
    <tr class="totals">
        <td colspan="2">
            Всего {$cart_amount} тов. на сумму {$cart_subtotal|format_price}
            {if $cart_discount}
                <br />Скидка: {$cart_discount|format_price}
            {/if}
            {if $shipping_subtotal}
                <br />Доставка: {$shipping_subtotal|format_price}
            {/if}
            <br />Итого: <strong>{$total|format_price}</strong>
        </td>
    </tr>
</table>
