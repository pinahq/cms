{content name="page_header"}Панель управления{/content}

<div class="row dashboard">
    <div class="col-sm-4">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-title"><h2>Структура сайта</h2></div>
            </div>
            <div class="panel-body">
                <p>
                    Сайт состоит из страниц. Страница может быть корневой или вложена в другую страницу. 
                    Таким образом образуется древовидная структура, напоминающая папки на вашем компьютере
                </p>
            </div>
            <div class="panel-footer">
                <a href="{link get="cp/:cp/resources"}" class="btn btn-raised btn-primary">Обзор</a>
                <a href="{link get="cp/:cp/resources/create"}" class="btn btn-raised btn-primary">Создать</a>
            </div>
        </div>
    </div>
    {composer position="dashboard.row"}
</div>

<center><a href="{link get="/"}" class="btn btn-raised btn-primary btn-lg btn-warning">На сайт</a></center>

{script}
{literal}
    <script>
        function justifyHeight(e, minWidth) {
            if (minWidth && $(window).outerWidth() < minWidth) {
                $(e).outerHeight("auto");
                return
            }
            var max = -1;
            $(e).each(function () {
                $(this).outerHeight("auto");
                if ($(this).outerHeight() > max) {
                    max = $(this).outerHeight()
                }
            });
            $(e).outerHeight(max);

            return max;
        }

        $(window).load(function () {
            justifyHeight(".dashboard .panel-body", 751);
        });
        $(window).resize(function () {
            justifyHeight(".dashboard .panel-body", 751);
        })
    </script>
{/literal}
{/script}