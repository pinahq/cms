{content name="page_header"}Добавить страницу{/content}

<div class="panel">
    {form action="cp/:cp/resources" method="post" class="form form-horizontal pina-form form-resource"}

    <fieldset>
        {view get="cp/:cp/resources/block" parent_id=$params.parent_id resource_type_id=$params.resource_type_id display="form"}



        <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
                <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
            </div>
        </div>

    </fieldset>
    {/form}
</div>

{script src="/static/default/js/pina.textarea-autosize.js"}{/script}


{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-resource").on("success", function (event, packet, status, xhr) {
            PinaRequest.handleRedirect(xhr);
        });
    </script>
{/literal}
{/script}