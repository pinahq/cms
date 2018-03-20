{assign var=vals value=','|explode:',col-sm-1,col-sm-2,col-sm-3,col-sm-4,col-sm-5,col-sm-6,col-sm-7,col-sm-8,col-sm-9,col-sm-10,col-sm-11,col-sm-12'}
{assign var=keys value=','|explode:',1 колонка,2 колонки,3 колонки,4 колонки,5 колонок,6 колонок,7 колонок,8 колонок,9 колонок,10 колонок,11 колонок,12 колонок'}
{assign var=params value=$vals|@array_combine:$keys}
{include file="Skin/form-line-select.tpl" labelColumn=12 title="Ширина" name="width" list=$params value=$param|default:''}