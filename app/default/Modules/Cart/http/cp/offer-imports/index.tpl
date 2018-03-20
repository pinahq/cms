{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/offers"}">Прайс-лист</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/create"}">Импорт</a></li>
    <li>История</li>
</ol>
{/content}

{content name="title"}
    Файл импорта
{/content}

{content name="page_header"}Импорт товаров{/content}

{if $params.from eq 'deleted'}
    <div class="alert alert-success" role="alert">Импорт был отменен</div>  
{/if}

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation"><a href="{link get="cp/:cp/offer-imports/create"}">Файл импорта</a></li>
    <li role="presentation" class="active"><a href="{link get="cp/:cp/offer-imports"}">История импорта</a></li>
</ul>

<table class="table table-styled">
    <thead>
        <tr>
            <th>Дата</th>
            <th>Название файла</th>
            <th>Статус</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$imports item=import}
            {if $import.file_name}
            <tr>
                <td>
                    {$import.created}
                </td>
                <td>
                    {$import.file_name|default:'&nbsp;'}
                </td>
                <td>
                    {$import.status}
                </td>
                <td>
                    <a href="{link get="cp/:cp/offer-imports/create" import_id=$import.id}">Повторить импорт</a>
                </td>
                <td>
                    {if $import.status eq 'confirm'}
                        <a href="{link get="cp/:cp/offer-imports/:import_id/offers" import_id=$import.id}">Закончить импорт</a>
                    {elseif $import.status eq 'import'}
                        <a href="{link get="cp/:cp/offer-imports/:import_id/results" import_id=$import.id}">Процесс импорта</a>
                    {/if}
                </td>
            </tr>
            {/if}
        {/foreach}
    </tbody>
</table>