{if $resources}
<h2>С этим товаром так же заказывают</h2>
{view get="products/block" display="items" resources=$resources}
{/if}