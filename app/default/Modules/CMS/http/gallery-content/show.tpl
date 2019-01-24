{style src="/vendor/responsiveslides/responsiveslides.css"}{/style}
{assign var=unique_id value=0|mt_rand:$smarty.const.PHP_INT_MAX}

{if $content.params.columns gt 0}
    {math equation="12/x" x=$content.params.columns assign=column_width}
{else}
    {assign var=column_width value=3}
{/if}

{assign var=index value=0}
<div id="gallery-{$unique_id}" class="pina-gallery gallery-wrapper">
    <div class="row">
        {foreach from=$content.params.images item=image}
            {if $image.enabled eq 'Y'}
                {if $content.params.columns && $index % $content.params.columns eq 0 && $index > 0}
                    </div><div class="row">
                {/if}
                <div class="col-md-{$column_width}">
                    <a href="{img image=$image return=src}" rel="gallery">
                        {img media=$image class="thumbnail" style="max-width: 100%;"}
                    </a>
                </div>
                    {assign var=index value=$index+1}
            {/if}
        {/foreach}
    </div>
</div>

{style src="/vendor/fancybox/jquery.fancybox.css"}{/style}
{script src="/vendor/fancybox/jquery.fancybox.js"}{/script}

{script}
{literal}
    <script>
        $(document).ready(function () {
            $('.pina-gallery a').fancybox();
        });
    </script>
{/literal}
{/script}
