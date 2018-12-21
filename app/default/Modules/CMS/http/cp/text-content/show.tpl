<div class="content-form form form-horizontal" {action_attributes put="cp/:cp/text-content/:content_id" content_id=$content.id}>
    <div class="textEditor slot-content-container" id="textEditor">
        {$content.text}
    </div>
    <div id="textEditorToolbar" style="position:fixed;top:0px;z-index:20;"></div>
    <textarea class="textEditorInput" name="text" style="display: none">{$content.text}</textarea>

    {script src="/vendor/tinymce/tinymce.min.js"}{/script}
    {script}
    {literal}
        <script>
            Pina.slotEditor.on('text-content', 'edit', function (currentBlock) {

                $('.content-editor_params', currentBlock).hide();

                var $editor = currentBlock.find('.textEditor');
                $editor.on('keydown', function (e) {
                    if ((e.key && e.key == 'Enter' && e.ctrlKey) || (e.keyCode && e.keyCode == 13 && e.ctrlKey)) {
                        e.preventDefault();

                        tinymce.triggerSave();

                        Pina.slotEditor.saveCurrentContent();
                    }

                    if ((e.key && e.key == 'Escape') || (e.keyCode && e.keyCode == 27)) {
                        e.preventDefault();

                        Pina.slotEditor.cancelCurrentContentChanges();
                    }
                });

                var textEditorInput = currentBlock.find('.textEditorInput');

                tinymce.baseURL = '/vendor/tinymce/';

                tinymce.remove();
                tinymce.init({
                    selector: '.slot-content-editor.on-edit .textEditor',
                    auto_focus: 'textEditor',
                    content_css: [],
                    inline: true,
                    language_url: '/static/vendor/tinymce_lang/ru.js',
                    fixed_toolbar_container: '#textEditorToolbar',
                    menubar: false,
                    plugins: [
                        'advlist autolink lists link hr anchor wordcount code table textcolor codesample'
                    ],
                    toolbar1: 'removeformat | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist table | blockquote codesample | hr link unlink | forecolor backcolor | code',
                    init_instance_callback: function (editor) {
                        editor.on('Change', function (e) {
                            $(textEditorInput).val(editor.getContent());
                        });
                    }
                });

            });
        </script>
    {/literal}
    {/script}

</div>