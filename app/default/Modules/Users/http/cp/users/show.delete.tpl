{capture name="user_title"}
    {if $user.firstname || $user.lastname}
        {$user.firstname} {$user.lastname}
        ({$user.email})
    {else}
        {$user.email}
    {/if}
{/capture}
{content name="page_header"}{t}Delete user{/t}{/content}
{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/users"}">{t}Users{/t}</a></li>
    <li><a href="{link get="cp/:cp/users/:id" id=$user.id}">{$smarty.capture.user_title}</a></li>
    <li class="active">{t}Delete user{/t}</li>
</ol>
{/content}

<div class="panel">
    <div class="panel-heading">
        <h2>{t}Are you sure to delete the user?{/t}</h2>
    </div>
    <div class="panel-body">
        {form action="/cp/:cp/users/:id" id=$user.id method="delete" name="" class="form form-horizontal form-delete pina-form"}
        
        

        <div class="button-bar row">
            <div class="col-sm-6">
                <button class="btn btn-danger btn-raised">{t}Delete user{/t} '{$smarty.capture.user_title}'</button>
            </div>
            <div class="col-sm-6 right" style="text-align: right;">
                <a href="{link}" class="btn btn-default btn-raised">{t}Cancel{/t}</a>
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
        $(".form-delete").on("success", function (event, packet, status, xhr) {
            if (!PinaRequest.handleRedirect(xhr)) {
                var parts = document.location.pathname.split('/');
                var path = parts.slice(0, parts.length - 1).join('/');
                document.location = document.location.origin + path + '?changed=' + Math.random();
            }
        });
    </script>
{/literal}
{/script}