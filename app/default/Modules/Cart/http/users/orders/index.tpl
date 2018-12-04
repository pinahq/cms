<h1 class="page-header">Список заказов</h1>

{if $orders}
    <table class="table table-hover table-orders">
        <thead>
            <tr>
                <th>#</th>
                <th>Дата</th>
                <th>Статус</th>
                <th>Кол-во</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$orders item=order}
                <tr>
                    <td>
                        <a href="{link get="users/:user_id/orders/:id" id=$order.id}">
                            {if $order.number}
                                {$order.number}
                            {else}
                                {$order.id}
                            {/if}
                        </a>
                    </td>
                    <td>
                        <a href="{link get="users/:user_id/orders/:id" id=$order.id}">
                            {$order.created|format_date}
                        </a>
                    </td>
                    <td>
                        <a href="{link get="users/:user_id/orders/:id" id=$order.id}">
                            {$order.order_status_title|default:$order.order_status|default:$order.order_status_group}
                        </a>
                    </td>
                    <td>
                        <a href="{link get="users/:user_id/orders/:id" id=$order.id}">
                            {$order.amount}
                        </a>
                    </td>
                    <td>
                        <a href="{link get="users/:user_id/orders/:id" id=$order.id}">
                            {$order.total|format_price}
                        </a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <p>Заказов нет</p>
{/if}