{if $user}

    <h1 class="page-header">Добро пожаловать на сайт</h1>
    {composer position="auth::authorized"}

    {script}
    {literal}
        <script>
            document.location = '/';
        </script>
    {/literal}
    {/script}

{else}
    {content name="body_class"}auth{/content}



    <div class="row">
        <div class="col-sm-8 col-lg-6">

            <h1 class="page-header">Войти</h1>
            
            {if $smarty.get.from eq "recovery"}
                <div class="alert alert-success" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <!--span class="sr-only">Error:</span-->
                    Пароль был успешно изменен. Воспользуйтесь новым паролем, чтобы зайти на сайт.
                </div>
            {/if}

            {view get="auth" display=block}

        </div>

    </div>
{/if}