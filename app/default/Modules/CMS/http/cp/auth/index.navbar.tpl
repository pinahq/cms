{if $user}
    <li><a href="{link get="cp/:cp/users/:user_id" user_id=$user.id}"><i class="material-icons">person</i></a></li>
    <li><a href="#" class="pina-action action-logout" {action_attributes delete="auth"}><i class="material-icons">exit_to_app</i></a></li>
{else}
    <li class="enter"><a href="{link get="auth"}">Вход</a></li>
{/if}

{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script type="text/javascript">
        $('.action-logout').on('success', function () {
            document.location = '/';
        });
    </script>
{/literal}
{/script}