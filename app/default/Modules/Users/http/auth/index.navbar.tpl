{if $user}
    <li><a href="{link get="/users/:id" id=$user.id}">{$user.firstname|default:$user.email}</a></li>
{else}
    <li><a href="{link get="auth"}">Вход</a></li>
    <li><a href="{link get="registration"}">Регистрация</a></li>
{/if}
