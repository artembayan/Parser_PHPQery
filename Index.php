<?php
require_once 'phpQuery/phpQuery/phpQuery.php';

const base_url = 'https://chaspik-samara.ru';
#const paginate = 'https://chaspik-samara.ru/catalog/picca-samara/?PAGEN_2=2&AJAX_PAGE=Y';

function get_content($url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    $pq = phpQuery::newDocument($result);
    return $pq;
}

function get_categories($base_url, $pq, $url, $categories){
    $cat_name = $pq->find('ul.font-fix > li.active > a')->text();
    $items = $pq->find('.col-xl-7 > nav > ul > li > a');
    array_push($categories, array($cat_name, $url, 0));
    foreach ($items as $item){
        $subcat_link = $base_url.pq($item)->attr('href');
        $subcat_name = pq($item)->text();
        array_push($categories, array($subcat_name, $subcat_link, 1));
    }
    return $categories;
}

function parse_products ($url, $pq, $products){
    $pq = get_content($url);
    $items = $pq->find('div.col-lg-3');
    foreach ($items as $item){
        preg_match('/\d+/', pq($item)->find('span.current-price > span')->text(), $matches);
        $name = pq($item)->find('a > span')->text();
        $description = str_replace(array("\n", "\t"), "", pq($item)->find('div.product-description')->text());
        $price = intval($matches[0]);
        $img = base_url.pq($item)->find('img.product-image')->attr('srcset');
        $props = pq($item)->find('.prop_');
        array_push($products, array($name, $description, $price, $img));
    }
    $next_page = $pq->find('a.ajax-pager-link')->attr('href');
    if (!empty($next_page)){
        $next_page = base_url.$next_page;
        $pq = get_content($next_page);
        $products = $products + parse_products($next_page, $pq, $products);
    }
    else{
        echo '<pre>'; echo 'Last page'; echo '</pre>';;
    }
    return $products;
}

function parse_wok($url, $pq, $products){

    return $products;
}

$pq = get_content(base_url);
$categories = $pq->find('ul.font-fix > li:not(.menu-extend-cont) > a');
$categories_urls = [];
$categories_list = [];
$products = [];

foreach ($categories as $cat) {
    $elem = pq($cat);
    $link = base_url.$elem->attr('href');
    array_push($categories_urls, $link);
}

foreach ($categories_urls as $url){
    $pq = get_content($url);
    $categ_list = get_categories(base_url, $pq, $url, $categories_list);
    $categories_list = $categories_list + $categ_list;
    $wok_page = $pq->find('div.base-list')->text();
    if (!empty($wok_page)){
        echo '<pre>'; echo 'Go to parse WOK page'; echo '</pre>';
        #$product = parse_wok($url, $pq, $products);
    }
    else {
        $products = parse_products($url, $pq, $products);
    }
}
echo '<pre>'; print_r($categories_list); echo '</pre>';
echo '<pre>'; print_r($products); echo '</pre>';

?>