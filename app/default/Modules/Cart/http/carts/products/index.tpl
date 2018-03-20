{extends layout=block}

{if $cart_offers}
    {form action="carts/:cart_id/products" cart_id=$params.cart_id method="put" class="cart-form" name="cart-content"}
    <table class="table table-cart-content">
        <col width="1" />
        <col />
        <col width="200" />
        <col />
        <col width="1" />
        <thead>
            <tr>
                <th></th>
                <th>Наименование:</th>
                <th>Кол-во:</th>
                <th>Стоимость:</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        {foreach from=$cart_offers item=offer}
            <tr>
                <td>
                    <a href="{link get=$offer.url}">
                        {img image=$offer|@mine:"image" width=220 height=220 trim=4 style="width:96px;"}
                    </a>
                </td>
                <td>
                    <a href="{link get=$offer.url}">
                        {$offer.title|tag_pattern:$offer.resource_type_pattern:$offer.resource_tags}
                    </a>
                    <div class="details">
                        {$offer.tags|substract_tag_pattern:$offer.resource_type_pattern|nl2br}
                    </div>
                </td>
                <td>
                    {if $offer.offer_amount ge 50}
                        <div class="input-group">
                            <a href="#" class="input-group-addon js-minus"><span class="glyphicon glyphicon-minus"></span></a>
                            <input type="text" name="amount[{$offer.id}]" class="form-control amount" style="min-width: 50px;" value="{$offer.amount}" data-min-amount="{$offer.min_amount}" data-max-amount="{$offer.offer_amount}" data-fold="{$offer.fold}" class="amount" />
                            <a href="#" class="input-group-addon js-plus"><span class="glyphicon glyphicon-plus"></span></a>
                        </div>
                    {else}
                        <select name="amount[{$offer.id}]" class="amount">
                            {section loop=$offer.offer_amount+1 start=$offer.min_amount name=amount}
                                <option {if $smarty.section.amount.index eq $offer.amount}selected="selected"{/if}>{$smarty.section.amount.index}</option>
                            {/section}
                        </select>
                    {/if}
                </td>
                <td>
                    {if $offer.amount > 1}
                        ({$offer.actual_price|format_price} * {$offer.amount})<br />
                    {/if}

                    {$offer.cart_offer_subtotal|format_price}
                </td>
                <td><a href="#" class="pina-action action-delete" {action_attributes delete="carts/:cart_id/products/:id" cart_id=$params.cart_id id=$offer.id}><span class="glyphicon glyphicon glyphicon-remove-sign"></span></a></td>
            </tr>
        {/foreach}
        <tr>
            <td colspan="3" style="text-align: right;">Всего: </td>
            <td><strong>{$cart_subtotal|format_price}</strong></td>
            <td>&nbsp;</td>
        </tr>
        {if $cart_discount}
            <tr>
                <td colspan="3" style="text-align: right;">Скидка: </td>
                <td><strong>{$cart_discount|format_price}</strong></td>
                <td>&nbsp;</td>
            </tr>    
        {/if}
    </table>
    {/form}
    <div class="row">
        <div class="col-sm-6">
            {module get="carts/:cart_id/coupon" cart_id=$params.cart_id}
        </div>
        <div class="col-sm-6" style="text-align: right;">
            <a class="btn btn-primary btn-lg" href="{link get="carts/:cart_id/checkout" cart_id=$params.cart_id}">Оформить</a>
        </div>
    </div>

{else}
    <p>Корзина пуста</p>
{/if}
