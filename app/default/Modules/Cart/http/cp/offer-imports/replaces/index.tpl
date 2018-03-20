{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/offers"}">Прайс-лист</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/create"}">Импорт</a></li>
    <li class="active">{$import.file_name}</li>
</ol>
{/content}

{content name="title"}
Замены и формулы
{/content}

{content name="page_header"}{t}Offer import{/t}{/content}

<ul class="nav nav-tabs">
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/offers" import_id=$params.import_id}">{t}Preview{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/schema" import_id=$params.import_id}">{t}Schema{/t}</a></li>
    <li class="active"><a href="{link get="cp/:cp/offer-imports/:import_id/replaces" import_id=$params.import_id}" class="action">{t}Replaces{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/keys" import_id=$params.import_id}">{t}Key fields{/t}</a></li>
</ul>

<div class="panel">
    <div class="panel-body">
        {form action="cp/:cp/offer-imports/:import_id/replaces" import_id=$params.import_id method="put" class="form-horizontal pina-form form-replaces"}
        <table class="table table-flat table-append">
            <thead>
                <tr>
                    <th>Поле</th>
                    <th>Искать</th>
                    <th>Заменить</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$replaces item=replace}
                    <tr>
                        <td data-name="field">
                            {include file="Skin/form-line-select.tpl" list=$header value=$replace.field}
                        </td>
                        <td data-name="search">
                            {include file="Skin/form-line-input.tpl"  value=$replace.search}
                        </td>
                        <td data-name="replace">
                            {include file="Skin/form-line-input.tpl"  value=$replace.replace}
                        </td>
                    </tr>
                {/foreach}
                <tr class="empty">
                    <td data-name="field">
                        {include file="Skin/form-line-select.tpl" list=$header}
                    </td>
                    <td data-name="search">
                        {include file="Skin/form-line-input.tpl"}
                    </td>
                    <td data-name="replace">
                        {include file="Skin/form-line-input.tpl"}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="form-group">
            <div class="col-sm-8">
                <h5>Примечания</h5>

                <h6>Формулы</h6>
                <ul class="small">
                    <li>Для использования формул в поле <b>"Заменить"</b>, начать выражение со знака <b>"="</b></li>
                    <li>Названия полей записываются со знаком <b>"$"</b> в начале</li>
                    <li>Пробелы в составных названиях заменяются на <b>"_"</b></li>
                    <li>В качестве переменных доступны все исходные заголовки из файла, включая пустые автоматически именованные</li>
                    <li>Пример: <b>"=$Доступное_количество + $Остаток + 10 + $_R5C6</b></li>
                </ul>
                <hr>

                <h6>Исходное значение</h6>
                <ul class="small">
                    <li>Для подстановки в заменяемое выражение исходного значения ячейки, в поле <b>"Заменить"</b>
                        прописать <b>"<***>"</b> в месте подстановки.
                    </li>
                    <li>Пример: <b>"http://mail.ru/<***></b></li>
                </ul>
                <hr>

                <h6>Комбинирование значений</h6>
                <ul class="small">
                    <li>Для подстановки в заменяемое выражение значения любого поля, в <b>"Заменить"</b>
                        прописать <b>"<$Название_поля>"</b> в месте подстановки.
                    </li>
                    <li>Пример: <b><$Штрихкод>.<$Артикул></b></li>
                </ul>
                <hr>

                <h6>Параметры замен</h6>
                <ul class="small">
                    <li>Для замены только пустых значений, оставить поле <b>"Искать"</b> пустым</li>
                    <li>Для замены любых значений, в поле <b>"Искать"</b> прописать <b>"***"</b></li>
                    <li>Для замены только непустых значений, в поле <b>"Искать"</b> прописать <b>"???"</b></li>
                </ul>
                <hr>

                <h6>Замены с условиями</h6>
                <ul class="small">
                    <li>Для замены с проверочным условием в поле <b>"Искать"</b> написать выражение: <b>"?:строка?:содержит?:замена"</b></li>
                    <li>Где <b>"строка"</b> - строка или поле для проверки</li>
                    <li>Где <b>"содержит"</b> - подстрока, которая должна содержаться в проверяемой строке</li>
                    <li>Где <b>"замена"</b> - правило или замена, которая будет произведена при успешной проверке условия</li>
                    <li>Если в выражении <b>"строка"</b> прописать <b>"***"</b>, проверочная строка заменится на значение текущего поля</li>
                    <li>Если в выражении <b>"строка"</b> прописать <b>"$Поле"</b>, проверочная строка заменится на значение любого другого поля</li>
                    <li>Пример: <b>?:$Бренд?:Modarola?:123</b> - для строки с брендом "Modarola" заменит "123" на значение из поля "Заменить"</li>
                </ul>
            </div>
        </div>

        <schemaet class="operations">
            <div class="button-bar row">
                <div class="col-sm-6">
                    <button class="btn btn-primary btn-raised">{t}Save and reload data{/t}</button>
                </div>
                <div class="col-sm-6" style="text-align:right;">
                    <button class="btn btn-danger" {action_attributes put="cp/:cp/offer-imports/:import_id" import_id=$params.import_id} data-redirect="{link get="cp/:cp/offer-imports/:import_id" import_id=$params.import_id}">Загрузить данные снова</button>
                </div>
            </div>        
        </schemaet>
        {/form}
    </div>
</div>

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-replaces").on("success", function (event, packet, status, xhr) {
            var parts = document.location.pathname.split('/');
            var path = parts.slice(0, parts.length - 1).join('/');
            document.location = document.location.origin + path + '?changed=' + Math.random();
        });
    </script>
{/literal}
{/script}

{script}
{literal}
    <script type="text/javascript">

        function renameTableAppendFields() {
            var index = 0;
            $('.table-append tbody tr').each(function () {
                $(this).find('input,select').each(function () {
                    var name = $(this).parents('td').attr('data-name');
                    $(this).attr('name', 'replaces[' + index + '][' + name + ']')
                });
                index++;
            });
        }

        function initAppendControl() {
            $('.table-append').on('change', 'input', function () {
                var val = $(this).val();
                var parentRow = $(this).parents('tr');
                if (parentRow.is('.empty')) {
                    var newEmptyRow = parentRow.clone();
                    parentRow.removeClass('empty');
                    $(newEmptyRow).find('input,select').val('');
                    $('.table-append tbody').append(newEmptyRow);
                    renameTableAppendFields();
                } else {
                    var isEmpty = true;
                    $(parentRow).find('input').each(function () {
                        if ($(this).val()) {
                            isEmpty = false;
                        }
                    });

                    if (isEmpty) {
                        parentRow.remove();
                        renameTableAppendFields();
                    }
                }
            });
        }

        $(document).ready(function () {
            renameTableAppendFields();
            initAppendControl();
        });

    </script>
{/literal}
{/script}