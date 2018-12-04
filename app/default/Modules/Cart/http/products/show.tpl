{content name=title}{$resource.title}{/content}

{content name="breadcrumb"}
{module get="resources/:resource_id/parents" resource_id=$resource.id display=breadcrumbs title=$resource.title}
{/content}

<div class="row product">
    <div class="col-sm-6 col-md-4">
        {if $resource.sale_price && $resource.sale_price ne "0.00"}
            <div class="product-label-sale">SALE</div>
        {/if}


        <div id="product-gallery">
            {if $resource.image_width < 1000}
                <a  href="{img image=$resource|@mine:"image" width=$resource.image_width height=$resource.image_width trim=4 return=src}" 
                    class="zoom product-image">
                    <img src="{img image=$resource|@mine:"image" width=360 height=360 trim=4 return="src"}"
                         data-zoom-image="{img image=$resource|@mine:"image" width=$resource.image_width height=$resource.image_width trim=4 return=src}" 
                         style="max-width:100%"
                         />
                </a>
            {else}
                <a href="{img image=$resource|@mine:"image" width=1000 height=1000 trim=4 return=src}"
                   class="zoom product-image">
                    <img src="{img image=$resource|@mine:"image" width=360 height=360 trim=4 return="src"}"
                         data-zoom-image="{img image=$resource|@mine:"image" width=1000 height=1000 trim=4 return=src}"
                         style="max-width:100%"
                         />
                </a>
            {/if}
            {module get="products/:resource_id/images" resource_id=$resource.id}
        </div>

        {style src="/vendor/fancybox/jquery.fancybox.css"}{/style}
        {script src="/vendor/fancybox/jquery.fancybox.pack.js"}{/script}
        {*script src="/static/vendor/zoom/jquery.zoom.min.js"}{/script*}
        {script src="/vendor/elevatezoom/jquery.elevateZoom-2.2.3.min.js"}{/script}
        {script}
        {literal}
            <script>

                var zoomWidth = $(".product-header").width();

                $(".zoom > img").elevateZoom({
                    gallery: 'product-gallery',
                    cursor: 'pointer',
                    galleryActiveClass: 'active',
                    imageCrossfade: true,
                    zoomWindowPosition: 1,
                    zoomWindowOffetx: 30,
                    zoomWindowWidth: zoomWidth
                });

                $(".zoom > img").on("click", function (e) {
                    var ez = $('.zoom > img').data('elevateZoom');
                    $.fancybox(ez.getGalleryList());
                    return false;
                });

            </script>
        {/literal}
        {/script}
    </div>
    <div class="col-sm-6 col-md-6">
        {if $resource.title}
            <h1 class="product-header">{$resource.title}</h1>
        {/if}

        <p>{$resource.resource_tags|tag:"Артикул"}</p>

        {module get="products/:resource_id/offers" resource_id=$resource.id discount_percent=$resource.discount_percent}

        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#properties" aria-controls="home" role="tab" data-toggle="tab">Характеристики</a></li>
                    {if $resource.text}
                    <li role="presentation"><a href="#description" aria-controls="profile" role="tab" data-toggle="tab">Описание</a></li>
                    {/if}
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="properties">
                    <table class="table table-striped table-hover">
                        {foreach from=$tag_groups key=group item=tags}
                            <tr>
                                <td>
                                    {$group}
                                </td>
                                <td>
                                    {foreach from=$tags item=tag}
                                        {if $tag.url}
                                            <a href="/{$tag.url}">{$tag.tag}</a><br />
                                        {else}
                                            {$tag.tag}<br />
                                        {/if}
                                    {/foreach}
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
                {if $resource.text}
                    <div role="tabpanel" class="tab-pane" id="description">
                        {$resource.text|format_description}
                    </div>
                {/if}
            </div>

        </div>




    </div>
    <div class="col-sm-12 col-md-2">
        {module get="products/:resource_id/recommendations" resource_id=$resource.id limit=3}
    </div>

</div>

{module get="products/:resource_id/similars" resource_id=$resource.id limit=6}
{module get="products/:resource_id/combined-purchases" resource_id=$resource.id limit=6}