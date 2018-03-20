{form action=$params.action method="get" class="form form-horizontal form-tag-filter pina-form"}
    {if $params.price}
        <input type="hidden" name="price" value="{$params.price}" />
    {/if}
    {if $params.stock}
        <input type="hidden" name="stock" value="{$params.stock}" />
    {/if}
    {if $params.resource_type_id}
        <input type="hidden" name="resource_type_id" value="{$params.resource_type_id}" />
    {/if}
    {if $params.status}
        <input type="hidden" name="status" value="{$params.status}" />
    {/if}
    {if $params.length}
        <input type="hidden" name="length" value="{$params.length}" />
    {/if}
    {if $params.search}
        <input type="hidden" name="search" value="{$params.search}" />
    {/if}

    <div class="md-form input-group">
        <div class="col-sm-6">
            {include file="Skin/form-line-select.tpl" list=$tag_types value=$params.tag_type_id name="tag_type_id" id="tag_type_id"}
        </div>
        <div class="col-sm-6">
            <input type="hidden" id="tag_id" name="tag_id" class="form-control" value="{$params.id}" />
            <input type="text" id="tag" name="tag" class="form-control" value="{$params.tag}" />
        </div>
            
        <span class="input-group-btn">
            <button class="btn btn btn-fab btn-fab-mini"><i class="material-icons">search</i></button>
        </span>
    </div>
{/form}

{script}
{literal}
<script>
    var tags = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '../../../cp/../cp/ru/tags-filter.json',
            prepare: function(query, settings) {
                var tagTypeId = parseInt($("#tag_type_id").val());
                if (tagTypeId == 0) {
                    return settings;
                }
                var q = '?q='+ query +'&tag_type_id='+ tagTypeId;

                var url = settings.url;
                settings.url = url + q;
                return settings;
            },
            cache: false
        }
    });

    $('#tag').typeahead({
        hint: true,
        highlight: true,
        minLength: 2
    },
    {
        name: 'tags',
        limit: 10,
        displayKey: 'tag',
        source: tags
    });

    $('#tag').on('typeahead:select', function(e, selected) {
        $("#tag_id").val(selected.id);
    });

    $('#tag').on('change', function() {
        $("#tag_id").val('');
    });

    $('.twitter-typeahead').attr('style', function(i, style) {
        return style.replace('display: inline-block;', '');
    });

    $("#tag_type_id").on("change", function () {
        $("#tag").val('');
        $("#tag_id").val('');
    });
</script>
{/literal}
{/script}