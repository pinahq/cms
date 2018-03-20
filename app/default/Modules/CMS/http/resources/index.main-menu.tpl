<nav class="menu">
    <div class="container">
        <ul class="nav">
            {foreach from=$resources item=r}
                {if ($params.parent_id && $r.parent_id eq $params.parent_id) or (!$params.parent_id && $r.parent_id eq 0)}
                    <li class="dropdown">
                        <a href="/{$r.url}" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">{$r.title}</a>
                        <div class="dropdown-menu">
                            <div class="menu-division menu-division-categories">
                                <h5>Разделы</h5>
                                <ul class="nav">
                                    {assign var="index" value=1}
                                    {foreach from=$resources item=rr name=column}
                                        {if $rr.parent_id eq $r.id}
                                            <li><a href="/{$rr.url}">{$rr.title}</a></li>
                                            {if $index%16 eq 0 && !$smarty.foreach.column.last}</ul><ul class="nav">{/if}
                                            {assign var="index" value=$index+1}
                                        {/if}
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    </li>
                {/if}
            {/foreach}
        </ul>
    </div>
</nav>

{script}
{literal}
    <script>
        function justifyWidth(e) {
            var max = -1;
            $(e).each(function () {
                $(this).outerWidth("auto");
                if ($(this).outerWidth() > max) {
                    max = $(this).outerWidth()
                }
            });
            $(e).outerWidth(max);

            return max;
        }

        $(".menu-division > ul.nav").each(function () {
            if ($(this).find('li').length == 0) {
                $(this).remove();
            }
        });

        $('.dropdown-menu').parent().on('shown.bs.dropdown', function () {
            justifyWidth($(this).find(".menu-division > ul.nav"), 751);
        });
        $(window).resize(function () {
            justifyWidth(".menu-division > ul.nav", 751);
        })
    </script>
{/literal}
{/script}