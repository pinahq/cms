<div class="form form-dialog form-horizontal">
    <fieldset>
        {include file="Skin/form-line-static.tpl" name="email" title="E-mail" value=$user.email labelColumn=4}

        {include file="Skin/form-line-static.tpl" name="lastname" title="Имя" value=$user.lastname labelColumn=4}

        {include file="Skin/form-line-static.tpl" name="firstname" title="Фамилия" value=$user.firstname labelColumn=4}

        {include file="Skin/form-line-static.tpl" name="phone" title="Телефон" value=$user.phone labelColumn=4}

        {include file="Skin/form-line-static.tpl" name="subscribed" title="Рассылки" value=$user.subscribed|replace:"Y":"Да"|replace:'N':'Нет' labelColumn=4}

        <center>
            <a class="btn btn-primary" href="{link get="users/:user_id" display=edit}">Поменять</a>
        </center>
    </fieldset>
</div>