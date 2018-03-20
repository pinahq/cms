{content name="page_header"}
Обращение пользователя {$submission.firstname} {$submission.middlename} {$submission.lastname}
{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/submissions"}">Обращения пользователей</a></li>
    <li class="active">#{$params.id} от {$submission.created|format_date}</li>
</ol>
{/content}

<div class="panel">
    <div class="panel-body">
        {include file="Skin/form-line-static.tpl" title="Время обращения" value=$submission.created|format_datetime}
        {include file="Skin/form-line-static.tpl" title="Тип" value=$submission.type|default:"-"}
        {include file="Skin/form-line-static.tpl" title="Тема" value=$submission.subject|default:"-"}

        {include file="Skin/form-line-static.tpl" title="Фамилия" value=$submission.lastname|default:"-"}
        {include file="Skin/form-line-static.tpl" title="Имя" value=$submission.firstname|default:"-"}
        {include file="Skin/form-line-static.tpl" title="Отчество" value=$submission.middlename|default:"-"}

        {include file="Skin/form-line-static.tpl" title="Email" value=$submission.email|default:"-"}
        {include file="Skin/form-line-static.tpl" title="Телефон" value=$submission.phone|default:"-"}

        {foreach from=$submission.data key=field item=value}
            {include file="Skin/form-line-static.tpl" title=$field value=$value|nl2br}
        {/foreach}

        <div class="button-bar row">
            <div class="col-sm-5 col-sm-offset-2">
            </div>
            <div class="col-sm-5 right" style="text-align: right;">
                <button class="btn btn-danger btn-raised pina-action action-delete"
                        {action_attributes delete="cp/:cp/submissions/:id" id=$submission.id}
                        data-confirm="{t}Are you sure?{/t}"
                        data-message="{t}Information has been deleted{/t}">{t}Delete{/t}</button>
            </div>
        </div>

    </div>
</div>

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script>
        $(".action-delete").on("success", function (event, packet, status, xhr) {
            console.log(event, packet, status, xhr);
            if (!PinaRequest.handleRedirect(xhr)) {
                var parts = document.location.pathname.split('/');
                var path = parts.slice(0, parts.length - 1).join('/');
                document.location = document.location.origin + path + '?changed=' + Math.random();
            }
        });
    </script>
{/literal}
{/script}