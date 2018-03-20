
{content name="page_header"}{$title}{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/resource-types"}">{t}Resource types{/t}</a></li>
    <li>{$title}</li>
</ol>
{/content}


<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='resource-types' display="sidebar"}
    </div>
    <div class="col-md-10">
        <div class="panel">
            <div class="panel-body">
                {form method="put" action="cp/:cp/resource-types/:id" id=$id class="form form-horizontal pina-form form-type"}

                <div class="form-group">
                    <label for="type" class="control-label col-sm-2">{t}Type{/t}</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{$type}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="control-label col-sm-2">{t}Title{/t}</label>
                    <div class="col-sm-10">
                        <input type="text" name="title" value="{$title}" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="pattern" class="control-label col-sm-2">{t}Pattern{/t}</label>
                    <div class="col-sm-10">
                        <input type="text" name="pattern" value="{$pattern}" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="cp_pattern" class="control-label col-sm-2">{t}Control panel pattern{/t}</label>
                    <div class="col-sm-10">
                        <input type="text" name="cp_pattern" value="{$cp_pattern}" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <p>Pattern rules:</p>
                        <ul>
                            {literal}
                                <li>*** - Resource title</li>
                                <li>{Tag title} - Tag 'Tag title'</li>
                                {/literal}
                        </ul>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
                    </div>
                </div>

                {/form}

            </div>
        </div>
    </div>
</div>

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script>
        $(".form-type").on("success", function (event, packet, status, xhr) {
            PinaRequest.handleRedirect(xhr);
        });
    </script>
{/literal}
{/script}