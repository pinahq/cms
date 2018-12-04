{content name="page_header"}{t}New user{/t}{/content}
{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/users"}">{t}Users{/t}</a></li>
    <li class="active">{t}New user{/t}</li>
</ol>
{/content}


<div class="panel">
    <div class="panel-heading">
        <h2>Профиль</h2>
    </div>
    <div class="panel-body">
        {form action="/cp/:cp/users" method="post" name="" class="form form-horizontal form-user pina-form"}
        {view get="cp/:cp/users/:id/block" display="form"}

        <div class="button-bar row">
            <div class="col-sm-5 col-sm-offset-2">
                <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
            </div>
        </div>

        {/form}
    </div>
</div>

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-user").on("success", function (event, packet, status, xhr) {
            if (!PinaRequest.handleRedirect(xhr)) {
                var parts = document.location.pathname.split('/');
                var path = parts.slice(0, parts.length - 1).join('/');
                document.location = document.location.origin + path + '?changed=' + Math.random();
            }
        });
    </script>
{/literal}
{/script}