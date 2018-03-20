{capture name=src}{img image=$image return=src}{/capture}
{strip}
    <div class="col col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <a class="remove action-remove" href="#"><i class="glyphicon glyphicon-remove"></i> {t}Delete{/t}</a>
                </h3>
            </div>
            <div class="panel-body" style="text-align:center;">
                <div class="image" style="background-image:url({$smarty.capture.src});display:inline-block;width:150px;height:150px;background-size:contain;background-position:center;background-repeat:no-repeat;" />
                <center class="sizes">{if $image.id}{$image.width}x{$image.height}{/if}</center>
            </div>
            <div class="panel-footer" style="margin:0;">
                <input type="hidden" class="image_id" name="image_id[]" value="{$image.id}" />
                <div class="form-group">
                    <label>{t}Link{/t}</label>
                    <input type="text" name="link_url[]" class="form-control" placeholder="Введите ссылку..." value="{$image.link_url}" />
                </div>
                <div class="form-group">
                    <label>{t}Status{/t}</label>
                    <select class="form-control" name="enabled[]">
                        <option value="Y" {if $image.enabled eq 'Y'}selected="selected"{/if}>Активен</option>
                        <option value="N" {if $image.enabled eq 'N'}selected="selected"{/if}>Скрыт</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
{/strip}
