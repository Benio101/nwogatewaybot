<?php

if(!defined('STDIN')){
	die();
}

// css
$O= '<style>'.<<<'HTML'
body, td{
	font: 13px tahoma,arial,verdana,sans-serif;
	color: gray;
	margin: 0;
	padding: 0;
}

td{
	width: 100px;
	max-width: 100px;
	text-overflow: ellipsis;
	white-space: nowrap;
	overflow: hidden;
}
HTML
.'</style>';

$guild = $gateway-> get('Client_RequestGuild', array(
	'id'		=>	'GUILDNAME',
	'params'	=>	array(),
), 'Proxy_Guild');

$O .= '<table>';

$desc = $guild->container->description;		// description
$motd = $guild->container->motd;		// motd
$recr_msg = $guild->container->recruitmessage;	// recruit message
$ranks = $guild->container->ranks;		// ranks
$members = $guild->container->members;		// members

$online = array();
foreach($members as $member_id => $member){
	if($member->online){
		array_push($online, array(
			0 => 7- intval($member->rank),
			'rank' => 1+ intval($member->rank),
			'name' => $member->name,
			'publicaccountname' => substr($member->publicaccountname, 1),
			'officerrank' => $member->officerrank,
		));
	}
}

sort($online);
foreach($online as $member_id => $member){
	$O .= '<tr>';
	$O .= '<td>'. $member['name'] .'</td>';
	$O .= '<td><span style=color:silver>@</span>'. $member['publicaccountname'] .'</td>';
	$O .= '<td><span style=color:silver>'. $member['rank'] .'</span> '. $member['officerrank'] .'</td>';
	$O .= '<tr>';
}

$O .= '</table>';

if(count($members)){
	file_put_contents('/home/nwostatus/Guild_status.cch', $O);
}

?>
