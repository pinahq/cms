<div class="toplist-block">
    <div class="toplist-content">
        <div class="toplist-girls">
            <div class="toplist-banner">
                {if $content.params.catalog.0.image_id}
                    {img image=$content.params.catalog.0|@mine:"image" class=pulse}
                {else}
                    <img src="/static/default/images/empty.gif" class="pulse" />
                {/if}
                <div class="toplist-banner-hover">              
                    <div class="toplist-groupbanner">
                        <div class="toplist-bannerinner">
                            <div class="tlb-heading">{$content.params.catalog.0.title}</div>
                            <div class="tlb-desc">&nbsp;</div>
                            <div class="tlb-action">
                                <a class="btn btn-2" href="{$content.params.catalog.0.url}">
                                    {$content.params.catalog.0.button|default:"Enter"|t}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {module get="catalog-matrix-content/:content_id/products" tags=$content.params.catalog.0.tags}
        </div>

        <div class="toplist-boys">
            <div class="toplist-banner">
                {if $content.params.catalog.1.image_id}
                    {img image=$content.params.catalog.1|@mine:"image" class=pulse}
                {else}
                    <img src="/static/default/images/empty.gif" class="pulse" />
                {/if}
                <div class="toplist-banner-hover">              
                    <div class="toplist-groupbanner">
                        <div class="toplist-bannerinner">
                            <div class="tlb-heading">{$content.params.catalog.1.title}</div>
                            <div class="tlb-desc">&nbsp;</div>
                            <div class="tlb-action">
                                <a class="btn btn-2" href="{$content.params.catalog.1.url}">
                                    {$content.params.catalog.1.button|default:"Enter"|t}
                                </a>
                            </div>
                        </div>         
                    </div>
                </div>
            </div>

            {module get="catalog-matrix-content/:content_id/products" tags=$content.params.catalog.1.tags}
        </div>
    </div>
</div>