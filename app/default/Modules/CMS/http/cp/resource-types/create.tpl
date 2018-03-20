{content name="page_header"}Добавить тип ресурса{/content}

<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='resource-types' display="sidebar"}
    </div>
    <div class="col-md-10">
        <div class="panel">
            {form action="cp/:cp/resource-types" method="post" class="form form-horizontal pina-form form-resource-type"}

            <fieldset>

                {include file="Skin/form-line-input.tpl" title="Title"|t name="title"}
                {include file="Skin/form-line-input.tpl" title="Type"|t name="type"}

                <div class="form-group">
                    <label class="control-label col-sm-2">{t}Tree-type{/t}</label>
                    <div class="col-sm-10">
                        <div class="togglebutton" style="margin: 1rem 0;">
                            <label>
                                <input type="checkbox" name="tree" value="Y" />
                            </label>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
                    </div>
                </div>

            </fieldset>
            {/form}
        </div>
    </div>
</div>

{script src="/static/default/js/pina.textarea-autosize.js"}{/script}


{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-resource-type").on("success", function (event, packet, status, xhr) {
            if (!PinaRequest.handleRedirect(xhr)) {
                var parts = document.location.pathname.split('/');
                var path = parts.slice(0, parts.length - 1).join('/');
                document.location = document.location.origin + path + '?changed=' + Math.random();
            }
        });
    </script>
{/literal}
{/script}