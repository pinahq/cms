{extends layout="email"}
{content name="mail_subject"}{$submission.subject|default:"Новое обращение"}{/content}
Здравствуйте!

Время обращения: {$submission.created}
Тип: {$submission.type}
Тема: {$submission.subject|default:"-"}

От: {if $submission.firstname || $submission.middlename || $submission.lastname}{$submission.firstname} {$submission.middlename} {$submission.lastname}{else}-{/if}

{if $submission.email}
Email: {$submission.email}
{/if}
    
{if $submission.phone}
Телефон: {$submission.phone}
{/if}

{if $submission.data}
Другая информация:
{$submission.data}
{/if}