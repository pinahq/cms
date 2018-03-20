<div class="panel">
    <div class="panel-heading">
        <h2>{$params.title}</h2>
    </div>
    <div class="panel-body">

        <div class="form-group">

            <label class="control-label col-sm-4">Изображение</label>

            <div class="image-control col-sm-6" {action_attributes post="cp/:cp/images"}>
                <div class="images select-images action-upload-image">
                    {if $params.item.image_id}
                        {view get="cp/:cp/catalog-matrix-content/0/images/:image_id" image=$item}
                    {else}
                    <div class="image thumbnail" style="background-color:#fff;cursor: pointer;">
                        <input type="hidden" name="image_id[]" value="0" />
                        <a class="image-center" href="#" target="blank"><span>{t}Click to upload{/t}</span></a>
                    </div>
                    {/if}
                </div>
            </div>

        </div>

        {include file="Skin/form-line-input.tpl" title="Заголовок" name="title[]" value=$item.title}
        {include file="Skin/form-line-input.tpl" title="Кнопка" name="button[]" value=$item.button}
        {include file="Skin/form-line-input.tpl" title="Ссылка" name="url[]" value=$item.url}
        
        {view get="cp/:cp/catalog-matrix-content/0/tags"
                    display="selector"
                    title="Теги товаров"
                    value=$item.tags
                    resource_id=$resource.resource_id}
    </div>
</div>