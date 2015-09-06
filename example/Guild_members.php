<?php

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

// tmp shutdown
//echo $O. '<span style=color:tomato>Error #-1</span>: Temporary forced shutdown';
//die();

// cache
if(file_exists('Guild_members.cch')){
	echo file_get_contents('Guild_members.cch');
} else {
	echo $O. '<span style=color:tomato>Error #0</span>: Cache error<br><br><span style="float:right">Update: '.date('d.m.Y H:i', time()) .'</span>';
}

die();

?>
