<?php
// Include the SimplePie library
// For 1.0-1.2:
#require_once('simplepie.inc');
// For 1.3+:
require_once('../php/autoloader.php');
 
// Create SimplePie objects for each feed, at least one with different settings
$digg = new SimplePie();
$digg->set_feed_url('http://digg.com/rss/index.xml');
 
$tuaw = new SimplePie();
$tuaw->set_feed_url('http://feeds.tuaw.com/weblogsinc/tuaw');
$tuaw->set_favicon_handler('handler_image.php');
$tuaw->init();
 
$uneasy = new SimplePie();
$uneasy->set_feed_url('http://feeds.uneasysilence.com/uneasysilence/blog');
 
// Let's merge them together.
$merged = SimplePie::merge_items(array($digg, $tuaw, $uneasy));
 
// Since we're using different feeds with different settings, let's set the header manually.
header('Content-type:text/html; charset=utf-8');
 
// Begin our XHTML markup
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">	
<head>
	<title>Awesome feeds</title>
	<link rel="stylesheet" href="../demo/for_the_demo/simplepie.css" type="text/css" media="screen" charset="utf-8" />
 
	<style type="text/css">
	h4.title {
		/* We're going to add some space next to the titles so we can fit the 16x16 favicon image. */
		background-color:transparent;
		background-repeat:no-repeat;
		background-position:0 1px;
		padding-left:20px;
	}
	</style>
</head>
<body>
	<div id="site">
 
		<h1>Awesome feeds</h1>
 
		<?php
		// Instead of calling $feed->get_items(), we'll use the $merged variable we created earlier.
		foreach ($merged as $item):
		?>
 
		<div class="chunk">
 
			<?php /* Here, we'll use the $item->get_feed() method to gain access to the parent feed-level data for the specified item. */ ?>
			<h4 class="title" style="background-image:url(<?php $feed = $item->get_feed(); echo $feed->get_favicon(); ?>);"><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a></h4>
 
			<?php echo $item->get_content(); ?>
 
			<p class="footnote">Source: <a href="<?php $feed = $item->get_feed(); echo $feed->get_permalink(); ?>"><?php $feed = $item->get_feed(); echo $feed->get_title(); ?></a> | <?php echo $item->get_date('j M Y | g:i a T'); ?></p>
 
		</div>
 
		<?php endforeach; ?>
 
	</div>
</body>
</html>