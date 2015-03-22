<?php
use Amp\Artax\Client;
use Amp\Artax\FormBody;
use Amp\Artax\Request;

class ClientFactory {
	public static function build($type, $options)
  	{
	    $client = "Client_" . ucwords($type);

	    if (class_exists($client))
	    	return new $client($options);
	    else 
	    	throw new Exception("Invalid client type given.");
  	}
}

class Client_QBittorrent {
	public $host;
	public $username;
	public $password;
	public $auth;

	public function __construct($options) {
		$this->host = $options['host'];
		$this->username = $options['username'];
		$this->password = $options['password'];
		$this->auth = $options['auth'];
	}

	public function addTorrents($files) {
		$body = (new FormBody);

		foreach ($files as $value) {
		    $body->addFile('torrents', $value, 'application/x-bittorrent');
		}

		// Prepare Request
		$request = (new Request)
		    ->setUri($this->host.'/command/upload')
		    ->setMethod('POST')
		    ->setBody($body);
		
		// Add auth header
		if ($this->auth)
		{
			// Digest Authentification
			$nonce = md5(uniqid());
			$uri = '/';
			$realm = 'Web UI Access';

			$A1 = md5("$this->username:$realm:$this->password");
			$A2 = md5("POST" . ":$uri");
			$response = md5("$A1:$nonce:$A2");

			$auth_header = sprintf('Digest username="%s", realm="Web UI Access", nonce="%s", uri="/", response="%s"',$this->username, $nonce, $response);

			$request->setHeader('WWW-Authenticate', $auth_header);
		}

		// Send Request
		try {
		    $promise = (new Client)->request($request);
		    $response = \Amp\wait($promise);
		} catch (Amp\Artax\ClientException $e) {
		    echo $e;
		}
	}

}

class Client_Utorrent {
	public $host;

	public function __construct($args) {
		$this->host = $args['host'];
	}

	public function addTorrents($files) {


	}
	
}

class Client_Transmission {
	public $host;

	public function __construct($args) {
		$this->host = $args['host'];
	}

	public function addTorrents($files) {


	}
	
}