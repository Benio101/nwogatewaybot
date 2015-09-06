<?php

// css
$O= <<<'HTML'
<!doctype html>
<meta charset=UTF-8>
<meta name=robots content=none>
<title>Info</title>
<link href="css/game-support.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet" id="stylesheet">
<link rel=stylesheet href=//benio.me/pub/res/css/bootstrap.min.css type=text/css>
<script src=//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js></script>
<script>window.jQuery||document.write('<script src="//benio.me/res/js/jQuery.js">\x3C/script>');jQuery.ajaxSetup({cache:true});</script>
<script src=//benio.me/pub/res/js/bootstrap.min.js></script>
<style>
:root{cursor: default;}
body{padding: 10px;}
a:hover{text-decoration: none;}
summary{outline: none;}
th,td{
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
}
#tooltip,.tooltip{display:none;opacity:1!important;margin:0;}
</style>
<script>
	$(document).ready(function(){
		$('a[href="#'+location.hash.substr(2)+'"]').tab('show');
		
		$('a[data-toggle="tab"]').click(function(e){
			$(this).tab('show');
			location.hash = '#@' +$(this).attr('href').substr(1);
			e.preventDefault();
			return;
		});
	});
</script>
<script src=tooltip.js></script>
<body>
	<div id="tooltip">
		<div class="dungeonTooltip fancy-box fit-content">
			<div class="fancy-box-top">
				<div class="fancy-box-left"></div>
				<div class="fancy-box-right"></div>
			</div>
			<div class="fancy-box-mid">
				<div class="fancy-box-left"></div>
				<div class="fancy-box-content">
					<div class="mobile-tooltip">
						<div class="tooltip">
							<div id="tooltip-content" class="item"></div>
						</div>
					</div>
				</div>
				<div class="fancy-box-right"></div>
			</div>
			<div class="fancy-box-bot">
				<div class="fancy-box-left"></div>
				<div class="fancy-box-right"></div>
			</div>
		</div>
	</div>
	<h1>Info</h1>
	<br>
	<div>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#zenshop" data-toggle="tab">Zen Shop</a></li>
			<li><a href="#enchants" data-toggle="tab">Enchants</a></li>
			<li><a href="#weapon_enchants" data-toggle="tab">Weapon enchants</a></li>
			<li><a href="#armor_enchants" data-toggle="tab">Armor enchants</a></li>
			<li><a href="#runes" data-toggle="tab">Runes</a></li>
			<li><a href="#companions" data-toggle="tab">Companions</a></li>
		</ul>

		<div class="tab-content">
HTML;

if(file_exists('Info_zenshop.cch')){
	$O .= '<div class="tab-pane active" id="zenshop" style="text-align: center;">';
	$O .= file_get_contents('Info_zenshop.cch');
	$O .= '</div>';
}

if(file_exists('Info_enchants.cch')){
	$O .= '<div class="tab-pane" id="enchants" style="text-align: center;">';
	$O .= file_get_contents('Info_enchants.cch');
	$O .= '</div>';
}

if(file_exists('Info_companions.cch')){
	$O .= '<div class="tab-pane" id="companions" style="text-align: center;">';
	$O .= file_get_contents('Info_companions.cch');
	$O .= '</div>';
}

$O .= '</div></div>';
echo $O;

?>
