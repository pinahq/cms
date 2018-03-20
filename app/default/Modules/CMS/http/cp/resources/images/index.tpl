<link rel="stylesheet" href="/static/default/css/image-control.css">

<fieldset class="image-control" {action_attributes post="cp/:cp/images"}>
    <div class="field">
        <div class="images select-images">
            {if $images}
                {foreach from=$images item=i}
                    {if $params.image_id eq $i.id}
                        {assign var="checked" value=true}
                    {else}
                        {assign var="checked" value=false}
                    {/if}
                    {view get="cp/:cp/images/:id" image=$i checked=$checked}
                {/foreach}                
            {/if}
            <div class="image action-upload-image thumbnail" style="background-color:#fff;cursor: pointer;">
                <a class="image-center" href="#" target="blank"><span>click to add</span></a>
            </div>
        </div>
    </div>
</fieldset>

{script src="/vendor/jquery-file-upload/js/jquery.fileupload.js"}{/script}
{script src="/static/default/js/pina.images.js"}{/script}

{script}
{literal}
<script>
    $(document).ready(function() {
        $("fieldset.image-control").manageImages();
    });
</script>
{/literal}
{/script}