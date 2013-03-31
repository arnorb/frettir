<?php

require_once('../php/phpQuery.php');

$url = $_GET["url"];
$src = file_get_contents($url);
$pq = phpQuery::newDocumentHTML($src);
$h1 = $pq->find('h1:first');
$text = $pq->find('.paragraph');
$image = $pq->find('.container');
$imagelink = $image->find('a');
$imgsrc = $imagelink->attr('href');



?>
<img src="<?php echo $imgsrc; ?>">
<h1><?php echo strip_tags($h1); ?></h1>
<?php echo $text; ?>
