{content name="page_header"}{t}Cities{/t}{/content}

<div class="row">
    <div class="col-sm-12" style="text-align: right;">
        <div class="btn-group btn-group-sm" style="margin:20px 0;">
            <a href="{link download=csv}" class="btn btn-raised" style="float:right;"><i class="material-icons">file_download</i></a>
            <a href="#" class="btn btn-raised" id="upload-csv" style="float:right;"><i class="material-icons">file_upload</i></a>
        </div>
    </div>
</div>
<div class="panel">
    <div class="panel-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>
                        {t}Country{/t}
                    </th>
                    <th>
                        {t}Region{/t}
                    </th>
                    <th>
                        {t}ID{/t}
                    </th>
                    <th>
                        {t}Title{/t}
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$cities item=city}
                    <tr>
                        <td>
                            {$city.country_key}
                        </td>
                        <td>
                            {$city.region_key}
                        </td>
                        <td>
                            {$city.id}
                        </td>
                        <td>
                            {$city.city}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>

<input style="display:none;" type="file" id="file" name="" value="" {action_attributes put="cp/:cp/cities"} />
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
                        //document.location.reload();
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