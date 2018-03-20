<div class="panel panel-default">
    <div class="panel-heading"{if $status.color} style="background-color: #{$status.color}"{/if}>
        <h2>{$status.title}</h2>
    </div>
    <div class="panel-body panel-statuses">
        <div class="row">
                <div class="col col-md-3 col-sm-6">
                    {view get="cp/:cp/orders/:order_id/statuses/block" display="buttons" statuses=$statusGroups.placed order_status_id=$params.order_status_id}
                    {view get="cp/:cp/orders/:order_id/statuses/block" display="buttons" statuses=$statusGroups.approval order_status_id=$params.order_status_id}
                </div>
                <div class="col col-md-3 col-sm-6">
                    {view get="cp/:cp/orders/:order_id/statuses/block" display="buttons" statuses=$statusGroups.assembling order_status_id=$params.order_status_id}
                </div>
                <div class="col col-md-3 col-sm-6">
                    {view get="cp/:cp/orders/:order_id/statuses/block" display="buttons" statuses=$statusGroups.delivering order_status_id=$params.order_status_id}
                    {view get="cp/:cp/orders/:order_id/statuses/block" display="buttons" statuses=$statusGroups.complete order_status_id=$params.order_status_id}
                </div>
                <div class="col col-md-3 col-sm-6">
                    {view get="cp/:cp/orders/:order_id/statuses/block" display="buttons" statuses=$statusGroups.cancelled order_status_id=$params.order_status_id}
                </div>
        </div>
    </div>
</div>

{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script>
        $(".pina-action").on("success", function (packet, status, xhr) {
            document.location.reload();
        });
    </script>
{/literal}
{/script}


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
            justifyHeight(".panel-statuses .col", 751);
        });
        $(window).resize(function () {
            justifyHeight(".panel-statuses .col", 751);
        })
    </script>
{/literal}
{/script}