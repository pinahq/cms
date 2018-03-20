{assign var=vals value=','|explode:',col-sm-offset-1,col-sm-offset-2,col-sm-offset-3,col-sm-offset-4,col-sm-offset-5,col-sm-offset-6,col-sm-offset-7,col-sm-offset-8,col-sm-offset-9,col-sm-offset-10,col-sm-offset-11'}
{assign var=keys value=','|explode:',1 колонка,2 колонки,3 колонки,4 колонки,5 колонок,6 колонок,7 колонок,8 колонок,9 колонок,10 колонок,11 колонок'}
{assign var=params value=$vals|@array_combine:$keys}
{include file="Skin/form-line-select.tpl" labelColumn=12 title="Отступ слева" name="offset_left" list=$params value=$param|default:''}