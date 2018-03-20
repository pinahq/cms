<title>{if $meta.title}{$meta.title}{else}{place name=title}{/if}</title>

{if $meta.description}
<meta name="description" content="{$meta.description}" />
{/if}

{if $meta.keywords}
<meta name="keywords" content="{$meta.keywords}" />
{/if}

{place name="meta"}