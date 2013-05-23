<?php


// A function for getting the source name as a string.
// A better implementation would be to use keys instead,
// but an even better implementation would be to use a database.
function getsource ($feed) {

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

		return $source;
	};

// getting the category name
function getcatname ($feed) {
	if ($feed->get_title() == 'Vísir - Innlent' || 
	$feed->get_title() == 'mbl.is - Innlendar fréttir' || 
	(strpos($feed->get_permalink(), 'innlent') !== false)
	) {
		$catname = 'innlent';
	} else if ($feed->get_title() == 'Vísir - Erlent' || 
	$feed->get_title() == 'mbl.is - Erlendar fréttir' || 
	(strpos($feed->get_permalink(), 'erlent') !== false)
	) {
		$catname = 'erlent';

	} else if ($feed->get_title() == 'Vísir - Viðskipti' || 
	$feed->get_title() == 'Viðskiptablaðið'){
		$catname = 'vidskipti';
	} else if ($feed->get_title() == 'Vísir - Lífið - Yfir' ||
 	$feed->get_title() == 'mbl.is - Fólk'){
		$catname = 'daegradvol';
	} else if ($feed->get_title() == 'Vísir - Sport' ||
 	strpos($feed->get_permalink(), 'sport') !== false ||
 	$feed->get_title() == 'mbl.is - Íþróttafréttir') {
		$catname = 'ithrottir';
	}

	return $catname;
}


function generateimage($item,$md5) {
	$feed = $item->get_feed();
}



?>