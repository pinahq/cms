<h2>Комментарии</h2>

{foreach from=$comments item=comment}
    <div>
        <h3>
            {$comment.firstname}
            {$comment.lastname}
        </h3>
        <p>
            {$comment.text}
        </p>
    </div>
{/foreach}

{view get="resources/:resource_id/comments/block" display=form resource_id=$params.resource_id}