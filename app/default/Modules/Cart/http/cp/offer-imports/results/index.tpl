{content name="breadcrumbs"}
    <li>Импорт товаров</li>
    <li><a href="{link get="cp/:cp/offer-imports"}">Файл импорта</a></li>
    <li class="active">Результаты импорта</li>
{/content}
    
{content name="title"}
Результаты импорта
{/content}

{content name="page_header"}Результаты импорта{/content}

<fielset>
    <table class="table">
        <tr>
            <td>Статус</td>
            <td>{$import.status}</td>
        </tr>
        <tr>
            <td>Добавлено страниц</td>
            <td>{$added}</td>
        </tr>
        <tr>
            <td>Обновлено страниц</td>
            <td>{$updated}</td>
        </tr>
        <tr>
            <td>Пропущено страниц</td>
            <td>{$skipped}</td>
        </tr>
        <tr>
            <td>Товарных предложений</td>
            <td>{$offers}</td>
        </tr>
    </table>
</fielset>

{if $import.status eq 'done'}
    <fieldset class="stats">
        <div class="import_stats" style="max-height: 200px; overflow: auto; margin-bottom: 20px">
            <ol style="font-size: 10px">
                {foreach from=$skippedStats item=message}
                    <li>{$message}</li>
                {/foreach}
            </ol>
        </div>
    </fieldset>
{/if}
        
<fieldset class="operations">
    <div class="button-bar row">
        <div class="col-sm-12">
            <a class="btn btn-primary js-save" href="{link get="/cp/:cp/offer-imports/create"}">Еще один импорт</a>
            <a class="btn btn-primary js-save" href="{link get="/cp/:cp/offer-imports"}">История импорта</a>
        </div>
    </div>
</fieldset>



{if $import.status ne 'done'}
{script}
{literal}
<script>
    setTimeout(function() {
        document.location.reload();
    }, 2000);
</script>
{/literal}
{/script}
{/if}