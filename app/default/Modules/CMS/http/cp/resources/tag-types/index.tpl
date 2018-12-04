{if $params.resource_id}
    {module get="cp/:cp/resources/:resource_id" resource_id=$params.resource_id display=header}
    {place name=tabs}
{else}
    {content name="breadcrumb"}

    {/content}

    {content name="page_header"}
    {t}Tag Settings{/t}
    {/content}
{/if}
<div class="panel">
    <div class="tab-content panel-body">
        {form action="/cp/:cp/resources/:id/tag-types" id=$params.resource_id}
        <div class="md-form input-group">
            <div class="form-group is-empty"><input type="search" name="search" class="form-control" placeholder="Search" value="{$params.search}"></div>
            <span class="input-group-btn">
                <button class="btn btn btn-fab btn-fab-mini"><i class="material-icons">search</i></button>
            </span>
        </div>
        {/form}

        {if $tag_types}

            {link_context search=$params.search}
            {include file="Skin/paging.tpl"}
            {/link_context}
            <table class="table table-hover">
                <col />
                <col width="15%" />
                <col width="15%" />
                <col width="15%" />
                <thead>
                    <tr>
                        <th>{t}Tag type{/t}</th>
                        <th>{t}Details{/t}</th>
                        <th>{t}List{/t}</th>
                        <th>{t}Filter{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$tag_types item=t}
                        <tr>
                            <td>
                                {$t.type}
                            </td>
                            <td>
                                <div class="togglebutton">
                                    <label>
                                        <input type="checkbox" class="action-toggle"
                                               {action_attributes put="cp/:cp/resources/:resource_id/tag-types/:id" resource_id=$params.resource_id  id=$t.id subject="details"}
                                               {if $t.details_tag_type_id} checked=""{/if}>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="togglebutton">
                                    <label>
                                        <input type="checkbox" class="action-toggle"
                                               {action_attributes put="cp/:cp/resources/:resource_id/tag-types/:id" resource_id=$params.resource_id  id=$t.id subject="list"}
                                               {if $t.list_tag_type_id} checked=""{/if}>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="togglebutton">
                                    <label>
                                        <input type="checkbox" class="action-toggle"
                                               {action_attributes put="cp/:cp/resources/:resource_id/tag-types/:id" resource_id=$params.resource_id  id=$t.id subject="filter"}
                                               {if $t.filter_tag_type_id} checked=""{/if}>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>

            {link_context search=$params.search}
            {include file="Skin/paging.tpl"}
            {/link_context}
        {else}
            <p>{t}Not found{/t}
            {/if}
    </div>
</div>

{script}
{literal}
    <script>
        $(".action-toggle").on('change', function () {
            var $el = $(this);
            var resource = $el.data('resource');
            var params = $el.data('params');
            var method = $el.is(":checked") ? $el.data('method') : 'delete';
            $.ajax({
                url: '/' + resource,
                method: method,
                data: params,
                headers: $el.data('csrf-token') ? {'X-CSRF-Token': $el.data('csrf-token')} : {},
                success: function (r) {
                    var current = $el.is(":checked") ? true : false;
                    if (r.relation != current) {
                        $el.prop('checked', r.relation ? true : false);
                    }
                }
            });
        });
    </script>
{/literal}
{/script}