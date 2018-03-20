{if $params.parent_id}
    {module get="cp/:cp/resources/:resource_id" resource_id=$params.parent_id display=header}
    {place name=tabs}
{elseif $params.tag_resource_id}
    {module get="cp/:cp/resources/:resource_id" resource_id=$params.tag_resource_id display=header}
    {place name=tabs}
{else}
    {content name="page_header"}
    Прайс-лист <small>цены и остатки на складе</small>
    {/content}

    {content name="breadcrumb"}
    <ol class="breadcrumb">
        <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
        <li>Прайс-лист</li>
    </ol>
    {/content}
{/if}

<div class="row">
    <div class="col-md-6 col-md-push-6">
        <div class="hidden-xs"  style="visibility: hidden">
            {link_context
                parent_id=$params.parent_id
                tag_resource_id=$params.tag_resource_id
                price=$params.price
                stock=$params.stock
                search=$params.search
                tag_type_id=$params.tag_type_id
                tag=$params.tag
                tag_id=$params.tag_id}
            {include file="Skin/paging.tpl" get="/cp/:cp/offers/"}
            {/link_context}
        </div>
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        {link_context stock=$params.stock parent_id=$params.parent_id tag_resource_id=$params.tag_resource_id search=$params.search tag_type_id=$params.tag_type_id tag=$params.tag tag_id=$params.tag_id}
                        <ul class="nav nav-pills">
                            <li {if !$params.price} class="active"{/if}>
                                <a href="{link get='cp/:cp/offers'}">{t}All{/t}</a>
                            </li>
                            <li {if $params.price eq 'none'} class="active"{/if}>
                                <a href="{link get='cp/:cp/offers' price=none}">{t}Empty price{/t}</a>
                            </li>
                        </ul>
                        {/link_context}
                    </div>
                    <div class="col-sm-6">
                        {link_context price=$params.price parent_id=$params.parent_id tag_resource_id=$params.tag_resource_id search=$params.search tag_type_id=$params.tag_type_id tag=$params.tag tag_id=$params.tag_id}
                        <ul class="nav nav-pills">
                            <li {if !$params.stock} class="active"{/if}>
                                <a href="{link get='cp/:cp/offers'}">{t}All{/t}</a>
                            </li>
                            <li {if $params.stock eq 'none'} class="active"{/if}>
                                <a href="{link get='cp/:cp/offers' stock=none}">{t}Out of stock{/t}</a>
                            </li>
                            <li {if $params.stock eq presented} class="active"{/if}>
                                <a href="{link get='cp/:cp/offers' stock=presented}">{t}Stock{/t}</a>
                            </li>
                        </ul>
                        {/link_context}
                    </div>
                    <div class="col-md-2">
                        <ul class="nav nav-pills">
                            <li {if $params.search or $params.tag or $params.tag_id}class="active"{/if}><a href="#" onclick="$(this).parent().toggleClass('active');$('.search').toggleClass('hidden');">Поиск</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel search {if not($params.search or $params.tag or $params.tag_id)}hidden{/if}">
            <div class="panel-body">
                {form action="/cp/:cp/offers" method="get" name="resource_search_form" class="form form-horizontal form-resource pina-form"}
                <div class="col-sm-12">
                    <div class="md-form input-group">
                        {if $params.price}
                            <input type="hidden" name="price" value="{$params.price}" />
                        {/if}
                        {if $params.stock}
                            <input type="hidden" name="stock" value="{$params.stock}" />
                        {/if}
                        {if $params.tag_type_id}
                            <input type="hidden" name="tag_type_id" value="{$params.tag_type_id}" />
                        {/if}
                        {if $params.tag}
                            <input type="hidden" name="tag" value="{$params.tag}" />
                        {/if}
                        {if $params.tag_id}
                            <input type="hidden" name="tag_id" value="{$params.tag_id}" />
                        {/if}
                        <input type="search" name="search" class="form-control" placeholder="{t}Search{/t}" value="{$params.search|default:''}" />
                        <span class="input-group-btn">
                            <button class="btn btn btn-fab btn-fab-mini"><i class="material-icons">search</i></button>
                        </span>
                    </div>
                </div>
                {/form}
            </div>
        </div>

        <div class="panel search {if not($params.search or $params.tag or $params.tag_id)}hidden{/if}">
            <div class="panel-body">
                {module get="cp/:cp/tags-filter" action="cp/:cp/offers" display="selector" 
            tag_id=$params.tag_id tag=$params.tag tag_type_id=$params.tag_type_id
            price=$params.price stock=$params.stock resource_type_id=$params.resource_type_id
            status=$params.status length=$params.length search=$params.search}
            </div>
        </div>
        {link_context
            tag_resource_id=$params.tag_resource_id
            price=$params.price
            stock=$params.stock
            search=$params.search
            tag_type_id=$params.tag_type_id
            tag=$params.tag
            tag_id=$params.tag_id}
            {capture name="url"}{link get="cp/:cp/offers"}{/capture}
        {/link_context}
        {module get="cp/:cp/resource-type-trees" parent_id=$params.parent_id url=$smarty.capture.url}
    </div>

    <div class="col-md-6 col-md-pull-6">
        {link_context
            parent_id=$params.parent_id
            tag_resource_id=$params.tag_resource_id
            price=$params.price
            stock=$params.stock
            search=$params.search
            tag_type_id=$params.tag_type_id
            tag=$params.tag
            tag_id=$params.tag_id}
        <div class="row">
            <div class="col-xs-8">
                {include file="Skin/paging.tpl" get="/cp/:cp/offers/"}
            </div>
            <div class="col-xs-4" style="text-align: right;">

                <div class="btn-group btn-group-sm" style="margin:20px 0;">
                    <a href="{link get=$paging.resource|cat:".csv"}" class="btn btn-raised" style="float:right;"><i class="material-icons">file_download</i></a>
                    <a href="{link get="cp/:cp/offer-imports/create"}" class="btn btn-raised" style="float:right;"><i class="material-icons">file_upload</i></a>
                </div>
            </div>
        </div>
        {/link_context}

        <div class="panel">
            <div class="panel-body">

                <table class="table table-striped table-hover ">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>{t}Amount{/t}</th>
                            <th>{t}Cost Price{/t}</th>
                            <th>{t}Price{/t}</th>
                            <th colspan="2">{t}Sale Price{/t}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {assign var="prev_resource_id" value=0}
                        {foreach from=$offers item=o}
                            {if $o.resource_id ne $prev_resource_id}
                                <tr>
                                    <td>
                                        <a href="{link get="cp/:cp/products/:resource_id/offers" resource_id=$o.resource_id}">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                    </td>
                                    <td colspan="7">
                                        {$o.title}<br />
                                        {$o.resource_tags|replace:"\n":"<br />"}
                                    </td>
                                </tr>
                            {/if}
                            <tr {if $o.actual_price le 0}class="danger"{elseif $o.amount eq 0}class="warning"{/if}>
                                <td>
                                    <a href="{link get="cp/:cp/products/:resource_id/offers/:id" resource_id=$o.resource_id id=$o.id}">
                                        <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                </td>
                                <td>
                                    {assign var="prev_resource_id" value=$o.resource_id}
                                    {$o.tags|replace:"\n":'<br />'}
                                </td>
                                <td>{$o.amount}</td>
                                <td>{$o.cost_price}</td>
                                <td>{$o.price}</td>
                                <td>{$o.sale_price}</td>
                                <td>
                                    <div class="togglebutton">
                                        <label>
                                            <input type="checkbox" class="action-toggle" data-key="enabled"
                                                   {action_attributes put="cp/:cp/offers/:id/status" id=$o.id}
                                                   {if $o.enabled eq 'Y'} checked=""{/if}>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{link_context
    parent_id=$params.parent_id
    tag_resource_id=$params.tag_resource_id
    price=$params.price
    stock=$params.stock
    search=$params.search
    tag_type_id=$params.tag_type_id
    tag=$params.tag
    tag_id=$params.tag_id}
{include file="Skin/paging.tpl" get="/cp/:cp/offers/"}
{/link_context}

{script src="/static/default/js/pina.toggle.js"}{/script}
