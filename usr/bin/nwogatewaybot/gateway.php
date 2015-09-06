<?php

// libs
require 'Curl.php';
use \Curl\Curl;

class gateway{
	private $logfile		= '/var/log/nwogatewaybot/log';
	private $cookiefile		= '/usr/bin/nwogatewaybot/gateway.cck';
	private $gmail_login;		// Gmail login (without @gmail.com)
	private $gmail_password;	// Gmail password
	private $mail;			// full Gmail adress
	private $nwo_character;		// Neverwinter character name
	private $nwo_account;		// Neverwinter account name
	private $nwo_password;		// Neverwinter password
	private $nwo_address;		// Neverwinter gateway address
	
	private $time;			// cached time of init call
	private $curl;			// curl handler
	
	private $idAccount;		// gateway account id
	private $idBrowser;		// gateway browser id
	private $idTicket;		// gateway ticket id
	private $idKey;			// gateway key id
	
	public $gateway_base_addr;	// gateway base address
	public $gateway_login_addr;	// gateway login address
	public $gateway_key_addr;	// gateway key address
	public $gateway_socket_addr;	// gateway socket address
	
	public function __construct($character = false, $data = false){
		// read config
		$this->cfg($data);
		
		// cache time
		$this->time = time();
		
		// connect
		$this->curl = new Curl();
		$this->curl->setCookieJar($this->cookiefile);
		$this->curl->setCookieFile($this->cookiefile);
		$this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
		
		//! curl -v -3 -k https://gateway.playneverwinter.com
		//$this->curl->setOpt(CURLOPT_SSLVERSION, 3);
		// curl -v -k https://gateway.playneverwinter.com
		
		$this->curl->setOpt(CURLOPT_CONNECTTIMEOUT, 3);
		$this->curl->setOpt(CURLOPT_TIMEOUT, 3);
		
		// login
		if(!$this->login($character)){
			throw new Exception('Login failed.');
		}
	}
	
	public function __destruct(){
		$this->curl->close();
	}
	
	private function cfg($data = false){
		require('gateway.cfg');
		
		$this->nwo_character	= $nwo_character;
		$this->nwo_account	= $nwo_account;
		$this->nwo_password	= $nwo_password;
		$this->nwo_address	= $nwo_address;
		$this->gmail_login	= $gmail_login;
		$this->gmail_password	= $gmail_password;
		
		// override default cfg if array passed via __construct
		if($data !== false){
			$this->set($data);
		}
		
		$this->mail			= 		 $this->gmail_login	.'@gmail.com';
		$this->gateway_base_addr	= 'http://'	.$this->nwo_address;
		$this->gateway_login_addr	= 'https://'	.$this->nwo_address;
		$this->gateway_key_addr		= 'http://'	.$this->nwo_address	.'/socket.io/1';
		$this->gateway_socket_addr	= 'http://'	.$this->nwo_address	.'/socket.io/1/xhr-polling/';
	}
	
	public function log($txt){
		if($this->logfile !== false){
			file_put_contents($this->logfile, date('m.d.Y H:i:s', time()) ."\t" .$txt ."\n", FILE_APPEND);
		}
	}
	
	public function error($txt, $id = false){
		$this->log('Error' .($id === false ? '' : ' #' .$id) .': ' .$txt);
	}
	
	private function login($character = false){
		// login
		$this->curl->post($this->gateway_login_addr, array(
			'user' => $this->mail,
			'pw' => $this->nwo_password,
		));
		
		// login check
		if($this->curl->response->result !== "user_login_ok"){
			$this->error('Login: Login failed early', '0.0');
			return 0;
		}
		
		// cache credentials
		$this->idAccount = intval($this->curl->response->idAccount);
		$this->idBrowser = (string)$this->curl->response->idBrowser;
		$this->idTicket = intval($this->curl->response->idTicket);
		
		// ticket checkout
		$this->curl->get($this->gateway_base_addr, array(
			'account' => $this->idAccount,
			'browser' => $this->idBrowser,
			'ticket' => $this->idTicket,
		));
		
		// get key from gate
		$this->curl->get($this->gateway_key_addr, array(
			't' => date('U'),
		));
		$idKey = preg_split("@:@isuDX", (string)$this->curl->response);
		
		// check key
		if(!is_array($idKey)){
			$this->error('Login: Invalid key id', '0.1');
			return 0;
		}
		
		// cache key
		$this->idKey = (string)$idKey[0];
		
		// authentication
		$success = false; // bool flag
		for($i = 0; $i < 5; ++$i){
			// login finalize init
			$this->curl->get($this->get_gateway_socket_addr());
			
			// filter 5::: responses only
			if(preg_match('@^5:::@isuDX', $this->curl->response)){
				$response = json_decode(substr($this->curl->response, 4));
				if(is_object($response)){
					//string(53) "5:::{"name":"Proxy_RequestOneTimeCode","args":[null]}"
					if($response->name === "Proxy_RequestOneTimeCode"){
						$this->log('Guard code request');
						
						// Account Guard requests for a code
						$code = '';
						
						for($j = 0; $j < 10; ++$j){
							// old read from local mail file
							//if(filemtime('/var/mail/user') >= $this->time){
							//	$mails = shell_exec('cat /var/mail/user');
							//	$mail = preg_replace('@^From From @suDX', 'From ', 'From '.end(preg_split('@\nFrom @suDX', $mails))); // last mail
							
							// read code from gmail account
							$inbox = imap_open('{imap.gmail.com:993/imap/ssl}INBOX', $this->mail, $this->gmail_password);
							if($inbox === false){
								$this->error('Gmail: imap_open failed', '1.0');
								continue;
							}
							
							$mails = imap_search($inbox, 'ALL');
							if($mails === false){
								$this->error('Gmail: imap_search failed', '1.1');
								continue;
							}
							
							rsort($mails);
							foreach($mails as $mail_id){
								$overview = imap_fetch_overview($inbox, $mail_id, 0);
								
								if($overview[0]->udate >= $this->time && preg_match('/donotreply@crypticstudios.com/isuDX', $overview[0]->from)){
									$mail = imap_fetchbody($inbox, $mail_id, 2);
									break;
								}
							}
							
							imap_close($inbox);
							preg_match(
								'@To access your account, please submit the following code into the Account Guard dialog box:'
								.'.+?(?<code>\d+).+?'
								.'If you did not attempt to access your account from this computer, we suggest you change your password immediately.@suDX'
								, $mail
								, $m
							);
							
							// code validation
							$code = (string)$m['code'];
							if($code != ''){
								break;
							}
							
							sleep(5);
						}
						
						// code final validation
						if($code == ''){
							$this->error('Guard: Reading guard code from gmail failed', '2.0');
							return 0;
						}
						
						// send code to account guard
						//5:::{"name":"Client_OneTimeCode","args":[{"code":"CODE","name":"NAME"}]}
						$this->post(array(
							'name'	=>	'Client_OneTimeCode',
							'args'	=>	array((object)array(
								'code'	=>	(string)$code,
								'name'	=>	'My Browser',
							)),
						));
						
						continue;
					}
					
					//5:::{"name":"Proxy_LoginSuccess",
					if($response->name === "Proxy_LoginSuccess"){
						$this->log('Login successed');
						
						$success = true;
						break;
					}
				}
			}
			
			sleep(2);
		}
		
		// authentication check
		if(!$success){
			$this->error('Guard: Authentication failed', '2.1');
			return 0;
		}
		
		// choose character
		if($character !== false){
			$this->get('Client_RequestEntity', array(
				'id'		=>	$character,
				'params'	=>	array(),
			));
		}
		
		return 1;
	}
	
	private function get_gateway_socket_addr(){
		return $this->gateway_socket_addr .$this->idKey .'?t=' .date('U');
	}
	
	public function post($data){
		// encode data
		if(is_array($data) || is_object($data)){
			$data = json_encode($data);
		}
		
		// prepare data
		$data = '5:::' .$data;
		
		// send request
		$this->curl->setURL($this->get_gateway_socket_addr());
		$this->curl->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
		$this->curl->setOpt(CURLOPT_POST, true);
		$this->curl->setOpt(CURLOPT_POSTFIELDS, $data);
		$this->curl->exec();
		
		return $this->curl->response == 1;
	}
	
	public function get($name, $args, $request = false, $attempts = 10, $interval = 2){
		if($this->post(array(
			'name'	=>	$name,
			'args'	=>	array((object)$args),
		))){
			if($request === false){
				return true;
			}
			
			for($i = 0; $i < $attempts; ++$i){
				$this->curl->get($this->get_gateway_socket_addr());
				
				if(preg_match('@^5:::@isuDX', $this->curl->response)){
					$response = json_decode(substr($this->curl->response, 4));
					if(is_object($response) && $response->name === $request){
						return $response->args[0];
					}
				}
				
				sleep($interval);
			}
			
			return NULL;
		} else {
			return false;
		}
	}
	
	public function set($key, $value = false){
		if(is_array($key)){
			foreach($key as $key_id => $key_el){
				$this->set($key_id, $key_el);
			}
			
			return;
		}
		if(in_array($key, array(
			'nwo_character',
			'nwo_account',
			'nwo_password',
			'nwo_address',
			'gmail_login',
			'gmail_password',
		))){
			$this->$key = $value;
		}
	}
	
	public function refresh(){
		$this->curl->get($this->get_gateway_socket_addr());
	}
}

?>
