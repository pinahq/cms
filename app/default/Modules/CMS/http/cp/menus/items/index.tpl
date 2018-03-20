<h2>Items</h2>

<div class="panel panel-default">
    <div class="panel-body">
        {if $menu_items}
            <div class="list-group menu-items" {action_attributes post="cp/:cp/menus/:key/items/:id/reorder" key=$params.key id=0}>
                {foreach from=$menu_items item=menu_item}
                    <div class="draggable menu-item" data-id="{$menu_item.id|default:0}">

                        <div class="list-group-item">
                            <div class="row-picture">
                                <i class="material-icons">open_with</i>
                            </div>
                            <div class="row-content">

                                <div class="least-content">
                                    <div class="togglebutton">
                                        <label>
                                            <input type="checkbox" class="action-toggle" data-key="enabled"
                                                   {action_attributes put="cp/:cp/menus/:key/items/:id/status" key=$menu_item.menu_key id=$menu_item.id}
                                                   {if $menu_item.enabled eq 'Y'} checked=""{/if} />
                                        </label>
                                    </div>
                                </div>

                                <h4 class="list-group-item-heading">
                                    <a href="{link get="cp/:cp/menus/:key/items/:id" key=$menu_item.menu_key id=$menu_item.id}">
                                        {$menu_item.title}
                                    </a>
                                </h4>

                                <p class="list-group-item-text">
                                    {if $menu_item.resource_id}
                                        {t}Resource{/t}: <a href="{link get="cp/:cp/resources/:id" id=$menu_item.resource_id}" target="_blank">{link get="cp/:cp/resources/:id" id=$menu_item.resource_id}</a>
                                    {else}
                                        {t}Link{/t}: <a href="{$menu_item.link}" target="_blank">{$menu_item.link}</a>
                                    {/if}
                                </p>
                            </div>
                        </div>
                        <div class="list-group-separator"></div>
                    </div>
                {/foreach}
            </div>
        {else}
            <p>{t}Menu items do not exist{/t}.</p>
        {/if}

    </div>
</div>
<a href="{link get="cp/:cp/menus/:key/items/create" key=$menu.key}" class="btn btn-primary btn-raised">{t}Create new item{/t}</a>

{script}
{literal}
    <script>
        $('.menu-items').sortable({
            stop: function (event, ui) {
                var resource = $(this).data('resource');
                var method = $(this).data('method');
                var headers = $(this).data('csrf-token') ? {'X-CSRF-Token': $(this).data('csrf-token')} : {};
                var data = [];
                $('.menu-items .menu-item').each(function () {
                    data.push($(this).data('id'));
                });

                $.ajax('/' + resource, {method: method, data: {id: data}, headers: headers});
            }
        });
    </script>
{/literal}
{/script}

{script src="/static/default/js/pina.toggle.js"}{/script}

