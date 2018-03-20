<select name="parent_id" id="parent_id" class="form-control" {action_attributes get="cp/:cp/resources/:id/parent-selector" id=$params.id}>
    <option value="0">/</option>
    {foreach from=$resources item=r}
        <option value="{$r.id}" {if $r.id eq $params.parent_id}selected="selected"{/if}>{$r.title}</option>
    {/foreach}
</select>

{if $params.async}
{script src="/vendor/select2/js/select2.min.js"}{/script}
{style src="/vendor/select2/css/select2.min.css"}{/style}
{script}
{literal}
    <script>
        $(document).ready(function () {
            $('select[name=parent_id]').each(function () {
                var resource = $(this).data('resource');
                $(this).select2({
                    width: '100%',
                    ajax: {
                        url: '/' + resource,
                        dataType: 'json',
                        data: function (params) {
                            var query = {
                                search: params.term,
                                page: params.page || 1
                            }
                            return query;
                        },
                        processResults: function (data) {
                            var r = {results: [], pagination: {more: true}};
                            for (var i in data.resources) {
                                r.results.push({id: data.resources[i].id, text: data.resources[i].title});
                            }
                            r.pagination.more = data.resources.length == data.paging.paging;
                            return r;
                        }
                    }
                });
            });
        });
    </script>
{/literal}
{/script}
{/if}