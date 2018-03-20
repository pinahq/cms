{*capture name="tag_json"}{$tags|@json_encode}{/capture*}
{include file="Skin/form-line-input.tpl" title=$params.title class="tag-selector" name="tags[]" value=$params.value}

