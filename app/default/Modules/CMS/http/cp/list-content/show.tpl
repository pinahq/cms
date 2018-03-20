{view get="list-content/:content_id" content=$content wrapper="div class=slot-content-container"}

<div class="content-form form" {action_attributes put="cp/:cp/list-content/:id" id=$content.id}>
    <div class="content-params" style="width:60%;">

        {assign var=vals value=','|explode:'0,1,2,3,4,6,12'}
        {assign var=keys value=','|explode:',1 колонка,2 колонки,3 колонки,4 колонки,6 колонок,12 колонок'}
        {assign var=params value=$vals|@array_combine:$keys}
        {include file="Skin/form-line-select.tpl" labelColumn=12 title="Количество колонок" name="columns" list=$params value=$content.params.columns}


        <h2 class="params-header">Список</h2>

        <div class="append-list" data-name="items">
            {foreach from=$content.params.items item=item name=items}
                <div class="panel append-row">
                    <div class="panel-footer">
                        <a href="#" class="append-delete" style="float: right;">Delete</a>
                        <div class="form-group append-field" data-name="title">
                            <label>Название</label>
                            <input type="text" class="form-control append-input" value="{$item.title}" />
                        </div>
                        <div class="form-group append-field" data-name="text">
                            <label>Текст</label>
                            <textarea class="form-control append-input" >{$item.text}</textarea>
                        </div>
                    </div>
                </div>
            {/foreach}
            <div class="panel append-row empty">
                <div class="panel-footer">
                    <a href="#" class="append-delete" style="float: right;display:none;">Delete</a>
                    <div class="form-group append-field" data-name="title">
                        <label>Название</label>
                        <input type="text" class="form-control append-input" value="" />
                    </div>
                    <div class="form-group append-field" data-name="text">
                        <label>Текст</label>
                        <textarea class="form-control append-input"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</div>

{script}
{literal}
    <script type="text/javascript">

        Pina.slotEditor.on('list-content', 'edit', function (currentBlock) {
            Pina.slotEditor.showCurrentContentParams();
        });

        function renameTableAppendFields() {
            var index = 0;
            var baseName = $('.append-list').attr('data-name');
            $('.append-list .append-row').each(function () {
                $(this).find('.append-input').each(function () {
                    var name = $(this).parents('.append-field').attr('data-name');
                    $(this).attr('name', baseName + '[' + index + '][' + name + ']')
                });
                index++;
            });
        }

        function initAppendControl() {
            $('.append-list').on('change', '.append-input', function () {
                var parentRow = $(this).parents('.append-row');
                if (parentRow.is('.empty')) {
                    var newEmptyRow = parentRow.clone();
                    parentRow.removeClass('empty');
                    parentRow.find('.append-delete').show();
                    $(newEmptyRow).find('.append-input').val('');
                    $('.append-list').append(newEmptyRow);
                    renameTableAppendFields();
                } else {
                    var isEmpty = true;
                    $(parentRow).find('.append-input').each(function () {
                        if ($(this).val()) {
                            isEmpty = false;
                        }
                    });

                    if (isEmpty) {
                        parentRow.remove();
                        renameTableAppendFields();
                    }
                }
            });

            $('.append-list').on('click', '.append-delete', function () {
                var parentRow = $(this).parents('.append-row');
                parentRow.remove();
                renameTableAppendFields();
            });
        }

        $(document).ready(function () {
            renameTableAppendFields();
            initAppendControl();
        });

    </script>
{/literal}
{/script}