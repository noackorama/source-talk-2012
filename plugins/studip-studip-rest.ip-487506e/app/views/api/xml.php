<?
$router->contentType('text/xml;charset=UTF-8');

$content = RestIP\Helper::arrayToXML($data, array(
    'root_node' => 'response',
), Studip\ENV === 'development');

echo $content;
