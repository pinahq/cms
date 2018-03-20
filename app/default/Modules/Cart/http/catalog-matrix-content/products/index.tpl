{foreach from=$products item=p}
    <div class="toplist-items">
        {view get="products/block" display=item resource=$p} 
    </div>
{/foreach}