<?php 

namespace Charmpitz;

abstract class AbstractTracker {

    protected $username;
    protected $password;

    protected $domain    = '';
    protected $loginUrl  = '';
    protected $searchUrl = '';

    public $html;
    public $torrents;
    public $searchName;

    protected $userAgent = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5';
    protected $curlHandler;

    public function __construct($credentials) {
        $this->username = $credentials['username'];
        $this->password = $credentials['password'];
    }

    public function execute() {
        $html = $this->getHtml();
        $data = $this->parseHtml($html);

        return $data;
    }

    public function connect() {
        $this->curlHandler = curl_init();

        # Capture cookie
        curl_setopt($this->curlHandler, CURLOPT_URL, $this->loginUrl);
        curl_setopt($this->curlHandler, CURLOPT_POST, 1);
        curl_setopt($this->curlHandler, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, 'username='.$this->username.'&password='.$this->password);
        curl_setopt($this->curlHandler, CURLOPT_COOKIEJAR, tempnam(sys_get_temp_dir(), 'Tripper'));
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, 1);

        $store = curl_exec($this->curlHandler);

        return $store;
    }

    public function disconnect() {
        return curl_close($this->curlHandler);
    }

    public function download($links, $path = false) {
        if (empty($links)) {
            return false;
        }

        $paths = array();

        # Get file data
        foreach ($links as $link) {
            if (!empty($link['href'])) {
                $data = $this->getData($link['href']);

                if (!$path) {
                    $fileName = tempnam($path, 'Tripper');
                } else {
                    $fileName = $path . $link['name'];
                }

                file_put_contents($fileName, $data);
                $paths[] = $fileName;
            }
        }

        return $paths;
    }

    public function getData($url) {
        # Get file data
        curl_setopt($this->curlHandler, CURLOPT_URL, $url);
        return curl_exec($this->curlHandler);
    }
}