{*module get="cp/:cp/orders/:order_id" order_id=$params.order_id title="Покупки" display="header"*}

<div class="panel panel-default">
    <div class="panel-heading">
        <h2>Покупки</h2>
    </div>
    <div class="panel-body">
        {form action="cp/:cp/orders/:order_id/offers" order_id=$params.order_id method="put" class="form form-horizontal form-order-offers pina-form"}
        <table class="table table-hover">
            <thead>
                <tr>
                    <th colspan="2">Товар</th>
                    <th>Статус</th>
                    <th>Цена</th>
                    <th>Цена распродажи</th>
                    <th>Скидка</th>
                    <th>Цена заказа</th>
                    <th>Кол-во</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$order_offers item=offer}
                    <tr>
                        <td>
                            {img id=$offer.media_id width=220 height=220 style="width:110px;"}
                        </td>
                        <td>
                            {$offer.resource_title}<br />{$offer.tags|replace:"\n":"<br />"}
                        </td>
                        <td>
                            <select name="status[{$offer.id}]" class="status">
                                {foreach from=$statuses item=status}
                                    <option value="{$status.id}" {if $offer.order_offer_status_id eq $status.id}selected="selected"{/if}>{$status.title}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td>
                            {$offer.price|format_price}
                        </td>
                        <td>
                            {if $offer.sale_price gt 0}
                                {$offer.sale_price|format_price}
                            {else}
                                -
                            {/if}
                        </td>
                        <td>
                            {if $offer.discount_percent|rtrim:"0."}
                                {$offer.discount_percent|rtrim:"0."}%
                            {else}
                                -
                            {/if}
                        </td>
                        <td>
                            <input type="text" name="price[{$offer.id}]" value="{$offer.actual_price}" />
                        </td>
                        <td>
                            {if $offer.offer_amount ge 50}
                                <input type="text" name="amount[{$offer.id}]" class="form-control amount" style="min-width: 50px;" value="{$offer.amount}" data-min-amount="{$offer.min_amount}" data-max-amount="{$offer.offer_amount}" data-fold="{$offer.fold}" class="amount" />
                            {else}
                                <select name="amount[{$offer.id}]" class="amount">
                                    {section loop=$offer.amount+$offer.offer_amount+1 start=0 name=amount}
                                        <option {if $smarty.section.amount.index eq $offer.amount}selected="selected"{/if}>{$smarty.section.amount.index}</option>
                                    {/section}
                                </select>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        <center><button class="btn btn-primary btn-raised">Сохранить</button></center>
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
        $(".form-order-offers").on("success", function (event, packet, status, xhr) {
            if (!PinaRequest.handleRedirect(xhr)) {
                document.location = document.location.origin + document.location.pathname.replace('/offers', '') + '?changed=' + Math.random();
            }
        });
    </script>
{/literal}
{/script}