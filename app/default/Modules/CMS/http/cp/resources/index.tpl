{if $params.parent_id}
    {module get="cp/:cp/resources/:resource_id" resource_id=$params.parent_id display="header"}
{elseif $params.resource_type_id}
    {module get="cp/:cp/resource-types/:resource_type_id" resource_type_id=$params.resource_type_id display="header"}
{else}
    {content name="breadcrumb"}
    <ol class="breadcrumb">
        <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
        <li>{t}Structure{/t}</li>
    </ol>
    {/content}
    {content name="page_header"}{t}Structure{/t}{/content}
{/if}

{place name="tabs"}

<div class="row">
    <div class="col-md-6 col-md-push-6">
        <div class="hidden-xs"  style="visibility: hidden">
            {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id status=$params.status length=$params.length search=$params.search tag_type_id=$params.tag_type_id tag=$params.tag tag_id=$params.tag_id}
            {include file="Skin/paging.tpl"}
            {/link_context}
        </div>
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id length=$params.length search=$params.search tag_type_id=$params.tag_type_id tag=$params.tag tag_id=$params.tag_id}
                        <ul class="nav nav-pills">
                            <li {if !$params.status}class="active"{/if}>
                                <a href="{link}">Все</a>
                            </li>
                            <li {if $params.status eq 'enabled'}class="active"{/if}>
                                <a href="{link status=enabled}">Активные</a>
                            </li>
                            <li {if $params.status eq 'disabled'}class="active"{/if}>
                                <a href="{link status=disabled}">Не активные</a>
                            </li>
                        </ul>
                        {/link_context}
                    </div>
                    <div class="col-md-4">
                        {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id status=$params.status search=$params.search tag_type_id=$params.tag_type_id tag=$params.tag tag_id=$params.tag_id}
                        <ul class="nav nav-pills">
                            <li {if !$params.length}class="active"{/if}>
                                <a href="{link}">Все</a>
                            </li>
                            <li {if $params.length eq 1}class="active"{/if}>
                                <a href="{link length=1}">Один уровень</a>
                            </li>
                        </ul>
                        {/link_context}
                    </div>
                    <div class="col-md-2">
                        <ul class="nav nav-pills">
                            <li {if $params.search or $params.tag or $params.tag_id}class="active"{/if}><a href="#" onclick="$(this).parent().toggleClass('active');
                                    $('.search').toggleClass('hidden');">Поиск</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>


        <div class="panel search {if not($params.search or $params.tag or $params.tag_id)}hidden{/if}">
            <div class="panel-body">
                {form action="/cp/:cp/resources" method="get" name="resource_search_form" class="form form-horizontal form-resource pina-form"}
                <div class="col-sm-12">
                    <div class="md-form input-group">
                        {if $params.resource_type_id}
                            <input type="hidden" name="resource_type_id" value="{$params.resource_type_id}" />
                        {/if}
                        {if $params.status}
                            <input type="hidden" name="status" value="{$params.status}" />
                        {/if}
                        {if $params.length}
                            <input type="hidden" name="length" value="{$params.length}" />
                        {/if}
                        {if $params.tag_type_id}
                            <input type="hidden" name="tag_type_id" value="{$params.tag_type_id}" />
                        {/if}
                        {if $params.tag}
                            <input type="hidden" name="tag" value="{$params.tag}" />
                        {/if}
                        {if $params.tag_id}
                            <input type="hidden" name="tag_id" value="{$params.tag_id}" />
                        {/if}
                        <input type="search" name="search" class="form-control" placeholder="{t}Search{/t}" value="{$params.search|default:''}" />
                        <span class="input-group-btn">
                            <button class="btn btn btn-fab btn-fab-mini"><i class="material-icons">search</i></button>
                        </span>
                    </div>
                </div>
                {/form}
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                {link_context parent_id=$params.parent_id status=$params.status search=$params.search tag_type_id=$params.tag_type_id tag=$params.tag tag_id=$params.tag_id}
                {module get="cp/:cp/resource-types" id=$params.resource_type_id display="nav"}
                {/link_context}
            </div>
        </div>
        <div class="panel search {if not($params.search or $params.tag or $params.tag_id)}hidden{/if}">
            <div class="panel-body">
                {module get="cp/:cp/tags-filter" action="cp/:cp/resources"
                    display="selector" 
                    id=$params.tag_id 
                    tag=$params.tag 
                    tag_type_id=$params.tag_type_id
                    price=$params.price
                    stock=$params.stock
                    resource_type_id=$params.resource_type_id
                    status=$params.status
                    length=$params.length
                    search=$params.search}
            </div>
        </div>

        {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id status=$params.status length=$params.length search=$params.search tag_type_id=$params.tag_type_id tag=$params.tag tag_id=$params.tag_id}
    {capture name="url"}{link get="cp/:cp/resources"}{/capture}
    {/link_context}
    {module get="cp/:cp/resource-type-trees" parent_id=$params.parent_id url=$smarty.capture.url}
</div>
<div class="col-md-6 col-md-pull-6">

    {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id status=$params.status length=$params.length search=$params.search tag_type_id=$params.tag_type_id tag=$params.tag tag_id=$params.tag_id}
    {include file="Skin/paging.tpl"}
    {/link_context}

    {view get="cp/:cp/resources/block" reorder_resource_id=$params.parent_id reorder=reorder display=items}

    {link_context parent_id=$params.parent_id resource_type_id=$params.resource_type_id status=$params.status length=$params.length search=$params.search tag_type_id=$params.tag_type_id tag=$params.tag tag_id=$params.tag_id}
    {include file="Skin/paging.tpl"}
    {/link_context}

</div>
</div>



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
            stop: function (event, ui) {
                var resource = $(this).data('resource');
                var method = $(this).data('method');
                var headers = $(this).data('csrf-token') ? {'X-CSRF-Token': $(this).data('csrf-token')} : {};
                var data = [];
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
