<li class="dropdown">
    <a href="{link get="/users/:id" id=$id}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="head-icon head-icon-person"></span> <span class="glyphicon glyphicon-menu-down"></span></a>
    <div class="dropdown-menu">
        <ul class="nav">
            {view get="users/block" display="menu"}
        </ul>
    </div>
</li>