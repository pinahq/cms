<h4>Новый комментарий</h4>
{form method="post" action="resources/:resource_id/comments" resource_id=$params.resource_id class="form pina-form form-comment"}
    {include file="Skin/form-line-textarea.tpl" title="Text"|t name="text" value="" class="auto-height"}
    
    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
        </div>
    </div>
{/form}

{script src="/static/default/js/pina.textarea-autosize.js"}{/script}

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script>
        $(".form-comment").on("success", function (event, packet, status, xhr) {
            if (!PinaRequest.handleRedirect(xhr)) {
                document.location.reload();
            }
        });
    </script>
{/literal}
{/script}
