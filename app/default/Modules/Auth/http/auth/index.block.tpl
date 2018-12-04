{if !$user}

    {form class="form form-auth form-dialog form-horizontal pina-form" action="/auth" method="post" id="auth_form"}

    <fieldset>

        <div class="form-group">
            <label class="control-label col-sm-4" for="email">E-mail</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="email" value="" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="password">Пароль</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" name="password" value="" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-4 col-xs-6">
                <a href="{link get="password-recovery"}" class="link">Забыл&nbsp;пароль&nbsp;&raquo;</a>
            </div>
            <div class="col-sm-4 col-xs-6" style="text-align: right;">
                <a href="{link get="registration"}" class="link">Зарегистрироваться&nbsp;&raquo;</a>
            </div>
        </div>

        <div class="operations form-group">
            <div class="col-sm-8 col-sm-offset-4">
                <button class="btn btn-primary" type="submit">Войти</button>
            </div>
        </div>

    </fieldset>
    {/form}

    {script src="/vendor/jquery.form.js"}{/script}
    {script src="/static/default/js/pina.skin.js"}{/script}
    {script src="/static/default/js/pina.request.js"}{/script}
    {script src="/static/default/js/pina.form.js"}{/script}

    {script}
    {literal}
        <script>
            $("#auth_form").on("success", function () {
                document.location.reload();
            });
        </script>
    {/literal}
    {/script}

{/if}