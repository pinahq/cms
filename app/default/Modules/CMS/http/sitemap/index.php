<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('sitemap');

$perSitemap = 1000;

$urlCount = ResourceUrlGateway::instance()->count();

$fileCount = ceil($urlCount / $perSitemap);

header('Content-type: application/xml');

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
for ($i = 0; $i <= $fileCount; $i++) {
    echo '<sitemap>';
    echo '<loc>' . App::link('sitemap/:id', ['id' => $i]) . '.xml</loc>';
    echo '<lastmod>2004-10-01T18:23:17+00:00</lastmod>';
    echo '</sitemap>';
}
echo '</sitemapindex>';

exit;
