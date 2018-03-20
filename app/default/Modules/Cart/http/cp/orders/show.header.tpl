{content name="page_header"}Заказ {$order.number} (#{$order.order_id}) от {$order.created|format_date}{/content}
{content name="breadcrumb"}
    <ol class="breadcrumb">
        <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
        <li><a href="{link get="cp/:cp/orders"}">Заказы</a></li>
        <li><a href="{link get="cp/:cp/orders/:order_id" order_id=$order.order_id}">Заказ {$order.number} (#{$order.order_id})</a></li>
        <li>{$params.title}</li>
    </ol>
{/content}