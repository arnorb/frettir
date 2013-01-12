<?php
include '../php/phpQuery.php';

$file = 'http://www.vb.is/frettir/79718/'; // see below for source

// loads the file
// basically think of your php script as a regular HTML page running client side with jQuery.  This loads whatever file you want to be the current page
$pq = phpQuery::newDocumentFileHTML($file);

// Once the page is loaded, you can then make queries on whatever DOM is loaded.  
// This example grabs the title of the currently loaded page.
// $titleElement = pq('title'); // in jQuery, this would return a jQuery object.  I'm guessing something similar is happening here with pq.
$div = $pq->find('.main_photo');
$img = $div->find('img');
$src = $img->attr('src');


// You can then use any of the functionality available to that pq object.  Such as getting the innerHTML like I do here.
//$title = $titleElement->html();

// And output the result
//echo '<h2>Title:</h2>';
// echo '<p>' . htmlentities( $title) . '</p>';

?>

<img src="http://www.vb.is<?php echo $src; ?>">