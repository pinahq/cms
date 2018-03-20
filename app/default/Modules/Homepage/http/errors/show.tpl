{extends layout="main"}

{content name="title"}{$code}{/content}

<h1 class="page-header">{$code|substr:0:3}</h1>

<p>{$code|substr:3|trim|strip_tags|default:"Error"}</p>