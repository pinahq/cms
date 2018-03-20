{module get="cp/:cp/resources/:resource_id" resource_id=$params.resource_id display=header}

{module get="cp/:cp/resources/:resource_id" resource_id=$params.resource_id display=header}
{place name=tabs}

<div class="panel">
    <div class="panel-body">
        {form method="put" action="cp/:cp/resources/:id/tagged" id=$params.resource_id class="form pina-form form-tags"}
        <div class="form-group">
            <input name="tags" type="text" class="form-control" />
        </div>
        {view get="cp/:cp/tags/block" display="script" tags=$tags}
        <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
        {/form}
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-md-push-6">
        <div class="hidden-xs"  style="visibility: hidden">
            {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id status=$params.status}
            {include file="Skin/paging.tpl"}
            {/link_context}
        </div>

        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id length=$params.length}
                        <ul class="nav nav-pills">
                            <li {if !$params.status}class="active"{/if}>
                                <a href="{link get=$paging.resource}">Все</a>
                            </li>
                            <li {if $params.status eq 'enabled'}class="active"{/if}>
                                <a href="{link get=$paging.resource status=enabled}">Активные</a>
                            </li>
                            <li {if $params.status eq 'disabled'}class="active"{/if}>
                                <a href="{link get=$paging.resource status=disabled}">Не активные</a>
                            </li>
                        </ul>
                        {/link_context}
                    </div>
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        {link_context parent_id=$params.parent_id status=$params.status length=$params.length}
                        {module get="cp/:cp/resource-types" id=$params.resource_type_id display="nav"}
                        {/link_context}
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-6 col-md-pull-6">

        {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id status=$params.status}
        {include file="Skin/paging.tpl"}
        {/link_context}

        {view get="cp/:cp/resources/block" reorder_resource_id=$params.resource_id reorder="tag-reorder" display=items}

        {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id status=$params.status}
        {include file="Skin/paging.tpl"}
        {/link_context}

    </div>
</div>

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script>
        $(".form-tags").on("success", function (event, packet, status, xhr) {
            document.location.reload();
        });
    </script>
{/literal}
{/script}

{script}
{literal}
    <script>
        $('.action-reorder').on('click', function () {
            var that = $('.resources');
            var resource = $(that).data('resource');
            var position = $(this).data('position');
            var headers = $(that).data('csrf-token') ? {'X-CSRF-Token': $(that).data('csrf-token')} : {};
            var id = $(this).parents('.resource').data('id');
            $.ajax('/' + resource + '/' + id, {method: 'put', data: {position: position}, headers: headers, success: function (response) {
                    document.location.reload();
                }});

        });
    </script>
{/literal}
{/script}

{script}
{literal}
    <script>
        $('.resources').sortable({
            placeholder: "col col-xs-6 col-sm-4 col-md-3 col-lg-2  resource",
            start: function (event, ui) {
                var elem = $(event.toElement).parents('.resource');
                var height = elem.height();
                var width = elem.width();
                ui.placeholder.html('<div class="col-sm-4 resource" style="border:width:' + width + 'px;height:' + height + 'px;">&nbsp;</div>');
            },
            stop: function (event, ui) {
                var resource = $(this).data('resource');
                var method = $(this).data('method');
                var headers = $(this).data('csrf-token') ? {'X-CSRF-Token': $(this).data('csrf-token')} : {};
                var data = [];
                var order = 0;
                $('.resources .resource').each(function () {
                    data.push($(this).data('id'));
                });

                $.ajax('/' + resource, {method: method, data: {resource_id: data}, headers: headers, success: function (response) {
                    }});
            }
        });
    </script>
{/literal}
{/script}
