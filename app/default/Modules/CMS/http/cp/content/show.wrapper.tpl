{extends layout=block}

<div class="slot-content-editor" id="slot-content-editor-{$content.id}" data-id="{$content.id}" data-type="{$content.type}">

{if !$params.slot_single}
<div class="content-sortable" title="Изменить порядок {$params.slot_single}">
<i class="glyphicon glyphicon-sort"></i>
</div>
{/if}

<div class="front-content-editor">
    <a href="#" class="content-editor_edit" title="Редактировать">
        <i class="glyphicon glyphicon-pencil"></i>
    </a>
    <a  href="#" class="content-editor_cancel" title="Отменить">
        <i class="glyphicon glyphicon-remove"></i>
    </a>
    <a  href="#" class="content-editor_params" title="Параметры">
        <i class="glyphicon glyphicon-cog"></i>
    </a>
    <a href="#" class="content-editor_save" title="Сохранить">
        <i class="glyphicon glyphicon-ok"></i>
    </a>
    <a href="#" class="content-editor_remove" title="Удалить">
        <i class="glyphicon glyphicon-trash"></i>
    </a>
</div>

<div class="content-cover"></div>

<div class="content-show clearfix">
    {view get=":content_type/:content_id" content_id=$content.id content=$content content_type=$content.type}
</div>

<div class="content-editable"></div>
</div>
