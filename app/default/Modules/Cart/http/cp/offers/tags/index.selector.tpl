{*capture name="tag_json"}{$tags|@json_encode}{/capture*}
{include file="Skin/form-line-input.tpl" title="Tags"|t name="tags" value=$offer.tags}

{script}
{literal}
    <script>
        
var tags = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tag'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: {
    url: '/cp/ru/tags.json',
    prepare: function(query, settings) {
        var url = settings.url;
        var q = '?q='+query;

        settings.url = url + q;

        return settings;
    },
    cache: false
  }
});
tags.initialize();
        
        var elt = $('input[name=tags]');
        elt.tagsinput({
            confirmKeys: [13, 14, 44],
            freeInput: true,
            itemValue: 'id',
            itemText: 'tag',
            typeaheadjs: {
                name: 'tags',
                displayKey: 'tag',
                source: tags.ttAdapter()
            }
        });
        $(elt).on('beforeItemAdd', function (event) {
            var tag = event.item;
            if (!event.options || !event.options.preventPost) {
                var headers = $("[name=csrf_token]").val() ? {'X-CSRF-Token': $("[name=csrf_token]").val()} : {};
                $.ajax('{/literal}{link get="cp/:cp/tags"}{literal}', {method: 'post', data: {tag: tag}, headers: headers, success: function (response) {
                        if (response.tag && response.id) {
                            elt.tagsinput('add', response, {preventPost: true});
                        } else {
                            // Remove the tag since there was a failure
                            // "preventPost" here will stop this ajax call from running when the tag is removed
                            elt.tagsinput('remove', tag, {preventPost: true});
                        }
                    }});
            }
        });
        {/literal}
        {foreach from=$tags item=tag}{literal}
        elt.tagsinput('add', {"id": {/literal}{$tag.id}{literal}, "tag": "{/literal}{$tag.tag|replace:"\n":" "}{literal}"}, {preventPost: true});
        {/literal}{/foreach}
        {literal}
    </script>
{/literal}
{/script}