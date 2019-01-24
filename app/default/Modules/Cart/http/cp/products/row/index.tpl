<div class="list-group-item">
    <div class="row-picture">
        {if $media_id}
            {img id=$media_id width=56 height=56}
        {else}
            <i class="material-icons">open_with</i>
        {/if}
    </div>
    <div class="row-content">

        <div class="least-content">
            <div class="togglebutton">
                <label>
                    <input type="checkbox" class="action-toggle" data-key="enabled"
                           {action_attributes put="cp/:cp/resources/:id/status" id=$id}
                           {if $enabled eq 'Y'} checked=""{/if} />
                </label>
            </div>
        </div>
        <div style="position:absolute;right:16px;bottom:0;">
            <a href="#" class="action-reorder" data-position="first" title="move to first position"><i class="material-icons">vertical_align_top</i></a>
            <a href="#" class="action-reorder" data-position="last" title="move to last position"><i class="material-icons">vertical_align_bottom</i></a>
        </div>
        <h4 class="list-group-item-heading"><a href="{link get="cp/:cp/resources/:id" id=$id}">{$title|tag_pattern:$resource_type_pattern:$tags}</a></h4>

        <p class="list-group-item-text">
            <strong>{$actual_price|format_price}</strong>{if $actual_price lt $price} <del>{$price|format_price}</del>{/if}

            {if $resource_type_tree eq 'Y'}
                | <a href="{link get="cp/:cp/resources" parent_id=$id length=$smarty.get.length}"><i class="material-icons">folder</i> <span class="badge">{$child_count}</span></a>
            {/if}
        </p>
        
    </div>
</div>