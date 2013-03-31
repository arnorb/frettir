<?php

require_once('../php/phpQuery.php');

$url = $_GET["url"];
$src = file_get_contents($url);
$pq = phpQuery::newDocumentHTML($src);
$h1 = $pq->find('h1:first');
$text = $pq->find('.paragraph')->find('hardreturn');

if ($text == "") {
	$text = $pq->find('.paragraph');
}

$image = $pq->find('.container');
$imagelink = $image->find('a');
$imgsrc = $imagelink->attr('href');


if ($imgsrc == ""){
	$image = $pq->find('.img');
	$imagelink = $image->find('a');
	$imgsrc = $imagelink->attr('href');
}


?>
<div class="loaded-item">
<div class="loaded-img"><a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $imgsrc; ?>"></a></div>
<h3><a href="<?php echo $url; ?>" target="_blank"><?php echo utf8_encode(strip_tags($h1)); ?></a></h3>
<div class="loaded-item-content"> <?php echo utf8_encode($text); ?></div>
</div>
