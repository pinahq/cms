<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('robots');

header('Content-type: text/plain');

echo "## Enable robots.txt rules for all crawlers    
User-agent: *
    
## Crawl-delay parameter: number of seconds to wait between successive requests to the same server.
## Set a custom crawl rate if you're experiencing traffic problems with your server.
# Crawl-delay: 30

Sitemap: " . App::link('sitemap') . ".xml
    
## Do not crawl development files and folders: CVS, svn directories and dump files
Disallow: /CVS
Disallow: /.git
Disallow: /*.svn$
Disallow: /*.idea$
Disallow: /*.sql$
Disallow: /*.tgz$
Disallow: /*.tgz$

## Do not crawl admin page
Disallow: /cp/

## Do not crawl common files
Disallow: /vendor/
Disallow: /LICENSE.html
Disallow: /LICENSE.txt
Disallow: /README.txt
Disallow: /RELEASE_NOTES.txt

## Do not crawl sub category pages that are sorted
Disallow: /*?sort=*

## Do not crawl sub category pages that are filtered
Disallow: /*?tag_id*

## Do not crawl checkout and user account pages
Disallow: /carts/
Disallow: /auth
Disallow: /registration
Disallow: /password-recovery


## Do not crawl seach pages and not-SEO optimized catalog links
Disallow: /search

## Do not crawl common server technical folders and files
Disallow: /cgi-bin/
Disallow: /*.php$

## Uncomment if you do not wish Google and Bing to index your images
# User-agent: Googlebot-Image
# Disallow: /
# User-agent: msnbot-media
# Disallow: /
";

exit;
