{view get="cp/:cp/resources/:resource_id" resource_type_id=$resource.resource_type_id display=header}

{place name="tabs"}
<div class="panel">
    <div class="panel-body tab-content">
        <div role="tabpanel" class="tab-pane active" id="details">
            {form method="put" action="cp/:cp/resources/:id" id=$resource.id class="form form-horizontal pina-form form-resource"}

            {view get="cp/:cp/resources/block" display="form"}

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <div style="float:right;">
                        {if !$resource.child_count}
                            <a class="btn btn-danger btn-raised pina-action action-delete" 
                               href="{link get="cp/:cp/resources/:id" id=$resource.id display=delete}">{t}Delete{/t}</a>
                        {/if}
                        {view get="cp/:cp/:type/:id/copy/block" type=$resource.resource_type id=$resource.id display=button fallback="cp/:cp/resources/:id/copy/block"}
                    </div>
                    <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
                </div>
            </div>

            {/form}
        </div>

        <div role="tabpanel" class="tab-pane" id="tags">
            {module get="cp/:cp/resource-types/:resource_type_id/tag-types" resource_type_id=$resource.resource_type_id}
        </div>
    </div>
</div>

{script src="/static/default/js/pina.textarea-autosize.js"}{/script}

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script>
        $(".form-resource").on("success", function (event, packet, status, xhr) {
            PinaRequest.handleRedirect(xhr);
        });
        $(".action-copy").on("success", function (event, packet, status, xhr) {
            PinaRequest.handleRedirect(xhr);
        });
    </script>
{/literal}
{/script}