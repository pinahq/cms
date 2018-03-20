{include file="Skin/form-line-input.tpl" id="" name="firstname" title="Firstname"|t value=$user.firstname}

{include file="Skin/form-line-input.tpl" id="" name="middlename" title="Middlename"|t value=$user.middlename}

{include file="Skin/form-line-input.tpl" id="" name="lastname" title="Lastname"|t value=$user.lastname}

{include file="Skin/form-line-input.tpl" id="" name="phone" title="Phone"|t value=$user.phone}

{include file="Skin/form-line-input.tpl" id="" name="email" title="E-mail"|t value=$user.email}


<div class="form-group">
    <label for="group" class="col-sm-2 control-label">Group</label>
    <div class="col-sm-10">
        <select name="group" class="form-control">
            <option {if $user.group eq 'unregistered'}selected="selected"{/if} value="unregistered">unregistered</option>
            <option {if $user.group eq 'registered'}selected="selected"{/if}value="registered">registered</option>
            <option {if $user.group eq 'manager'}selected="selected"{/if}value="manager">manager</option>
            <option {if $user.group eq 'root'}selected="selected"{/if}value="root">root</option>
        </select>
    </div>
</div>


<div class="form-group">
    <label for="status" class="col-sm-2 control-label">Status</label>
    <div class="col-sm-10">
        <select name="status" class="form-control">
            <option {if $user.status eq 'new'}selected="selected"{/if} value="new">new</option>
            <option {if $user.status eq 'active'}selected="selected"{/if}value="active">active</option>
            <option {if $user.status eq 'suspended'}selected="selected"{/if}value="suspended">suspended</option>
            <option {if $user.status eq 'disabled'}selected="selected"{/if}value="disabled">disabled</option>
        </select>
    </div>
</div>