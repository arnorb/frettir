RSS fréttalesari fyrir nokkra íslenska miðla
============================================
Þetta er RSS lesari fyrir íslenska miðla.

Hafa skal í huga að þetta er byrjendaverk. Búið er að færa sumt af virkninni í föll, en fæst þó. Þetta er nokkuð týpískt php forrit sem gerir lítið nema að rugla þann sem les kóðann, og þá sérstaklega þann sem skrifaði hann upphaflega.

Aðalskráin er now.php, hún sækir nýjustu fréttirnar og myndirnar sem hanga með þeim. Til að fá einhverja almennilega virkni á vefþjóni þarf annaðhvort að fá cache til að virka með fleiri en einni síðu (sem mér hefur ekki tekist) eða að fara auðveldu leiðina (sem ég gerði) og nota cron til að keyra now.php á 10 mínútna fresti inn í skrá sem heitir index.eithvað (`*/10 * * * * /usr/bin/curl -o /home/frettir/a/index.php --user user:pass http://example.com/now.php`). Þriðji valmöguleikinn er ómögulegur, en hann virkar og það er að keyra now.php í hvert skipti sem síðan er sótt (en það er fáránlegt).

Þar sem myndamappan fyllist fljótt tók ég á það ráð að keyra cron job sem eyðir myndum sem eru eldri en sólarhringsgamlar út úr myndamöppunni (`0 * * * * find /home/frettir/a/img -mtime 1 -exec rm {} \;`)

Allar viðbætur við kóðann eru vel þegnar, skjótið á mig "pull request".

Requirements
------------
Þetta forrit keyrir á [SimplePie](http://simplepie.org/) og allt sem það þarf er það sama og SimplePie. Svo náðu í SimplePie, settu það upp og í skrána `php` í sömu möppu og þú setur þetta fréttaforrit og allt ætti að vera í ágætis lagi.

Leyfi
-----
[MIT](http://opensource.org/licenses/MIT) 