<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('sitemap/:index');

$index = Request::input('index');

$perSitemap = 1000;

if ($index <= 0) {
    header('Content-type: application/xml');
    echo "<?xml version='1.0' encoding='UTF-8'?>";
    echo '<urlset xmlns:xsi = "http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation = "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns = "http://www.sitemaps.org/schemas/sitemap/0.9">';
    echo '<url>';
    echo '<loc>' . App::link('/') . '</loc>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<changefreq>weekly</changefreq>';
    echo '</url>';
    echo '</urlset>';
} else {

    //при выключении странички, страницы из следующих sitemap`ов не должны переезжать в сайтмеп, в котором была выключена страница
    $urls = ResourceUrlGateway::instance()->limit(($index - 1) * $perSitemap, $perSitemap)->orderBy('resource_id', 'asc')->select('url')->selectAs('resource_enabled', 'enabled')->get();
    header('Content-type: application/xml');
    echo "<?xml version='1.0' encoding='UTF-8'?>";
    echo '<urlset xmlns:xsi = "http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation = "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns = "http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $url) {
        if ($url['enabled'] == 'Y') {
            echo '<url>';
            echo '<loc>' . App::link($url['url']) . '</loc>';
            echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '</url>';
        }
    }
    echo '</urlset>';
}
exit;
