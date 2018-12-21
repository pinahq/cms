{module get="cp/:cp/resources/:resource_id/images"
        resource_id=$resource.id
        image_id=$resource.image_id}

<fieldset>

    {if $resource_types}
        {module get="cp/:cp/resource-types" display=selector id=$params.resource_type_id}
    {else}
        {include file="Skin/form-line-static.tpl" title="Type"|t name="resource_type" value=$resource.resource_type_title}
    {/if}
    <div class="form-group">
        <label class="control-label col-sm-2">{t}Activity{/t}</label>
        <div class="col-sm-10">
            <div class="togglebutton" style="margin: 1rem 0;">
                <label>
                    <input type="checkbox" name="enabled" value="Y" {if $resource.enabled eq 'Y' || !$resource}checked="checked"{/if} />
                </label>
            </div>
        </div>
    </div>

    {include file="Skin/form-line-input.tpl" title="Title"|t name="title" value=$resource.title}
    {include file="Skin/form-line-textarea.tpl" title="Text"|t name="text" value=$resource.text|escape class="auto-height text-editor"}

    {module get="cp/:cp/resources/:id/parent-selector" display="selector" id=$resource.id parent_id=$resource.parent_id|default:$params.parent_id}

    {module get="cp/:cp/resources/:id/tags" display="selector" id=$resource.id}

</fieldset>

<fieldset>
    {include file="Skin/form-line-input.tpl" title="Meta Title"|t name="meta_title" value=$resource.meta_title}
    {include file="Skin/form-line-input.tpl" title="Meta Description"|t name="meta_description" value=$resource.meta_description}
    {include file="Skin/form-line-input.tpl" title="Meta Keywords"|t name="meta_keywords" value=$resource.meta_keywords}

    {include file="Skin/form-line-input.tpl" title="URL token"|t name="resource" value=$resource.resource}
</fieldset>

{script src="/vendor/tinymce/tinymce.min.js"}{/script}
{script}
{literal}
    <script>
       $(document).ready(function () {
           
            $(".form-resource").on("submit", function (event, packet, status, xhr) {
                if (tinymce) {
                    for (var i in tinymce.editors) {
                        tinymce.editors[0].save();
                    }
                }
            });
           
            tinymce.baseURL = '/vendor/tinymce/';

            tinymce.remove();
            tinymce.init({
                selector: 'textarea',
                content_css: [],
                language_url: '/static/vendor/tinymce_lang/ru.js',
                menubar: false,
                plugins: [
                    'advlist autolink lists link hr anchor wordcount code table textcolor codesample'
                ],
                toolbar1: 'removeformat | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist table',
                toolbar2: 'blockquote codesample | hr link unlink | forecolor backcolor | code',
                statusbar: false
            });
            
        });
    </script>
{/literal}
{/script}