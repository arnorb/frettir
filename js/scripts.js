/*$(document).ready(function(){
	 Colorbox stuff 
	$('a.cboxElement').colorbox({height:"100%",fixed:true,transition:"fade",title:""});

	$("<br><br>").replaceWith("</p><p>");

	$().bind('cbox_complete', function(){
	        $("#cboxTitle").hide();
	});
});*/

if ($.cookie('innlent') === 'false') {
	$('.innlent-article').hide();
	$('.innlent-button').addClass('button-gray');
} 
if ($.cookie('erlent') === 'false') {
	$('.erlent-article').hide();
	$('.erlent-button').addClass('button-gray');
} 
if ($.cookie('vidskipti') === 'false') {
	$('.vidskipti-article').hide();
	$('.vidskipti-button').addClass('button-gray');
} 
if ($.cookie('daegradvol') === 'false') {
	$('.daegradvol-article').hide();
	$('.daegradvol-button').addClass('button-gray');
} 


$('.innlent-button').click(function(){
	$('.innlent-article').toggle();
	$(this).toggleClass('button-gray');

	if ($(this).hasClass('button-gray')) {
		$.cookie('innlent', false, { expires: 365 });
	} else {
		$.cookie('innlent', true, { expires: 365 });
	}

});

$('.erlent-button').click(function(){
	$('.erlent-article').toggle();
	$(this).toggleClass('button-gray');

	if ($(this).hasClass('button-gray')) {
		$.cookie('erlent', false, { expires: 365 });
	} else {
		$.cookie('erlent', true, { expires: 365 });
	}
});

$('.vidskipti-button').click(function(){
	$('.vidskipti-article').toggle();
	$(this).toggleClass('button-gray');

	if ($(this).hasClass('button-gray')) {
		$.cookie('vidskipti', false, { expires: 365 });
	} else {
		$.cookie('vidskipti', true, { expires: 365 });
	}
});

$('.daegradvol-button').click(function(){
	$('.daegradvol-article').toggle();
	$(this).toggleClass('button-gray');

	if ($(this).hasClass('button-gray')) {
		$.cookie('daegradvol', false, { expires: 365 });
	} else {
		$.cookie('daegradvol', true, { expires: 365 });
	}
});



