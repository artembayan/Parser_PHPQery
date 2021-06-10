<?php
require_once 'phpQuery/phpQuery/phpQuery.php';

const url = 'https://chaspik-samara.ru';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($curl);

$pq = phpQuery::newDocument($result);
$categories = $pq->find('ul.font-fix > li:not(.menu-extend-cont) > a');
$categs = [];

foreach ($categories as $cat) {
    $elem = pq($cat);
    $c = array($elem->text(), url.$elem->attr('href'));
    array_push($categs, $c);
}

echo ('')
?>