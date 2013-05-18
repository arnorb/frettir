
<?php

$time = microtime(true);

require_once('../php/autoloader.php');
require_once('../php/phpQuery.php');
require('relativedate.php');
require('functions.php');

$feeds = array(
	'http://www.visir.is/apps/pbcs.dll/section?Category=FRETTIR01&Template=rss&mime=xml',
	'http://www.visir.is/apps/pbcs.dll/section?Category=VIDSKIPTI&Template=rss&mime=xml',
	'http://www.visir.is/apps/pbcs.dll/section?Category=LIFID&Template=rss&mime=xml',
	'http://www.visir.is/apps/pbcs.dll/section?Category=FRETTIR02&Template=rss&mime=xml',
	'http://www.visir.is/apps/pbcs.dll/section?Category=IDROTTIR&Template=rss&mime=xml',
	'http://www.mbl.is/feeds/innlent/',
	'http://www.mbl.is/feeds/erlent/',
	'http://www.mbl.is/feeds/folk/',
	'http://www.mbl.is/feeds/sport/',
	'http://www.vb.is/rss/',
	'http://www.ruv.is/rss/innlent',
	'http://www.ruv.is/rss/erlent',
	'http://ruv.is/rss/sport'
	);

$first_items = array();

foreach ($feeds as $url)
{
    // Use the long syntax
    $feed = new SimplePie();
    $feed->set_feed_url($url);
    $feed->init();
 
	// How many items per feed should we try to grab?
	$items_per_feed = 5;
 
	// As long as we're not trying to grab more items than the feed has, go through them one by one and add them to the array.
	for ($x = 0; $x < $feed->get_item_quantity($items_per_feed); $x++)
	{
		$first_items[] = $feed->get_item($x);
	}

	// cache settings
	$feed->enable_cache(true);
	$feed->set_cache_location('../cache');
	$feed->set_cache_duration(1800);
 
    // We're done with this feed, so let's release some memory.
    unset($feed);
}

// We need to sort the items by date with a user-defined sorting function.  Since usort() won't accept "SimplePie::sort_items", we need to wrap it in a new function.
function sort_items($a, $b)
{
	return SimplePie::sort_items($a, $b);
}

function is_image($path) {
		$a = getimagesize($path);
		$image_type = $a[2];
		
		if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG)))
		{
			return true;
		}
		return false;
}
 
// Now we can sort $first_items with our custom sorting function.
usort($first_items, "sort_items");

setlocale(LC_ALL, 'is_IS.utf8');

// run SimplePie
//$feed->init();

//$feed->handle_content_type();

?><!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<!-- 

		   _  _______      ________
		  / |/ / __/ | /| / / __/ /
		 /    / _/ | |/ |/ /\ \/_/ 
		/_/|_/___/ |__/|__/___(_)  
                           

	For your sake and mine, don't mindlessly copy my code. 

	If you really need it take some of it, I guess I can't stop you. 

	But if you really want to learn to code, you should type yourself.
	

	-->


	<title>Hvað er að frétta?</title>
	<script type="text/javascript" src="//use.typekit.net/fzr6zim.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	<link href='http://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic|Open+Sans:300italic,400italic,700italic,400,300,700' rel='stylesheet' type='text/css'>
	<link href='style.css' rel='stylesheet' type='text/css'>
	<link href='colorbox.css' rel='stylesheet' type='text/css'>

</head>

<body>


	<section id="wrapper">

		<header>
			<h1>Hvað er að frétta? <span class="alpha">alpha</span> <a href="#" class="settings-toggle"><i class="icon-cog"></i></a> </h1>

			<section id="settingspanel">
				<ul>
					<li> <a href="#" class="category-select innlent-button">Innlent</a></li>
					<li> <a href="#" class="category-select erlent-button">Erlent</a></li>
					<li> <a href="#" class="category-select vidskipti-button">Viðskipti</a></li>
					<li> <a href="#" class="category-select daegradvol-button">Dægradvöl</a></li>
					<li> <a href="#" class="category-select ithrottir-button">Íþróttir</a></li>
				</ul>
			</section>

		</header>	



		<section id="content">



		<?php foreach ($first_items as $item):
		$feed = $item->get_feed();
		$source = getsource($feed);
		$catname = getcatname($feed); 


		$md5 = md5($source . $item->get_date('YmdHis') . $item->get_title() . $item->get_description() );

		?>

		 <?php if ($md5 != $lastitem) : 

				// if ($source == 'mbl' || $source == 'visir' || $source == 'ruv') {
					
					$filename = 'img/' . $md5 . '.jpg';
					$filename2x = 'img/' . $md5 . '@2x.jpg';

				if (!file_exists($filename) ) {

					$hasimage = true;

					// The image src from mbl is embedded in the description, so we have to fetch it from the DOM, using phpQuery.
					if ($source == 'mbl' || $source == 'ruv') {
						$src = '';
						$texthtml = htmlspecialchars_decode($item->get_description()); 
						$pq = phpQuery::newDocumentHTML($texthtml);
						$img = $pq->find('img:first');
						$src = $img->attr('src');

					// Unfortunately, vbl doesn't include any images in their feed. We have to get it via the DOM.
					} elseif ($source == 'vb') {
						$file = $item->get_permalink();
						$pq = phpQuery::newDocumentFileHTML($file);
						$div = $pq->find('.main_photo');
						$img = $div->find('img');
						$src = 'http://www.vb.is' . $img->attr('src');
					// Visir has the src as a media thumbnail, but it is urlencoded. PHP makes it simple to decode, thankfully.
					} elseif ($source == 'visir') {
						if ($enclosure = $item->get_enclosure()) {  
							$src = rawurldecode($enclosure->get_thumbnail()); 
						} 
					}

					// Rúv serves a tiny image. Their url schema is simple so we just need to replace the url with a new dir.
					// I guess there must be a better way to do this, but this will do for now.
					if ($source == 'ruv') {
						$expl = explode('/', $src);
						$src = 'http://www.ruv.is/files/imagecache/frmynd-stok-460x272/myndir/' . $expl[7];

					}

					// Trying to get a better img from mbl, currently not working.
					if ($source == 'mbl') {
						$expl = explode('/', $src);
						$mblfile = substr($expl[6],0,-5);
						$src = 'http://www.mbl.is/frimg/' . $expl[4] . '/' . $expl[5] . '/' . $mblfile . '.jpg';

					}					

					if (is_image($src) == true) {

						// Uncomment for debug
						// echo $src;
					
						// Rúv sometimes confuses us with png images and I suppose we might run into that from others as well.
						if (exif_imagetype($src) == 3) {
							$image = imagecreatefrompng($src);
						} elseif (exif_imagetype($src) == 1) {
							$image = imagecreatefromgif($src);
						} else {
							$image = imagecreatefromjpeg($src);
						} 

						$thumb_width = 150;
						$thumb_height = 150;

						$width = imagesx($image);
						$height = imagesy($image);

						$original_aspect = $width / $height;
						$thumb_aspect = $thumb_width / $thumb_height;

						if ( $original_aspect >= $thumb_aspect )
						{
						   // If image is wider than thumbnail (in aspect ratio sense)
						   $new_height = $thumb_height;
						   $new_width = $width / ($height / $thumb_height);
						}
						else
						{
						   // If the thumbnail is wider than the image
						   $new_width = $thumb_width;
						   $new_height = $height / ($width / $thumb_width);
						}

						$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

						// Resize and crop
						imagecopyresampled($thumb,
						                   $image,
						                   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
						                   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
						                   0, 0,
						                   $new_width, $new_height,
						                   $width, $height);
						imagejpeg($thumb, $filename, 80);

						imagedestroy($image);
						imagedestroy($thumb);

					} else {
						$replacements = array('noimg.jpg','noimg2.jpg','noimg3.jpg');
						$rand_key = array_rand($replacements,1);
						$filename = $replacements[$rand_key];
					}

				}

				if (!file_exists($filename2x) ) {

					$hasimage = true;

					// The image src from mbl is embedded in the description, so we have to fetch it from the DOM, using phpQuery.
					if ($source == 'mbl' || $source == 'ruv') {
						$src = '';
						$texthtml = htmlspecialchars_decode($item->get_description()); 
						$pq = phpQuery::newDocumentHTML($texthtml);
						$img = $pq->find('img:first');
						$src = $img->attr('src');

					// Unfortunately, vbl doesn't include any images in their feed. We have to get it via the DOM.
					} elseif ($source == 'vb') {
						$file = $item->get_permalink();
						$pq = phpQuery::newDocumentFileHTML($file);
						$div = $pq->find('.main_photo');
						$img = $div->find('img');
						$src = 'http://www.vb.is' . $img->attr('src');
					// Visir has the src as a media thumbnail, but it is urlencoded. PHP makes it simple to decode, thankfully.
					} elseif ($source == 'visir') {
						if ($enclosure = $item->get_enclosure()) {  
							$src = rawurldecode($enclosure->get_thumbnail()); 
						} 
					}

					// Rúv serves a tiny image. Their url schema is simple so we just need to replace the url with a new dir.
					// I guess there must be a better way to do this, but this will do for now.
					if ($source == 'ruv') {
						$expl = explode('/', $src);
						$src = 'http://www.ruv.is/files/imagecache/frmynd-stok-460x272/myndir/' . $expl[7];

					}

					if (is_image($src) == true) {
					
						// Rúv sometimes confuses us with png images and I suppose we might run into that from others as well.
						if (exif_imagetype($src) == 3) {
							$image = imagecreatefrompng($src);
						} elseif (exif_imagetype($src) == 1) {
							$image = imagecreatefromgif($src);
						} else {
							$image = imagecreatefromjpeg($src);
						} 

						$thumb_width2x = 300;
						$thumb_height2x = 300;

						$width = imagesx($image);
						$height = imagesy($image);

						$original_aspect = $width / $height;
						$thumb_aspect = $thumb_width2x / $thumb_height2x;

						if ( $original_aspect >= $thumb_aspect )
						{
						   // If image is wider than thumbnail (in aspect ratio sense)
						   $new_height = $thumb_height2x;
						   $new_width = $width / ($height / $thumb_height2x);
						}
						else
						{
						   // If the thumbnail is wider than the image
						   $new_width = $thumb_width2x;
						   $new_height = $height / ($width / $thumb_width2x);
						}

						$thumb = imagecreatetruecolor( $thumb_width2x, $thumb_height2x );

						// Resize and crop
						imagecopyresampled($thumb,
						                   $image,
						                   0 - ($new_width - $thumb_width2x) / 2, // Center the image horizontally
						                   0 - ($new_height - $thumb_height2x) / 2, // Center the image vertically
						                   0, 0,
						                   $new_width, $new_height,
						                   $width, $height);
						imagejpeg($thumb, $filename2x, 80);

						imagedestroy($image);
						imagedestroy($thumb);

					} else {
						$replacements = array('noimg.jpg','noimg2.jpg','noimg3.jpg');
						$rand_key = array_rand($replacements,1);
						$filename = $replacements[$rand_key];
					}

				}

				?> 

				<?php ?>

				<article class="<?php echo $source; ?> <?php echo $catname; ?>-article">

				<div class="newsimage">
					<a href="<?php echo $item->get_permalink(); ?>" target="_blank"><img src="<?php echo $filename ?>"></a>
				</div>


				<p class="meta">

					<span class="item-source">
						
						<?php

							$feed = $item->get_feed();

							//echo $feed->get_description();

							if (strpos($feed->get_title(), 'mbl.is') !== false) {
								echo 'mbl';
							}
							if (strpos($feed->get_title(), 'Vísir') !== false) {
								echo 'vísir';
							}
							if (strpos($feed->get_title(), 'Viðskiptablaðið') !== false) {
								echo 'vb';
							}
							if (strpos($feed->get_permalink(), 'ruv.is') !== false) {
								echo 'rúv';
							}

						?></span>
	
					<span class="item-date">fyrir <?php echo doRelativeDate( $item->get_date( SIMPLEPIE_RELATIVE_DATE ) );?></span>

				</p>
				
				<h2><a href="

				<?php

				/*if ($source == "visir") {
					echo "/cleaner.php?url=";
				}*/

				 echo $item->get_permalink(); ?>"

				 <?php
				 /*
				 	if ($source == "visir") {
						echo "class=\"cboxElement\" ";
					}
				*/
				 ?>

				  target="_blank"><?php echo htmlspecialchars_decode($item->get_title()); ?></a></h2>

				
			  	<a href="<?php echo $item->get_permalink(); ?>" target="_blank" class="excerpt">
				<p><span class="item-category <?php echo $catname; ?>">
						<?php

							if ($catname == 'innlent') {
								echo 'innlent';
							} else if ($catname == 'erlent') {
								echo 'erlent';

							} else if ($catname == 'vidskipti'){
									echo 'viðskipti';
							} else if ($catname == 'daegradvol'){
									echo 'dægradvöl';
							} else if ($catname == 'ithrottir') {
								echo 'íþróttir';
							}
						?></span> <?php

							$texthtml = htmlspecialchars_decode($item->get_description());

							if ($source == 'ruv') {
								$pq2 = phpQuery::newDocumentHTML($texthtml);
								$content = $pq2->find('div:first');
								echo strip_tags($content);
							} else {
								echo strip_tags($texthtml);
							}

						$lastitem = $md5;

						$itemnumber++;
							
						?>
						</p>
					</a>
			</article>



			<?php if ($itemnumber == 3) : ?>

			<?php endif; ?>	

			<?php endif; ?>	

		<?php endforeach; ?>

		<div class="icon-large"><i class="icon-newspaper"></i></div>

		<footer>
			<!--<?php echo "Vinnslutími: ".round((microtime(true) - $time),2)." sekúndur"; ?>-->
		</footer>

		</section>



	</section>		

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>
<!--<script type="text/javascript" src="js/retina.js"></script>-->
<script type="text/javascript" src="js/scripts.js"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-37483608-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>

</html>