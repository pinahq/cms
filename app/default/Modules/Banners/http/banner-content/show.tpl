{style src="/vendor/responsiveslides/responsiveslides.css"}{/style}
{assign var=unique_id value=0|mt_rand:$smarty.const.PHP_INT_MAX}

<div id="home-slideshow-{$unique_id}" class="slideshow-wrapper">
    <ul class="slides bxslider rslides">
        {foreach from=$content.params.images item=image}
            {if $image.enabled eq 'Y'}
                <li>
                    <a href="{$image.link_url|default:"#"}">
                        {img image=$image}
                    </a>
                </li>
            {/if}
        {/foreach}
    </ul>
</div>
{script src="/vendor/responsiveslides/responsiveslides.min.js"}{/script}
{script}
{literal}
    <script>
        $(document).ready(function () {
            $('#home-slideshow-{/literal}{$unique_id}{literal} > .rslides').responsiveSlides();
        });
    </script>
{/literal}
{/script}
