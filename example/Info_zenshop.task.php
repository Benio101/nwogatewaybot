<?php

if(!defined('STDIN')){
	die();
}

$O = '<h5 class="text-muted text-left">Update: <span class=text-info>__UPDATE__</span></h5>';

$guild = $gateway-> get('Client_RequestCStore', array(
	'id'		=>	'cstore',
	'params'	=>	array(),
), 'Proxy_CStore');

$products = array();
foreach($guild->container->products as $product_id => $product){
	array_push($products, array(
		0 => intval($product-> id),
		'id' => intval($product-> id),
		'count' => intval($product-> count),
		'featured' => intval($product-> featured),
		'new' => intval($product-> new),
		'quality' => $product-> quality,
		'key' => $product-> key,
		'itemdefnames' => $product-> itemdefnames,
		'description' => $product-> def-> description,
		'displayname' => $product-> def-> displayname,
		'icon' => $product-> def-> icon,
		'iconlarge' => $product-> def-> iconlarge,
		'name' => $product-> def-> name,
		'previews' => $product-> def-> previews,
		'usageinfo' => $product-> def-> usageinfo,
	));
}

foreach($guild->container->userproducts as $product_id => $product){
	foreach($products as $pid => $p){
		if($p['id'] == $product->id){
			$products[$pid] = array_merge($products[$pid], array(
				'discount' => $product-> discount,
				'errorreason' => $product-> errorreason,
				'errortype' => $product-> errortype,
				'fullprice' => $product-> fullprice,
				'prereqsmet' => $product-> prereqsmet,
				'price' => $product-> price,
			));
		}
	}
}

foreach($products as $product_id => $product){
	$O .= '<table class=table style="text-align: center;display: inline-block;vertical-align: top;width:500px;margin-top:5px;padding: 50px;padding-bottom: 0;">';
	$O .= '<tr>';
	$O .= '<td style="text-align: right; border-top: 0;">'. ($product['count'] == 0 ? '' : $product['count'] .' &times;') .'</td>';
	$O .= '<th style="border-top: 0;" colspan=2>'. $product['displayname'] .'</th>';
	if($product['fullprice'] == $product['price']){
		$O .= '<td colspan=3 style="text-align: right; border-top: 0;">'. $product['fullprice'] .' <img src="http://gateway.playneverwinter.com/tex/Currency_Icon_Tiny_Cryptic.png"></td>';
	} else {
		$O .= '<td style="text-align: right; border-top: 0;">'. $product['fullprice'] .' <img src="http://gateway.playneverwinter.com/tex/Currency_Icon_Tiny_Cryptic.png"></td>';
		$O .= '<td style="text-align: right; border-top: 0;">&minus; '. $product['discount'] .' <img src="http://gateway.playneverwinter.com/tex/Currency_Icon_Tiny_Cryptic.png"></td>';
		$O .= '<td style="text-align: right; border-top: 0;">= '. $product['price'] .' <img src="http://gateway.playneverwinter.com/tex/Currency_Icon_Tiny_Cryptic.png"></td></tr>';
	}
	$O .= '<tr><td colspan=3 rowspan=3 style="width: 256px;"><img width=256 src="http://gateway.playneverwinter.com/tex/'. $product['iconlarge'] .'"></td>';
	$O .= '<td style="text-align:right;vertical-align:bottom;padding-top: 0;padding-bottom: 0;">id';
	$O .= '<td style="text-align:left;vertical-align:bottom;padding-top: 0;padding-bottom: 0;">'. $product['id'] .'</td>';
	
	// cleanUpTooltip
	$product['usageinfo'] = preg_replace('@(<img[^>]+)src=([^ >]+)@isuDX', '$1src="http://gateway.playneverwinter.com/tex/$2', $product['usageinfo']);
	$product['description'] = preg_replace('@(<img[^>]+)src=([^ >]+)@isuDX', '$1src="http://gateway.playneverwinter.com/tex/$2', $product['description']);
	$product['usageinfo'] = preg_replace('@<font style=([^ >]+)@isuDX', '<span class="$1"', $product['usageinfo']);
	$product['description'] = preg_replace('@<font style=([^ >]+)@isuDX', '<span class="$1"', $product['description']);
	$product['usageinfo'] = preg_replace('@<font@isuDX', '<span', $product['usageinfo']);
	$product['description'] = preg_replace('@<font@isuDX', '<span', $product['description']);
	$product['usageinfo'] = preg_replace('@</font@isuDX', '</span', $product['usageinfo']);
	$product['description'] = preg_replace('@</font@isuDX', '</span', $product['description']);
	$product['usageinfo'] = preg_replace('@align=(right|left)@isuDX', 'class="align-$1"', $product['usageinfo']);
	$product['description'] = preg_replace('@align=(right|left)@isuDX', 'class="align-$1"', $product['description']);
	$product['usageinfo'] = preg_replace('@(<span[^>]+)color=@isuDX', '$1class=', $product['usageinfo']);
	$product['description'] = preg_replace('@(<span[^>]+)color=@isuDX', '$1class=', $product['description']);
	$product['usageinfo'] = preg_replace('@<table>@isuDX', '<table class="tooltipTable">', $product['usageinfo'], 1);
	$product['description'] = preg_replace('@<table>@isuDX', '<table class="tooltipTable">', $product['description'], 1);
	$product['usageinfo'] = preg_replace('@<table>@isuDX', '<table class="gemslots">', $product['usageinfo'], 1);
	$product['description'] = preg_replace('@<table>@isuDX', '<table class="gemslots">', $product['description'], 1);
	$product['usageinfo'] = preg_replace('@border=4@isuDX', 'class="tableSets"', $product['usageinfo']);
	$product['description'] = preg_replace('@border=4@isuDX', 'class="tableSets"', $product['description']);
	$product['usageinfo'] = preg_replace('@width=100%@isuDX', '', $product['usageinfo']);
	$product['description'] = preg_replace('@width=100%@isuDX', '', $product['description']);
	
	$item_name = null;
	//if(!empty($product['itemdefnames'])){
		// add item tooltips from descriptions
		if(preg_match_all('@data-tt-item="([^"]+)"@isuDX', $product['usageinfo'] .$product['description'], $m)){
			foreach($m[1] as $entry){
				array_push($product['itemdefnames'], $entry);
			}
		}
		
		// get tooltips
		foreach($product['itemdefnames'] as $item_id => $item){
			if(
					!	file_exists('/home/nwostatus/tooltip/' .$item)
				||	filemtime('/home/nwostatus/tooltip/' .$item) + 86400*14 < time()
			){
				$tooltip = $gateway-> get('Client_RequestItemTooltip', array(
					'id'		=>	$item,
					'params'	=>	array(),
				), 'Proxy_ItemTooltip');
				
				if(!empty($tooltip->container->tip))
				file_put_contents('/home/nwostatus/tooltip/' .$item, $tooltip->container->tip);
			}
			
			if($item_name == null){
				$item_name = $item;
			}
		}
	//}
	
	$O .= '<td rowspan=3><div data-tt-item="'. $item_name .'" class="tooltip-target icon-slot xlarge '. $product['quality'] .' " style="width: 74px; height: 74px;">';
	$O .= '<img width=64 height=64 src="http://gateway.playneverwinter.com/tex/'. $product['icon'] .'"></div></td></td></tr>';
	$O .= '<tr><td style="vertical-align:middle;text-align: right;padding-top: 0;padding-bottom: 0;border:0;">New</td>';
	$O .= '<td style="vertical-align:middle;text-align: left;padding-top: 0;padding-bottom: 0;border:0;">'. ($product['new'] ? 'Yes' : 'No') .'</td></tr>';
	$O .= '<tr><td style="vertical-align:top;text-align: right;padding-top: 0;padding-bottom: 0;border:0;">Featured</td>';
	$O .= '<td style="vertical-align:top;text-align: left;padding-top: 0;padding-bottom: 0;border:0;">'. ($product['featured'] ? '<br>Yes' : 'No') .'</td></tr>';
	$O .= '<tr><td colspan=6 style="text-align: left; padding: 0;margin: 0;"><span style="text-align: left; display: inline; width: 256px; white-space: pre-line;">'. $product['usageinfo'] .'</span></td></tr>';
	$O .= '<tr><td colspan=6 style="text-align: left; padding: 0;margin: 0;"><span style="text-align: left; display: inline; width: 256px; white-space: pre-line;">'. $product['description'] .'</span></td></tr>';
	$O .= '</table>';
}

$O = str_replace('__UPDATE__', date('d.m.Y H:i'), $O);
file_put_contents('/home/nwostatus/Info_zenshop.cch', $O);

?>
