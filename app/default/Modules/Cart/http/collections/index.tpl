{group_brands brands=$resources assign="groups"}

<div class="row sitemap">
    {foreach from=$groups key=key item=group}
        <div class="col-sm-4 col-md-3 brand">
            <h2>{$key}</h2>
            <ul class="nav">
                {foreach from=$group item=rr}
                    <li>
                        <a href="/{$rr.url}">{$rr.title}</a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/foreach}
</div>


{script}
{literal}
    <script>
        function justifyHeight(e, minWidth) {
            if (minWidth && $(window).outerWidth() < minWidth) {
                $(e).outerHeight("auto");
                return
            }
            var max = -1;
            $(e).each(function () {
                $(this).outerHeight("auto");
                if ($(this).outerHeight() > max) {
                    max = $(this).outerHeight()
                }
            });
            $(e).outerHeight(max);

            return max;
        }

        $(window).load(function () {
            justifyHeight(".sitemap .brand", 751);
        });
        $(window).resize(function () {
            justifyHeight(".sitemap .brand", 751);
        })
    </script>
{/literal}
{/script}