<div class="panel">
    <div class="panel-heading">
        <h2>Платежи</h2>
    </div>
    <div class="panel-body">
        {if $payments}
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Платеж</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$payments item=payment}
                    <tr>
                        <td>
                            №{$payment.id}
                            <br />{$payment.created|format_date} {$payment.created|format_time}
                        </td>
                        <td>
                            <strong>{$payment.title}</strong>
                        </td> 
                        <td>
                            <strong>{$payment.total|format_price}</strong>
                            <br />{$payment.status}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        {else}
            <p>Не найдены</p>
        {/if}
    </div>
</div>
