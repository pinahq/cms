{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/offers"}">Прайс-лист</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/create"}">Импорт</a></li>
    <li class="active">{$import.file_name}</li>
</ol>
{/content}

{content name="title"}
Предпросмотр
{/content}

{content name="page_header"}Offer import{/content}

<ul class="nav nav-tabs">
    <li class="active"><a href="{link get="cp/:cp/offer-imports/:import_id/offers" import_id=$params.import_id}">{t}Preview{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/schema" import_id=$params.import_id}">{t}Schema{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/replaces" import_id=$params.import_id}" class="action">{t}Replaces{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/keys" import_id=$params.import_id}">{t}Key fields{/t}</a></li>
    <li><a href="{link get="cp/:cp/offer-imports/:import_id/settings" import_id=$params.import_id}">{t}Settings{/t}</a></li>
</ul>

<ul class="catalog-allrow-manage-icon" style="position:absolute;margin:84px 0px 0px -60px;">
    <li><a href="#" class="last remove"><i class="fa fa-times"></i></a></li>
</ul>

<ul class="nav nav-pills">
    <li><a>Отображать:</a></li>
    <li{if !$params.filter} class="active"{/if}><a href="{link get="cp/:cp/offer-imports/:import_id/offers/"  import_id=$params.import_id}">Все</a></li>
    <li{if $params.filter eq "errors"} class="active"{/if}><a href="{link get="cp/:cp/offer-imports/:import_id/offers/"  import_id=$params.import_id filter="errors"}">С ошибками</a></li>
</ul>

<div class="panel">
    <div class="panel-body">
        <div style="width:100%;overflow-x: scroll;">
            <table class="table table-hover table-data-truncated">
                <thead>
                    <tr>
                        <th rowspan="2" colspan="2">ROW #</th>
                            {foreach from=$header name=cells item=item}
                            <th>
                                {$item}
                            </th>
                        {/foreach}
                    </tr>
                    <tr>
                        {foreach from=$schema name=cells item=item}
                            <th>
                                {$item|nl2br}
                            </th>
                        {/foreach}
                    </tr>
                </thead>

                <tbody>
                    {if $rows}
                        {foreach from=$rows item=row}
                            <tr {if $row.error_cells} class="danger"{/if}>
                                <td>
                                    {$row.row}
                                </td>
                                <td>
                                    <a href="#" class="pina-action js-delete-row" {action_attributes delete="cp/:cp/offer-imports/:import_id/offers/:row" import_id=$params.import_id row=$row.row}>
                                        <i class="material-icons">delete</i>
                                    </a>
                                </td>

                                {foreach from=$row.preview_cells name=cells key=key item=cell}
                                    <td class="{if $row.error_cells.$key}error{/if}"
                                        style="{if $row.error_cells.$key}cursor: help; color: red; font-weight: bold;{/if} {if not $schema.$key}opacity: 0.2;{/if}"
                                        {if $row.error_cells.$key}title="{$row.error_cells.$key}"{/if}
                                        >
                                        {if $cell}
                                            {$cell}
                                        {elseif $row.error_cells.$key}
                                            <i>{$row.error_cells.$key}</i>
                                        {/if}
                                    </td>
                                {/foreach}
                            </tr>
                        {/foreach}
                    {else}
                        <tr><td></td><td colspan="{$smarty.foreach.cells.index+2}"><center>{t}Not found{/t}</center></td></tr>
                    {/if}
                </tbody>
            </table>
        </div>

        {if $params.filter}
            {assign var="add_pagination" value="filter="|cat:$params.filter}
        {/if}

        {link_context filter=$params.filter}
        {include file="Skin/paging.tpl"}
        {/link_context}

        <div class="button-bar row">
            <div class="col-sm-4">
                <a href="#" {action_attributes delete="cp/:cp/offer-imports/:import_id" import_id=$params.import_id} class="btn btn-danger btn-raised pina-action js-cancel">
                    {t}Cancel{/t}
                </a>
            </div>
            <div class="col-sm-4" style="text-align:center;">
                <a href="#" {action_attributes put="cp/:cp/offer-imports/:import_id" import_id=$params.import_id} class="btn btn-default btn-raised pina-action js-reload">{t}Reload data{/t}</a>
            </div>
            <div class="col-sm-4" style="text-align:right;">
                <a href="{link get="cp/:cp/offer-imports/:import_id/keys" import_id=$params.import_id}" class="btn btn-primary btn-raised">{t}Proceed{/t}</a>
            </div>
        </div>

    </div>
</div>

{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script type="text/javascript">
        $('.js-delete-row').on('success', function () {
            document.location.reload();
        });
        
        $('.js-cancel').on('success', function () {
            var parts = document.location.pathname.split('/');
            var path = parts.slice(0, parts.length - 2).join('/');
            document.location = document.location.origin + path + '/create?changed=' + Math.random();
        });
        
        $('.js-reload').on('success', function () {
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
        $('.table-data-truncated td,.table-data-truncated th').on('click', function () {
            $(this).toggleClass('unfolded');
        });


        function getSelectedIds() {
            var ids = [];

            $("#table-product-clone .check").each(function () {
                if ($(this)[0].checked) {
                    ids.push($(this).val());
                }
            });

            return ids;
        }

        $(document).on("click", "#table-product-clone .check-all", function () {
            var checkAll = $(this);
            var dataTarget = checkAll.attr('data-target');
            var set = $('#table-product-clone .' + dataTarget);

            var checked = checkAll.is(":checked");
            set.each(function () {
                $(this)[0].checked = checked;
            });

            if (checked) {
                $(".catalog-allrow-manage-icon").show();
            } else {
                $(".catalog-allrow-manage-icon").hide();
            }
        });

        $(document).on("change", "#table-product-clone .check", function () {
            var checked = $(this).is(":checked");
            var issetChecked = false;
            $(".check").each(function () {
                if ($(this)[0].checked) {
                    issetChecked = true;
                }
            });

            if (issetChecked) {
                $(".catalog-allrow-manage-icon").show();
            } else {
                $(".catalog-allrow-manage-icon").hide();
            }
        });

        $(".remove").on("click", function () {

            var ids = getSelectedIds();

            $(this).confirmDeleteMessage(function () {

                Pina.ajax({
                    type: 'delete',
                    url: "accounts/{/literal}{$params.account_id}{literal}/imports/{/literal}{$params.import_id}{literal}/offers/",
                    data: {
                        ids: ids
                    },
                    success: function (data, status, jqXHR) {
                        if (PinaRequest.handle(".product-list", data)) {
                            if (data && data.e) {
                                var errors = []
                                for (i = 0; i < data.e.length; i++) {
                                    errors.push(data.e[i].m);
                                }

                                if (errors.length > 0) {
                                    alert(errors.join('\n'));
                                }
                            } else {
                                location.reload();
                            }
                        };
                    },
                    error: function (xhr) {
                        var packet = eval('(' + xhr.responseText + ')');
                        Pina.table.handleError(packet);
                    },
                    dataType: 'json'
                });

            });
        });

    </script>
{/literal}
{/script}