<?php 
use Sunra\PhpSimple\HtmlDomParser;

class TrackerFactory {
	public static function build($type, $credentials, $options, $search_name)
  	{
	    $tracker = "Tracker_" . ucwords($type);

	    if (class_exists($tracker))
	    	return new $tracker($credentials, $options, $search_name);
	    else 
	    	throw new Exception("Invalid tracker type given.");
  	}
}

abstract class TrackerGeneral {

	// Credentials
	public $username;
	public $password;

	// Domain and urls
	public $domain;
	public $login_url;
	public $search_url;

	// Result vars and s/o options
	public $html;
	public $torrents;
	public $search_name;

	// Other internal vars
	protected $agent = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5';
	protected $curl_handler;

	public function __construct($credentials, $options, $search_name = '') {
		$this->username = $credentials['username'];
		$this->password = $credentials['password'];
		$this->options = $options;
		$this->search_name = $search_name;
	}

	public function execute() {
		$html = $this->getHtml();
		$data = $this->parseHtml($html);

		return $data;
	}

	public function connect () {
		$this->curl_handler = curl_init();

		// Capture cookie
		curl_setopt($this->curl_handler, CURLOPT_URL, $this->login_url);
		curl_setopt($this->curl_handler, CURLOPT_POST, 1);
		curl_setopt($this->curl_handler, CURLOPT_USERAGENT, $this->agent);
		curl_setopt($this->curl_handler, CURLOPT_POSTFIELDS, 'username='.$this->username.'&password='.$this->password);
		curl_setopt($this->curl_handler, CURLOPT_COOKIEJAR, tempnam(sys_get_temp_dir(), 'Tripper'));
		curl_setopt($this->curl_handler, CURLOPT_RETURNTRANSFER, 1);

		$store 		= curl_exec($this->curl_handler);

		return $store;
	}

	public function disconnect () {
		return curl_close($this->curl_handler);
	}

	public function download($links, $path = false) {
		if (empty($links))
			return false;

		$paths = array();

		// Get file data
		foreach ($links as $link) {
			if (!empty($link['href']))
			{
				curl_setopt($this->curl_handler, CURLOPT_URL, $link['href']);
				$data 	= curl_exec($this->curl_handler);

				if (!$path)
				{
					$temp_name = tempnam($path, 'Tripper');
					file_put_contents($temp_name, $data);
					$paths[] = $temp_name;
				}
				else
				{
					file_put_contents($path.$link['name'], $data);
					$paths[] = $path . $link['name'];
				}
			}
		}

		return $paths;
	}

	public function getData ($url) {

		// Get file data
		curl_setopt($this->curl_handler, CURLOPT_URL, $url);
		$data 	= curl_exec($this->curl_handler);

		return $data;
	}

	protected function getHtml($path = '') {

		if (!empty($path))
			return file_get_contents($path);

		$html = $this->getData($this->search_url.urlencode($this->search_name));

		$dom = HtmlDomParser::str_get_html($html);
		$elements = $dom->find("a[href*='&page=']");

		foreach($elements as $a)
		{
			$html .= $this->getData($a->href);
			output($a->href);
		}
		return $html;
	}

	protected function parseHtml($html) {
		// General function
	}
}

class Tracker_FreshonTv extends TrackerGeneral {

	public $domain = "https://freshon.tv";
	public $login_url = "https://freshon.tv/login.php?action=makelogin";
	public $search_url = "https://freshon.tv/browse.php?search=";

	public function __construct($credentials, $options, $search_name = '') {

		// Execute parent __construct
		$args = func_get_args();
		call_user_func_array(array($this, 'parent::__construct'), $args);

		// Add tab=hd to the search link
		if ($this->options['hd_only'])
		{
			$this->search_url = "https://freshon.tv/browse.php?tab=hd&search=";
		}
	}

	protected function parseHtml($html) {
		$dom = HtmlDomParser::str_get_html($html);
		$data = array();
		// Search for the anchors
		$elements = $dom->find("a.torrent_name_link");

		foreach($elements as $a)
		{
			$data[] = array(
				'href' 	=> $this->domain.str_replace('details', 'download', $a->href) . "&type=torrent",
				'name' 	=> $a->innertext
			);
		}

		return $data;
	}
}

class Tracker_Filelist extends TrackerGeneral {

	public $domain = "http://filelist.ro/";
	public $download_link = "http://filelist.ro/download.php/";
	public $login_url = "http://filelist.ro/takelogin.php";
	public $search_url = "http://filelist.ro/browse.php?search=";

	protected function parseHtml($html) {
		$dom = HtmlDomParser::str_get_html($html);

		// Search for the anchors
		$elements = $dom->find("a[href*='details.php?']");

		foreach($elements as $a)
		{
			// Get the id of the torrent
			preg_match("/id=([0-9]+)/", $a->href, $out);

			if (!empty($a->title))
			{
				$data[] = array(
					'href' 	=> $this->download_link.$out[1]."/".$a->title.".torrent",
					'name' 	=> $a->title
				);
			}
		}

		return $data;
	}
}