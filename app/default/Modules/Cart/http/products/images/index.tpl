<div class="product-thumbnails">
    {foreach from=$images item=image}
        <a class="product-thumbnail" 
           href="{img image=$image width=360 height=360 trim=4 return=src}"
           data-image="{img image=$image width=360 height=360 trim=4 return=src}"
            {if $image.width < 1000}
                data-zoom-image="{img image=$image width=$image.width height=$image.width trim=4 return=src}" 
            {else}
                data-zoom-image="{img image=$image width=1000 height=1000 trim=4 return=src}" 
            {/if}
        >
            {img image=$image width=220 height=220 trim=4 style="width:50px;"}
        </a>
    {/foreach}
</div>