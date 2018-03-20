{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="/">Домой</a></li>
    <li>Ваша корзина</li>
    <li class="active">Статус оплаты</li>
</ol>
{/content}

{if $status eq 'new'}
<h1 class="page-header">Оплата иницирована</h1>
{elseif $status eq 'processed'}
<h1 class="page-header">Оплата размещена</h1>
{elseif $status eq 'payed'}
<h1 class="page-header">Оплата успешно проведена</h1>
{elseif $status eq 'canceled'}
<h1 class="page-header">Оплата отменена</h1>
{elseif $status eq 'failed'}
<h1 class="page-header">Не удалось провести оплату</h1>
{/if}

{if $status eq 'new' or $status eq 'processed'}
<p>Завершите оплату в платежной системе.</p>
{else}
<p><a href="{if $return_url}{$return_url}{else}{link get="carts/:cart_id/orders/:order_id" cart_id=$cart_id order_id=$order_id}{/if}" class="btn btn-primary">Перейти к деталям заказа</a></p>
{/if}