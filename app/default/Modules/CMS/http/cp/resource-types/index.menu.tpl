<div class="col-lg-2 col-md-4 col-sm-6">
    <h3>CMS</h3>
    <ul>
        {foreach from=$resource_types item=type}
            <li {if $params.id eq $type.id} class="active"{/if}>
                <a href="{if $type.tree eq 'Y'}{link get="cp/:cp/resources" resource_type_id=$type.id length=1}{else}{link get="cp/:cp/resources" resource_type_id=$type.id}{/if}">{$type.title}</a>
            </li>
        {/foreach}
        <li><a href="{link get="cp/:cp/users"}">Пользователи</a></li>
        {composer position="cms.menu"}

        <li class="has-child">
            <a>Настройки</a>
            <ul class="child-menu">
                {module get="cp/:cp/config" display=menu}
            </ul>
        </li>
    </ul>
</div>