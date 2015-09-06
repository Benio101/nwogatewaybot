# nwogatewaybot
PHP–based Neverwinter gateway bot — daemon service to get data from Neverwinter [gateway](http://gateway.playneverwinter.com).

## Requirements
* PHP at `/usr/bin/php`
* crontab*
* Gmail account at `@gmail.com`
* Neverwinter account at `@gmail.com`

\* crontab is recommended to copying tasks, but can be eventually replaced.

## Installation
Pull the following files into their responding directories:
* `/etc/init.d/nwogatewaybot`
  bash init script
* `/usr/bin/nwogatewaybot/Curl.php`
  [php-curl-class](https://github.com/php-curl-class/php-curl-class) [`src/Curl/Curl.php` 3.4.4](https://github.com/php-curl-class/php-curl-class/blob/3.4.4/src/Curl/Curl.php) used to `socket.io` more friendly
* `/usr/bin/nwogatewaybot/gateway.cck`
  cookie file
* `/usr/bin/nwogatewaybot/gateway.cfg`
  config file
* `/usr/bin/nwogatewaybot/gateway.php`
  `gateway` class — bot engine
* `/usr/bin/nwogatewaybot/nwogatewaybot.php`
  bot daemon
* `/usr/bin/nwogatewaybot/tasks` folder
  tasks container
* `/var/log/nwogatewaybot/log`
  log file
* `/var/log/nwogatewaybot/error`
  error log file

## Configuration
edit `/usr/bin/nwogatewaybot/gateway.cfg` configuration file.

## Usage
* `service nwogatewaybot start|stop|restart`
start|stop|restart the nwogatewaybot<br>
* `service nwogatewaybot status`
show if bot is running and how many tasks are in queue<br>
* `service nwogatewaybot clear`
clear all tasks

## Description
1. Bot itself works as a daemon run from `/etc/init.d/nwogatewaybot`, with `/var/run/nwogatewaybot.pid` pid, as a `/usr/bin/nwogatewaybot/nwogatewaybot.php`.<br>
2. Bot uses `/usr/bin/nwogatewaybot/gateway.php` class and initializes `socket.io` connection with gateway address from `/usr/bin/nwogatewaybot/gateway.cfg` `$nwo_address` at `$nwo_character`@`$nwo_account` account using `$nwo_password` password.<br>
3. If Account Guard asks for a pin, login to `$gmail_login`@`gmail.com` occurs with `$gmail_password` password to the `{imap.gmail.com:993/imap/ssl}INBOX` imap and pin is automatically red and submitted.<br>
4. Finally, bot loops, refreshing connection every 20 seconds and sending `Client_Heartbeat` every 60s to keep `socket.io` connection up. Every second, all `/usr/bin/nwogatewaybot/tasks/*` tasks are executed if correct, and then always deleted, even if broken.

## Tasks
Tasks should be made every rational time interval.
It is strongly recommended not to execute tasks too often and copy task files from crontab.

Let's assume our tasks source directory is `/home/nwostatus`.
Task files will be named `*.task.php` to differ them easy from other php files.

### Creating a new task
Let's make a `/home/nwostatus/foo.task.php` file.

### crontabbing the task
To crontab the task, `cp /path/to/source/TASKNAME.task.php` to the `/usr/bin/nwogatewaybot/tasks`, renaming the task to the `ddd-taskname` where `ddd` is a prioroty number and `taskname` is a name of task.<br>

The lower `ddd` number, the higher prioroty, because tasks are executed ASCII alphabetically.<br>
It is strongly recommended to follow the `ddd-taskname` markup and to use the priority reasonably.<br>

Ex. important, weekly task should remain low numbered (like `001-weekly` or so), dailies should remain about `100`+, hourlies `300`+ and minuties `600`+. It is also recommended not to overwrite any task until its done.<br>

Example for `foo.task.php` being executed 27 minutes past every hour with a `950` priority:
```
27  *  * * *   cd /usr/bin/nwogatewaybot && ([ -e tasks/foo.task.php ] || cp /home/nwostatus/foo.task.php tasks/950-foo)
```

## Task PHP code
Let's check if the file is executed internally, we don't wont any additional user–side task requests, only the crontab ones.
```
<?php

if(!defined('STDIN')){
	die();
}
```

Let's add information about when the last update was made to the upcomming HTML cache file:
```

$O = '<h5 class="text-muted text-left">Update: <span class=text-info>__UPDATE__</span></h5>';
```
Now, we will need a `Client_REQUESTID` that is made in the `socket.io`, we can track it using browser network console.<br>
Ex. Entering
```
http://gateway.playneverwinter.com/#char(name@account)/exchange-sellzen
```
we shall see the
```
5:::{"name":"Client_RequestExchangeAccountData","args":[{"id":"ACCOUNTID","params":{}}]}
```
request payload, where `ACCOUNTID` is an account id.<br>
With the following response:
```
5:::{name: "Proxy_ExchangeAccountData", args: [{id: "ACCOUNTID",…}]}
```

Let's expand it:
```
args
	0
		container
				forsaleescrow
				globaldata
					buyprices
						0
							price: 500
							quantity: 4180126
						1
						2
						3
						4
					enabled: 1
					maxmtcprice: 500
					maxplayeropenorders: 5
					maxquantityperorder: 5000
					minmtcprice: 50
					minquantityperorder: 1
					sellprices: []
				logentries
				openorders
				readytoclaimescrow
				readytoclaimmtc
			id: "ACCOUNTID"
			status: "online"
		name: "Proxy_ExchangeAccountData"
```

In this case, `Client_REQUESTID` is a `Client_RequestExchangeAccountData`, `SUBREQID` is an account id and `Proxy_RESPONSEID` is a `Proxy_ExchangeAccountData`.

Then, we add the following bot request:
```
$data = $gateway-> get('Client_REQUESTID', array(
	'id'		=>	'SUBREQID',
	'params'	=>	array(),
), 'Proxy_RESPONSEID');
```

In our case, this will be a
```
$data = $gateway-> get('Client_RequestExchangeAccountData', array(
	'id'		=>	'ACCOUNTID',
	'params'	=>	array(),
), 'Proxy_ExchangeAccountData');
```

And thus, `$data->container->globaldata->buyprices` will contain the full list of exchange: sell ZEN data.
And zeroth entry's `price` will contain current ZEN buy price and its `quantity` contains the amount of ZEN to buy in queue.

This said, we add the following code to our task file:
```
foreach($data->container->globaldata->buyprices as $buyrecords_id => $buyrecords){
	$O .= 'ZEN buy price: ' .$buyrecords->price .'<br>';
	$O .= 'Quantity: ' .$buyrecords->quantity .'<br>';
	
	break;
}
```

Finally, we replace the header update time with current time:
```
$O = str_replace('__UPDATE__', date('d.m.Y H:i'), $O);
```
and save the cache file to a chosen path:
```
file_put_contents('/path/to/file.cch', $O);

?>
```

That's all.

Full example `foo.task.php` file:
```
<?php

if(!defined('STDIN')){
	die();
}

$O = '<h5 class="text-muted text-left">Update: <span class=text-info>__UPDATE__</span></h5>';

$data = $gateway-> get('Client_RequestExchangeAccountData', array(
	'id'		=>	'ACCOUNTID',
	'params'	=>	array(),
), 'Proxy_ExchangeAccountData');

foreach($data->container->globaldata->buyprices as $buyrecords_id => $buyrecords){
	$O .= 'ZEN buy price: ' .$buyrecords->price .'<br>';
	$O .= 'Quantity: ' .$buyrecords->quantity .'<br>';
	
	break;
}

$O = str_replace('__UPDATE__', date('d.m.Y H:i'), $O);
file_put_contents('/path/to/file.cch', $O);

?>
```
To show the ZAX info, just simply:
```
echo file_get_contents('/path/to/file.cch');
```
in another PHP, stable file.

## Troubleshouting
### Syntax checking
Task files are [syntax checked](https://github.com/Benio101/nwogatewaybot/blob/master/usr/bin/nwogatewaybot/nwogatewaybot.php#L28) before execution. Disable syntax checking if neccesary.

### Eval is evil, but have a cute tail
Task files are `eval('?>' .file_get_contents($task))`ed to make the daemon running even if task crashed upon executing. To enable deep debugging, change `eval` to [`include`](https://github.com/Benio101/nwogatewaybot/blob/master/usr/bin/nwogatewaybot/nwogatewaybot.php#L31).

### Hang fix
If some task lasts infinitelly:

1. `service nwogatewaybot stop`
2. `service nwogatewaybot clear` to clear the tasks (including the corrupted one)
3. fix the task, eventually manually copying them to execute immediately after bot starts
4. `service nwogatewaybot start`

### Logs
Trace your log files:
```
/var/log/nwogatewaybot/log
/var/log/nwogatewaybot/error
```
See if everything works fine.

### CURLOPT_SSLVERSION
`CURLOPT_SSLVERSION` used to get changed a several times already (SSL issues).<br>
[Enable](https://github.com/Benio101/nwogatewaybot/blob/master/usr/bin/nwogatewaybot/gateway.php#L45) V3 if, and only if necessary.

### Cookies
After some maintenances, removing cookie cache will be necessary. Type simple:
```
cat <<< "" > /usr/bin/nwogatewaybot/gateway.cck
service nwogatewaybot restart
```
