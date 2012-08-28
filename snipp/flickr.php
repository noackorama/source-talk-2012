<?php
$url = 'http://www.flickr.com/services/feeds/photos_public.gne';
foreach(simplexml_load_file($url)->entry as $it) echo $it->content;
?>