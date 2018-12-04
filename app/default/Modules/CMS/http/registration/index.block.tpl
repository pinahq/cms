{if !$registered}

    {form class="form form-registration form-dialog form-horizontal pina-form form-utm" action="/registration" method="post" id="form-registration"}

    <fieldset>

        <div class="form-group">
            <label for="email" class="control-label col-sm-4">E-mail</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="email" value="" placeholder="Ваш e-mail" />
            </div>
        </div>

        <div class="form-group">
            <label for="new_password" class="control-label col-sm-4">Пароль</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" name="new_password" value="" />
            </div>
        </div>

        <div class="form-group">
            <label for="new_password2" class="control-label col-sm-4">Повторите пароль</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" name="new_password2" value="" />
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-8 col-sm-offset-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="subscribed" checked="checked" value="Y"> Получать рассылки
                    </label>
                </div>
            </div>
        </div>
        
        <div class="operations form-group">
            <div class="col-sm-8 col-sm-offset-4">
                {composer position="captcha" wrapper="div name=captcha"}
                <button class="btn btn-primary" type="submit">Готово!</button>
            </div>
        </div>

    </fieldset>
    {/form}

{/if}