{capture name="user_title"}
    {if $user.firstname || $user.lastname}
        {$user.firstname} {$user.lastname}
        ({$user.email})
    {else}
        {$user.email}
    {/if}
{/capture}
{content name="page_header"}Пользователь {$smarty.capture.user_title}{/content}
{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/users"}">{t}Users{/t}</a></li>
    <li class="active">{$smarty.capture.user_title}</li>
</ol>
{/content}

<div class="panel">
    <div class="panel-heading">
        <h2>Профиль</h2>
    </div>
    <div class="panel-body">
        {form action="/cp/:cp/users/:id" id=$user.id method="put" name="" class="form form-horizontal form-user pina-form"}
        {view get="cp/:cp/users/:id/block" display="form" user=$user}
        
        {module get="cp/:cp/users/:id/tags" display="selector" id=$user.id}
        
        {view get="cp/:cp/users/:id/block" display="utm" user=$user}

        <div class="button-bar row">
            <div class="col-sm-5 col-sm-offset-2">
                <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
            </div>
            <div class="col-sm-5 right" style="text-align: right;">
                <a href="{link display=delete}" class="btn btn-danger btn-raised">{t}Delete{/t}</a>
            </div>
        </div>

        {/form}
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <h2>Сменить пароль</h2>
    </div>
    <div class="panel-body">
        {form action="/cp/:cp/users/:id" id=$user.id method="put" name="" class="form form-horizontal form-user pina-form"}
        {view get="cp/:cp/users/:id/block" display="form-password" user=$user}
        <div class="button-bar row">
            <div class="col-sm-5 col-sm-offset-2">
                <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
            </div>
            <div class="col-sm-5 right" style="text-align: right;">
                <button class="btn btn-default btn-raised pina-action" {action_attributes post="cp/:cp/users/:id/login" id=$user.id}>{t}Login as this user{/t}</button>
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
                document.location.reload();
            }
        });
    </script>
{/literal}
{/script}