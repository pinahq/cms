{extends layout="email"}
{content name="mail_subject"}Ссылка на восстановление пароля{/content}
Здравствуйте!

Вы или кто-то от вашего имени запросил смену пароля. Если вы не отправляли эту заявку, просто проигнорируйте это письмо.

Ссылка для восстановления пароля: {link get="password-recovery/:token" token=$params.token}