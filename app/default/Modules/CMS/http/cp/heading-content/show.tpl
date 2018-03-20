<div class="content-form form form-horizontal" {action_attributes put="cp/:cp/heading-content/:content_id" content_id=$content.id}>
    <div class="slot-content-container">
        {view get="heading-content/:content_id" content_id=$content.id content=$content}

    </div>

    <input id="titleEditorInput{$content.id|default:0}" type="hidden" name="title" value="{$content.text|default:"Введите текст..."}">

    <div class="content-params">
        <h2 class="params-header">Параметры<br>заголовка</h2>
        <div>
            <fieldset>
                <div class="field">
                    {view get="cp/:cp/heading-content/block" display=size param=$content.params.h|default:"h2"}
                </div>
            </fieldset>
        </div>
    </div>
</div>
{script}
{literal}
    <script>
        Pina.slotEditor.on('heading-content', 'edit', function (currentBlock) {
            var id = currentBlock.attr('data-id');
            var $editor = $(currentBlock).find('.content-editable .slot-content-container > *');
            var $input = $('#titleEditorInput' + id);

            var initEditor = function ($editor) {
                $editor.attr('contenteditable', 'true');
                $editor.css('word-wrap', 'normal');

                $editor.on('input', function () {
                    var val = $(this).text();
                    $input.val(val.trim());
                });

                $editor.on('keydown', function (e) {
                    if ((e.key && e.key == 'Enter') || (e.keyCode && e.keyCode == 13)) {
                        e.preventDefault();

                        Pina.slotEditor.saveCurrentContent();
                    }

                    if ((e.key && e.key == 'Escape') || (e.keyCode && e.keyCode == 27)) {
                        e.preventDefault();

                        Pina.slotEditor.cancelCurrentContentChanges();
                    }
                });
            }
            initEditor($editor);

            $editor.focus();

            currentBlock.find('.content-params')
                .find('select')
                .on('change', function () {
                    var name = $(this).attr('name');
                    var value = $(this).val();

                    var container = $(currentBlock).find('.content-editable .slot-content-container');

                    if (name == 'h') {
                        $editor.replaceWith('<' + value + '>' + $editor.text() + '</' + value + '>');
                        $editor = $(currentBlock).find('.content-editable .slot-content-container > *');
                        initEditor($editor);
                    }
                });
        });

    </script>
{/literal}
{/script}