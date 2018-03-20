{if $resources}
<h2>{$params.title|default:"Похожие товары"}</h2>
{view get="products/block" display="items" resources=$resources}
{/if}