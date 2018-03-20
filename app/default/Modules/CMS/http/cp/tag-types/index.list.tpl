<div class="panel panel-default">
    <div class="panel-body">
        <ul class="nav nav-pills nav-stacked">
            <li>
                <a href="{link get="cp/:cp/tag-types/:id" id=0}">
                    <i>{t}Empty tag type{/t}</i>
                </a>
            </li>
            {foreach from=$tag_types item=type}
                <li>
                    <a href="{link get="cp/:cp/tag-types/:id" id=$type.id}">
                        {$type.type}
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
</div>
