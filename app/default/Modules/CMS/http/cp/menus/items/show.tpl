
{content name="page_header"}{t}Menu item{/t} "{$menu_item.title}"{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/menus"}">{t}Menu management{/t}</a></li>
    <li><a href="{link get="cp/:cp/menus/:key" key=$params.key}">{$params.key}</a></li>
    <li>{$menu_item.title}</li>
</ol>
{/content}

<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='menus' display="sidebar"}
    </div>
    <div class="col-md-10">
        <div class="panel panel-default">
            <div class="panel-body">
                {form method="put" action="cp/:cp/menus/:key/items/:id" key=$params.key id=$menu_item.id class="form form-horizontal pina-form form-menu-item"}
                {view get="/cp/:cp/menus/:key/items/block" display="form"}
                <div class="form-group">
                    <div class="col-sm-5 col-sm-offset-2 col-xs-6">
                        <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
                    </div>
                    <div class="col-sm-5 col-xs-6" style="text-align: right;">
                        <a class="btn btn-danger btn-raised pina-action action-delete" href="#" 
                           {action_attributes delete="cp/:cp/menus/:key/items/:id" key=$params.key id=$menu_item.id}>
                            {t}Delete{/t}
                        </a>
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
        $(".form-menu-item,.action-delete").on("success", function (event, packet, status, xhr) {
            var parts = document.location.pathname.split('/');
            var path = parts.slice(0, parts.length - 2).join('/');
            document.location = document.location.origin + path + '?changed=' + Math.random();
        });
    </script>
{/literal}
{/script}