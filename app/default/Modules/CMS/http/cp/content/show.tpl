{extends layout=block}

{view get="cp/:cp/:content_type/:content_id" content_id=$content.id content=$content content_type=$content.type}