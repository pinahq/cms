{if $resources}
    {view get="products" 
    page=$params.page
    sort=$params.sort
    tag_id=$params.tag_id}
{else}
    <p>В настоящий момент товары на распродаже все проданы. Если вы хотите получать информацию о специальных предложениях, оставьте нам свой емейл</p>
    <div class="row" style="margin-bottom: 100px;">
        <div class="col-md-4">
            {module get="subscription"}
        </div>
    </div>
{/if}