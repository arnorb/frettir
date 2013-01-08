
<?php

$time = microtime(true);

require_once('../php/autoloader.php');
require_once('../php/phpQuery.php');
require('relativedate.php');

$feeds = array(
	'http://www.visir.is/apps/pbcs.dll/section?Category=FRETTIR01&Template=rss&mime=xml',
	'http://www.visir.is/apps/pbcs.dll/section?Category=VIDSKIPTI&Template=rss&mime=xml',
	'http://www.visir.is/apps/pbcs.dll/section?Category=LIFID&Template=rss&mime=xml',
	'http://www.visir.is/apps/pbcs.dll/section?Category=FRETTIR02&Template=rss&mime=xml',
	'http://www.mbl.is/feeds/innlent/',
	'http://www.mbl.is/feeds/erlent/',
	'http://www.mbl.is/feeds/folk/',
	'http://www.vb.is/rss/',
	'http://www.ruv.is/rss/innlent',
	'http://www.ruv.is/rss/erlent'
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

	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,700,300' rel='stylesheet' type='text/css'>
	<link href='style.css' rel='stylesheet' type='text/css'>

</head>

<body>


	<section id="wrapper">

		<header>
			<h1>Hvað er að frétta? <span class="alpha">alpha</h1>
		</header>	

		<section id="content">

		<?php foreach ($first_items as $item): ?>

			<article>

				<?php

				$feed = $item->get_feed();

				// Let's start with a default image. If no other is provided, this one is used.
				$src='';

				// figure out the source or provider
				if (strpos($feed->get_title(), 'mbl.is') !== false) {
					$source = 'mbl';
				}
				elseif (strpos($feed->get_title(), 'Vísir') !== false) {
					$source = 'visir';
				}
				elseif (strpos($feed->get_title(), 'Viðskiptablaðið') !== false) {
					$source = 'vb';
				} elseif (strpos($feed->get_permalink(), 'ruv.is') !== false) {
					$source = 'ruv';
				} else {
					$source = 'unknown';
				}

				$md5 = md5($source . $item->get_date('YmdHis') . $item->get_title() . $item->get_description() );

				if ($source == 'mbl' || $source == 'visir' || $source == 'ruv') {
					
					$filename = 'img/' . $md5 . '.jpg';

				// Viðskiptablaðið has no image at all, so we just serve a small screenshot of their header
				} elseif ($source == 'vb') {
					$filename = 'vb.jpg';
				}

				if (!file_exists($filename) ) {

					$hasimage = true;

					// The image src from mbl is embedded in the description, so we have to fetch it from the DOM, using phpQuery.
					if ($source == 'mbl' || $source == 'ruv') {
						$src = '';
						$texthtml = htmlspecialchars_decode($item->get_description()); 
						$pq = phpQuery::newDocumentHTML($texthtml);
						$img = $pq->find('img:first');
						$src = $img->attr('src');

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
						$filename = 'noimg.jpg';
					}

				}

				?> 



				<?php //if(is_image($filename)) : ?>
				<div class="newsimage">
					<a href="<?php echo $item->get_permalink(); ?>" title="Frétt birt þann <?php echo $item->get_local_date('%e. %B %Y kl. %k:%M') ?>" target="_blank"><img src="<?php echo $filename ?>"></a>
				</div>

				<?php //endif; ?>


				<p class="meta">


					<span class="item-title">
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
				
				<h2><a href="<?php echo $item->get_permalink(); ?>" title="Frétt birt þann <?php echo $item->get_local_date('%e. %B %Y kl. %k:%M') ?>" target="_blank"><?php echo htmlspecialchars_decode($item->get_title()); ?></a></h2>

				

				<p><span class="item-category 
							<?php

							$feed = $item->get_feed();

							if ($feed->get_title() == 'Vísir - Innlent' || 
								$feed->get_title() == 'mbl.is - Innlendar fréttir' || 
								(strpos($feed->get_permalink(), 'innlent') !== false)
								) {
								echo 'innlent';
							} else if ($feed->get_title() == 'Vísir - Erlent' || 
								$feed->get_title() == 'mbl.is - Erlendar fréttir' || 
								(strpos($feed->get_permalink(), 'erlent') !== false)
								) {
								echo 'erlent';

							} else if ($feed->get_title() == 'Vísir - Viðskipti' || 
									   $feed->get_title() == 'Viðskiptablaðið'){
									echo 'vidskipti';
							} else if ($feed->get_title() == 'Vísir - Lífið - Yfir' ||
									   $feed->get_title() == 'mbl.is - Fólk'){
									echo 'daegradvol';
							}
						?>
						">
						<?php

							if ($feed->get_title() == 'Vísir - Innlent' ||
								$feed->get_title() ==  'mbl.is - Innlendar fréttir' || 
								(strpos($feed->get_permalink(), 'innlent') !== false)
								) {
								echo 'innlent';
							} else if ($feed->get_title() == 'Vísir - Erlent' || 
								$feed->get_title() == 'mbl.is - Erlendar fréttir' || 
								(strpos($feed->get_permalink(), 'erlent') !== false)
								) {
								echo 'erlent';

							} else if ($feed->get_title() == 'Vísir - Viðskipti' || 
									   $feed->get_title() == 'Viðskiptablaðið'){
									echo 'viðskipti';
							} else if ($feed->get_title() == 'Vísir - Lífið - Yfir' ||
									   $feed->get_title() == 'mbl.is - Fólk'){
									echo 'dægradvöl';
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
							
						?>
						</p>
			</article>

		<?php endforeach; ?>

		<footer>
			<?php echo "Time Elapsed: ".(microtime(true) - $time)."s"; ?>
		</footer>

		</section>



	</section>		

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<!--<script src="scripts.js"></script>-->

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