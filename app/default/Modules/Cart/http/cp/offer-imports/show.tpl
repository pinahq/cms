{content name="page_header"}Импорт товаров{/content}

<p>Запущен: {$import.created|format_datetime}</p>
{if $import.status eq 'read'}
    
    <p>Обработано: <span class="last-row">{$import.last_row}</span></p>
    {script}
    {literal}
        <script>
            setTimeout(function () {
                document.location.reload();
            }, 2000);
        </script>
    {/literal}
    {/script}
{else}
    <a class="btn btn-primary load-now" href="{link get="cp/:cp/offer-imports/:import_id/offers" import_id=$import.id}">
        Предпросмотр товаров
    </a>
    {script}
    {literal}
        <script>
            document.location = $(".load-now").attr('href');
        </script>
    {/literal}
    {/script}
{/if}
