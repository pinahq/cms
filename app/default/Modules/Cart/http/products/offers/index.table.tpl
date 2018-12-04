{form action="carts" method="post" class="cart-form pina-form form-cart"}
{foreach from=$offers item=offer name=offers}{/foreach}
{if $smarty.foreach.offers.total eq 1}
    <div class="price">
        {foreach from=$offers item=offer}
            <span class="price-sale">{$offer.actual_price|format_price}</span>
            {if $offer.actual_price lt $offer.price}
                <del class="price-compare">{$offer.price|format_price}</del>
            {/if}
        {/foreach}
    </div>

    <div class="form-group" style="max-width: 200px">
        <label for="amount[{$offer.id}]" class="control-label">Количество:</label>
        {if $offer.available_amount ge 50}
            <div class="input-group">
                <a href="#" class="input-group-addon js-minus"><span class="glyphicon glyphicon-minus"></span></a>
                <input type="text" name="amount[{$offer.id}]" class="form-control amount" style="min-width: 50px;" 
                       value="0" data-min-amount="{$offer.min_amount}" data-max-amount="{$offer.available_amount}" data-fold="{$offer.fold}" class="amount" />
                <a href="#" class="input-group-addon js-plus"><span class="glyphicon glyphicon-plus"></span></a>
            </div>
        {else}
            <select name="amount[{$offer.id}]" class="amount">
                {section loop=$offer.available_amount+1 start=$offer.min_amount name=amount}
                    <option>{$smarty.section.amount.index}</option>
                {/section}
            </select>
        {/if}
    </div>
    <input type="submit" class="btn btn-primary" value="Купить" />
{elseif $offers}
    <table class="table">
        <thead>
            <tr>
                {foreach from=$tag_types item=tag_type}
                    <th>{$tag_type}</th>
                    {/foreach}
                <th>Доступно</th>
                <th>Цена</th>
                <th>Кол-во</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$offers item=offer}
                <tr>
                    {foreach from=$tag_types item=tag_type}
                        <td>{$offer.tags|tag:$tag_type}</td>
                    {/foreach}
                    <td>{$offer.available_amount}</td>
                    <td>
                        <span class="price-sale">{$offer.actual_price|format_price}</span>
                        {if $offer.actual_price lt $offer.price}
                            <del class="price-compare">{$offer.price|format_price}</del>
                        {/if}
                    </td>
                    <td>
                        {if $offer.available_amount ge 50}
                            <div class="input-group">
                                <a href="#" class="input-group-addon js-minus"><span class="glyphicon glyphicon-minus"></span></a>
                                <input type="text" name="amount[{$offer.id}]" class="form-control amount" style="min-width: 50px;" 
                                       value="0" data-min-amount="0" data-max-amount="{$offer.available_amount}" data-fold="{$offer.fold}" class="amount" />
                                <a href="#" class="input-group-addon js-plus"><span class="glyphicon glyphicon-plus"></span></a>
                            </div>
                        {else}
                            <select name="amount[{$offer.id}]" class="amount">
                                <option>0</option>
                                {section loop=$offer.available_amount+1 start=$offer.min_amount name=amount}
                                    <option>{$smarty.section.amount.index}</option>
                                {/section}
                            </select>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    <input type="submit" class="btn btn-primary" value="Купить" />
{/if}
{/form}

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-cart").on("success", function (event, packet, status, xhr) {
            PinaRequest.handleRedirect(xhr);
        });
    </script>
{/literal}
{/script}

{script src="/static/default/js/pina.cookie.js"}{/script}
{script}
{literal}
    <script>
        $('.cart-form').on('submit', function () {
            var guid = function () {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            }
            var cartId = Pina.cookie.get('cart_id');
            if (!cartId) {
                cartId = guid();
                Pina.cookie.set('cart_id', cartId);
            }
            $(this).attr('action', '/carts/' + cartId + '/products');
            return true;
        });
    </script>
{/literal}
{/script}

{script}
{literal}
    <script>
        $(document).ready(function () {
            $('.cart-form').on('change', ".amount", function () {
                var input = $(this);
                var minAmount = input.data('min-amount');
                var value = parseInt(input.val());
                var fold = parseInt(input.data('fold'));

                var newValue = value;
                if (value % fold) {
                    newValue = value - value % fold;
                }
                if (newValue < minAmount) {
                    newValue += fold;
                }
                if (newValue != value) {
                    input.val(newValue);
                }
                return false;
            });

            $('.cart-form').on('click', '.js-minus', function () {
                var input = $(this).next();
                var minAmount = input.data('min-amount');
                var fold = parseInt(input.data('fold'));
                var value = parseInt(input.val());
                var newValue = value - fold;
                if (newValue < minAmount) {
                    return false;
                }
                input.val(newValue).trigger('change');

                return false;
            });

            $('.cart-form').on('click', '.js-plus', function () {
                var input = $(this).prev();
                var minAmount = input.data('min-amount');
                var maxAmount = input.data('max-amount');
                var fold = parseInt(input.data('fold'));
                var value = parseInt(input.val());
                var newValue = value + fold;
                if (newValue < minAmount) {
                    newValue = minAmount;
                }
                if (newValue > maxAmount) {
                    return;
                }
                input.val(newValue).trigger('change');

                return false;
            });
        });
    </script>
{/literal}
{/script}