<h3 style="color:white;">Сообщения сегодня <small>({$submissions|@count})</small></h3>
{if $submissions}
    <ul>
        {foreach from=$submissions item=s name=submissions}
            {if $smarty.foreach.submissions.index le 2}
            <li>
                <a href="{link get="cp/:cp/submissions/:id" id=$s.id}">
                    {if $params.date eq 'today'}
                        {$s.created|format_time}
                    {else}
                        {$s.created|format_datetime}
                    {/if}
                    {$s.subject|default:"-"}<br />
                    {if $s.firstname || $s.middlename || $s.lastname}
                        {$s.firstname} 
                        {$s.middlename}
                        {$s.lastname}
                    {else}
                        -
                    {/if}
                    {if $s.email}
                        {$s.email}
                    {/if}
                    {if $s.phone}
                        {$s.phone}
                    {/if}
                </a>
            </li>
            {/if}
        {/foreach}
        <li><a href="{link get="cp/:cp/submissions"}"><strong>{t}All submissions{/t}</strong></a></li>
    </ul>
{else}
    <p>Not Found</p>
{/if}