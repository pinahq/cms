<div class="panel">
    <div class="panel-heading">
        <h2>Покупки</h2>
    </div>
    <div class="panel-body">
        {if $order_offers}
        <table class="table table-hover">
            <thead>
                <tr>
                    <th colspan="2">Товар</th>
                    <th>Статус</th>
                    <th>Кол-во</th>
                    <th>Цена</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$order_offers item=offer}
                    <tr {if $offer.order_offer_status_color} style="background-color: #{$offer.order_offer_status_color}"{/if}>
                        <td>
                            <a href="/{$offer.url}">
                                {img id=$offer.image_id width=220 height=220 style="width:110px;"}
                            </a>
                        </td>
                        <td>
                            {$offer.title}<br />{$offer.tags|replace:"\n":"<br />"}
                        </td>
                        <td>
                            {$offer.order_offer_status_title}
                        </td>
                        <td>
                            {$offer.amount}
                        </td>
                        <td>
                            {$offer.actual_price|format_price}
                        </td>
                    </tr>

                {/foreach}
            </tbody>
        </table>
        {else}
            <p>Не найдены</p>
        {/if}
    </div>
    <div class="panel-footer">
        <a class="btn btn-default" href="{link get="cp/:cp/orders/:order_id/offers" order_id=$params.order_id display="edit"}">Редактировать</a>
        <a class="btn btn-default" href="{link get="cp/:cp/orders/:order_id/offers/create" order_id=$params.order_id}">Добавить</a>
    </div>
</div>
