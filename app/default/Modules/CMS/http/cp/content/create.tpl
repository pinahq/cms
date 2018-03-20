{extends layout=block}

<div style="display: none" id="block-menu">
    <div class="block-menu_content">
        <h2>Добавить блок</h2>

        <div class="block-list_container">
            <ul class="block-list">
                {foreach from=$content_types item=content_type}
                    <li class="block-list_item" {action_attributes post="cp/:cp/content" type=$content_type.type slot=$params.slot resource_id=$params.resource_id}>
                        {$content_type.title}
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>

    <div class="block-menu_close">
        <i class="glyphicon glyphicon-remove"></i>
    </div>
</div>