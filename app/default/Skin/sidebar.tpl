{module action="category.tree" category_type="category" level=4 title="categories"|lng class="categories"}
{if $main eq "category.view"}
	{module action="product-filter.filter" category_id=$category.category_id}
	{module action="option.category-filters" category_id=$category.category_id}
	{module action="product-type.category-filters" category_id=$category.category_id}
{/if}
{module action="post.main-blog-last"}
