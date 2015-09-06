<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

if(!defined('STDIN')){
	header($_SERVER['SERVER_PROTOCOL'] .' 404 Not Found');
	die();
}

require __DIR__ .'/gateway.php';
while(true){
	try{
		require __DIR__ .'/gateway.cfg';
		$gateway = new gateway($nwo_character .'@' .$nwo_account);
	} catch(Exception $e){
		$gateway-> error('Daemon: ' .$e-> getMessage(), '11.0');
		
		sleep(60);
		continue;
	}

	while(true){
		for($i = 0; $i < 60; ++$i){
			foreach(glob(__DIR__ .'/tasks/*') as $task){
				if(is_file($task)){
					// syntax checking (prefiltering ill tasks)
					if(substr(exec('php -l ' .$task), 0, 28) === 'No syntax errors detected in'){
						$gateway-> log('Daemon: Running task ' .preg_replace('@^tasks/(.*)$@suDX', '$1', $task));
						
						//include $task;
						if(eval('?>' .file_get_contents($task)) === false){
							$gateway-> error('Daemon: Task ' .preg_replace('@^tasks/(.*)$@suDX', '$1', $task) .' failed (Eval Error).', '11.1');
						} else {
							$gateway-> log('Daemon: Task ' .preg_replace('@^tasks/(.*)$@suDX', '$1', $task) .' finished.');
						}
					} else {
						$gateway-> error('Daemon: Skipping task ' .preg_replace('@^tasks/(.*)$@suDX', '$1', $task) .' (Parsing Error).', '11.2');
					}
					
					unlink($task);
				}
			}
			
			if($i % 20 === 10){
				$gateway-> refresh();
			}
			
			sleep(1);
		}
		
		$alive = false;
		for($i = 0; $i < 5; ++$i){
			$alive = $gateway-> post('{"name":"Client_Heartbeat"}');
			if($alive){
				break;
			}
			
			sleep(2);
		}
		
		if(!$alive){
			$gateway-> error('Daemon: Connection died.', '11.3');
			break;
		}
	}
	
	sleep(60);
}

?>
