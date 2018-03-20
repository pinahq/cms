<div class="panel panel-default">
    <div class="panel-body">
        {if $tags}
            <div class="list-group tag-items" {action_attributes post="cp/:cp/tag-types/:tag_type_id/tag-reorder" tag_type_id=$params.tag_type_id}>
                {foreach from=$tags item=tag}
                    <div class="draggable" data-id="{$tag.id|default:0}">

                        <div class="list-group-item">
                            <div class="row-picture">
                                <i class="material-icons">open_with</i>
                            </div>
                            <div class="row-content">
                                <h4 class="list-group-item-heading">
                                    {$tag.tag}
                                </h4>

                                <p class="list-group-item-text">

                                    {if $tag.resource_id}
                                        {t}Resource{/t}: <a href="{link get="cp/:cp/resources/:id" id=$tag.resource_id}" target="_blank">{if $tag.resource_title}{$tag.resource_title}{else}#{$tag.resource_id}{/if}</a>
                                    {/if}

                                </p>
                            </div>
                        </div>
                        <div class="list-group-separator"></div>
                    </div>
                {/foreach}
            </div>
        {else}
            <p>{t}Not found{/t}</p>
        {/if}
    </div>
</div>

{include file="Skin/paging.tpl"}

<div class="row">
    <div class="col-sm-12">
        <button class="btn btn-default btn-raised js-sort-abc"  {action_attributes post="cp/:cp/tag-types/:tag_type_id/tag-reorder" tag_type_id=$params.tag_type_id}>Отсортировать по алфавиту</button>
        <button class="btn btn-default btn-raised js-sort-123"  {action_attributes post="cp/:cp/tag-types/:tag_type_id/tag-reorder" tag_type_id=$params.tag_type_id}>Отсортировать числа</button>
    </div>
</div>

{script}
{literal}
    <script>
        $('.tag-items').sortable({
            stop: function (event, ui) {
                var resource = $(this).data('resource');
                var method = $(this).data('method');
                var headers = $(this).data('csrf-token') ? {'X-CSRF-Token': $(this).data('csrf-token')} : {};
                var data = [];
                $('.tag-items .draggable').each(function () {
                    data.push($(this).data('id'));
                });

                $.ajax('/' + resource, {method: method, data: {id: data}, headers: headers});
            }
        });

        $('.js-sort-abc').on('click', function () {
            var resource = $(this).data('resource');
            var method = $(this).data('method');
            var headers = $(this).data('csrf-token') ? {'X-CSRF-Token': $(this).data('csrf-token')} : {};

            var items = [];
            var ids = [];
            $('.tag-items .draggable').each(function () {
                var value = $(this).find('h4 a').text().trim();
                items.push(value);
                ids[value] = $(this).data('id');
            });
            items = items.sort();
            var data = [];
            for (var i in items) {
                var value = items[i];
                data.push(ids[value]);
            }

            $.ajax('/' + resource, {method: method, data: {id: data}, headers: headers, success: function () {
                    document.location.reload();
                }});
        });

        $('.js-sort-123').on('click', function () {
            var resource = $(this).data('resource');
            var method = $(this).data('method');
            var headers = $(this).data('csrf-token') ? {'X-CSRF-Token': $(this).data('csrf-token')} : {};

            var items = [];
            var ids = [];
            $('.tag-items .draggable').each(function () {
                var parts = $(this).find('h4 a').text().trim().split(':', 2);
                var value = parts[1] ? parseFloat(parts[1].trim()) : 0
                items.push(value);
                ids[value] = $(this).data('id');
            });
            items = items.sort(function (a, b) {
                if (a < b) {
                    return -1;
                } else if (a > b) {
                    return 1;
                }
                return 0;
            });
            var data = [];
            for (var i in items) {
                var value = items[i];
                if (data.indexOf(ids[value]) == -1) {
                    data.push(ids[value]);
                }
            }

            $.ajax('/' + resource, {method: method, data: {id: data}, headers: headers, success: function () {
                    document.location.reload();
                }});
        });
    </script>
{/literal}
{/script}
