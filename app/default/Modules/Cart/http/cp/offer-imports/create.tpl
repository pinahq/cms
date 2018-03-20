{content name="page_header"}{t}Offer import{/t}{/content}
{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/offers"}">Прайс-лист</a></li>
    <li>Импорт</li>
</ol>
{/content}


<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="{link get="cp/:cp/offer-imports/create"}">{t}File{/t}</a></li>
    <li role="presentation"><a href="{link get="cp/:cp/offer-imports"}">{t}History{/t}</a></li>
</ul>

<div class="panel">
    <div class="panel-body">

        {form action="cp/:cp/offer-imports" method="POST" id="import_form" enctype="multipart/form-data" class="form form-horizontal pina-form form-import"}
        <input type="hidden" name="import_id" value="{$import.id}" />
        <fieldset>
            {if $import.path}
                <div class="form-group">
                    <label for="path_reuse" class="col-sm-2 control-label">{t}Use already uploaded file{/t}</label>
                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="path_reuse" value="Y" checked="checked">{t}Use the file{/t}: <i>{$import.file_name}</i>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="path_reuse" value="N">{t}Upload new one{/t}
                            </label>
                        </div>
                    </div>
                </div>
            {/if}

            <div class="form-group" id="file_select_block" {if $import.path}style="display: none"{/if}>
                <label for="path" class="col-sm-2 control-label">{t}File{/t}</label>
                <div class="col-sm-10" style="padding-top: 10px;">
                    <input type="text" readonly="" class="form-control" placeholder="Browse...">
                    <input type="file" accept=".csv, .xls, .xlsx, .xml, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, text/xml" name="path" id="path">
                </div>
            </div>

            <div class="form-group">
                <label for="mark" class="col-sm-2 control-label">{t}Format{/t}</label>
                <div class="col-sm-10">
                    {foreach name=types from=$formats item=lbl key=type}
                        <div class="radio">
                            <label>
                                <input type="radio" name="format" value="{$type}" 
                                    {if !$import.format && $smarty.foreach.types.first || $import.format eq $type}checked="checked"{/if}
                                    /> {t}{$lbl}{/t}
                            </label>
                        </div>
                    {/foreach}
                </div>
            </div>

            <div class="import-params-block" data-format-type="csv|excel">
                {include file="Skin/form-line-input.tpl" name="header_row" value=$import.header_row|default:1 title="Header line number"|t type="number"}
                {include file="Skin/form-line-input.tpl" name="start_row" value=$import.start_row|default:2 title="Data start line number"|t type="number"}
            </div>

        </fieldset>
        <fieldset class="operations">
            <div class="button-bar row">
                <div class="col-sm-2">
                </div>
                <div class="col-sm-10">
                    <button class="btn btn-primary btn-raised">{t}Import{/t}</button>
                </div>
            </div>        
        </fieldset>
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
        $(".form-import").on("success", function (event, packet, status, xhr) {
            PinaRequest.handleRedirect(xhr);
        });
    </script>
{/literal}
{/script}

{script}
{literal}
    <script type="text/javascript">

        $(document).ready(function () {
            function updateParamsBlocks() {
                var selected = $('input[name="format"]:checked').val();

                $('.import-params-block').each(function () {
                    var avail = $(this).attr('data-format-type').split('|');
                    if (avail.indexOf(selected) !== -1) {
                        $(this).show();
                        $(this).find(':input').attr('disabled', false);
                    } else {
                        $(this).hide();
                        $(this).find(':input').attr('disabled', true);
                    }
                });
            }

            function updateFileSelectBlock() {
                var selected = $('input[name="path_reuse"]:checked').val();

                $('#file_select_block').toggle(selected);
            }

            $('input[name="format"]').on('change', function (e) {
                updateParamsBlocks();
            });

            $('input[name="path_reuse').on('change', function (e) {
                updateFileSelectBlock();
            })

            updateParamsBlocks();
        });

    </script>
{/literal}
{/script}


