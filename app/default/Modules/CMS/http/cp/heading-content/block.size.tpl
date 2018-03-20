{assign var=vals value=','|explode:'h1,h2,h3,h4,h5'}
{assign var=keys value=','|explode:'h1,h2,h3,h4,h5'}
{assign var=params value=$vals|@array_combine:$keys}
{include file="Skin/form-line-select.tpl" labelColumn=12 title="Размер" name="h" list=$params value=$param|default:'h2'}