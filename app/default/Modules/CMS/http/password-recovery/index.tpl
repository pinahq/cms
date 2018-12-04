<h1 class="page-header">Восстановление пароля</h2>

<div class="row">
    <div class="col-sm-6">

        {form class="form form-dialog form-horizontal pina-form form-password-recovery" action="password-recovery" method="post"}

        <div class="form-group request">
            <label class="control-label col-sm-4" for="email">E-mail</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="email" value="" placeholder="Ваш e-mail" />
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-4 col-xs-6">
                <a href="{link get="auth"}" class="auth">Вспомнил пароль &raquo;</a>
            </div>
        </div>

        <div class="form-group operations request">
            <div class="col-sm-8 col-sm-offset-4">
                <button class="btn btn-primary" type="submit">Продолжить</button>
            </div>
        </div>
        <div class="success" style="display:none;">
            <p>Письмо со ссылкой на восстановление пароля выслано вам на почту</p>
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
        $(".form-password-recovery").on("success", function () {
            $(".form-password-recovery .request").hide();
            $(".form-password-recovery .success").show();
        });
    </script>
{/literal}
{/script}
