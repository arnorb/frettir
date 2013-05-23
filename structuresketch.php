<?php

// a proposed structure for keeping the data would be something like an array within an array

$feeds = array(
	array('visir','innlent','http://www.visir.is/apps/pbcs.dll/section?Category=FRETTIR01&Template=rss&mime=xml'),
	array('visir','erlent','http://www.visir.is/apps/pbcs.dll/section?Category=FRETTIR02&Template=rss&mime=xml'),
	array('visir','vidskipti','http://www.visir.is/apps/pbcs.dll/section?Category=VIDSKIPTI&Template=rss&mime=xml'),
	array('visir','daegradvol','http://www.visir.is/apps/pbcs.dll/section?Category=IDROTTIR&Template=rss&mime=xml'),
	array('visir','ithrottir','http://www.visir.is/apps/pbcs.dll/section?Category=IDROTTIR&Template=rss&mime=xml'),

	'http://www.mbl.is/feeds/innlent/',
	'http://www.mbl.is/feeds/erlent/',
	'http://www.mbl.is/feeds/folk/',
	'http://www.mbl.is/feeds/sport/',
	'http://www.vb.is/rss/',
	'http://www.ruv.is/rss/innlent',
	'http://www.ruv.is/rss/erlent',
	'http://ruv.is/rss/sport'
	);