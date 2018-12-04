{content name="page_header"}Стоимость доставки{/content}

{if $fees}
    <div class="row">
        <div class="col-sm-12" style="text-align: right;">
            <div class="btn-group btn-group-sm" style="margin:20px 0;">
                <a href="{link download=csv}" class="btn btn-raised" style="float:right;"><i class="material-icons">file_download</i></a>
                <a href="#{*link get="cp/:cp/shipping-methods/:id/fee/create" id=$params.shipping_method_id*}" class="btn btn-raised" id="upload-csv" style="float:right;"><i class="material-icons">file_upload</i></a>
            </div>
        </div>
    </div>
    <div class="panel">
        <div class="panel-body">
            {form method="put" action="cp/:cp/shipping-methods/:shipping_method_id/fee" enctype="multipart/form-data" shipping_method_id=$params.shipping_method_id class="pina-form form-fee"}
            <table class="table">
                <thead>
                    <tr>
                        <th>Страна</th>
                        <th>Регион</th>
                        <th>Город</th>
                        <th>Стоимость</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$fees item=fee}
                        <tr>
                            <td>{$fee.country}</td>
                            <td>{$fee.region}</td>
                            <td>{$fee.city}</td>
                            <td><input class="form-control" type="text" name="fee[{$fee.country_key|default:0}][{$fee.region_key|default:0}][{$fee.city_id|default:0}][fee]" value="{$fee.fee}" /></td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary btn-raised">Сохранить</button>
            {/form}
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
            $(".form-fee").on("success", function (event, packet, status, xhr) {
                document.location.reload();
            });
        </script>
    {/literal}
    {/script}

    <input style="display:none;" type="file" id="file" name="" value="" {action_attributes put="cp/:cp/shipping-methods/:id/fee" id=$params.shipping_method_id} />

    {literal}
        <script type="text/javascript">


            window.onload = function () {

                $("#upload-csv").on('click', function () {
                    $("#file").trigger('click');
                });

                var sendFile = function (method, resource, csrf_token, file) {
                    var reader = new FileReader();
                    var xhr = new XMLHttpRequest();

                    xhr.open(method, '/' + resource, true);
                    xhr.setRequestHeader('Content-type', 'text/csv; charset=cp1251');
                    xhr.setRequestHeader('X-CSRF-Token', csrf_token);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            // Handle response.
                            document.location.reload();
                        }
                    };
                    reader.onload = function (evt) {
                        xhr.send(evt.target.result);
                    };
                    reader.readAsDataURL(file);
                }

                $('#file').bind('change', function (evt) {
                    if (typeof evt.target.files != "undefined")
                    {
                        var files = evt.target.files;
                        for (var i = 0, f; f = files[i]; i++) {
                            sendFile($(this).attr('data-method'), $(this).attr('data-resource'), $(this).attr('data-csrf-token'), f);
                        }
                    }
                });

                var dropzone = document.getElementById("dropzone");
                if (dropzone) {
                    dropzone.ondragover = dropzone.ondragenter = function (event) {
                        event.stopPropagation();
                        event.preventDefault();
                    }

                    dropzone.ondrop = function (event) {
                        event.stopPropagation();
                        event.preventDefault();

                        var filesArray = event.dataTransfer.files;
                        for (var i = 0; i < filesArray.length; i++) {
                            sendFile(filesArray[i]);
                        }
                    }
                }
            }
        </script>
    {/literal}

{else}
    <p>Регионы не настроены</p>
{/if}

