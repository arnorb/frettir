<?php

		$completeurl = 'http://xmlweather.vedur.is/?op_w=xml&type=obs&lang=is&view=xml&ids=1&time=3h';

		//echo $completeurl;

		$xml = simplexml_load_file($completeurl);

		$station = $xml->station;

		$date = DateTime::createFromFormat('Y-m-d H:i:s', $station->time);
		$datehr = $date->format('G');
		$name = $station->name;
		$vindhradi = $station->F;
		$vindatt = $station->D;
		$hiti = $station->T;
		$lysing = $station->W;

		if ($datehr == 0) {
			$night = true;
		} else {
			$night = false;
		}


		switch ($lysing) {
			case 'Skýjað':
				$icon = 'cloud';
				break;
			
			default:
				break;
		}




	?>
<span><?php echo $name ?> <?php echo ($datehr == 0 ? 'á miðnætti' : 'kl. ' . $datehr . ' í dag'); ?>:</span> <?php echo $lysing ?>, <?php echo $vindatt . $vindhradi ?>, hiti <?php echo $hiti ?>&deg; C