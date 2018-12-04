
<select name="{$params.name}" class="form-control" {action_attributes get="cp/:cp/tags"}>
    <option value="0"></option>
    {if $tag}
        <option value="{$tag.id}" {if $tag.id eq $params.value}selected="selected"{/if}>{$tag.tag}</option>
    {/if}
</select>

{script src="/vendor/select2/js/select2.min.js"}{/script}
{style src="/vendor/select2/css/select2.min.css"}{/style}
{script}
{literal}
    <script>
        $(document).ready(function () {
            $('select[name={/literal}{$params.name}{literal}]').each(function () {
                var resource = $(this).data('resource');
                $(this).select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: '',
                    ajax: {
                        url: '/' + resource,
                        dataType: 'json',
                        data: function (params) {
                            var query = {
                                q: params.term,
                            }
                            return query;
                        },
                        processResults: function (data) {
                            console.log(data);
                            var r = {results: []};
                            for (var i in data) {
                                r.results.push({id: data[i].id, text: data[i].tag});
                            }
                            return r;
                        }
                    }
                });
            });
        });
    </script>
{/literal}
{/script}