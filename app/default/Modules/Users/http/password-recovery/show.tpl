<h1 class="page-header">Восстановление пароля</h1>

{form class="form form-dialog form-horizontal pina-form form-password-recovery" action="password-recovery/:token" token=$token method="delete"}    

<div class="form-group">
    <label  class="control-label col-sm-4" for="new_password">Новый пароль</label>
    <div class="col-sm-8">
        <input type="password" class="form-control" name="new_password" value="" />
    </div>
</div>

<div class="form-group">
    <label  class="control-label col-sm-4" for="new_password2">Повторите пароль</label>
    <div class="col-sm-8">
        <input type="password" class="form-control" name="new_password2" value="" />
    </div>
</div>
<div class="form-group operations request">
    <div class="col-sm-8 col-sm-offset-4">
        <button class="btn btn-primary" type="submit">Поменять</button>
    </div>
</div>
{/form}

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-password-recovery").on("success", function (event, packet, status, xhr) {
            if (!PinaRequest.handleRedirect(xhr)) {
                document.location = '/auth';
            }
        });
    </script>
{/literal}
{/script}
