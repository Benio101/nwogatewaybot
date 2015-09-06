<?php

if(!defined('STDIN')){
	die();
}

// css
$O= <<<'HTML'
<!doctype html>
<meta charset=UTF-8>
<meta name=robots content=none>
<title>__NAME__</title>
<link rel=stylesheet href=//benio.me/pub/res/css/bootstrap.min.css type=text/css>
<link rel=stylesheet href=//benio.me/pub/res/css/fixedheadertable.css type=text/css>
<script src=//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js></script>
<script>window.jQuery||document.write('<script src="//benio.me/res/js/jQuery.js">\x3C/script>');jQuery.ajaxSetup({cache:true});</script>
<script src=//benio.me/pub/res/js/bootstrap.min.js></script>
<script src=//benio.me/pub/res/js/jquery.fixedheadertable.min.js></script>
<script src=//benio.me/pub/res/js/jquery.tablesorter.min.js></script>
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
table.tablesorter thead tr .header {
	background-image: url(//benio.me/pub/res/img/jquery.tablesorter.bg.gif);
	background-repeat: no-repeat;
	background-position: center right;
	cursor: pointer;
}
table.tablesorter thead tr .headerSortUp {
	background-image: url(//benio.me/pub/res/img/jquery.tablesorter.asc.gif);
}
table.tablesorter thead tr .headerSortDown {
	background-image: url(//benio.me/pub/res/img/jquery.tablesorter.desc.gif);
}
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
<script>$(function(){$('.table-fixed').fixedHeaderTable();});</script>
<script>$(function(){$('.tablesorter').tablesorter();});</script>
<body>
	<h1>__NAME__</h1>
	<h5 class=text-muted>Aktualizacja: <span class=text-info>__UPDATE__</span></h5>
	<br><div>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#info" data-toggle="tab">Info</a></li>
			<li><a href="#ranks" data-toggle="tab">Ranks</a></li>
			<li><a href="#members" data-toggle="tab">Members</a></li>
			<li><a href="#list" data-toggle="tab">List</a></li>
			<li><a href="#activity" data-toggle="tab">Activity</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="info">
				<table class="table" style="width: auto">
					<tr>
						<th style=border-top:none class=text-right><abbr title="World Wide Web">WWW</abbr></th>
						<td style=border-top:none colspan=3><a href="http://__WEBSITE__">__WEBSITE__</a></td>
						
						<th style=border-top:none class=text-right style=border-top:none>Funding date</th>
						<td style=border-top:none>__DATE__</td>
					</tr>
					<tr><th class=text-right><abbr title="Message of the Day">MotD</abbr></th><td colspan=5>__MOTD__</td></tr>
					<tr><th class=text-right>Description</th><td colspan=5>__DESC__</td></tr>
					<tr><th class=text-right>Recruiting<br>message</th><td colspan=5>__RECR_MSG__</td></tr>
					<tr>
						<th class=text-right>Members</th><td>__TOTAL_MEMBERS__</td>
						<th class=text-right>Minimum level</th><td>__MIN_LVL__</td>
						<th class=text-right>id</th><td>__ID__</td>
					</tr>
				</table>
			</div>
HTML;

$guild = $gateway-> get('Client_RequestGuild', array(
	'id'		=>	'GUILDNAME',
	'params'	=>	array(),
), 'Proxy_Guild');

$desc = preg_replace('@(https?://)([^\s]+)@isuDX', '<a href="\1\2">\2</a>', $guild->container->description);
$desc = preg_replace('@((?:\n[\r\s]*){2,})([^\n]+):([\r\s]*\n)@isuDX', '\1<b>\2</b>\3', $desc);
$desc = nl2br($desc, false);

$O = str_replace('__UPDATE__', date('d.m.Y H:i'), $O);
$O = str_replace('__NAME__', $guild->container->name, $O);
$O = str_replace('__DATE__', date('d.m.Y', strtotime($guild->container->createdon)), $O);
$O = str_replace('__WEBSITE__', preg_replace('@^https?://@isuDX', '', $guild->container->website), $O);
$O = str_replace('__ID__', $guild->container->id, $O);
$O = str_replace('__MOTD__', $guild->container->motd, $O);
$O = str_replace('__DESC__', $desc, $O);
$O = str_replace('__RECR_MSG__', nl2br(preg_replace('@(https?://)([^\s]+)@isuDX', '<a href="\1\2">\2</a>', $guild->container->recruitmessage), false), $O);
$O = str_replace('__MIN_LVL__', $guild->container->minlevelrecruit, $O);
$O = str_replace('__TOTAL_MEMBERS__', $guild->container->totalmembers, $O);

$ranks = $guild->container->ranks;
$members = $guild->container->members;

$O .= <<<'HTML'
<div class="tab-pane" id="ranks">
<table class="table" style="width: auto">
<thead><tr><th>id</th><th>Rank</th><th>Permissions</th></tr></thead>
<tbody>
HTML;

$guild_ranks = array();
foreach($ranks as $rank_id => $rank){
	$perms = array();
	foreach($rank-> permissions as $perm_id => &$perm){
		//$json = @json_decode(file_get_contents('http://__SITENAME__/nwodb/ClientMessagesEnglish.php?translate=Staticdefine_Guildrankpermissions_' .$perm));
		//if($json-> success){
		//	$perm_name = preg_replace('@^{k:guild_title}@suDX', 'Guild', $json-> value);
		//	$perm_name = str_replace('{k:guild_title}', 'guild', $perm_name);
		//	
		//	$perms[]= $perm_name;
		//} else {
			if($perm != '(null)'){
				$perms[]= '<span class="text-muted">' .$perm .'</span>';
			}
		//}
	}
	
	array_push($guild_ranks, array(
		0 => 1+ intval($rank-> rank),
		'name' => $rank-> name,
		'perms' => '<ol><li>' .implode('</li><li>', $perms) .'</ol>',
	));
}

sort($guild_ranks);
foreach($guild_ranks as $rank_id => $rank){
	$O .= '<tr>';
	$O .= '<th>'. $rank[0] .'</th>';
	$O .= '<td>'. $rank['name'] .'</td>';
	$O .= '<td>'. $rank['perms'] .'</td>';
	$O .= '<tr>';
}

$O .= '</tbody></table></div>';

$O .= <<<'HTML'
<div class="tab-pane" id="members" style=padding-top:5px;line-height:1>
HTML;

$guild_members = array();
foreach($members as $member_id => $member){
	array_push($guild_members, array(
		0 => 1- intval($member-> online),
		1 => 70- intval($member-> level),
		2 => 7- intval($member-> rank),
		3 => $member-> name,
		4 => $member-> publicaccountname,
		'online' => intval($member-> online),
		'rank' => 1+ intval($member-> rank),
		'officerrank' => $member-> officerrank,
		'name' => $member-> name,
		'publicaccountname' => substr($member-> publicaccountname, 1),
		'classtype' => $member-> classtype,
		'guildcontribution1' => $member-> guildcontribution1,
		'guildcontribution2' => $member-> guildcontribution2,
		'id' => $member-> id,
		'joined' => $member-> joined,
		'level' => $member-> level,
		'lfg' => $member-> lfg,
		'location' => $member-> location,
		'logouttime' => $member-> logouttime,
		'officercomment' => $member-> officercomment,
		'publiccomment' => $member-> publiccomment,
		'status' => $member-> status,
	));
}

sort($guild_members);
foreach($guild_members as &$member){
	$o = <<<'HTML'
<table class="table table-condensed" style="margin: 0; display: inline-block; width: 512px; height: 287px; border: 1px solid #eee;">
	<tr>
		<th style=width:138px;max-width:138px;height:33px;border-top:none; colspan=2 class="active text-right"><strong>__NAME__</strong></th>
		<td style="width:118px;max-width:118px;height:33px;border-top:none;border-right: 1px solid #eee;" class="active"><small class="text-muted">@</small>__HANDLE__</td>
		<td style=width:256px;max-width:256px;height:256px;border-top:none; rowspan=8>
			<img width=246 height=246 src="http://gateway.playneverwinter.com/shot/body/__ID__.png" onError="this.onerror=null; this.src='img/default_body.png';">
		</td>
	</tr>
	<tr>
		<th style=width:100px;max-width:100px;height:30px class=text-right>Lvl</th>
		<td style=width:38px;max-width:38px;height:30px class=text-center>__LEVEL__</td>
		__ONLINE__
	</tr>
	<tr>
		<th style=width:100px;max-width:100px;height:36px class=text-right>Klasa</th>
		<td style=width:38px;max-width:38px;height:36px class=text-center><img width=20 height=26 src="img/Icon_Build___CLASSTYPE___Tiny.png"></td>
		<td style="width:118px;max-width:118px;height:36px;border-right: 1px solid #eee;">__CLASSNAME__</td>
	</tr>
	<tr>
		<th style=width:100px;max-width:100px;height:30px class=text-right>Lokacja</th>
		<td style="width:156px;max-width:156px;height:30px;border-right: 1px solid #eee;" colspan=2>__LOCATION__</td>
	</tr>
	<tr>
		<th style=width:100px;max-width:100px;height:30px class="warning text-right">Ranga</th>
		<td style=width:38px;max-width:38px;height:30px class="warning text-center">__RANK__</td>
		<td style="width:118px;max-width:118px;height:30px;border-right: 1px solid #eee;" class="warning">__OFFICERRANK__</td>
	</tr>
	<tr>
		<th style=width:100px;max-width:100px;height:30px class="warning text-right">Staż</th>
		<td style="width:156px;max-width:156px;height:30px;border-right: 1px solid #eee;" class="warning" colspan=2>__SENIORITY__</td>
	</tr>
	<tr>
		<th style=width:100px;max-width:100px;height:30px class="text-right">__LOG_IN_OUT__</th>
		<td style="width:156px;max-width:156px;height:30px;border-right: 1px solid #eee;" colspan=2>__LOGOUTTIME__</td>
	</tr>
	<tr>
		<th style=width:100px;max-width:100px;height:30px class="text-right">id</th>
		<td style="width:156px;max-width:156px;height:30px;border-right: 1px solid #eee;" colspan=2>__ID__</td>
	</tr>
	<tr>
		<th style=width:100px;max-width:100px;height:30px class="text-right">Status</th>
		<td style=width:38px;max-width:38px;height:30px;vertical-align:middle; class=text-center><img width=16 height=16 src="img/lfg___LFG__.png"></td>
		<td style="width:374px;max-width:374px;height:30px;" class=warning colspan=2>__STATUS__</td>
	</tr>
</table>
HTML;
	
	$classname = $member['classtype'];
	//$json = @json_decode(file_get_contents('http://__SITENAME__/nwodb/ClientMessagesEnglish.php?translate=Characterclass.' .$member['classtype'] .'.Displayname'));
	//if($json-> success){
	//	$classname = $json-> value;
	//}
	$member['classname'] = $classname;
	
	$seniority = intval((time() - strtotime($member['joined']))/60/60/24);
	$member['seniority'] = $seniority;
	if($seniority == 0){
		$seniority = date('H:i', strtotime($member['joined']));
	} else {
		$seniority = $seniority .' ' .($seniority == 1 ? 'day' : 'days');
	}
	
	$onoffline = intval((time() - strtotime($member['logouttime']))/60/60/24);
	$member['onoffline'] = $onoffline;
	if($onoffline == 0){
		$onoffline = date('H:i', strtotime($member['logouttime']));
	} else {
		$onoffline = $onoffline .' ' .($seniority == 1 ? 'day' : 'days');
	}
	
	$status = htmlentities(preg_replace('@</?[^<>]+>@isuDX', '', $member['publiccomment']), ENT_NOQUOTES | ENT_HTML5);
	$member['status'] = $status;
	
	$online = ($member['online']
		? '<td style=width:118px;max-width:118px;height:30px;text-transform:uppercase;color:green class="text-center">Online</td>'
		: '<td style="width:118px;max-width:118px;height:30px;text-transform:uppercase;color:red;border-right: 1px solid #eee;" class="text-center">Offline</td>'
	);
	
	$o = str_replace('__ID__', $member['id'], $o);
	$o = str_replace('__NAME__', $member['name'], $o);
	$o = str_replace('__HANDLE__', $member['publicaccountname'], $o);
	$o = str_replace('__CLASSTYPE__', $member['classtype'], $o);
	$o = str_replace('__CLASSNAME__', $classname, $o);
	$o = str_replace('__LEVEL__', $member['level'], $o);
	$o = str_replace('__RANK__', $member['rank'], $o);
	$o = str_replace('__OFFICERRANK__', $member['officerrank'], $o);
	$o = str_replace('__ONLINE__', $online, $o);
	$o = str_replace('__LOCATION__', $member['location'], $o);
	$o = str_replace('__SENIORITY__', $seniority, $o);
	$o = str_replace('__LOG_IN_OUT__', $member['online'] ?'Online' :'Offline', $o);
	$o = str_replace('__LOGOUTTIME__', $onoffline, $o);
	$o = str_replace('__STATUS__', $status, $o);
	$o = str_replace('__LFG__', intval(in_array($member['lfg'], array('Open', 'RequestOnly'))), $o);
	
	$O .= $o;
}

$O .= '</div>';
$O .= <<<'HTML'
<div class="tab-pane" id="activity" style=padding-top:5px;line-height:1>
<table class="table table-condensed" style="margin: 0; display: inline-block; border: 1px solid #eee;">
<thead><tr><th colspan=2>Date</th><th>Type</th><th>Activity</th></tr></thead><tbody>
HTML;

$activity = $guild->container->activityentries;
$entries = array();
$last_day = null;
$last_days = 0;
$id = 0;
sort($entries);
foreach($activity as $activity_id => $entry){
	$day = date('d.m.Y', strtotime($entry-> time));
	array_push($entries, array(
		0 => strtotime($entry-> time),
		1 => ++$id,
		'string' => $entry-> string,
		'day' => $day,
		'time' => date('H:i:s', strtotime($entry-> time)),
		'type' => $entry-> type,
		'rowspan' => ($last_day === $day ? ++$last_days : $last_days = 1),
	));
	$last_day = $day;
}

rsort($entries);
$last = 0;
$id = 0;
foreach($entries as $entry_id => $entry){
	$O .= '<tr>';
	
	if($entry['rowspan'] >= $last){
		$O .= '<th rowspan='. $entry['rowspan'] .'>' .$entry['day'] .'</th>';
	}
	$last = $entry['rowspan'];
	
	$entry['type'] = '<span style=color:#337ab7>' .$entry['type'] .'</span>';
	$O .= '<td>' .$entry['time'] .'</td><td>' .$entry['type'] .'</td><td>' .$entry['string'] .'</td></tr>';
}

$O .= '</tbody></table></div>';

$O .= <<<'HTML'
<div class="tab-pane" id="list" style=padding-top:5px;line-height:1>
<table class="table table-striped table-striped-column table-bordered table-hover table-grid tablesorter" style="width: 100%; margin: 0; display: inline-block; border: 1px solid #eee;">
<thead><tr>
<th style="padding-right: 20px;">Nick</th>
<th style="padding-right: 20px;">Handle</th>
<th style="padding-right: 20px;">Lvl</th>
<th style="padding-right: 20px;">Online</th>
<th style="padding-right: 20px;">Class</th>
<th style="padding-right: 20px;">Location</th>
<th style="padding-right: 20px;">#</th>
<th style="padding-right: 20px;">Rank</th>
<th style="padding-right: 20px;">Seniority</th>
<th style="padding-right: 20px;">Offline</th>
<th style="padding-right: 20px;">id</th>
<th style="padding-right: 20px;">Status</th>
</tr></thead><tbody>
HTML;

foreach($guild_members as $member_id => $member){
	$offline = $member['onoffline'];
	if($member['onoffline']>=30){
		$offline = '<span style=color:red>'.$member['onoffline'].'</span>';
	} else
	if($member['onoffline']>=14){
		$offline = '<span style=color:tomato>'.$member['onoffline'].'</span>';
	} else
	if($member['onoffline']>=7){
		$offline = '<span style=color:darkorange>'.$member['onoffline'].'</span>';
	}
	
	$O .= '<tr>';
	$O .= '<td>' .$member['name'] .'</td>';
	$O .= '<td>' .$member['publicaccountname'] .'</td>';
	$O .= '<td style="text-align: right;">' .$member['level'] .'</td>';
	$O .= '<td>' .($member['online'] ?'<span style=color:green>Online</span>' :'Offline') .'</td>';
	$O .= '<td>' .$member['classname'] .'</td>';
	$O .= '<td>' .$member['location'] .'</td>';
	$O .= '<td style="text-align: right;">' .$member['rank'] .'</td>';
	$O .= '<td>' .$member['officerrank'] .'</td>';
	$O .= '<td style="text-align: right;">' .$member['seniority'] .'</td>';
	$O .= '<td style="text-align: right;">' .$offline .'</td>';
	$O .= '<td style="text-align: right;">' .$member['id'] .'</td>';
	$O .= '<td>' .$member['status'] .'</td>';
	$O .= '</tr>';
}

$O .= '</tbody></table></div>';
$O .= '</div>';
$O .= '</div></div>';

if(count($members)){
	file_put_contents('/home/nwostatus/Guild_members.cch', $O);
}

?>
