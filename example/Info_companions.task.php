<?php

if(!defined('STDIN')){
	die();
}

$O = '<h5 class="text-muted text-left">Update: <span class=text-info>__UPDATE__</span></h5>';

$StickerBook = $gateway-> get('Client_RequestStickerBook', array(
	'id'		=>	'Companion',
	'params'	=>	array(),
), 'Proxy_StickerBook');
var_dump($StickerBook);

$companions = array();
foreach($StickerBook->container->locations as $location_id => $location){
	foreach($location->items as $item_id => $item){
		array_push($companions, array(
			'cstoreproductid' => $item -> cstoreproductid,
			'description' => $item -> description,
			'displayname' => $item -> displayname,
			'enchantments' => $item -> enchantments,
			'fromcstore' => $item -> fromcstore,
			'fromlocation' => $item -> fromlocation,
			'icon' => $item -> icon,
			'name' => $item -> name,
			'quality' => $item -> quality,
			'scpname' => $item -> scpname,
			'sublocation' => $item -> sublocation,
		));
	}
}

$O .= '<table class=table style="width: 0; margin-top: 10px;"><tr><th>Name</th><th>Icon</th><th>Costume 1</th><th>Costume 2</th><th>Costume 3</th><th>Costume 4</th></tr>';
foreach($companions as $companion_id => $companion){
	$O .= '<tr>';
	$O .= '<td style="text-align: left;">' .$companion['displayname'] .'</td>';
	$O .= '<td style="text-align: center;">';
	$O .= '<div data-tt-item="'. $companion['name'] .'" class="tooltip-target icon-slot xlarge '. $companion['quality'] .' " style="width: 74px; height: 74px;">';
	$O .= '<img width=64 height=64 src="http://gateway.playneverwinter.com/tex/'. $companion['icon'] .'.png"></div></td>';
	
	$shot_name = preg_replace('@^Pet_@suDX', 'Companion_', $companion['scpname']);
	$O .= '<td style="text-align: center;"><img width=64 height=64 src="http://gateway.playneverwinter.com/shot/costume/' .$shot_name .'_01.png"></td>';
	$O .= '<td style="text-align: center;"><img width=64 height=64 src="http://gateway.playneverwinter.com/shot/costume/' .$shot_name .'_02.png"></td>';
	$O .= '<td style="text-align: center;"><img width=64 height=64 src="http://gateway.playneverwinter.com/shot/costume/' .$shot_name .'_03.png"></td>';
	$O .= '<td style="text-align: center;"><img width=64 height=64 src="http://gateway.playneverwinter.com/shot/costume/' .$shot_name .'_04.png"></td>';
	
	// get tooltips
	if(
			!	file_exists('/home/nwostatus/tooltip/' .$companion['name'])
		||	filemtime('/home/nwostatus/tooltip/' .$companion['name']) + 86400*14 < time()
	){
		$tooltip = $gateway-> get('Client_RequestItemTooltip', array(
			'id'		=>	$companion['name'],
			'params'	=>	array(),
		), 'Proxy_ItemTooltip');
		
		if(!empty($tooltip->container->tip))
		file_put_contents('/home/nwostatus/tooltip/' .$companion['name'], $tooltip->container->tip);
	}
}

$O .= '</table>';
$O = str_replace('__UPDATE__', date('d.m.Y H:i'), $O);

file_put_contents('/home/nwostatus/Info_companions.cch', $O);

?>
