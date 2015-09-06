<?php

if(!defined('STDIN')){
	die();
}

$O = '<h5 class="text-muted text-left">Update: <span class=text-info>__UPDATE__</span></h5>';

$enchants = array(
	array(
		'name' => 'Silvery',
		'key' => 'Green',
		'icon' => 'Silverglyph',
	),
	array(
		'name' => 'Radiant',
		'key' => 'Red',
		'icon' => 'Radiantsigil',
	),
	array(
		'name' => 'Dark',
		'key' => 'Blue',
		'icon' => 'Darkemblem',
	),
	array(
		'name' => 'Azure',
		'key' => 'Purple',
		'icon' => 'Azurebrand',
	),
	
	array(
		'name' => 'Brutal',
		'key' => 'Brutal',
		'icon' => 'Brutal',
	),
	array(
		'name' => 'Savage',
		'key' => 'Yellow',
		'icon' => 'Savage',
	),
	array(
		'name' => 'Cruel',
		'key' => 'Cruel',
		'icon' => 'Cruel',
	),
	
	array(
		'name' => 'Draconic',
		'key' => 'Dragon',
		'icon' => 'Dragon',
	),
	array(
		'name' => 'Black Ice',
		'key' => 'Blackice',
		'icon' => 'Blackice',
	),
	
	array(
		'name' => 'Tymora',
		'key' => 'Tymora',
		'icon' => 'Tymora',
	),
	array(
		'name' => 'Fey Blessing',
		'key' => 'Feyblessing',
		'icon' => 'Fey',
	),
	array(
		'name' => 'Dragon`s Hoard',
		'key' => 'Gemfinder',
		'icon' => 'Gemfinder',
	),
	array(
		'name' => 'Salvage',
		'key' => 'Salvager',
		'icon' => 'Salvage',
	),
	
	array(
		'name' => 'Tenebrous',
		'key' => 'Special_Lockbox_Nightmare',
		'icon' => 'Brilliantinsignia',
	),
	array(
		'name' => 'Tranquil',
		'key' => 'Tranquil',
		'icon' => 'Tranquil',
	),
);

$O .= '<table class=table style="width: 0; margin-top: 10px;"><tr><th>Rank</th>';
for($i = 1; $i <=12; ++$i){
	$O .= '<th style="text-align: center;">' .$i .'</th>';
}
$O .= '</tr>';

foreach($enchants as $enchant_id => $enchant){
	$O .= '<tr><th style="text-align: left;">' .$enchant['name'] .'</th>';
	for($i = 1; $i <=12; ++$i){
		$v = '_01';
		
		// 8+
		if(in_array($enchant['name'], array(
			'Fey Blessing',
			'Dragon`s Hoard',
			'Tenebrous',
			'Tranquil',
			'Gemfinder'
		)) && $i < 8){
			$O .= '<td style="text-align: center;"></td>';
			continue;
		}
		
		// Blackice: 3+
		if($enchant['name'] == 'Black Ice' && $i < 4){
			$O .= '<td style="text-align: center;"></td>';
			continue;
		}
		
		// Tenebrous fix: 10 = 7
		if($enchant['name'] == 'Tenebrous' && $i == 10){
			$i = 7;
		}
		
		// Salvage: 6 - 8
		if($enchant['name'] == 'Salvage' && ($i < 5 || $i > 7)){
			$O .= '<td style="text-align: center;"></td>';
			continue;
		}
		
		// Fey Blessing: 10-
		if($enchant['name'] == 'Fey Blessing' && $i > 10){
			$O .= '<td style="text-align: center;"></td>';
			continue;
		}
		
		// Salvage image version fix
		if($enchant['name'] == 'Salvage'){
			$v = '';
		}
		
		if(
				!	file_exists('/home/nwostatus/tooltip/' .'T'. $i  .'_Enchantment_'. $enchant['key'])
			||	filemtime('/home/nwostatus/tooltip/' .'T'. $i  .'_Enchantment_'. $enchant['key']) + 86400*14 < time()
		){
			$tooltip = $gateway-> get('Client_RequestItemTooltip', array(
				'id'		=>	'T'. $i  .'_Enchantment_'. $enchant['key'],
				'params'	=>	array(),
			), 'Proxy_ItemTooltip');
			
			if(!empty($tooltip->container->tip))
			file_put_contents('/home/nwostatus/tooltip/T'. $i  .'_Enchantment_'. $enchant['key'], $tooltip->container->tip);
		}
		
		$O .= '<td style="text-align: center;"><span data-tt-item="T'. $i  .'_Enchantment_'. $enchant['key'] .'"><img src="http://gateway.playneverwinter.com/tex/Icon_Inventory_Enchantment_'. $enchant['icon']  .'_T'.  $i .$v .'.png"></span></td>';
		
		// Tenebrous fix: 7 = 10
		if($enchant['name'] == 'Tenebrous' && $i == 7){
			$i = 10;
		}
	}
	$O .= '</tr>';
}

foreach(array(
	array(
		'name' => 'Graycloak`s Insignia',
		'icon' => 'Icon_Inventory_Enchantment_Greycloak_01',
	),
	array(
		'name' => 'Founder`s Glorious',
		'icon' => 'Icon_Inventory_Armorenchant_Founders_T8_01',
	),
) as $enchant_id => $enchant){
	$O .= '<tr><th style="text-align: left;">' .$enchant['name'] .'</th>';
	$O .= '<td style="text-align: center;"><img src="http://gateway.playneverwinter.com/tex/'. $enchant['icon']  .'.png"></td>';
	$O .= '</tr>';
}

$O .= '</table></div>';
$O .= '<div class="tab-pane" id="runes" style="text-align: center;">';
$O .= '<h5 class="text-muted text-left">Update: <span class=text-info>__UPDATE__</span></h5>';

$runes = array(
	array(
		'name' => 'Arcane',
		'key' => 'Green',
		'icon' => 'Arcane',
	),
	array(
		'name' => 'Eldritch',
		'key' => 'Blue',
		'icon' => 'Eldritch',
	),
	array(
		'name' => 'Empowered',
		'key' => 'Red',
		'icon' => 'Empowered',
	),
	array(
		'name' => 'Profane',
		'key' => 'Purple',
		'icon' => 'Profane',
	),
	array(
		'name' => 'Training',
		'key' => 'Yellow',
		'icon' => 'Training',
	),
	
	array(
		'name' => 'Bonding',
		'key' => 'Fine',
		'icon' => 'Bonding',
	),
	array(
		'name' => 'Indominate',
		'key' => 'Special_Lockbox_Nightmare',
		'icon' => 'Special_Lockbox_Nightmare',
	),
	array(
		'name' => 'Serene',
		'key' => 'Serene',
		'icon' => 'Serene',
	),
);

$O .= '<table class=table style="width: 0; margin-top: 10px;"><tr><th>Rank</th>';
for($i = 1; $i <=12; ++$i){
	$O .= '<th style="text-align: center;">' .$i .'</th>';
}
$O .= '</tr>';

foreach($runes as $rune_id => $rune){
	$O .= '<tr><th style="text-align: left;">' .$rune['name'] .'</th>';
	for($i = 1; $i <=12; ++$i){
		$v = '_01';
		
		// 8+
		if(in_array($rune['name'], array(
			'Bonding',
			'Indominate',
			'Serene',
		)) && $i < 8){
			$O .= '<td style="text-align: center;"></td>';
			continue;
		}
		
		if(
				!	file_exists('/home/nwostatus/tooltip/' .'T'. $i  .'_Runestone_'. $rune['key'])
			||	filemtime('/home/nwostatus/tooltip/' .'T'. $i  .'_Runestone_'. $rune['key']) + 86400*14 < time()
		){
			$tooltip = $gateway-> get('Client_RequestItemTooltip', array(
				'id'		=>	'T'. $i  .'_Runestone_'. $rune['key'],
				'params'	=>	array(),
			), 'Proxy_ItemTooltip');
			
			if(!empty($tooltip->container->tip))
			file_put_contents('/home/nwostatus/tooltip/T'. $i  .'_Runestone_'. $rune['key'], $tooltip->container->tip);
		}
		
		$O .= '<td style="text-align: center;"><span data-tt-item="T'. $i  .'_Runestone_'. $rune['key'] .'"><img src="http://gateway.playneverwinter.com/tex/Icon_Inventory_Runestone_'. $rune['icon']  .'_T'.  $i .$v .'.png"></span></td>';
	}
	$O .= '</tr>';
}

$O .= '</table></div>';
$O .= '<div class="tab-pane" id="weapon_enchants" style="text-align: center;">';
$O .= '<h5 class="text-muted text-left">Update: <span class=text-info>__UPDATE__</span></h5>';

$enchants = array(
	array(
		'name' => 'Bilethorn',
		'key' => 'Bilethorn',
		'icon' => 'Bileton',
	),
	array(
		'name' => 'Bronzewood',
		'key' => 'Bronzewood',
		'icon' => 'Bronzewood',
	),
	array(
		'name' => 'Feytouched',
		'key' => 'Feytouched',
		'icon' => 'Feytouched',
	),
	array(
		'name' => 'Flaming',
		'key' => 'Flaming',
		'icon' => 'Flaming',
	),
	array(
		'name' => 'Frost',
		'key' => 'Frost',
		'icon' => 'Frost',
	),
	array(
		'name' => 'Holy Avenger',
		'key' => 'Holyavenger',
		'icon' => 'Holyavenger',
	),
	array(
		'name' => 'Lifedrinker',
		'key' => 'Lifedrinker',
		'icon' => 'Lifedrinker',
	),
	array(
		'name' => 'Lightning',
		'key' => 'Lightning',
		'icon' => 'Lightning',
	),
	array(
		'name' => 'Plague Fire',
		'key' => 'Special_Lockbox_Nightmare',
		'icon' => 'Plaguefire',
	),
	array(
		'name' => 'Terror',
		'key' => 'Terror',
		'icon' => 'Terror',
	),
	array(
		'name' => 'Vorpal',
		'key' => 'Vorpal',
		'icon' => 'Vorpal',
	),
);

$O .= '<table class=table style="width: 0; margin-top: 10px;"><tr><th>Rank</th>';
for($i = 6; $i <=12; ++$i){
	$O .= '<th style="text-align: center;">' .$i .'</th>';
}
$O .= '</tr>';

foreach($enchants as $enchant_id => $enchant){
	$O .= '<tr><th style="text-align: left;">' .$enchant['name'] .'</th>';
	for($i = 6; $i <=12; ++$i){
		$v = '_01';
		
		// Plague Fire: no 6
		if($enchant['name'] == 'Plague Fire' && $i == 6){
			$O .= '<td style="text-align: center;"></td>';
			continue;
		}
		
		if(
				!	file_exists('/home/nwostatus/tooltip/' .'Weapon_Enhancement_'. $enchant['key'] .'_'. $i)
			||	filemtime('/home/nwostatus/tooltip/' .'Weapon_Enhancement_'. $enchant['key'] .'_'. $i) + 86400*14 < time()
		){
			$tooltip = $gateway-> get('Client_RequestItemTooltip', array(
				'id'		=>	'Weapon_Enhancement_'. $enchant['key'] .'_'. $i,
				'params'	=>	array(),
			), 'Proxy_ItemTooltip');
			
			if(!empty($tooltip->container->tip))
			file_put_contents('/home/nwostatus/tooltip/Weapon_Enhancement_'. $enchant['key'] .'_'. $i, $tooltip->container->tip);
		}
		
		$O .= '<td style="text-align: center;"><span data-tt-item="Weapon_Enhancement_'. $enchant['key'] .'_'. $i .'"><img src="http://gateway.playneverwinter.com/tex/Icon_Inventory_Weapenchant_'. $enchant['icon']  .'_T'.  $i .$v .'.png"></span></td>';
	}
	$O .= '</tr>';
}

foreach(array(
	array(
		'name' => 'Brilliant Energy',
		//'icon' => 'Icon_Inventory_Armorenchant_NAME_T8_01',
	),
) as $enchant_id => $enchant){
	$O .= '<tr><th style="text-align: left;">' .$enchant['name'] .'</th>';
	$O .= '<td colspan=2></td><td style="text-align: center;"><img src="http://hydra-media.cursecdn.com/neverwinter.gamepedia.com/3/3d/Brilliant_Energy_Enhancement.png"></td>';
	//$O .= '<td style="text-align: center;"><img src="http://gateway.playneverwinter.com/tex/'. $enchant['icon']  .'.png"></td>';
	$O .= '</tr>';
}

$O .= '</table></div>';
$O .= '<div class="tab-pane" id="armor_enchants" style="text-align: center;">';
$O .= '<h5 class="text-muted text-left">Update: <span class=text-info>__UPDATE__</span></h5>';

$enchants = array(
	array(
		'name' => 'Barkshield',
		'key' => 'Barkskin',
		'icon' => 'Barkskin',
	),
	array(
		'name' => 'Bloodtheft',
		'key' => 'Bloodtheft',
		'icon' => 'Bloodtheft',
	),
	array(
		'name' => 'Briartwine',
		'key' => 'Briartwine',
		'icon' => 'Briartwine',
	),
	array(
		'name' => 'Elven Battle',
		'key' => 'Elvenbattle',
		'icon' => 'Elvenbattle',
	),
	array(
		'name' => 'Fireburst',
		'key' => 'Fireburst',
		'icon' => 'Fireburst',
	),
	array(
		'name' => 'Frostburn',
		'key' => 'Frostburn',
		'icon' => 'Frostburn',
	),
	array(
		'name' => 'Negation',
		'key' => 'Negation',
		'icon' => 'Negation',
	),
	array(
		'name' => 'Soulforged',
		'key' => 'Soulforged',
		'icon' => 'Soulforged',
	),
	array(
		'name' => 'Thunderhead',
		'key' => 'Thunderhead',
		'icon' => 'Thunderhead',
	),
);

$O .= '<table class=table style="width: 0; margin-top: 10px;"><tr><th>Rank</th>';
for($i = 6; $i <=12; ++$i){
	$O .= '<th style="text-align: center;">' .$i .'</th>';
}
$O .= '</tr>';

foreach($enchants as $enchant_id => $enchant){
	$O .= '<tr><th style="text-align: left;">' .$enchant['name'] .'</th>';
	for($i = 6; $i <=12; ++$i){
		$v = '_01';
		
		if(
				!	file_exists('/home/nwostatus/tooltip/' .'Armor_Enhancement_'. $enchant['key'] .'_'. $i)
			||	filemtime('/home/nwostatus/tooltip/' .'Armor_Enhancement_'. $enchant['key'] .'_'. $i) + 86400*14 < time()
		){
			$tooltip = $gateway-> get('Client_RequestItemTooltip', array(
				'id'		=>	'Armor_Enhancement_'. $enchant['key'] .'_'. $i,
				'params'	=>	array(),
			), 'Proxy_ItemTooltip');
			
			if(!empty($tooltip->container->tip))
			file_put_contents('/home/nwostatus/tooltip/Armor_Enhancement_'. $enchant['key'] .'_'. $i, $tooltip->container->tip);
		}
		
		$O .= '<td style="text-align: center;"><span data-tt-item="Armor_Enhancement_'. $enchant['key'] .'_'. $i .'"><img src="http://gateway.playneverwinter.com/tex/Icon_Inventory_Armorenchant_'. $enchant['icon']  .'_T'.  $i .$v .'.png"></span></td>';
	}
	$O .= '</tr>';
}

foreach(array(
	array(
		'name' => 'Loamweave',
		'icon' => 'Icon_Inventory_Armorenchant_Loamweave_T8_01',
	),
) as $enchant_id => $enchant){
	$O .= '<tr><th style="text-align: left;">' .$enchant['name'] .'</th>';
	$O .= '<td colspan=2></td><td style="text-align: center;"><img src="http://gateway.playneverwinter.com/tex/'. $enchant['icon']  .'.png"></td>';
	$O .= '</tr>';
}

$O .= '</table>';
$O = str_replace('__UPDATE__', date('d.m.Y H:i'), $O);

file_put_contents('/home/nwostatus/Info_enchants.cch', $O);

?>
