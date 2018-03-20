{if $content.params.type}
    {module get=":type" type=$content.params.type 
        resource_type_id=$content.params.type_id parent_id=$content.params.parent_id length=$content.params.length 
        page=$smarty.get.page tag_id=$smarty.get.tag_id sort=$smarty.get.sort token=$smarty.get.token}
{else}
    <p>{t}Please configure content{/t}</p>
{/if}
