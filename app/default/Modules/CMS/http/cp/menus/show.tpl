
{content name="page_header"}{t}Menu{/t} "{$menu.title}"{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/menus"}">{t}Menu management{/t}</a></li>
    <li>{$menu.key}</li>
</ol>
{/content}

<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='menus' display="sidebar"}
    </div>
    <div class="col-md-10">
        <div class="panel panel-default">
            <div class="panel-body">
                {form method="put" action="cp/:cp/menus/:key" key=$menu.key class="form form-horizontal pina-form form-menu"}
                <div class="form-group">
                    <label for="title" class="control-label col-sm-2">{t}Title{/t}</label>
                    <div class="col-sm-10">
                        <input type="text" name="title" value="{$menu.title}" class="form-control" />
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
        {module get="cp/:cp/menus/:key/items" key=$menu.key}
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
        $(".form-menu").on("success", function (event, packet, status, xhr) {
            document.location.reload();
        });
    </script>
{/literal}
{/script}