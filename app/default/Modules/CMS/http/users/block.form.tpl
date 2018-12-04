{form class="form form-horizontal pina-form" action="/users/:user_id" id=$user.id method="put" id="form-user"}

<fieldset>
    {include file="Skin/form-line-static.tpl" id="" name="email" title="E-mail" value=$user.email labelColumn=4}

    {include file="Skin/form-line-input.tpl" id="" name="firstname" title="Имя" value=$user.firstname labelColumn=4}

    {include file="Skin/form-line-input.tpl" id="" name="lastname" title="Фамилия" value=$user.lastname labelColumn=4}

    {include file="Skin/form-line-input.tpl" id="" name="phone" title="Телефон" value=$user.phone labelColumn=4}

    <div class="form-group">
        <div class="col-sm-8 col-sm-offset-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="subscribed"{if $user.subscribed eq 'Y'} checked="checked"{/if} value="Y"> Получать рассылки
                </label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-8 col-sm-offset-4">
            <h3>Сменить пароль</h3>
        </div>
    </div>


    {include file="Skin/form-line-input.tpl" id="" type="password" name="new_password" title="Пароль" value="" labelColumn=4}

    {include file="Skin/form-line-input.tpl" id="" type="password" name="new_password2" title="Повторите пароль" value="" labelColumn=4}

</fieldset>

<center>
    <button class="btn btn-primary" type="submit">Обновить</button>
</center>
</fieldset>

{/form}

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $("#form-user").on("success", function () {
            document.location = document.location.origin + document.location.pathname + '?changed=' + Math.random();
        });
    </script>
{/literal}
{/script}
